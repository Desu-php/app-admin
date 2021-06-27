<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddedThemeToSbisAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sbis_accounts', function (Blueprint $table) {
            //
            $table->boolean('create_lead')->default(0);
            $table->string('theme')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sbis_accounts', function (Blueprint $table) {
            //
        });
    }
}
