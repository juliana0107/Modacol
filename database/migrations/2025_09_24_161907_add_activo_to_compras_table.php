<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::table('compras', function (Blueprint $table) {
        // Verifica si la columna 'Activo' no existe antes de agregarla
        if (!Schema::hasColumn('compras', 'Activo')) {
            $table->tinyInteger('Activo')->default(1); // Agrega 'Activo' solo si no existe
        }
    });
}

public function down()
{
    Schema::table('compras', function (Blueprint $table) {
        $table->dropColumn('Activo');
    });
}
};