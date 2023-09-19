<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('article_word', function (Blueprint $table) {
            //$table->id();
            $table->bigInteger('article_id')->unsigned();
            $table->bigInteger('word_id')->unsigned();
            $table->integer('quantity');
            $table->timestamps();

            $table->primary(['word_id', 'article_id']);
            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
            $table->foreign('word_id')->references('id')->on('words')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_word');
    }
};
