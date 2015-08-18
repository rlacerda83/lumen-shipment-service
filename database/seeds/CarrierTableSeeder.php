<?php

use Illuminate\Database\Seeder;
use App\Models\Carrier;
use App\Models\Carrier\Services;
use Illuminate\Database\Eloquent\Model;

class CarrierTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->command->info('Start carrier seeder!');
		$carrier = Carrier::create([
			'name' => 'Correios'
		]);

		$carrier->services()->saveMany([
			new Services([
				'code' => 40010,
				'delivery_time' => 8,
				'name' => 'SEDEX',
				'description' => '',
				'status' => 1
			]),
			new Services([
				'code' => 40045,
				'delivery_time' => 1,
				'name' => 'SEDEX a Cobrar',
				'description' => '',
				'status' => 1
			]),
			new Services([
                'code' => 40215,
                'delivery_time' => 1,
                'name' => 'SEDEX 10, sem contrato',
                'description' => '',
                'status' => 1
			]),
			new Services([
                'code' => 40290,
                'delivery_time' => 1,
                'name' => 'SEDEX Hoje, sem contrato',
                'description' => '',
                'status' => 0
			]),
			new Services([
                'code' => 41068,
                'delivery_time' => 1,
                'name' => 'PAC',
                'description' => '',
                'status' => 0
			]),
			new Services([
                'code' => 41106,
                'delivery_time' => 1,
                'name' => 'PAC, sem contrato',
                'description' => '',
                'status' => 1
			]),
            new Services([
                'code' => 81019,
                'delivery_time' => 5,
                'name' => 'e-SEDEX',
                'description' => '',
                'status' => 0
            ])
		]);

        $this->command->info('Carrier table seeded!');
	}

}
