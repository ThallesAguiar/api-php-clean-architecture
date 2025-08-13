<?php

use App\Infra\Database\Blueprint;
use App\Infra\Database\Migration;

return new class extends Migration
{
    public function up()
    {
        // Blueprint::create('{{tableName}}', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });
    }

    public function down()
    {
        // Blueprint::dropIfExists('{{tableName}}');
    }
};
