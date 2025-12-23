<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            // [
            //     'name' => 'كل مناطق الكويت',
            //     'slug' => 'all_areas_of_kuwait',
            //     'status' => Area::STATUS_INACTIVE,
            // ],
            [
                'name' => 'الجهراء',
                'slug' => 'al_jahra_governorate',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'المطلاع',
                'slug' => 'al_mutlaa',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'سعد العبدالله',
                'slug' => 'saad_al_abdullah',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'النعيم',
                'slug' => 'al_naeem',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الجهراء القديمة',
                'slug' => 'old_al_jahra',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الصليبية',
                'slug' => 'al_sulaibiya',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'العبدلي',
                'slug' => 'al_abdali',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'القصر',
                'slug' => 'al_qasr',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'النسيم',
                'slug' => 'al_naseem',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الواحة',
                'slug' => 'al_waha',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'امغرة الصناعية',
                'slug' => 'amghara_industrial',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'كبد',
                'slug' => 'kabd',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الصبية',
                'slug' => 'al_subiya',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'العيون',
                'slug' => 'al_oyoun',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'اسطبلات الجهراء',
                'slug' => 'al_jahra_stables',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الجهراء الصناعية',
                'slug' => 'al_jahra_industrial',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الهجن',
                'slug' => 'al_hajan',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'جنوب سعد العبدالله',
                'slug' => 'south_saad_al_abdullah',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'تيماء',
                'slug' => 'taimaa',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الخويسات',
                'slug' => 'al_khuwaisat',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'النعايم - السالمي',
                'slug' => 'al_naayem_al_salmi',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الفروانية',
                'slug' => 'al_farwaniya_governorate',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'جنوب عبدالله المبارك',
                'slug' => 'south_abdullah_al_mubarak',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'غرب عبدالله المبارك',
                'slug' => 'west_abdullah_al_mubarak',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'خيطان الجنوبي الجديدة',
                'slug' => 'khaitan_al_janoubi_al_jadidah',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'خيطان',
                'slug' => 'khaitan',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'عبدالله المبارك - غرب الجليب',
                'slug' => 'abdullah_al_mubarak_west_al_jleeb',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'اسطبلات الفروانية',
                'slug' => 'al_farwaniya_stables',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'اشبيلية',
                'slug' => 'ishbiliya',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الاندلس',
                'slug' => 'al_andalus',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الرابية',
                'slug' => 'al_rabia',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الرحاب',
                'slug' => 'al_rehab',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الرقعي',
                'slug' => 'al_riqqaei',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الري',
                'slug' => 'al_rai',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الضجيج',
                'slug' => 'al_dajeej',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'العارضية',
                'slug' => 'al_ardiya',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'العمرية',
                'slug' => 'al_omariya',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الفردوس',
                'slug' => 'al_firdous',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'جليب الشيوخ - الحساوي',
                'slug' => 'jleeb_al_shuyoukh_al_hasawi',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'صباح الناصر',
                'slug' => 'sabah_al_nasser',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'العارضية الحرفية - الصناعية',
                'slug' => 'al_ardiya_al_herafiya_al_sinaiya',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الفروانية',
                'slug' => 'al_farwaniya',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الاحمدي',
                'slug' => 'al_ahmadi_governorate',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'صباح الاحمد السكنية',
                'slug' => 'sabah_al_ahmad_residential',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الوفرة السكنية',
                'slug' => 'al_wafra_residential',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الوفرة',
                'slug' => 'al_wafra',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'ابو حليفة',
                'slug' => 'abu_halifa',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الاحمدي',
                'slug' => 'al_ahmadi',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الرقة',
                'slug' => 'al_riqqa',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'علي صباح السالم - ام الهيمان',
                'slug' => 'ali_sabah_al_salem_umm_al_hayman',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'جنوب صباح الاحمد',
                'slug' => 'south_sabah_al_ahmad',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'اسطبلات الاحمدي',
                'slug' => 'al_ahmadi_stables',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الجليعة',
                'slug' => 'al_julaiaa',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الزور',
                'slug' => 'al_zour',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الصباحية',
                'slug' => 'al_sabahiya',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الظهر',
                'slug' => 'al_dhaher',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'العقيلة',
                'slug' => 'al_aqaila',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الفحيحيل',
                'slug' => 'al_fahaheel',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الفنطاس',
                'slug' => 'al_fintas',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'المنقف',
                'slug' => 'al_mangaf',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'المهبولة',
                'slug' => 'al_mahboula',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'النويصيب',
                'slug' => 'al_nuwaiseeb',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'بنيدر',
                'slug' => 'bnaider',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'جابر العلي',
                'slug' => 'jaber_al_ali',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'صباح الاحمد البحرية - الخيران',
                'slug' => 'sabah_al_ahmad_sea_city_al_khiran',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'فهد الاحمد',
                'slug' => 'fahad_al_ahmad',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الشعيبة الصناعية',
                'slug' => 'al_shuaiba_industrial',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'ميناء عبدالله',
                'slug' => 'mina_abdullah',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'هدية',
                'slug' => 'hadiya',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الخيران السكنية - الجانب البري',
                'slug' => 'al_khiran_residential_desert_side',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الضباعية',
                'slug' => 'al_dubaaiya',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'العاصمة',
                'slug' => 'al_asimah_governorate',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'جابر الاحمد',
                'slug' => 'jaber_al_ahmad',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الفيحاء',
                'slug' => 'al_faiha',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الخالدية',
                'slug' => 'al_khalidiya',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الدسمة',
                'slug' => 'al_dasma',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الدعية',
                'slug' => 'al_daiya',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الدوحة',
                'slug' => 'al_doha',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الروضة',
                'slug' => 'al_rawda',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'السرة',
                'slug' => 'al_surra',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الشامية',
                'slug' => 'al_shamiya',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الشويخ الصناعية',
                'slug' => 'al_shuwaikh_industrial',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الصليبيخات',
                'slug' => 'al_sulaibikhat',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'العديلية',
                'slug' => 'al_adailiya',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'القادسية',
                'slug' => 'al_qadisiya',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'القبلة - جبلة',
                'slug' => 'al_qibla_jleebla',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'القيروان',
                'slug' => 'al_qairawan',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'المباركية',
                'slug' => 'al_mubarakia',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'المرقاب',
                'slug' => 'al_mirqab',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'المنصورية',
                'slug' => 'al_mansouriya',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'النزهة',
                'slug' => 'al_nuzha',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'النهضة',
                'slug' => 'al_nahda',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'اليرموك',
                'slug' => 'al_yarmouk',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'بنيد القار',
                'slug' => 'bnaid_al_qar',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'دسمان',
                'slug' => 'dasman',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الشرق',
                'slug' => 'al_sharq',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'شمال غرب الصليبيخات',
                'slug' => 'north_west_al_sulaibikhat',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'عبدالله السالم',
                'slug' => 'abdullah_al_salem',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'غرناطة',
                'slug' => 'gharnata',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'قرطبة',
                'slug' => 'qortuba',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'كيفان',
                'slug' => 'kaifan',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الشويخ السكنية',
                'slug' => 'al_shuwaikh_residential',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'ضاحية حصه المبارك',
                'slug' => 'dahiya_hessah_al_mubarak',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'حولي',
                'slug' => 'hawally_governorate',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'البدع',
                'slug' => 'al_bida',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الجابرية',
                'slug' => 'al_jabriya',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الرميثية',
                'slug' => 'al_rumaithiya',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الزهراء',
                'slug' => 'al_zahra',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'السالمية',
                'slug' => 'al_salmiya',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'السلام',
                'slug' => 'al_salam',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الشعب السكني',
                'slug' => 'al_shaab_residential',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الشهداء',
                'slug' => 'al_shuhada',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الصديق',
                'slug' => 'al_siddiq',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'بيان',
                'slug' => 'bayan',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'حطين',
                'slug' => 'hattin',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'سلوى',
                'slug' => 'salwa',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'غرب مشرف - مبارك العبدالله',
                'slug' => 'west_mushrif_mubarak_al_abdullah',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'مشرف',
                'slug' => 'mushrif',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'ميدان حولي',
                'slug' => 'maidan_hawally',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'حولي',
                'slug' => 'hawally',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الشعب البحري',
                'slug' => 'al_shaab_al_bahri',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'مبارك الكبير',
                'slug' => 'mubarak_al_kabeer_governorate',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'ابو فطيرة',
                'slug' => 'abu_futaira',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'العدان',
                'slug' => 'al_adan',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'الفنيطيس',
                'slug' => 'al_funaitis',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'القرين',
                'slug' => 'al_qurain',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'القصور',
                'slug' => 'al_qusour',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'المسايل',
                'slug' => 'al_masayel',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'المسيلة',
                'slug' => 'al_messeila',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'ابو الحصانية',
                'slug' => 'abu_al_hasaniyah',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'صباح السالم',
                'slug' => 'sabah_al_salem',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'صبحان',
                'slug' => 'subhan',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'مبارك الكبير',
                'slug' => 'mubarak_al_kabeer',
                'status' => Area::STATUS_ACTIVE,
            ],
            [
                'name' => 'اسواق القرين - غرب ابو فطيرة الحرفية',
                'slug' => 'aswaq_al_qurain_west_abu_futaira_al_herafiya',
                'status' => Area::STATUS_ACTIVE,
            ],
        ];

        foreach ($areas as $area) {
            Area::create($area);
            // Wait for 0.1 seconds before creating the next area.
            usleep(100000);
        }
    }
}
