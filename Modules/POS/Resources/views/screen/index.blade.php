@extends('layouts.app')

@section('title', 'شاشة الكاشير ونقاط البيع السريعة')

@section('content')
<style>
/* POS Custom UI Styles */
.pos-container {
    display: flex;
    gap: 16px;
    height: calc(100vh - 120px);
    min-height: 600px;
}
.pos-catalog-panel {
    flex: 1.5;
    display: flex;
    flex-direction: column;
    gap: 14px;
    overflow: hidden;
}
.pos-cart-panel {
    flex: 1;
    min-width: 380px;
    max-width: 460px;
    background: #FFFFFF;
    border-radius: 16px;
    border: 1.5px solid var(--border-color);
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.category-pill {
    padding: 8px 16px;
    border-radius: 20px;
    background: #F1F5F9;
    color: var(--text-main);
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    border: 1px solid transparent;
    white-space: nowrap;
    transition: all 0.2s;
}
.category-pill.active, .category-pill:hover {
    background: var(--primary);
    color: #FFFFFF;
}
.product-card {
    background: #FFFFFF;
    border: 1.5px solid var(--border-color);
    border-radius: 14px;
    padding: 14px;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.15s ease-in-out;
    user-select: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}
.product-card:hover {
    border-color: var(--primary);
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(15, 81, 50, 0.1);
}
.product-card:active {
    transform: scale(0.97);
}
.cart-items-scroll {
    flex: 1;
    overflow-y: auto;
    padding: 12px 16px;
}
.cart-item-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px dashed var(--border-color);
    gap: 8px;
}
.qty-btn {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    background: #F8FAFC;
    font-weight: 800;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
.qty-btn:hover {
    background: var(--primary-light);
    color: var(--primary);
}
</style>

@if(!$activeShift)
    <!-- No Active Shift: Open Shift Dialog Screen -->
    <div style="max-width: 520px; margin: 40px auto; background: #fff; border-radius: 20px; padding: 32px; border: 1.5px solid var(--border-color); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05); text-align: center;">
        <div style="width: 70px; height: 70px; border-radius: 50%; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 16px auto;">
            <i class="fa-solid fa-cash-register"></i>
        </div>
        <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); margin-bottom: 8px;">
            فتح وردية كاشير جديدة
        </h2>
        <p style="font-size: 13.5px; color: var(--text-muted); margin-bottom: 24px;">
            مرحباً بك! لبدء تشغيل شاشة نقاط البيع السريعة، يرجى اختيار نقطة البيع وتسجيل رصيد العهدة النقدية في الدرج.
        </p>

        <form action="{{ route('pos.shifts.open') }}" method="POST" style="text-align: right;">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                        نقطة البيع / الكاشير <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="terminal_id" required style="width: 100%; padding: 11px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px; font-weight: 700;">
                        @foreach($terminals as $term)
                            <option value="{{ $term->id }}">{{ $term->name }} (مستودع: {{ $term->store->name ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                        رصيد العهدة النقدية المستلمة في الدرج (Opening Cash) <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="number" step="0.01" min="0" name="opening_balance" value="0.00" required placeholder="0.00" style="width: 100%; padding: 11px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 16px; font-weight: 900; color: var(--primary); text-align: left;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                        ملاحظات استلام الوردية
                    </label>
                    <textarea name="start_notes" rows="2" placeholder="أي فئات نقدية خاصة أو ملاحظات..." style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;"></textarea>
                </div>

                <button type="submit" class="btn-primary-custom" style="width: 100%; padding: 14px; font-size: 16px; justify-content: center; margin-top: 8px;">
                    <i class="fa-solid fa-play"></i>
                    فتح الوردية وبدء البيع
                </button>
            </div>
        </form>
    </div>
@else
    <!-- Active Shift: High-Speed POS Interface -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: -10px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <span class="badge" style="background: var(--primary-light); color: var(--primary); font-size: 13px; font-weight: 800; padding: 6px 14px;">
                <i class="fa-solid fa-cash-register"></i> {{ $activeShift->terminal->name ?? 'POS' }}
            </span>
            <span style="font-size: 13px; font-weight: 700; color: var(--text-main);">
                الوردية: <strong style="font-family: monospace; color: var(--accent-gold);">{{ $activeShift->shift_number }}</strong>
            </span>
            <span style="font-size: 12.5px; color: var(--text-muted);">
                الكاشير: <strong>{{ Auth::user()->name }}</strong>
            </span>
        </div>

        <div style="display: flex; gap: 8px; align-items: center;">
            <button onclick="openHeldOrdersModal()" style="background: #FFFBEB; border: 1px solid #FDE68A; color: #B45309; padding: 8px 14px; border-radius: 10px; font-weight: 700; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-clock-rotate-left"></i>
                الفواتير المعلقة (<span id="heldOrdersCount">{{ $activeShift->heldOrders()->count() }}</span>)
            </button>

            <button onclick="document.getElementById('closeShiftModal').style.display='flex'" style="background: #FEE2E2; border: 1px solid #FECACA; color: #DC2626; padding: 8px 16px; border-radius: 10px; font-weight: 700; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-lock"></i>
                إغلاق الوردية والدرج
            </button>
        </div>
    </div>

    <!-- Main Workspace -->
    <div class="pos-container">

        <!-- Products Catalog Column -->
        <div class="pos-catalog-panel">
            
            <!-- Barcode & Search Bar -->
            <div style="background: #fff; padding: 12px 16px; border-radius: 14px; border: 1px solid var(--border-color); display: flex; gap: 12px; align-items: center;">
                <div style="flex: 1.5; position: relative;">
                    <input type="text" id="barcodeInput" autofocus placeholder="مسدس الباركود السريع (امسح الباركود أو اضغط Enter)..." style="width: 100%; padding: 10px 38px 10px 14px; border-radius: 10px; border: 1.5px solid var(--primary); font-size: 14px; font-weight: 700;">
                    <i class="fa-solid fa-barcode" style="position: absolute; right: 12px; top: 12px; color: var(--primary); font-size: 18px;"></i>
                </div>

                <div style="flex: 1; position: relative;">
                    <input type="text" id="searchFilter" oninput="filterProducts()" placeholder="بحث باسم الصنف..." style="width: 100%; padding: 10px 34px 10px 12px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">
                    <i class="fa-solid fa-search" style="position: absolute; right: 12px; top: 12px; color: #94A3B8;"></i>
                </div>
            </div>

            <!-- Categories Horizontal Pills Bar -->
            <div style="display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px;" id="categoryPills">
                <button type="button" class="category-pill active" onclick="filterCategory(null, this)">الكل ({{ $items->count() }})</button>
                @foreach($categories as $cat)
                    <button type="button" class="category-pill" onclick="filterCategory({{ $cat->id }}, this)">{{ $cat->name }}</button>
                @endforeach
            </div>

            <!-- Touch Product Cards Grid -->
            <div id="productGrid" style="flex: 1; overflow-y: auto; display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; padding-bottom: 8px;">
                @foreach($items as $item)
                    <div class="product-card" data-category="{{ $item->category_id }}" data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-code="{{ $item->code }}" data-barcode="{{ $item->barcode }}" data-price="{{ $item->sell_price }}" data-wholesale="{{ $item->wholesale_price ?: $item->sell_price }}" data-half="{{ $item->half_wholesale_price ?: $item->sell_price }}" onclick="addToCart({{ $item->id }})">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px;">
                                <span style="font-size: 10.5px; background: #F1F5F9; color: #475569; padding: 2px 6px; border-radius: 4px; font-family: monospace;">{{ $item->code }}</span>
                                <span style="font-size: 10px; color: #16A34A; font-weight: 700;">متوفر</span>
                            </div>
                            <strong style="font-size: 13px; color: var(--text-main); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                                {{ $item->name }}
                            </strong>
                        </div>
                        <div style="margin-top: 10px; display: flex; justify-content: space-between; align-items: baseline;">
                            <span style="font-size: 15px; font-weight: 900; color: var(--primary);">
                                {{ formatMoney($item->sell_price) }}
                            </span>
                            <span style="font-size: 11px; color: var(--text-muted);"><i class="fa-solid fa-plus-circle"></i></span>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>

        <!-- POS Cart & Checkout Panel -->
        <div class="pos-cart-panel">
            
            <!-- Cart Header / Customer selector -->
            <div style="padding: 14px 16px; border-bottom: 1.5px solid var(--border-color); background: #F8FAFC;">
                <label style="display: block; font-size: 11.5px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">العميل (الخصم والشريحة التسعيرية):</label>
                <select id="posClientSelect" onchange="onPosClientChange()" style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; font-weight: 700;">
                    <option value="" data-tier="retail" data-discount="0">-- عميل نقدي عام (تجزئة) --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" data-tier="{{ $c->group ? $c->group->price_tier : 'retail' }}" data-discount="{{ $c->group ? $c->group->discount_percentage : 0 }}">
                            {{ $c->name }} ({{ $c->group ? $c->group->name : 'فئة عامة' }})
                        </option>
                    @endforeach
                </select>
                <div id="posClientDiscountNotice" style="display: none; font-size: 11.5px; color: var(--primary); font-weight: 700; margin-top: 4px;"></div>
            </div>

            <!-- Cart Items List (Scrollable) -->
            <div class="cart-items-scroll" id="cartItemsContainer">
                <div id="emptyCartNotice" style="text-align: center; color: var(--text-muted); padding: 40px 0;">
                    <i class="fa-solid fa-basket-shopping" style="font-size: 44px; color: #CBD5E1; margin-bottom: 10px; display: block;"></i>
                    السلة فارغة حالياً. اضغط على أي صنف أو امسح الباركود لإضافته.
                </div>
                <div id="cartItemsList"></div>
            </div>

            <!-- Cart Calculation Breakdown -->
            <div style="background: #F8FAFC; border-top: 1.5px solid var(--border-color); padding: 14px 16px;">
                <div style="display: flex; flex-direction: column; gap: 6px; font-size: 13px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">المجموع:</span>
                        <strong id="posSubtotalText">0.00</strong>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--text-muted);">خصم إضافي:</span>
                        <div style="display: flex; gap: 4px; width: 45%;">
                            <input type="number" step="0.01" min="0" id="posDiscountInput" value="0.00" oninput="recalcCart()" placeholder="0.00" style="width: 100%; padding: 4px 6px; border-radius: 6px; border: 1px solid var(--border-color); font-size: 12px; text-align: left; font-weight: 700;">
                            <select id="posDiscountType" onchange="recalcCart()" style="padding: 4px; border-radius: 6px; border: 1px solid var(--border-color); font-size: 11px;">
                                <option value="percentage">%</option>
                                <option value="fixed">مبلغ</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--text-muted);">ضريبة (VAT):</span>
                        <select id="posTaxSelect" onchange="recalcCart()" style="padding: 4px 8px; border-radius: 6px; border: 1px solid var(--border-color); font-size: 12px; font-weight: 600;">
                            <option value="" data-rate="0">بدون ضريبة (0%)</option>
                            @foreach($taxes as $t)
                                <option value="{{ $t->id }}" data-rate="{{ $t->rate }}" {{ $t->is_default ? 'selected' : '' }}>{{ $t->name }} ({{ $t->rate }}%)</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1.5px dashed var(--border-color); padding-top: 8px; margin-top: 2px;">
                        <span style="font-size: 16px; font-weight: 900; color: var(--primary);">الإجمالي المستحق:</span>
                        <strong id="posGrandTotalText" style="font-size: 22px; font-weight: 900; color: var(--primary);">0.00</strong>
                    </div>

                    <!-- Cash Input & Change -->
                    <div style="display: flex; justify-content: space-between; align-items: center; background: #FFFBEB; padding: 6px 10px; border-radius: 8px; border: 1px solid #FDE68A; margin-top: 4px;">
                        <span style="font-size: 12px; font-weight: 700; color: #92400E;">المستلم من العميل:</span>
                        <input type="number" step="0.01" min="0" id="posCashReceived" oninput="calcChange()" placeholder="0.00" style="width: 90px; padding: 4px 6px; border-radius: 6px; border: 1px solid #D97706; font-size: 13px; font-weight: 800; text-align: left;">
                        <span style="font-size: 12px; font-weight: 700; color: #0F5132;">الباقي: <strong id="posChangeText">0.00</strong></span>
                    </div>
                </div>

                <!-- Action Checkout Buttons Grid -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 12px;">
                    <button type="button" onclick="checkout('cash')" class="btn-primary-custom" style="padding: 12px; justify-content: center; font-size: 14px; font-weight: 800;">
                        <i class="fa-solid fa-money-bill-wave"></i> نقدي (كاش)
                    </button>
                    <button type="button" onclick="checkout('card')" style="background: #1E3A8A; color: #fff; border: none; border-radius: 10px; padding: 12px; font-size: 14px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <i class="fa-solid fa-credit-card"></i> شبكة / مدى
                    </button>
                    <button type="button" onclick="holdCurrentCart()" style="background: #FEF3C7; color: #B45309; border: 1px solid #FDE68A; border-radius: 10px; padding: 8px; font-size: 12.5px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <i class="fa-solid fa-hand"></i> تعليق الفاتورة
                    </button>
                    <button type="button" onclick="clearCart()" style="background: #FEE2E2; color: #DC2626; border: 1px solid #FECACA; border-radius: 10px; padding: 8px; font-size: 12.5px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <i class="fa-solid fa-trash-can"></i> مسح السلة
                    </button>
                </div>
            </div>

        </div>

    </div>

    <!-- Modal: Close Shift -->
    <div id="closeShiftModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050; align-items: center; justify-content: center; padding: 16px;">
        <div style="background: #fff; width: 100%; max-width: 480px; border-radius: 16px; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                <h3 style="font-size: 17px; font-weight: 900; color: #DC2626; margin: 0;">
                    <i class="fa-solid fa-lock"></i> تقفيل الوردية وجرد النقدية بالدرج
                </h3>
                <button onclick="document.getElementById('closeShiftModal').style.display='none'" style="background: none; border: none; font-size: 18px; color: #94A3B8; cursor: pointer;">&times;</button>
            </div>

            <form action="{{ route('pos.shifts.close', $activeShift->id) }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 10px; padding: 12px; font-size: 13px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                            <span style="color: var(--text-muted);">رصيد العهدة الافتتاحي:</span>
                            <strong>{{ formatMoney($activeShift->opening_balance) }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                            <span style="color: var(--text-muted);">مبيعات الكاش بالوردية:</span>
                            <strong style="color: #0F5132;">{{ formatMoney($activeShift->cash_sales) }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-weight: 900; color: var(--primary); border-top: 1px dashed var(--border-color); padding-top: 6px;">
                            <span>النقدية المتوقعة في الدرج:</span>
                            <span>{{ formatMoney($activeShift->expected_cash) }}</span>
                        </div>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            المبلغ الفعلي المحصى في الدرج (Actual Cash) <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="number" step="0.01" min="0" name="actual_cash" required placeholder="0.00" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 16px; font-weight: 900; text-align: left;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            ملاحظات الإغلاق والتسليم
                        </label>
                        <textarea name="end_notes" rows="2" placeholder="أي فروقات أو ملاحظات استلام للمشرف..." style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;"></textarea>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                        <button type="button" onclick="document.getElementById('closeShiftModal').style.display='none'" style="background: #E2E8F0; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; color: #475569; cursor: pointer;">إلغاء</button>
                        <button type="submit" style="background: #DC2626; color: #fff; border: none; padding: 10px 24px; border-radius: 10px; font-weight: 800; cursor: pointer;">تأكيد الإغلاق وطباعة تقرير Z</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Held Orders -->
    <div id="heldOrdersModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050; align-items: center; justify-content: center; padding: 16px;">
        <div style="background: #fff; width: 100%; max-width: 540px; border-radius: 16px; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                <h3 style="font-size: 17px; font-weight: 900; color: #B45309; margin: 0;">
                    <i class="fa-solid fa-clock-rotate-left"></i> الفواتير المعلقة بالوردية
                </h3>
                <button onclick="document.getElementById('heldOrdersModal').style.display='none'" style="background: none; border: none; font-size: 18px; color: #94A3B8; cursor: pointer;">&times;</button>
            </div>

            <div id="heldOrdersList" style="max-height: 350px; overflow-y: auto;">
                <!-- Loaded via JS -->
            </div>
        </div>
    </div>

    <!-- Hidden Printable Receipt Frame -->
    <iframe id="receiptPrintFrame" style="display: none;"></iframe>

    <!-- Client-side POS Engine Script -->
    <script>
    const posShiftId = {{ $activeShift->id }};
    const catalogItems = @json($items);
    let cart = []; // Array of { item_id, name, code, quantity, unit_price, subtotal }

    // Auto-focus barcode scanner
    const barcodeInput = document.getElementById('barcodeInput');
    barcodeInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const val = this.value.trim();
            if (!val) return;

            // Match barcode or code or id
            const matched = catalogItems.find(i => (i.barcode && i.barcode.toLowerCase() === val.toLowerCase()) || (i.code && i.code.toLowerCase() === val.toLowerCase()));
            if (matched) {
                addToCart(matched.id);
                this.value = '';
            } else {
                alert('لم يتم العثور على صنف بالباركود: ' + val);
                this.select();
            }
        }
    });

    function addToCart(itemId) {
        const item = catalogItems.find(i => i.id === itemId);
        if (!item) return;

        // Check if customer tier provides a different price
        const clientSelect = document.getElementById('posClientSelect');
        const opt = clientSelect.options[clientSelect.selectedIndex];
        const tier = opt ? opt.dataset.tier : 'retail';

        let price = parseFloat(item.sell_price) || 0;
        if (tier === 'wholesale') {
            price = parseFloat(item.wholesale_price) || price;
        } else if (tier === 'half_wholesale') {
            price = parseFloat(item.half_wholesale_price) || price;
        }

        const existing = cart.find(c => c.item_id === itemId);
        if (existing) {
            existing.quantity += 1;
            existing.subtotal = existing.quantity * existing.unit_price;
        } else {
            cart.push({
                item_id: item.id,
                name: item.name,
                code: item.code,
                quantity: 1,
                unit_price: price,
                subtotal: price
            });
        }

        renderCart();
    }

    function updateQty(idx, delta) {
        if (!cart[idx]) return;
        cart[idx].quantity += delta;
        if (cart[idx].quantity <= 0) {
            cart.splice(idx, 1);
        } else {
            cart[idx].subtotal = cart[idx].quantity * cart[idx].unit_price;
        }
        renderCart();
    }

    function removeCartItem(idx) {
        cart.splice(idx, 1);
        renderCart();
    }

    function clearCart() {
        if (cart.length > 0 && confirm('هل أنت متأكد من مسح جميع عناصر السلة؟')) {
            cart = [];
            renderCart();
        }
    }

    function renderCart() {
        const list = document.getElementById('cartItemsList');
        const emptyNotice = document.getElementById('emptyCartNotice');

        if (cart.length === 0) {
            emptyNotice.style.display = 'block';
            list.innerHTML = '';
        } else {
            emptyNotice.style.display = 'none';
            let html = '';
            cart.forEach((c, idx) => {
                html += `
                    <div class="cart-item-row">
                        <div style="flex: 2;">
                            <strong style="font-size: 13px; color: var(--text-main); display: block;">${c.name}</strong>
                            <small style="color: var(--text-muted); font-size: 11px;">${c.unit_price.toFixed(2)} للوحدة</small>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <button type="button" class="qty-btn" onclick="updateQty(${idx}, -1)">-</button>
                            <span style="font-weight: 800; font-size: 13.5px; min-width: 20px; text-align: center;">${c.quantity}</span>
                            <button type="button" class="qty-btn" onclick="updateQty(${idx}, 1)">+</button>
                        </div>
                        <div style="flex: 1; text-align: left;">
                            <strong style="font-size: 13.5px; color: var(--primary);">${c.subtotal.toFixed(2)}</strong>
                        </div>
                        <button type="button" onclick="removeCartItem(${idx})" style="background: none; border: none; color: #DC2626; cursor: pointer; padding: 4px;">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                `;
            });
            list.innerHTML = html;
        }

        recalcCart();
        barcodeInput.focus();
    }

    function recalcCart() {
        let subtotal = 0;
        cart.forEach(c => subtotal += c.subtotal);
        document.getElementById('posSubtotalText').innerText = subtotal.toFixed(2);

        // Discount
        const discType = document.getElementById('posDiscountType').value;
        const discVal = parseFloat(document.getElementById('posDiscountInput').value) || 0;
        let discAmount = discType === 'percentage' ? (subtotal * discVal) / 100 : Math.min(discVal, subtotal);
        const afterDiscount = Math.max(0, subtotal - discAmount);

        // Tax
        const taxSelect = document.getElementById('posTaxSelect');
        const taxRate = parseFloat(taxSelect.options[taxSelect.selectedIndex].dataset.rate) || 0;
        const taxAmount = (afterDiscount * taxRate) / 100;

        const grandTotal = afterDiscount + taxAmount;
        document.getElementById('posGrandTotalText').innerText = grandTotal.toFixed(2);

        calcChange();
    }

    function calcChange() {
        const grandTotal = parseFloat(document.getElementById('posGrandTotalText').innerText) || 0;
        const received = parseFloat(document.getElementById('posCashReceived').value) || 0;
        const change = Math.max(0, received - grandTotal);
        document.getElementById('posChangeText').innerText = change.toFixed(2);
    }

    function onPosClientChange() {
        const clientSelect = document.getElementById('posClientSelect');
        const opt = clientSelect.options[clientSelect.selectedIndex];
        const notice = document.getElementById('posClientDiscountNotice');

        if (opt && parseFloat(opt.dataset.discount) > 0) {
            notice.style.display = 'block';
            notice.innerText = 'خصم الشريحة التلقائي: ' + opt.dataset.discount + '%';
            document.getElementById('posDiscountInput').value = opt.dataset.discount;
            document.getElementById('posDiscountType').value = 'percentage';
        } else {
            notice.style.display = 'none';
        }

        // Recalculate cart unit prices with client tier
        cart.forEach(c => {
            const item = catalogItems.find(i => i.id === c.item_id);
            if (item) {
                const tier = opt ? opt.dataset.tier : 'retail';
                let price = parseFloat(item.sell_price) || 0;
                if (tier === 'wholesale') {
                    price = parseFloat(item.wholesale_price) || price;
                } else if (tier === 'half_wholesale') {
                    price = parseFloat(item.half_wholesale_price) || price;
                }
                c.unit_price = price;
                c.subtotal = c.quantity * price;
            }
        });

        renderCart();
    }

    function filterCategory(catId, btn) {
        document.querySelectorAll('.category-pill').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        document.querySelectorAll('.product-card').forEach(card => {
            if (!catId || card.dataset.category == catId) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function filterProducts() {
        const q = document.getElementById('searchFilter').value.toLowerCase().trim();
        document.querySelectorAll('.product-card').forEach(card => {
            const name = card.dataset.name.toLowerCase();
            const code = (card.dataset.code || '').toLowerCase();
            if (!q || name.includes(q) || code.includes(q)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Checkout via AJAX
    function checkout(paymentType) {
        if (cart.length === 0) {
            alert('السلة فارغة! يرجى إضافة أصناف أولاً.');
            return;
        }

        const clientId = document.getElementById('posClientSelect').value;
        const discountType = document.getElementById('posDiscountType').value;
        const discountVal = document.getElementById('posDiscountInput').value;
        const taxId = document.getElementById('posTaxSelect').value;
        const paidAmount = document.getElementById('posCashReceived').value;

        const payload = {
            _token: '{{ csrf_token() }}',
            shift_id: posShiftId,
            client_id: clientId || null,
            payment_type: paymentType,
            discount_type: discountType,
            discount_value: discountVal,
            tax_id: taxId || null,
            paid_amount: paidAmount || null,
            items: cart.map(c => ({
                item_id: c.item_id,
                quantity: c.quantity,
                unit_price: c.unit_price
            }))
        };

        fetch("{{ route('pos.checkout') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Trigger instant thermal print in background frame
                if (data.print_url) {
                    const frame = document.getElementById('receiptPrintFrame');
                    frame.src = data.print_url;
                }

                alert(`تم بنجاح! فاتورة رقم: ${data.invoice_number}\nالمطلوب: ${data.grand_total}\nالمسدد: ${data.paid_amount}\nالباقي: ${data.change_amount}`);
                cart = [];
                document.getElementById('posCashReceived').value = '';
                renderCart();
            } else {
                alert('فشل العملية: ' + data.message);
            }
        })
        .catch(err => {
            alert('حدث خطأ في الاتصال بالسيرفر: ' + err);
        });
    }

    // Hold / Park Order
    function holdCurrentCart() {
        if (cart.length === 0) {
            alert('لا توجد عناصر لتعليقها بالسلة!');
            return;
        }

        const clientId = document.getElementById('posClientSelect').value;
        let subtotal = 0;
        cart.forEach(c => subtotal += c.subtotal);

        fetch("{{ route('pos.hold') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                _token: '{{ csrf_token() }}',
                shift_id: posShiftId,
                client_id: clientId || null,
                cart_data: cart,
                subtotal: subtotal
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                cart = [];
                renderCart();
                updateHeldCount();
            }
        });
    }

    function openHeldOrdersModal() {
        fetch("{{ url('pos/held') }}/" + posShiftId)
            .then(res => res.json())
            .then(data => {
                const list = document.getElementById('heldOrdersList');
                if (!data.orders || data.orders.length === 0) {
                    list.innerHTML = '<div style="text-align: center; color: var(--text-muted); padding: 30px;">لا توجد فواتير معلقة حالياً</div>';
                } else {
                    let html = '';
                    data.orders.forEach(ord => {
                        html += `
                            <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 10px; padding: 12px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <strong style="color: var(--primary); font-size: 14px;">${ord.reference_code}</strong>
                                    <div style="font-size: 12px; color: var(--text-muted);">العميل: ${ord.client ? ord.client.name : 'نقدي عام'} | القيمة: ${ord.subtotal.toFixed(2)}</div>
                                </div>
                                <button type="button" onclick="recallHeld(${ord.id})" class="btn-primary-custom" style="padding: 6px 14px; font-size: 12px;">
                                    استعادة للسلة
                                </button>
                            </div>
                        `;
                    });
                    list.innerHTML = html;
                }
                document.getElementById('heldOrdersModal').style.display = 'flex';
            });
    }

    function recallHeld(id) {
        fetch("{{ url('pos/held') }}/" + id, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                cart = data.cart_data;
                renderCart();
                document.getElementById('heldOrdersModal').style.display = 'none';
                updateHeldCount();
            }
        });
    }

    function updateHeldCount() {
        fetch("{{ url('pos/held') }}/" + posShiftId)
            .then(res => res.json())
            .then(data => {
                document.getElementById('heldOrdersCount').innerText = data.orders.length;
            });
    }
    </script>
@endif
@endsection

