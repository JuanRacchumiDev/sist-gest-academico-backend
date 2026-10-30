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
        Schema::create('academic.programa_horarios', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_programa');
            $table->unsignedBigInteger('id_grado_academico')->nullable();
            $table->unsignedBigInteger('id_seccion_academico')->nullable();
            $table->unsignedBigInteger('id_docente')->nullable();

            $table->integer('anio')->nullable();
            $table->tinyInteger('mes')->nullable();

            $table->string('plataforma', 50)->nullable();
            $table->string('link_plataforma', 255)->nullable();
            $table->time('hora_inicio');
            $table->time('hora_fin')->nullable();
            $table->string('zona_horaria', 30)->default('Lima GMT-5');
            $table->string('frecuencia_texto', 255)->nullable()->comment('Ej: 1er sábado, 1er domingo... de cada mes');
            $table->jsonb('fechas_sesiones')->nullable();

            $table->string('fecha_crea', 10)->nullable();
            $table->string('fecha_actualiza', 10)->nullable();
            $table->string('fecha_elimina', 10)->nullable();

            $table->string('user_crea', 12)->nullable();
            $table->string('user_actualiza', 12)->nullable();
            $table->string('user_elimina', 12)->nullable();

            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->foreign('id_programa')
                ->references('id')
                ->on('academic.programa')
                ->onDelete('cascade');

            $table->foreign('id_grado_academico')
                ->references('id')
                ->on('academic.grado_academico')
                ->onDelete('cascade');

            $table->foreign('id_seccion_academico')
                ->references('id')
                ->on('academic.seccion_academico')
                ->onDelete('cascade');

            $table->foreign('id_docente')
                ->references('id')
                ->on('academic.persona')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic.programa_horarios', function (Blueprint $table) {
            $table->dropForeign('id_programa');
            $table->dropColumn('id_programa');

            $table->dropForeign('id_grado_academico');
            $table->dropColumn('id_grado_academico');

            $table->dropForeign('id_seccion_academico');
            $table->dropColumn('id_seccion_academico');

            $table->dropForeign('id_docente');
            $table->dropColumn('id_docente');
        });

        Schema::dropIfExists('academic.programa_horarios');
    }
};
