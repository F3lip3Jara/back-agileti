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
        Schema::table('sd_iblpns', function (Blueprint $table) {
            $table->string('iblpnHdrCustShortText7', 100)->nullable();
            $table->string('iblpnHdrCustShortText8', 100)->nullable();
            $table->string('iblpnHdrCustShortText9', 100)->nullable();
            $table->string('iblpnHdrCustShortText10', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sd_iblpns', function (Blueprint $table) {
            $table->dropColumn('iblpnHdrCustShortText7');
        });
    }
};
