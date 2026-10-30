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
        Schema::create('academic.persona', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('codigo_tipo_documento');
            $table->unsignedBigInteger('codigo_tipo_parentesco')->nullable();
            $table->unsignedBigInteger('codigo_pais')->nullable();
            $table->unsignedBigInteger('id_parentesco')->nullable();

            $table->string('numero_documento', 13);
            $table->string('nombres', 30);
            $table->string('apellido_paterno', 30);
            $table->string('apellido_materno', 30);
            $table->string('nombre_completo', 100);
            $table->string('departamento', 50)->nullable();
            $table->string('provincia', 50)->nullable();
            $table->string('distrito', 50)->nullable();
            $table->string('direccion', 60)->nullable();
            $table->string('direccion_completa', 150)->nullable();
            $table->string('email', 60)->nullable();
            $table->string('telefono', 13)->nullable();
            $table->string('ubigeo_reniec', 12)->nullable();
            $table->string('ubigeo_sunat', 12)->nullable();
            $table->string('ubigeo', 12)->nullable();
            $table->string('fecha_nacimiento', 10)->nullable();
            $table->string('estado_civil', 15)->nullable();
            $table->string('foto', 100)->nullable();
            $table->enum('sexo', ['M', 'F'])->default('M');
            $table->enum('origen', ['API', 'WEB', 'APP'])->default('WEB');

            $table->string('fecha_crea', 10)->nullable();
            $table->string('fecha_actualiza', 10)->nullable();
            $table->string('fecha_elimina', 10)->nullable();

            $table->string("user_crea", 12)->nullable();
            $table->string('user_actualiza', 12)->nullable();
            $table->string('user_elimina', 12)->nullable();

            $table->boolean('allow_notificacion')->default(false);
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->foreign('codigo_tipo_documento')
                ->references('codigo')
                ->on('academic.detalle_parametro')
                ->onDelete('cascade');

            $table->foreign('codigo_tipo_parentesco')
                ->references('codigo')
                ->on('academic.detalle_parametro')
                ->onDelete('cascade');

            $table->foreign('codigo_pais')
                ->references('codigo')
                ->on('academic.detalle_parametro')
                ->onDelete('cascade');

            $table->foreign('id_parentesco')
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
        Schema::table('academic.persona', function (Blueprint $table) {
            $table->dropForeign('codigo_tipo_documento');
            $table->dropColumn('codigo_tipo_documento');

            $table->dropForeign('codigo_tipo_parentesco');
            $table->dropColumn('codigo_tipo_parentesco');

            $table->dropForeign('codigo_pais');
            $table->dropColumn('codigo_pais');

            $table->dropForeign('id_parentesco');
            $table->dropColumn('id_parentesco');
        });

        Schema::dropIfExists('academic.persona');
    }
};
