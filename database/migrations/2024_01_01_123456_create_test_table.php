<?php

use App\Infra\Database\Migration;

class CreateTestTable extends Migration
{
    public function up(): void
    {
        $this->createTable('test_table', function($table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->dropTable('test_table');
    }
} 