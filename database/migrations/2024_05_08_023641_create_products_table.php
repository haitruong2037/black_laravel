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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('price');
            $table->integer('discount')->nullable();
            $table->integer('quantity');
            $table->string('image')->nullable();
            $table->longText('description')->nullable();
            $table->integer('view')->default(0);
            $table->boolean('hot')->default(false);
            $table->smallInteger('status')->default(1);
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('admin_id')->nullable();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('restrict');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
