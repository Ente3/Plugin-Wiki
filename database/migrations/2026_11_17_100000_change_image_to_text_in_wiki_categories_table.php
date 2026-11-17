<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Image links (for example from a cloud storage) can be longer than the
     * default string length, which is only 191 characters on some websites.
     */
    public function up(): void
    {
        Schema::table('wiki_categories', function (Blueprint $table) {
            $table->text('image')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wiki_categories', function (Blueprint $table) {
            $table->string('image')->nullable()->change();
        });
    }
};
