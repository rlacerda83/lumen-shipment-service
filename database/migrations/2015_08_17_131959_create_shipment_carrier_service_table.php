<?php

use Illuminate\Database\Schema\Blueprint;
use App\Models\Carrier;
use App\Models\CarrierService;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentCarrierServiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(CarrierService::getTableName(), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('carrier_id')->unsigned();
            $table->string('name');
            $table->string('code');
            $table->string('description')->nullable();
            $table->tinyInteger('delivery_time')->default(1);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->index(['name']);
            $table->index(['carrier_id', 'code']);
            $table->foreign('carrier_id')->references('id')->on(Carrier::getTableName())->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable(CarrierService::getTableName())) {
            Schema::drop(CarrierService::getTableName());
        }
    }
}
