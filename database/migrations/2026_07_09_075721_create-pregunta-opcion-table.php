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
        Schema::create('academic.pregunta_opcion', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_pregunta');

            $table->text('texto_opcion');
            $table->boolean('is_correcta')->default(false);
            $table->integer('orden')->default(1);

            $table->string('fecha_crea', 10)->nullable();
            $table->string('fecha_actualiza', 10)->nullable();
            $table->string('fecha_elimina', 10)->nullable();

            $table->string('user_crea', 12)->nullable();
            $table->string('user_actualiza', 12)->nullable();
            $table->string('user_elimina', 12)->nullable();
            $table->boolean('estado')->default(true);

            $table->timestamps();

            $table->foreign('id_pregunta')
                ->references('id')
                ->on('academic.pregunta')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic.pregunta_opcion', function (Blueprint $table) {
            $table->dropForeign('id_pregunta');
            $table->dropColumn('id_pregunta');
        });

        Schema::dropIfExists('academic.pregunta_opcion');
    }
};
