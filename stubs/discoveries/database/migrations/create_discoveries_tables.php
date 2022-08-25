<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discovery_topics', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('status')->default('Draft');
            $table->mediumText('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->date('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('discovery_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('status')->default('Draft');
            $table->foreignId('discovery_topic_id');
            $table->foreignId('author_id');
            $table->mediumText('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->date('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discovery_topics');
        Schema::dropIfExists('discovery_articles');
    }
};
