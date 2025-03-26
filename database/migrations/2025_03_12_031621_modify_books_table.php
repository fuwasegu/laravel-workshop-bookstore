<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('publisher')->nullable()->comment('出版社');
            $table->date('publish_date')->nullable()->comment('出版日（yyyymm）');
            $table->json('authors')->comment('著者');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('publisher');
            $table->dropColumn('publish_date');
            $table->dropColumn('authors');
        });
    }
};
