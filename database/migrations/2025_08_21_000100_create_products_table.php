<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->index();
            $table->unsignedBigInteger('external_id')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->index();
            $table->string('image_url')->nullable();
            $table->json('raw')->nullable();
            $table->timestamps();


            $table->foreign('category_id')
                ->references('id')->on('categories')
                ->onUpdate('cascade')->onDelete('restrict');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
