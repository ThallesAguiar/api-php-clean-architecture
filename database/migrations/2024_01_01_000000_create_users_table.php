<?php

use App\Infra\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        $this->createTable('users', function($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->dropTable('users');
    }
} 