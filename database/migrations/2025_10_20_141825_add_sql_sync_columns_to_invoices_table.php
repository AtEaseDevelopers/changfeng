<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSqlSyncColumnsToInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('sql_sync_status')->nullable()->after('status')->index();
            $table->text('sql_sync_respond')->nullable()->after('sql_sync_status');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['sql_sync_status']);
            $table->dropColumn(['sql_sync_status', 'sql_sync_respond']);
        });
    }
};
