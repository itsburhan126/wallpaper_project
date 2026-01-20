<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix Banners table
        if (Schema::hasTable('banners')) {
            Schema::table('banners', function (Blueprint $table) {
                if (!Schema::hasColumn('banners', 'link')) {
                    $table->string('link')->nullable();
                }
                if (!Schema::hasColumn('banners', 'status')) {
                    $table->boolean('status')->default(true);
                }
            });
        } else {
            Schema::create('banners', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('image');
                $table->string('link')->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }

        // Fix Categories table
        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                if (!Schema::hasColumn('categories', 'status')) {
                    $table->boolean('status')->default(true);
                }
                if (!Schema::hasColumn('categories', 'image')) {
                    $table->string('image')->nullable();
                }
            });
        } else {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('image')->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }

        // Fix Wallpapers table
        if (Schema::hasTable('wallpapers')) {
            Schema::table('wallpapers', function (Blueprint $table) {
                if (!Schema::hasColumn('wallpapers', 'status')) {
                    $table->boolean('status')->default(true);
                }
                if (!Schema::hasColumn('wallpapers', 'views')) {
                    $table->integer('views')->default(0);
                }
                if (!Schema::hasColumn('wallpapers', 'downloads')) {
                    $table->integer('downloads')->default(0);
                }
                if (!Schema::hasColumn('wallpapers', 'tags')) {
                    $table->string('tags')->nullable();
                }
                if (!Schema::hasColumn('wallpapers', 'thumbnail')) {
                    $table->string('thumbnail')->nullable();
                }
                if (!Schema::hasColumn('wallpapers', 'category_id')) {
                    $table->foreignId('category_id')->constrained()->onDelete('cascade');
                }
            });
        } else {
             Schema::create('wallpapers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained()->onDelete('cascade');
                $table->string('image');
                $table->string('thumbnail')->nullable();
                $table->string('tags')->nullable();
                $table->integer('downloads')->default(0);
                $table->integer('views')->default(0);
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No safe reverse for this fix
    }
};
