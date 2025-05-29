import React, { useState } from "react";
import { router } from "@inertiajs/react";
import { showModificableAlert } from "@/utils/alerts";

export default function AddClienteModal({ isOpen, onClose, onAdd }) {
    const [form, setForm] = useState({
        nombre: "",
        identificacion: "",
        telefono: "",
        email: "",
        direccion: "",
        tipo_comprador: "Minorista",
    });

    const handleChange = (e) => {
        setForm({ ...form, [e.target.name]: e.target.value });
    };

    const handleSubmit = (e) => {
        e.preventDefault();

        if (form.nombre && form.identificacion && form.email) {
            onClose();

            router.post(route("compradores.store"), form, {
                onSuccess: () => {
                    showModificableAlert("Cliente añadido", `${form.nombre} registrado correctamente.`, "success");
                    router.visit(route("compradores.index"), { preserveScroll: true });

                    if (onAdd) onAdd(form);
                },
                onError: (errors) => {
                    showModificableAlert("Error al añadir cliente", `Error: ${JSON.stringify(errors)}`, "error");
                },
            });
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
            <div className="bg-white p-6 rounded-lg w-96 space-y-3 shadow-xl">
                <h2 className="text-lg font-bold text-slate-700">Añadir Cliente</h2>
                <input name="nombre" placeholder="Nombre" onChange={handleChange} value={form.nombre} className="w-full p-2 border rounded" />
                <input name="identificacion" placeholder="Identificación" onChange={handleChange} value={form.identificacion} className="w-full p-2 border rounded" />
                <input name="telefono" placeholder="Teléfono" onChange={handleChange} value={form.telefono} className="w-full p-2 border rounded" />
                <input name="email" placeholder="Email" onChange={handleChange} value={form.email} className="w-full p-2 border rounded" />
                <input name="direccion" placeholder="Dirección" onChange={handleChange} value={form.direccion} className="w-full p-2 border rounded" />
                <select name="tipo_comprador" onChange={handleChange} value={form.tipo_comprador} className="w-full p-2 border rounded">
                    <option value="empresa">Empresa</option>
                    <option value="particular">Particular</option>
                </select>
                <div className="flex justify-end space-x-2">
                    <button onClick={onClose} className="text-red-500 hover:underline">Cancelar</button>
                    <button onClick={handleSubmit} className="bg-slate-600 text-white px-4 py-2 rounded hover:bg-slate-700">Guardar</button>
                </div>
            </div>
        </div>
    );
}
