<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | نظام العزيزي ERP</title>

    <!-- Google Fonts (Cairo) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #0F5132;
            --primary-hover: #0B3B24;
            --accent-gold: #D97706;
            --sidebar-bg: #062E20;
            --border-color: #E2E8F0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #062E20 0%, #0F5132 50%, #064E3B 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: #1E293B;
        }

        .login-card {
            background: #FFFFFF;
            width: 100%;
            max-width: 480px;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--accent-gold) 0%, var(--primary) 100%);
        }

        .brand-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--accent-gold) 0%, #B45309 100%);
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            font-size: 30px;
            margin-bottom: 16px;
            box-shadow: 0 8px 20px rgba(217, 119, 6, 0.4);
        }

        .brand-title {
            font-size: 24px;
            font-weight: 900;
            color: #062E20;
            letter-spacing: -0.5px;
        }

        .brand-subtitle {
            font-size: 13.5px;
            color: #64748B;
            font-weight: 600;
            margin-top: 4px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13.5px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #334155;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group i {
            position: absolute;
            right: 14px;
            color: #94A3B8;
            font-size: 16px;
        }

        .form-control {
            width: 100%;
            padding: 12px 42px 12px 16px;
            border: 1.5px solid var(--border-color);
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.2s;
            outline: none;
            background: #F8FAFC;
        }

        .form-control:focus {
            border-color: var(--primary);
            background: #FFFFFF;
            box-shadow: 0 0 0 4px rgba(15, 81, 50, 0.1);
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, #064E3B 100%);
            color: #FFFFFF;
            border: none;
            padding: 13px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 6px 18px rgba(15, 81, 50, 0.3);
            margin-top: 10px;
        }

        .btn-submit:hover {
            box-shadow: 0 8px 22px rgba(15, 81, 50, 0.45);
            transform: translateY(-1px);
        }

        .alert-error {
            background-color: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FCA5A5;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .demo-box {
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px dashed var(--border-color);
            text-align: center;
        }

        .demo-label {
            font-size: 12px;
            font-weight: 700;
            color: #64748B;
            margin-bottom: 12px;
            display: block;
        }

        .demo-btns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .btn-demo {
            background: #F1F5F9;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 8px 10px;
            font-size: 12px;
            font-weight: 700;
            color: #334155;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-demo:hover {
            background: var(--primary);
            color: #FFFFFF;
            border-color: var(--primary);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="brand-header">
            <div class="brand-icon">
                <i class="fa-solid fa-shapes"></i>
            </div>
            <h1 class="brand-title">نظام العزيزي ERP</h1>
            <p class="brand-subtitle">منظومة إدارة الموارد والتجارة المتكاملة (SaaS)</p>
        </div>

        @if($errors->any())
            <div class="alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">البريد الإلكتروني</label>
                <div class="input-group">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', 'manager@elazizy.com') }}" required autofocus placeholder="name@company.com">
                </div>
            </div>

            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label class="form-label" for="password" style="margin: 0;">كلمة المرور</label>
                </div>
                <div class="input-group">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" id="password" class="form-control" value="12345678" required placeholder="••••••••">
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
                <input type="checkbox" name="remember" id="remember" style="accent-color: var(--primary); width: 16px; height: 16px;">
                <label for="remember" style="font-size: 13px; font-weight: 600; color: #475569; cursor: pointer;">تذكر تسجيل دخولي</label>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-arrow-right-to-bracket" style="margin-left: 6px;"></i>
                تسجيل الدخول للنظام
            </button>
        </form>

        <div class="demo-box">
            <span class="demo-label">حسابات تجريبية جاهزة للاختبار بنقرة واحدة:</span>
            <div class="demo-btns">
                <button type="button" class="btn-demo" onclick="fillLogin('manager@elazizy.com', '12345678')">
                    <i class="fa-solid fa-user-tie"></i> مدير شركة العزيزي
                </button>
                <button type="button" class="btn-demo" onclick="fillLogin('admin@elazizy-erp.com', '12345678')">
                    <i class="fa-solid fa-shield-halved"></i> Super Admin
                </button>
            </div>
        </div>
    </div>

    <script>
        function fillLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>

