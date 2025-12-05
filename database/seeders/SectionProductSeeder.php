<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionProductSeeder extends Seeder
{
    public function run()
    {
        // // مسح البيانات القديمة
        // Product::truncate();
        // Section::truncate();

        // البنوك
        $banks = [
            'البنك الأهلي السعودي',
            'مصرف الراجحي',
            'بنك الرياض',
            'البنك السعودي الفرنسي',
        ];

        // أنواع القروض
        $loans = [
            'قرض شخصي',
            'قرض السيارات',
            'قرض الرهن العقاري',
            'قرض التعليم',
            'قرض العلاج الطبي',
            'قرض الزواج',
            'قرض المشاريع',
            'قرض العمرة',
        ];

        foreach ($banks as $bank) {
            $section = Section::create([
                'section_name' => $bank,
                'description' => 'بنك سعودي يقدم خدمات مصرفية',
            ]);

            // إضافة 4 قروض لكل بنك
            foreach (array_rand($loans, 4) as $key) {
                Product::create([
                    'product_name' => $loans[$key],
                    'description' => 'تمويل مصرفي',
                    'section_id' => $section->id,
                ]);
            }
        }
    }
}