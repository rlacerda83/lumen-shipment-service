<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Carrier;
use App\Models\Country;

class CreateShipmmentCarriersCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_carriers_countries', function (Blueprint $table) {
            $table->bigInteger('carrier_id')->unsigned();
            $table->bigInteger('country_id')->unsigned();

            $table->foreign('carrier_id')->references('id')->on(Carrier::getTableName())->onDelete('cascade');
            $table->foreign('country_id')->references('id')->on(Country::getTableName())->onDelete('cascade');

            $table->primary(['carrier_id', 'country_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('shipment_carriers_countries')) {
            Schema::drop('shipment_carriers_countries');
        }
    }
}
