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
        Schema::create('academic.plantilla', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_institucion')->nullable();
            $table->unsignedBigInteger('id_sucursal')->nullable();
            $table->unsignedBigInteger('codigo_tipo_programa')->nullable();

            $table->string('nombre', 100);
            $table->string('descripcion', 150)->nullable();
            $table->string('path_imagen_fondo', 150)->nullable();
            $table->string('path_imagen_publica', 150)->nullable();
            $table->string('path_pdf_fondo', 150)->nullable();
            $table->string('tipo_disenio', 100)->nullable();
            $table->string('disenio_default', 100)->nullable();

            $table->string('fecha_crea', 10)->nullable();
            $table->string('fecha_actualiza', 10)->nullable();
            $table->string('fecha_elimina', 10)->nullable();

            $table->string('user_crea', 12)->nullable();
            $table->string('user_actualiza', 12)->nullable();
            $table->string('user_elimina', 12)->nullable();

            $table->boolean('estado')->default(true);

            $table->timestamps();

            $table->foreign('id_institucion')
                ->references('id')
                ->on('academic.institucion')
                ->onDelete('cascade');

            $table->foreign('id_sucursal')
                ->references('id')
                ->on('academic.institucion')
                ->onDelete('cascade');

            $table->foreign('codigo_tipo_programa')
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
        Schema::table('academic.plantilla', function (Blueprint $table) {
            $table->dropForeign('id_institucion');
            $table->dropColumn('id_institucion');

            $table->dropForeign('id_sucursal');
            $table->dropColumn('id_sucursal');

            $table->dropForeign('codigo_tipo_programa');
            $table->dropColumn('codigo_tipo_programa');
        });

        Schema::dropIfExists('academic.plantilla');
    }
};
