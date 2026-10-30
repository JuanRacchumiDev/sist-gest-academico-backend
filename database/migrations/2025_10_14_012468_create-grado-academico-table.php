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
        Schema::create('academic.grado_academico', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('codigo_nivel_academico');
            $table->string('nombre', 50);

            $table->string('fecha_crea', 10)->nullable();
            $table->string('fecha_actualiza', 10)->nullable();
            $table->string('fecha_elimina', 10)->nullable();

            $table->string('user_crea', 12)->nullable();
            $table->string('user_actualiza', 12)->nullable();
            $table->string('user_elimina', 12)->nullable();

            $table->boolean('estado')->default(true);

            $table->timestamps();

            $table->foreign('codigo_nivel_academico')
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
        Schema::table('academic.grado_academico', function (Blueprint $table) {
            $table->dropForeign('codigo_nivel_academico');
            $table->dropColumn('codigo_nivel_academico');
        });

        Schema::dropIfExists('academic.grado_academico');
    }
};
