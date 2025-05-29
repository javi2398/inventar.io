"use client";

import { useState } from "react";
import Chip from "@/Components/Chip";
import EditProductModal from "@/Components/Modales/EditProductModal";
import DeleteProductModal from "@/Components/Modales/DeleteProductModal";
import { router } from "@inertiajs/react";

export default function ProductTableRow({
    product, /* este product le llega de InventarioTabla o de OrdenesCompra */
    context, /* indica si se va a comportar como un row de inventario o de pedidos */
    almacenes = [],
    onUpdate,
    categorias,
    proveedores = [],
    onCantidadClick,
    clickable, /* Esto va a indicar si el row debe ser boton o no */
    props,
}) {
    const [isEditModalOpen, setEditModalOpen] = useState(false);
    const [isDeleteModalOpen, setDeleteModalOpen] = useState(false);

    const handleEditSave = (updatedData) => {
        if (onUpdate) {
            onUpdate({ ...product, ...updatedData });
        }
        setEditModalOpen(false);
    };

    const detalleProducto = () => {
        router.get(route("producto.index", { id: product.id_producto }), {
            onSuccess: () => {},
            onError: (errors) => {
                showModificableAlert(
                    "Error al mostrar los detalles del producto",
                    `Error: ${JSON.stringify(errors)}`,
                    "error"
                );
            },
        });
    };

    return (
        <>
            {/* Efecto clickable */}
            <div
                className={`relative group grid grid-cols-8 items-center bg-white rounded-sm border border-slate-200 p-2 gap-4 transition-all
                ${
                    clickable 
                        ? "hover:px-1 hover:border-slate-600 hover:border-2 cursor-pointer"
                        : "cursor-default"
                }`}
                title={clickable ? "Ver detalle del producto" : ""}
                onClick={clickable ? detalleProducto : null}
            >
                {/* Código */}
                <div className="text-gray-700 font-semibold text-left pl-2">
                    {product.codigo}
                </div>

                {/* Nombre con tooltip */}
                <div className="relative group flex items-center col-span-2">
                    <span className="text-gray-800 font-bold truncate w-full text-left">
                        {product.nombre}
                    </span>
                    <div className="absolute top-6 left-6 max-w-xs bg-slate-700 text-white text-xs rounded-md py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity z-10 overflow-hidden text-ellipsis whitespace-nowrap">
                        {product.nombre}
                    </div>
                </div>

                {/* Precio */}
                <div className="text-gray-700 text-center">
                    {product.precio_unitario} €
                </div>

                {/* Cantidad */}
                <div className="text-gray-700 text-center">
                    {product.cantidad_actual}
                </div>

                {/* Estado o almacén */}
                <div className="text-gray-700 text-center">
                    {context === "orders" ? (
                        <Chip status={!!product.estado} />
                    ) : (
                        <span>{product.almacen_nombre || "Sin asignar"}</span>
                    )}
                </div>

                {/* Fecha */}
                <div className="text-gray-700 text-center">
                    {product.fecha_entrada ?? product.fecha_compra}
                </div>

                {/* Acciones */}
                <div className="flex justify-center gap-4 items-center">
                    {/* Editar */}
                    <div
                        className="flex items-center justify-center w-8 h-8 rounded-full text-slate-500 hover:text-slate-700 cursor-pointer"
                        onClick={(e) => {
                            e.stopPropagation();
                            setEditModalOpen(true);
                        }}
                        title="Editar producto"
                    >
                        <span className="material-icons">edit</span>
                    </div>

                    {/* Eliminar: solo si no está recibido */}

                    {!product.estado ? (
                        <div
                            className="flex items-center justify-center text-red-400 w-8 h-8 rounded-full hover:text-red-500 cursor-pointer"
                            onClick={(e) => {
                                e.stopPropagation();
                                setDeleteModalOpen(true);
                            }}
                            title="Eliminar producto"
                        >
                            <span className="material-icons">delete</span>
                        </div>
                    ) : (
                           <div
                        className="flex items-center justify-center text-slate-200 w-8 h-8 rounded-full"
                        title="Eliminar producto"
                    >
                        <span className="material-icons">delete</span>
                    </div>
                    )}
                    {/* Vender o Recibir */}
                    <div
                        className={`flex items-center justify-center w-8 h-8 rounded-full cursor-pointer ${
                            context === "orders"
                                ? "text-blue-500 hover:text-blue-600"
                                : "text-green-500 hover:text-green-600"
                        }`}
                        onClick={(e) => {
                            e.stopPropagation();
                            if (onCantidadClick) {
                                onCantidadClick(
                                    context === "orders"
                                        ? "recepcion"
                                        : "venta",
                                    product
                                );
                            }
                        }}
                        title={
                            context === "orders"
                                ? "Marcar como recibido"
                                : "Vender producto"
                        }
                    >
                        <span className="material-icons">
                            {context === "orders" ? "inventory_2" : "sell"}
                        </span>
                    </div>
                </div>
            </div>

            {/* Modal de edición */}
            {isEditModalOpen && (
                <EditProductModal
                    product={product}
                    context={context}
                    almacenes={almacenes}
                    categorias={categorias}
                    producto={product}
                    proveedores={proveedores}
                    onClose={() => setEditModalOpen(false)}
                    onSave={handleEditSave}
                />
            )}

            {/* Modal de eliminación */}
            {isDeleteModalOpen && (
                <DeleteProductModal
                    product={product}
                    totalAmount={product.cantidad_actual}
                    onClose={() => setDeleteModalOpen(false)}
                    contexto={context}
                />
            )}
        </>
    );
}
