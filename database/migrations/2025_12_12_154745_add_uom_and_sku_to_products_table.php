<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUomAndSkuToProductsTable extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('uom', 255)->after('name')->index();
            $table->string('sku', 500)->nullable()->after('uom');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['uom']); // drop index first
            $table->dropColumn('uom');
            $table->dropColumn('sku');
        });
    }
}

