import React, { useState } from "react";
import { router } from "@inertiajs/react";
import { showModificableAlert } from "@/utils/alerts";

export default function AddProveedorModal({ isOpen, onClose, onAdd }) {
    const [form, setForm] = useState({
        nombre: "",
        telefono: "",
        email: "",
    });

    const handleChange = (e) => {
        setForm({ ...form, [e.target.name]: e.target.value });
    };

    const handleSubmit = (e) => {
        e.preventDefault();

        if (form.nombre && form.email) {
            onClose();

            router.post(route("proveedor.store"), form, {
                onSuccess: () => {
                    showModificableAlert("Proveedor añadido", `${form.nombre} registrado correctamente.`, "success");
                    router.visit(route("proveedores.index"), { preserveScroll: true });

                    if (onAdd) onAdd(form);
                },
                onError: (errors) => {
                    showModificableAlert("Error al añadir proveedor", `Error: ${JSON.stringify(errors)}`, "error");
                },
            });
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
            <div className="bg-white p-6 rounded-lg w-96 space-y-4 shadow-xl">
                <h2 className="text-lg font-bold text-slate-700">Añadir Proveedor</h2>
                <input name="nombre" placeholder="Nombre" onChange={handleChange} value={form.nombre} className="w-full p-2 border rounded" />
                <input name="telefono" placeholder="Teléfono" onChange={handleChange} value={form.telefono} className="w-full p-2 border rounded" />
                <input name="email" placeholder="Email" onChange={handleChange} value={form.email} className="w-full p-2 border rounded" />
                <div className="flex justify-end space-x-2">
                    <button onClick={onClose} className="text-red-500 hover:underline">Cancelar</button>
                    <button onClick={handleSubmit} className="bg-slate-600 text-white px-4 py-2 rounded hover:bg-slate-700">Guardar</button>
                </div>
            </div>
        </div>
    );
}
