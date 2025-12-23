<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\User;
use App\Models\Category;
use App\Models\PropertyType;
use App\Models\Area;
use Faker\Factory as Faker;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Faker is still used for other data, but description is handled manually
        $faker = Faker::create();

        $users = User::all();
        $categories = Category::all();
        $propertyTypes = PropertyType::all();
        $areas = Area::all();

        if ($users->isEmpty() || $categories->isEmpty() || $propertyTypes->isEmpty() || $areas->isEmpty()) {
            echo "Error: Dependent seeders (Users, Categories, PropertyTypes, Areas) must be run first.\n";
            return;
        }

        for ($i = 0; $i < 50; $i++) {
            // Get random models
            $user = $users->random();
            $category = $categories->random();
            $propertyType = $propertyTypes->random();
            $area = $areas->random();

            // Generate the title using the helper function
            $title = titleGenerator($propertyType->id, $category->id, $area->id);

            Property::create([
                'user_id'          => $user->id,
                'category_id'      => $category->id,
                'property_type_id' => $propertyType->id,
                'area_id'          => $area->id,
                'title'            => $title,
                // Use the new custom function to generate the Arabic description
                'description'      => $this->generateArabicDescription(),
                'price'            => $faker->randomFloat(2, 500, 500000),
                'status'           => Property::STATUS_OPEN,
                'is_featured'      => $faker->boolean,
                'expires_at'       => $faker->dateTimeBetween('now', '+1 year'),
                'renew_at'         => $faker->dateTimeBetween('now', '+1 year'),
            ]);
        }
    }

    /**
     * Generates a random Arabic property description.
     *
     * @return string
     */
    protected function generateArabicDescription(): string
    {
        $descriptions = [
            'شقة واسعة ومجهزة بالكامل في منطقة هادئة، تتميز بتصميم داخلي عصري وإطلالة خلابة. مثالية للعائلات وتوفر كافة وسائل الراحة.',
            'فيلا جديدة وفاخرة للبيع، بتصميم معماري حديث ومساحات واسعة. تحتوي على حديقة خاصة ومسبح. الموقع استراتيجي وقريب من جميع الخدمات.',
            'محل تجاري للإيجار في موقع حيوي ومميز، مناسب لمختلف الأنشطة التجارية. مساحة كبيرة مع واجهة زجاجية تسمح بعرض ممتاز للمنتجات.',
            'أرض سكنية للبيع في منطقة راقية ومخطط لها بشكل جيد. فرصة استثمارية رائعة لبناء منزل أحلامك أو مشروع تطوير عقاري.',
            'مكتب إداري للإيجار في برج عصري، يتميز بموقعه المركزي وسهولة الوصول. المكتب مزود بأحدث التقنيات ومساحات عمل مرنة.',
        ];

        return $descriptions[array_rand($descriptions)];
    }
}
