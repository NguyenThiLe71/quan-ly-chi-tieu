<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_interactions', function (Blueprint $table) {

            $table->id();

            // user thao tác
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            // module
            $table->string('module');
            // transactions
            // categories
            // budgets
            // goals

            // hành động
            $table->string('action');
            // create update delete search

            // keyword tìm kiếm
            $table->string('keyword')->nullable();

            // ip user
            $table->string('ip')->nullable();

            // trình duyệt
            $table->text('user_agent')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_interactions');
    }
};