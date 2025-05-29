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
        Schema::create('detalle_compras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_compra');
            $table->unsignedBigInteger('id_producto');
            $table->unsignedBigInteger('id_almacen');

            $table->integer('cantidad_actual');
            $table->decimal('precio_unitario');
            $table->boolean('estado');
            $table->date('fecha_vencimiento')->nullable();
            $table->timestamps();

            $table->foreign('id_compra')->references('id')->on('compras');
            $table->foreign('id_producto')->references('id')->on('productos');
            $table->foreign('id_almacen')->references('id')->on('almacenes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_compras');
    }
};
