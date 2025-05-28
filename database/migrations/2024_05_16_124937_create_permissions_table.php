<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id')->comment('fk role คนที่มีสิท');
            $table->string('table')->comment('ชื่อตารางสิทธิ์');
            $table->tinyInteger('create')->default(0)->comment('มีสิทธิ์ใช้งานหรือไม่ 0 คือไม่มี 1 คือ มี');
            $table->tinyInteger('update')->default(0)->comment('มีสิทธิ์ใช้งานหรือไม่ 0 คือไม่มี 1 คือ มี');
            $table->tinyInteger('delete')->default(0)->comment('มีสิทธิ์ใช้งานหรือไม่ 0 คือไม่มี 1 คือ มี');
            $table->tinyInteger('view')->default(0)->comment('มีสิทธิ์ใช้งานหรือไม่ 0 คือไม่มี 1 คือ มี');
            $table->tinyInteger('status')->default(0)->comment('มีสิทธิ์ใช้งานหรือไม่ 0 คือไม่มี 1 คือ มี');
            $table->timestamps();
        });

        DB::table('permissions')->insert(
            [['role_id' => 2,
            'table' => 'account',
            'create' => 0,
            'update' => 1,
            'delete' => 0,
            'view' => 1,
            'status' => 1]
            , [
                'role_id' => 2,
                'table' => 'products',
                'create' => 1,
                'update' => 1,
                'delete' => 1,
                'view' => 1,
                'status' => 1
            ]
            ,[
                'role_id' => 2,
                'table' => 'categories',
                'create' => 1,
                'update' => 1,
                'delete' => 1,
                'view' => 1,
                'status' => 1
            ],[
                'role_id' => 2,
                'table' => 'article',
                'create' => 1,
                'update' => 1,
                'delete' => 1,
                'view' => 1,
                'status' => 1
            ]]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
