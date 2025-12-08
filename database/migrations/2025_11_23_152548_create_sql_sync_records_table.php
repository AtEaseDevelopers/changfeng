<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSqlSyncRecordsTable extends Migration
{
    public function up()
    {
        Schema::create('sql_sync_records', function (Blueprint $table) {
            $table->id();

            $table->string('target_id')->nullable();     // DO ID, Invoice ID, CN ID...
            $table->string('action');                    // do, invoice, cn
            $table->string('target_name')->nullable();   // document no / customer name

            $table->text('details')->nullable();         // request body + headers
            $table->text('response')->nullable();        // SDK / SQL Accounting response

            $table->enum('status', ['pending', 'success', 'failed', 'expired'])
                ->default('pending');

            $table->string('remark')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('target_id');
            $table->index('action');
            $table->index('target_name');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sql_sync_records');
    }
}
