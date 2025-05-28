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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('fname')->comment('firt name');
            $table->string('lname')->comment('lname');
            $table->enum('gender', ['M', 'F', 'OTHER'])->default('M')->comment('M ผู้ใช้ F ผู้หญิง OTHER ไม่ระบุ');
            $table->date('birthday')->default(now())->comment('วันเกิด');
            $table->string('email')->comment('อีเมลล์');
            $table->string('tel', 20)->nullable()->comment('เบอร์โทร');
            $table->string('avatar')->nullable()->comment('path image avatar');
            $table->bigInteger('user_id')->nullable()->comment('fk user');
            $table->tinyInteger('status')->default(1)->comment('1 ใช้งาน 0 ไม่ใช้งาน');
            $table->string('created_by')->nullable()->comment('สร้างโดย');
            $table->string('updated_by')->nullable()->comment('อัพเดตโดย');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account');
    }
};
