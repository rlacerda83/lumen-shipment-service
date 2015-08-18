<?php

use Illuminate\Database\Schema\Blueprint;
use App\Models\Carrier;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentCarrierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Carrier::getTableName(), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('document1')->nullable();
            $table->string('document2')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->float('min_volume')->nullable();
            $table->float('max_volume')->nullable();
            $table->longText('config')->nullable();
            $table->tinyInteger('status');
            $table->timestamps();
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable(Carrier::getTableName())) {
            Schema::drop(Carrier::getTableName());
        }
    }
}
