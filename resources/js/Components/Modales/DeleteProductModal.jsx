import { useState } from "react";
import { router } from "@inertiajs/react";
import { showModificableAlert } from "@/utils/alerts";

export default function DeleteProductModal({ product, totalAmount, onClose, contexto }) {
  const [reduceAmount, setReduceAmount] = useState(0);
    console.log(product)
    console.log(contexto)
  const rutaDelete = contexto === 'orders' ? 'pedidos.destroy' : 'producto.delete';
  const rutaPatch = contexto === 'orders' ? 'pedidos.patchInventario' : 'producto.patch';
  const rutaRedirect = contexto === 'orders' ? 'pedidos.index' : 'inventario.index';

    

  const handleDeleteAll = () => { 
      onClose();
      router.delete(route(rutaDelete), {
        data: {
          id_producto: product.id_producto,
          id_almacen: product.id_almacen,
          precio_unitario: product.precio_unitario,
          id_detalle: product.id_detalle ?? ""
        },
        onSuccess: () => {
          showModificableAlert(
            "Elemento eliminado",
            `${product.nombre} eliminado del sistema.`,
            "success"
          );
          router.visit(route(rutaRedirect));
        },
        onError: (error) =>
          showModificableAlert(
            `Error al eliminar ${product.nombre}`,
            `Error: ${JSON.stringify(error)}`,
            "error"
          ),
      });
  };

  const handleDeletePartial = () => {
    if (reduceAmount <= product.cantidad_actual) {
      onClose();
      router.patch(
        route(rutaPatch),
        {
          id_almacen: product.id_almacen,
          id_producto: product.id_producto,
          cantidad_actual: product.cantidad_actual - reduceAmount,
          id_detalle: product.id_detalle ?? ""
        },
        {
          onSuccess: () => {
            showModificableAlert(
              "Cantidad reducida",
              `Cantidad de ${product.nombre} actualizada.`,
              "success"
            );
            router.visit(route(rutaRedirect));
          },
          onError: (error) =>
            showModificableAlert(
              `Error al reducir la cantidad de ${product.nombre}.`,
              `Error: ${JSON.stringify(error)}`,
              "error"
            ),
        }
      );
    } else {
      showModificableAlert(
        "Cantidad excedida",
        `La cantidad máxima a reducir es de: ${product.cantidad_actual}.`,
        "warning"
      );
    }
  };

  return (
    <div className="fixed inset-0 bg-slate-800 bg-opacity-30 flex items-center justify-center z-50">
      <div className="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
        <h2 className="text-2xl font-bold text-gray-800 mb-4">
          Eliminar producto
        </h2>

        <p className="text-gray-700 mb-3">
          {contexto === "stock"
            ? "¿Deseas eliminar el producto completo o reducir su cantidad?"
            : "¿Deseas eliminar este producto de la orden?"}
        </p>

        {contexto === "stock" && (
          <>
            <div className="mb-4 font-bold text-gray-700">
              Cantidad disponible:{" "}
              <span className="font-semibold text-black">{product.cantidad_actual}</span>
            </div>

            <div className="mb-4">
              <label className="block text-gray-700 mb-1">Cantidad a reducir</label>
              <div className="flex gap-2">
                <input
                  type="number"
                  min="0"
                  max={totalAmount}
                  value={reduceAmount}
                  onChange={(e) => setReduceAmount(Number(e.target.value))}
                  className="w-full border border-slate-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-400"
                  placeholder="Cantidad a reducir"
                />
                <button
                  type="button"
                  className="px-4 py-2 rounded-md bg-slate-500 text-white hover:bg-slate-700 transition"
                  onClick={handleDeletePartial}
                >
                  Reducir
                </button>
              </div>
            </div>
          </>
        )}

        <div className="flex justify-end gap-3">
          <button
            onClick={onClose}
            className="px-4 py-2 rounded-md border border-gray-300 text-gray-600 hover:bg-gray-100 transition"
          >
            Cancelar
          </button>
          <button
            onClick={handleDeleteAll}
            className="px-4 py-2 rounded-md text-white bg-red-500 hover:bg-red-600 transition"
          >
            Eliminar
          </button>
        </div>
      </div>
    </div>
  );
}
