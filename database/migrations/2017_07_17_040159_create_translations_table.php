<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateLaravelReporterTables.
 */
class CreateTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $table = config('admin.extensions.translations.table', 'laravel_translations');

        Schema::connection($connection)->create($table, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('status')->default(0);
            $table->string('locale');
            $table->string('group');
            $table->string('key');
            $table->text('value')->nullable();
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
        $connection = config('admin.database.connection') ?: config('database.default');

        $table = config('admin.extensions.translations.table', 'laravel_translations');

        Schema::connection($connection)->dropIfExists($table);
    }
}
