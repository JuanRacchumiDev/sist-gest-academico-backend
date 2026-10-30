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
        Schema::create('academic.seccion_academico', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_grado_academico');

            $table->string('nombre', 20);
            $table->integer('capacidad_minima')->nullable();
            $table->integer('capacidad_maxima')->nullable();

            $table->string('fecha_crea', 10)->nullable();
            $table->string('fecha_actualiza', 10)->nullable();
            $table->string('fecha_elimina', 10)->nullable();

            $table->string('user_crea', 12)->nullable();
            $table->string('user_actualiza', 12)->nullable();
            $table->string('user_elimina', 12)->nullable();

            $table->boolean('estado')->default(true);

            $table->timestamps();

            $table->foreign('id_grado_academico')
                ->references('id')
                ->on('academic.seccion_academico')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic.seccion_academico', function (Blueprint $table) {
            $table->dropForeign('id_grado_academico');
            $table->dropColumn('id_grado_academico');
        });

        Schema::dropIfExists('academic.academic_academico');
    }
};
