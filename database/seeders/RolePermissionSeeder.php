<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $adminRole = Role::create(['name' => 'supor_admin']);



        $permissions = [

        'الفواتير',
        'قائمة الفواتير',
        'الفواتير المدفوعة',
        'الفواتير المدفوعة جزئيا',
        'الفواتير الغير مدفوعة',
        'ارشيف الفواتير',
        'المستخدمين',
        'قائمة المستخدمين',
        'صلاحيات المستخدمين',
        'الاعدادات',
        'المنتجات',
        'الاقسام',


        'اضافة فاتورة',
        'حذف الفاتورة',
        'تصدير EXCEL',
        'تغير حالة الدفع',
        'تعديل الفاتورة',
        'طباعةالفاتورة',
        'اضافة مرفق',
        'حذف المرفق',

        'اضافة مستخدم',
        'تعديل مستخدم',
        'حذف مستخدم',

        'عرض صلاحية',
        'اضافة صلاحية',
        'تعديل صلاحية',
        'حذف صلاحية',

        'اضافة منتج',
        'تعديل منتج',
        'حذف منتج',

        'اضافة قسم',
        'تعديل قسم',
        'حذف قسم',
        'الاشعارات',

        ];

        foreach($permissions as $permission){
            Permission::create(['name' => $permission]);
            $adminRole->givePermissionTo($permission);
        }

        // Assign role to user
        $user = User::find(1); // Example user with ID 1
        $user->assignRole('supor_admin');
    }
}
