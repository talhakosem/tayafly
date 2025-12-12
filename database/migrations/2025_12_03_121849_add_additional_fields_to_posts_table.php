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
        Schema::table('posts', function (Blueprint $table) {
            $table->string('cover_image')->nullable()->after('slug');
            $table->text('short_description')->nullable()->after('cover_image');
            $table->string('category')->nullable()->after('content');
            $table->json('tags')->nullable()->after('category');
            $table->string('meta_title')->nullable()->after('tags');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->boolean('is_published')->default(true)->after('meta_description');
            $table->boolean('comments_enabled')->default(false)->after('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'cover_image',
                'short_description',
                'category',
                'tags',
                'meta_title',
                'meta_description',
                'is_published',
                'comments_enabled'
            ]);
        });
    }
};
