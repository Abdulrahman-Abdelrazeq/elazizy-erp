<?php

namespace Modules\HR\Database\Seeders;

use App\Models\Branch;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\HR\Entities\Attendance;
use Modules\HR\Entities\Department;
use Modules\HR\Entities\Designation;
use Modules\HR\Entities\Employee;
use Modules\HR\Entities\EmployeeLeave;
use Modules\HR\Entities\EmployeeLoan;
use Modules\HR\Entities\SalaryClause;
use Modules\Treasury\Entities\Treasury;

class HRDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds for Module 9: HR & Payroll.
     */
    public function run(): void
    {
        $tenant = Tenant::first() ?? Tenant::create([
            'name'         => 'مؤسسة العزيزي للتجارة والمقاولات',
            'company_name' => 'شركة العزيزي للأنظمة والحلول المتكاملة',
            'domain'       => 'elazizy',
            'is_active'    => true,
        ]);

        $tenantId = $tenant->id;
        $branch = Branch::where('tenant_id', $tenantId)->first();
        $branchId = $branch ? $branch->id : null;
        $user = User::where('tenant_id', $tenantId)->first() ?? User::first();
        $userId = $user ? $user->id : null;
        $treasury = Treasury::where('tenant_id', $tenantId)->first();
        $treasuryId = $treasury ? $treasury->id : null;

        // 1. Departments (الأقسام)
        $departmentsData = [
            ['name' => 'الإدارة العامة والتنفيذية', 'code' => 'DEPT-01', 'manager_name' => 'أحمد عبد العزيز'],
            ['name' => 'الشؤون المالية والمحاسبة', 'code' => 'DEPT-02', 'manager_name' => 'محمود إبراهيم'],
            ['name' => 'المبيعات والتسويق', 'code' => 'DEPT-03', 'manager_name' => 'عمر طارق'],
            ['name' => 'المستودعات والخدمات اللوجستية', 'code' => 'DEPT-04', 'manager_name' => 'يوسف كريم'],
            ['name' => 'تقنية المعلومات والتحول الرقمي', 'code' => 'DEPT-05', 'manager_name' => 'سامح رضوان'],
        ];

        $deptModels = [];
        foreach ($departmentsData as $d) {
            $deptModels[$d['code']] = Department::updateOrCreate(
                ['tenant_id' => $tenantId, 'code' => $d['code']],
                ['name' => $d['name'], 'manager_name' => $d['manager_name'], 'is_active' => true]
            );
        }

        // 2. Designations (المسميات الوظيفية)
        $designationsData = [
            ['title' => 'المدير التنفيذي العام', 'dept_code' => 'DEPT-01', 'description' => 'الإشراف العام على الإدارات واستراتيجية الشركة'],
            ['title' => 'مدير الإدارة المالية', 'dept_code' => 'DEPT-02', 'description' => 'إدارة التقارير المالية والتدقيق المحاسبي'],
            ['title' => 'محاسب عام أول', 'dept_code' => 'DEPT-02', 'description' => 'القيود المحاسبية ومطابقات البنوك والرواتب'],
            ['title' => 'مشرف مبيعات أول', 'dept_code' => 'DEPT-03', 'description' => 'إدارة فريق المبيعات وعلاقات كبار العملاء'],
            ['title' => 'أمين مستودع رئيسي', 'dept_code' => 'DEPT-04', 'description' => 'إدارة حركات المخزون والجرد الدوري'],
            ['title' => 'مهندس نظم وبرمجيات', 'dept_code' => 'DEPT-05', 'description' => 'تطوير وصيانة البنية التقنية ونظام ERP'],
        ];

        $desigModels = [];
        foreach ($designationsData as $idx => $desig) {
            $dept = $deptModels[$desig['dept_code']] ?? null;
            $desigModels[$desig['title']] = Designation::updateOrCreate(
                ['tenant_id' => $tenantId, 'title' => $desig['title']],
                [
                    'department_id' => $dept ? $dept->id : null,
                    'description'   => $desig['description'],
                    'is_active'     => true,
                ]
            );
        }

        // 3. Salary Clauses (بنود البدلات والخصومات)
        $clausesData = [
            ['name' => 'بدل سكن', 'type' => 'earning', 'is_fixed' => true, 'default_amount' => 1500.00],
            ['name' => 'بدل انتقال ومواصلات', 'type' => 'earning', 'is_fixed' => true, 'default_amount' => 600.00],
            ['name' => 'بدل اتصال وهاتف', 'type' => 'earning', 'is_fixed' => false, 'default_amount' => 200.00],
            ['name' => 'مكافأة أداء وتميز', 'type' => 'earning', 'is_fixed' => false, 'default_amount' => 0.00],
            ['name' => 'تأمينات اجتماعية', 'type' => 'deduction', 'is_fixed' => true, 'default_amount' => 0.00],
            ['name' => 'خصم غياب وتأخير', 'type' => 'deduction', 'is_fixed' => false, 'default_amount' => 0.00],
        ];

        foreach ($clausesData as $c) {
            SalaryClause::updateOrCreate(
                ['tenant_id' => $tenantId, 'name' => $c['name']],
                ['type' => $c['type'], 'is_fixed' => $c['is_fixed'], 'default_amount' => $c['default_amount'], 'is_active' => true]
            );
        }

        // 4. Employees (سجلات الموظفين)
        $employeesData = [
            [
                'emp_code'            => 'EMP-001',
                'name'                => 'أحمد عبد العزيز العزيزي',
                'dept_code'           => 'DEPT-01',
                'desig_title'         => 'المدير التنفيذي العام',
                'phone'               => '0501112233',
                'email'               => 'ahmed@elazizy.com',
                'national_id'         => '1012345678',
                'hire_date'           => '2022-01-01',
                'basic_salary'        => 12000.00,
                'housing_allowance'   => 2500.00,
                'transport_allowance' => 1000.00,
                'other_allowances'    => 500.00,
                'bank_name'           => 'مصرف الراجحي',
                'bank_account'        => 'SA4480000123456789012345',
                'status'              => 'active',
            ],
            [
                'emp_code'            => 'EMP-002',
                'name'                => 'محمود إبراهيم السيد',
                'dept_code'           => 'DEPT-02',
                'desig_title'         => 'مدير الإدارة المالية',
                'phone'               => '0502223344',
                'email'               => 'mahmoud@elazizy.com',
                'national_id'         => '1023456789',
                'hire_date'           => '2022-03-15',
                'basic_salary'        => 8500.00,
                'housing_allowance'   => 1800.00,
                'transport_allowance' => 800.00,
                'other_allowances'    => 300.00,
                'bank_name'           => 'البنك الأهلي السعودي',
                'bank_account'        => 'SA2210000987654321098765',
                'status'              => 'active',
            ],
            [
                'emp_code'            => 'EMP-003',
                'name'                => 'خالد عبد الرحمن الشريف',
                'dept_code'           => 'DEPT-02',
                'desig_title'         => 'محاسب عام أول',
                'phone'               => '0503334455',
                'email'               => 'khaled@elazizy.com',
                'national_id'         => '1034567890',
                'hire_date'           => '2023-02-01',
                'basic_salary'        => 5500.00,
                'housing_allowance'   => 1200.00,
                'transport_allowance' => 500.00,
                'other_allowances'    => 0.00,
                'bank_name'           => 'بنك الرياض',
                'bank_account'        => 'SA7720000456123789456123',
                'status'              => 'active',
            ],
            [
                'emp_code'            => 'EMP-004',
                'name'                => 'عمر طارق الفاسي',
                'dept_code'           => 'DEPT-03',
                'desig_title'         => 'مشرف مبيعات أول',
                'phone'               => '0504445566',
                'email'               => 'omar@elazizy.com',
                'national_id'         => '1045678901',
                'hire_date'           => '2023-06-10',
                'basic_salary'        => 4800.00,
                'housing_allowance'   => 1000.00,
                'transport_allowance' => 700.00,
                'other_allowances'    => 500.00,
                'bank_name'           => 'مصرف الراجحي',
                'bank_account'        => 'SA4480000555666777888999',
                'status'              => 'active',
            ],
            [
                'emp_code'            => 'EMP-005',
                'name'                => 'يوسف كريم النجار',
                'dept_code'           => 'DEPT-04',
                'desig_title'         => 'أمين مستودع رئيسي',
                'phone'               => '0505556677',
                'email'               => 'youssef@elazizy.com',
                'national_id'         => '1056789012',
                'hire_date'           => '2023-09-01',
                'basic_salary'        => 4200.00,
                'housing_allowance'   => 900.00,
                'transport_allowance' => 500.00,
                'other_allowances'    => 0.00,
                'bank_name'           => 'بنك البلاد',
                'bank_account'        => 'SA1115000333222111444555',
                'status'              => 'active',
            ],
        ];

        $empModels = [];
        foreach ($employeesData as $emp) {
            $dept = $deptModels[$emp['dept_code']] ?? null;
            $desig = $desigModels[$emp['desig_title']] ?? null;

            $empModels[$emp['emp_code']] = Employee::updateOrCreate(
                ['tenant_id' => $tenantId, 'emp_code' => $emp['emp_code']],
                [
                    'branch_id'           => $branchId,
                    'department_id'       => $dept ? $dept->id : null,
                    'designation_id'      => $desig ? $desig->id : null,
                    'name'                => $emp['name'],
                    'phone'               => $emp['phone'],
                    'email'               => $emp['email'],
                    'national_id'         => $emp['national_id'],
                    'hire_date'           => $emp['hire_date'],
                    'basic_salary'        => $emp['basic_salary'],
                    'housing_allowance'   => $emp['housing_allowance'],
                    'transport_allowance' => $emp['transport_allowance'],
                    'other_allowances'    => $emp['other_allowances'],
                    'bank_name'           => $emp['bank_name'],
                    'bank_account'        => $emp['bank_account'],
                    'status'              => $emp['status'],
                ]
            );
        }

        // 5. Active Employee Loan (سلفة موظف نشطة)
        $loanEmp = $empModels['EMP-003'] ?? null;
        if ($loanEmp) {
            EmployeeLoan::updateOrCreate(
                ['tenant_id' => $tenantId, 'loan_number' => 'LOAN-0001'],
                [
                    'employee_id'         => $loanEmp->id,
                    'treasury_id'         => $treasuryId,
                    'date'                => Carbon::now()->subMonth()->format('Y-m-d'),
                    'amount'              => 3000.00,
                    'monthly_installment' => 500.00,
                    'total_installments'  => 6,
                    'paid_amount'         => 0.00,
                    'status'              => 'active',
                    'notes'               => 'سلفة زواج ميسرة تسدد على أقساط شهرية متساوية',
                    'created_by'          => $userId,
                ]
            );
        }

        // 6. Leaves (إجازة معتمدة)
        $leaveEmp = $empModels['EMP-005'] ?? null;
        if ($leaveEmp) {
            EmployeeLeave::updateOrCreate(
                ['tenant_id' => $tenantId, 'employee_id' => $leaveEmp->id, 'start_date' => Carbon::now()->addDays(5)->format('Y-m-d')],
                [
                    'leave_type'  => 'annual',
                    'end_date'    => Carbon::now()->addDays(7)->format('Y-m-d'),
                    'days_count'  => 3,
                    'reason'      => 'إجازة سنوية اعتيادية معتمدة',
                    'status'      => 'approved',
                    'approved_by' => $userId,
                ]
            );
        }

        // 7. Attendances (سجلات الحضور اليومي)
        $today = Carbon::today()->format('Y-m-d');
        foreach ($empModels as $code => $empModel) {
            $isLate = ($code === 'EMP-004');
            Attendance::updateOrCreate(
                ['tenant_id' => $tenantId, 'employee_id' => $empModel->id, 'date' => $today],
                [
                    'status'         => $isLate ? 'late' : 'present',
                    'check_in'       => $isLate ? '08:45:00' : '08:00:00',
                    'check_out'      => '16:00:00',
                    'late_minutes'   => $isLate ? 45 : 0,
                    'overtime_hours' => 0.00,
                    'notes'          => $isLate ? 'تأخر 45 دقيقة بعذر مروري' : 'حضور كامل منتظم',
                ]
            );
        }
    }
}

