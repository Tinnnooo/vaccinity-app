<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use App\Models\Society;

class AddHashedPasswordToSocietiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('societies', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
        });

        $societies = Society::all();

        foreach ($societies as $society) {
            $society->password = Hash::make($society->password);
            $society->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('societies', function (Blueprint $table) {
            $table->dropColumn('password');
        });
    }
}
