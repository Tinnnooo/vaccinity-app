<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('societies', function (Blueprint $table) {
            $table->id();
            $table->char('id_card_number');
            $table->string('password');
            $table->string('name');
            $table->date('born-date');
            $table->enum('gender', ['male', 'female']);
            $table->text('address');
            $table->bigInteger('regional_id')->unsigned();
            $table->text('login_tokens');
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('societies');
    }
};
