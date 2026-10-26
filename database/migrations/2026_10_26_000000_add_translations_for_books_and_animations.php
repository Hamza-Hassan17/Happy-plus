<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// English stays in the main tables; the other app languages live in the *_translations tables.
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
        });

        Schema::create('book_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('book_pdf')->nullable();
            $table->timestamps();
            $table->unique(['book_id', 'locale']);
        });

        Schema::create('animation_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animation_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name')->nullable();
            $table->timestamps();
            $table->unique(['animation_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animation_translations');
        Schema::dropIfExists('book_translations');

        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
