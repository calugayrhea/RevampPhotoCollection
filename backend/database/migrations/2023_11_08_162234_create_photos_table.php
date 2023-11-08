<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('collection_id');
            $table->string('file_path');
            $table->timestamps();

            $table->foreign('collection_id')
                ->references('id')
                ->on('collections')
                ->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropForeign(['collection_id']);
        });

        Schema::dropIfExists('photos');
    }
};

