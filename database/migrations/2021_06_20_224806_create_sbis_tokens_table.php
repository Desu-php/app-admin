<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSbisTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sbis_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sbis_account_id')->constrained('sbis_accounts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('access_token');
            $table->string('sid');
            $table->string('token');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sbis_tokens');
    }
}
