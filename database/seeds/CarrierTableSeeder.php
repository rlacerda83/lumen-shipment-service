<?php

use Illuminate\Database\Seeder;
use App\Models\Carrier;
use App\Models\CarrierService;
use App\Models\Country;
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

		$config = [
			'url' => 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx?WSDL',
			'company' => '',
			'password' => '',
			'postalCodeOrigin' => '05346000',
			'format' => '1',
			'ownHands' => '5',
			'deliveryNotification' => 'N'
		];

		$carrier = Carrier::create([
			'name' => 'Correios',
			'code' => 'CO',
            'model_reference' => '\App\Services\Shipment\Shippers\PostOffice',
            'config' => json_encode($config)
		]);

		$services = $carrier->hasMany('App\Models\CarrierService');

		$services->saveMany([
			new CarrierService([
				'code' => 40010,
				'delivery_time' => 8,
				'name' => 'SEDEX',
				'description' => '',
				'status' => 1
			]),
			new CarrierService([
				'code' => 40045,
				'delivery_time' => 1,
				'name' => 'SEDEX a Cobrar',
				'description' => '',
				'status' => 1
			]),
			new CarrierService([
                'code' => 40215,
                'delivery_time' => 1,
                'name' => 'SEDEX 10, sem contrato',
                'description' => '',
                'status' => 1
			]),
			new CarrierService([
                'code' => 40290,
                'delivery_time' => 1,
                'name' => 'SEDEX Hoje, sem contrato',
                'description' => '',
                'status' => 0
			]),
			new CarrierService([
                'code' => 41068,
                'delivery_time' => 1,
                'name' => 'PAC',
                'description' => '',
                'status' => 0
			]),
			new CarrierService([
                'code' => 41106,
                'delivery_time' => 1,
                'name' => 'PAC, sem contrato',
                'description' => '',
                'status' => 1
			]),
            new CarrierService([
                'code' => 81019,
                'delivery_time' => 5,
                'name' => 'e-SEDEX',
                'description' => '',
                'status' => 0
            ])
		]);

        $carrierTest = Carrier::create([
            'name' => 'Flat Rate Shipping',
            'code' => 'FRS',
            'model_reference' => '\App\Services\Shipment\Shippers\FlatRateShipping'
        ]);

        $services = $carrierTest->hasMany('App\Models\CarrierService');

        $services->saveMany([
            new CarrierService([
                'code' => 'FRS',
                'delivery_time' => 10,
                'name' => 'Default',
                'description' => '',
                'status' => 1
            ]),
        ]);

        $this->command->info('Carrier table seeded!');
	}

}
