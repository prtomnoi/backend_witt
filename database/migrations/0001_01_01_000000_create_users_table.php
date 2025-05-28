<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->unsignedBigInteger('role_id');
            $table->string('tel', 20)->unique();
            $table->timestamp('tel_verified_at')->nullable();
            $table->string('password');
            $table->string('auth_type')->default('LOCAL')->comment('login ด้วย LOCAL, GOOGLE,FACEBOOK,APPLE');
            $table->text('auth_id_token')->nullable()->comment('token or id login กรณี ที่ไม่ใช่ local');
            $table->enum('status', ['A', 'I'])->default('A')->comment('สถานะข้อมูล A = ใช้งาน , I = ไม่ใช้งาน');
            $table->string('created_by')->nullable()->comment('สร้างโดย');
            $table->string('updated_by')->nullable()->comment('อัพเดตโดย');
            $table->rememberToken();
            $table->timestamps();
        });

        // Schema::create('password_reset_tokens', function (Blueprint $table) {
        //     $table->string('email')->primary();
        //     $table->string('token');
        //     $table->timestamp('created_at')->nullable();
        // });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
        DB::insert('insert into users (name, email, password, role_id, tel) values (?, ?, ?, ?, ?)', ['SuperAdmin', 'superadmin@testing.com', Hash::make('123456789'), 1, '0123456789']);
        DB::insert('insert into users (name, email, password, role_id, tel) values (?, ?, ?, ?, ?)', ['Admin', 'admin@testing.com', Hash::make('123456789'), 2, '0123456781']);
        DB::insert('insert into users (name, email, password, role_id, tel) values (?, ?, ?, ?, ?)', ['rider', 'rider@testing.com', Hash::make('123456789'), 3, '01234567892']);
        DB::insert('insert into users (name, email, password, role_id, tel) values (?, ?, ?, ?, ?)', ['user', 'user@testing.com', Hash::make('123456789'), 4, '01234567893']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        // Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
