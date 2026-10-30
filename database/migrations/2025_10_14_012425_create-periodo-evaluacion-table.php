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
        Schema::create('academic.periodo_evaluacion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_periodo_academico')->nullable();
            $table->unsignedBigInteger('codigo_tipo_periodo_academico')->nullable();

            $table->string('nombre', 50);
            $table->string('fecha_inicio', 10);
            $table->string('fecha_final', 10);

            $table->string('fecha_crea', 10)->nullable();
            $table->string('fecha_actualiza', 10)->nullable();
            $table->string('fecha_elimina', 10)->nullable();

            $table->string('user_crea', 12)->nullable();
            $table->string('user_actualiza', 12)->nullable();
            $table->string('user_elimina', 12)->nullable();

            $table->boolean('estado')->default(true);

            $table->timestamps();

            $table->foreign('id_periodo_academico')
                ->references('id')
                ->on('academic.periodo_academico')
                ->onDelete('cascade');

            $table->foreign('codigo_tipo_periodo_academico')
                ->references('codigo')
                ->on('academic.detalle_parametro')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic.periodo_evaluacion', function (Blueprint $table) {
            $table->dropForeign('id_periodo_academico');
            $table->dropColumn('id_periodo_academico');

            $table->dropForeign('codigo_tipo_periodo_academico');
            $table->dropColumn('codigo_tipo_periodo_academico');
        });

        Schema::dropIfExists('academic.periodo_evaluacion');
    }
};
