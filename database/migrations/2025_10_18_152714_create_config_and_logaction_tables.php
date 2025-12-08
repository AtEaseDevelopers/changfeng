<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Config table
       Schema::create('config', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->text('value')->nullable();
            $table->string('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->index('key'); // add index to key column
        });


        // Logaction table
        Schema::create('log_action', function (Blueprint $table) {
            $table->id();
            $table->string('action_by')->nullable()->index();
            $table->string('action_name')->nullable()->index();
            $table->string('action_ref_no')->nullable()->index();
            $table->text('request')->nullable();
            $table->text('headers')->nullable();
            $table->text('body')->nullable();
            $table->text('respond')->nullable();
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_action');
        Schema::dropIfExists('config');
    }
};
