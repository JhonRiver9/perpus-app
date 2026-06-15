<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsToBooksAndBorrows extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->integer('stok')->default(1);
        });

        Schema::table('borrows', function (Blueprint $table) {
            $table->date('tanggal_dikembalikan')->nullable();
            $table->integer('denda')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('stok');
        });

        Schema::table('borrows', function (Blueprint $table) {
            $table->dropColumn(['tanggal_dikembalikan', 'denda']);
        });
    }
}