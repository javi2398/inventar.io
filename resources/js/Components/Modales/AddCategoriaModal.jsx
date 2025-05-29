import React, { useState } from "react";
import { router } from "@inertiajs/react";
import { showModificableAlert } from "@/utils/alerts";

export default function AddCategoriaModal({ isOpen, onClose, onAdd }) {
    const [form, setForm] = useState({
        nombre: "",
    });

    const handleChange = (e) => {
        setForm({ ...form, [e.target.name]: e.target.value });
    };

    const handleSubmit = (e) => {
        e.preventDefault();

        if (form.nombre) {
            onClose();

            router.post(route("categorias.store"), form, {
                onSuccess: () => {
                    showModificableAlert("Categoría añadida", `${form.nombre} creada correctamente.`, "success");
                    router.visit(route("categorias.index"), { preserveScroll: true });

                    if (onAdd) onAdd(form);
                },
                onError: (errors) => {
                    showModificableAlert("Error al añadir categoría", `Error: ${JSON.stringify(errors)}`, "error");
                },
            });
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
            <div className="bg-white p-6 rounded-lg w-96 space-y-4 shadow-xl">
                <h2 className="text-lg font-bold text-slate-700">Añadir Categoría</h2>
                <input
                    name="nombre"
                    placeholder="Nombre"
                    onChange={handleChange}
                    value={form.nombre}
                    className="w-full p-2 border rounded"
                />
                <div className="flex justify-end space-x-2">
                    <button onClick={onClose} className="text-red-500 hover:underline">Cancelar</button>
                    <button onClick={handleSubmit} className="bg-slate-600 text-white px-4 py-2 rounded hover:bg-slate-700">Guardar</button>
                </div>
            </div>
        </div>
    );
}
