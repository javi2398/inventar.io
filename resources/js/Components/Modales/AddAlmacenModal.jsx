import React, { useState } from "react";
import { router } from "@inertiajs/react";
import { showModificableAlert } from "@/utils/alerts"; // Función para mostrar alertas personalizadas

// Componente modal para agregar un nuevo almacén
export default function AddAlmacenModal({ isOpen, onClose, onAdd }) {
    // Estado local para el formulario
    const [form, setForm] = useState({
        nombre: "",
        productos_count: 0,     // Inicialmente 0 productos
        precio_total: 0,        // Precio total inicializado en 0
        direccion: "",
    });

    // Función para actualizar el estado del formulario al escribir en los inputs
    const handleChange = (e) => {
        setForm({ ...form, [e.target.name]: e.target.value });
    };

    

    // Función que maneja el envío del formulario
    const handleSubmit = (e) => {
        e.preventDefault();

        // Validación básica: nombre y dirección deben estar llenos
        if (form.nombre && form.direccion) {
            onClose(); // Cierra el modal

            // Envía los datos usando Inertia
            router.post(route("entidad.almacen.store"), form, {
                onSuccess: () => {
                    // Muestra alerta de éxito
                    showModificableAlert(
                        "Almacén añadido",
                        `${form.nombre} agregado al inventario.`,
                        "success"
                    );
                    // Redirige o refresca la vista del inventario sin perder el scroll
                    router.visit(route("entidades.index"), { preserveScroll: true });

                    // Llama a la función onAdd si fue proporcionada por el componente padre
                    if (onAdd) onAdd(form);
                },
                onError: (errors) => {
                    // Muestra alerta de error con los errores devueltos
                    showModificableAlert(
                        "Error al añadir el almacén",
                        `Error: ${JSON.stringify(errors)}`,
                        "error"
                    );
                },
            });
        }
    };

    // Si el modal no debe estar visible, no renderiza nada
    if (!isOpen) return null;

    // Renderizado del modal
    return (
        <div className="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
            <div className="bg-white p-6 rounded-lg w-96 space-y-4 shadow-xl">
                <h2 className="text-lg font-bold text-slate-700">Añadir Almacén</h2>

                {/* Input para el nombre del almacén */}
                <input
                    name="nombre"
                    placeholder="Nombre"
                    onChange={handleChange}
                    value={form.nombre}
                    className="w-full p-2 border rounded"
                />

                {/* Input para la dirección del almacén */}
                <input
                    name="direccion"
                    placeholder="Dirección"
                    onChange={handleChange}
                    value={form.direccion}
                    className="w-full p-2 border rounded"
                />

                {/* Botones de acción */}
                <div className="flex justify-end space-x-2">
                    <button
                        onClick={onClose}
                        className="text-red-500 hover:underline"
                    >
                        Cancelar
                    </button>
                    <button
                        onClick={handleSubmit}
                        className="bg-slate-600 text-white px-4 py-2 rounded hover:bg-slate-700"
                    >
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    );
}
