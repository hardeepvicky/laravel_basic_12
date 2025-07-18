<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId("document_category_id")->constrained("document_categories")->onDelete('restrict');
            $table->string("type", 45);
            $table->string("name", 255);
            $table->string("url", 512)->nullable();
            $table->string("storage_base_path", 255)->nullable();
            $table->string("file_original", 512)->nullable();
            $table->string("file", 512)->nullable();
            $table->string("file_full_path", 1024)->nullable();
            $table->string("file_name", 180);
            $table->string("file_ext", 20);
            $table->float("file_size_in_mb")->nullable();
            $table->integer("image_height")->nullable();
            $table->integer("image_width")->nullable();
            $table->integer("video_duration_in_seconds")->nullable();
            $table->integer("audio_duration_in_seconds")->nullable();
            $table->timestamps();
            $table->bigInteger("created_by")->nullable();
            $table->bigInteger("updated_by")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_files');
    }
}
