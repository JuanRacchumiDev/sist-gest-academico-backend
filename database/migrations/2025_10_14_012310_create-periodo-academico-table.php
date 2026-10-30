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
        Schema::create('academic.periodo_academico', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('codigo_estado_periodo_academico')->nullable();

            $table->string('nombre', 100);
            $table->year('year');
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

            $table->foreign('codigo_estado_periodo_academico')
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
        Schema::table('academic.periodo_academico', function (Blueprint $table) {
            $table->dropForeign('codigo_estado_periodoacademico');
            $table->dropColumn('codigo_estado_periodoacademico');
        });

        Schema::dropIfExists('academic.periodo_academico');
    }
};
