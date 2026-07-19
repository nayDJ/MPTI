<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['id' => 13, 'name' => 'Budi Santoso',     'phone' => '081234567890', 'address' => 'Jl. Merdeka No. 123, Jakarta',                 'is_active' => true],
            ['id' => 14, 'name' => 'Siti Nurhaliza',   'phone' => '085678901234', 'address' => 'Jl. Sudirman No. 45, Jakarta',                 'is_active' => true],
            ['id' => 15, 'name' => 'Ahmad Fauzi',      'phone' => '087890123456', 'address' => 'Perumahan Griya Indah Blok A3, Bekasi',       'is_active' => true],
            ['id' => 16, 'name' => 'Ratna Dewi',       'phone' => '081345678901', 'address' => 'Jl. Gatot Subroto No. 78, Jakarta',           'is_active' => true],
            ['id' => 17, 'name' => 'Hendra Gunawan',   'phone' => '082156789012', 'address' => 'Jl. Pahlawan No. 12, Depok',                  'is_active' => true],
            ['id' => 18, 'name' => 'Dian Permata',     'phone' => '085712345678', 'address' => 'Komplek Permata Hijau Blok C5, Tangerang',    'is_active' => false],
            ['id' => 19, 'name' => 'Rudi Hartono',     'phone' => '081298765432', 'address' => 'Jl. Diponegoro No. 67, Jakarta',              'is_active' => true],
            ['id' => 20, 'name' => 'Maya Anggraini',   'phone' => '087823456789', 'address' => 'Perumahan Bumi Asri Blok D2, Bekasi',        'is_active' => true],
            ['id' => 21, 'name' => 'Agus Wijaya',      'phone' => '082134567890', 'address' => 'Jl. Ahmad Yani No. 34, Depok',               'is_active' => true],
            ['id' => 22, 'name' => 'Fitri Handayani',  'phone' => '085690123456', 'address' => 'Jl. Rasuna Said No. 89, Jakarta',             'is_active' => true],
            ['id' => 23, 'name' => 'Doni Prasetyo',    'phone' => '081278901234', 'address' => 'Komplek Villa Bogor Indah Blok B1, Bogor',    'is_active' => true],
            ['id' => 24, 'name' => 'Sri Wahyuni',      'phone' => '087845678901', 'address' => 'Jl. Kebon Jeruk No. 56, Jakarta',            'is_active' => false],
            ['id' => 25, 'name' => 'Bayu Nugroho',     'phone' => '082167890123', 'address' => 'Perumahan Pondok Indah Blok E4, Tangerang',  'is_active' => true],
            ['id' => 26, 'name' => 'Rina Marlina',     'phone' => '085723456789', 'address' => 'Jl. Thamrin No. 90, Jakarta',                'is_active' => true],
            ['id' => 27, 'name' => 'Eko Prasetyo',     'phone' => '081289012345', 'address' => 'Jl. Sisingamangaraja No. 23, Bekasi',        'is_active' => false],
        ];

        foreach ($customers as $customer) {
            $customer['created_at'] = now();
            $customer['updated_at'] = now();
            DB::table('customers')->insert($customer);
        }
    }
}
