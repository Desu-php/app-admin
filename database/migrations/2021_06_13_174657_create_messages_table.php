<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('messageId')->nullable();
            $table->string('chatType')->nullable();
            $table->string('chatId')->nullable();
            $table->string('channelId')->nullable();
            $table->string('authorType')->nullable();
            $table->bigInteger('dateTime')->nullable();
            $table->string('type')->nullable();
            $table->string('status')->nullable();
            $table->text('text')->nullable();
            $table->string('authorName')->nullable();
            $table->text('content')->nullable();
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
        Schema::dropIfExists('messages');
    }
}
