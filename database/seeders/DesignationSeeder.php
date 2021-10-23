<?php

namespace Database\Seeders;
use App\Models\Designation;
use Illuminate\Database\Seeder;
use DB;
class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $designations = [
            [
                'name' => 'Manager'
            ],
            [
                'name' => 'President'
            ],
            [
                'name' => 'Developer'
            ],
            [
                'name' => 'Team Lead'
            ]
            ];
        DB::table('designations')->insert($designations);
    }
}
