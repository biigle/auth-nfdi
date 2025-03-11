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
        Schema::create('nfdi_login_ids', function (Blueprint $table) {
            // The ID can be anything, really, but we hope that it's not longer than
            // 128 characters.
            $table->string('id', 128)->primary();

            $table->timestamps();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nfdi_login_ids');
    }
};
