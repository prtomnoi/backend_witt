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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('product_slug'); // เช่น product-abc
            $table->string('image'); // ชื่อไฟล์หรือ path
            $table->string('link')->nullable(); // ลิงก์ไปหน้าสินค้า / external URL
            $table->integer('position')->default(1); // ลำดับแสดง
            $table->enum('status', ['A', 'I'])->default('A'); // A = แสดง, I = ซ่อน
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
