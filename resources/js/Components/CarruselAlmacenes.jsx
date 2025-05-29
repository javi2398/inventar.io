"use client";

import { useState } from "react";
import { router } from '@inertiajs/react';
import { showModificableAlert } from "@/utils/alerts";

function AddAlmacenModal({ isOpen, onClose, onAdd }) {
    const [form, setForm] = useState({
        nombre: "",
        productos_count: 0,
        precio_total: 0,
        direccion: "",
    });

    const handleChange = (e) =>
        setForm({ ...form, [e.target.name]: e.target.value });

    const handleSubmit = (e) => {
        e.preventDefault();

        if (form.nombre && form.direccion) {
            onClose();
            router.post(route('inventario.store'), form, {
                onSuccess: () => {
                    showModificableAlert('Almacén añadido', `${form.nombre} agregado al inventario.`, 'success');
                    router.visit(route('inventario.index'), { preserveScroll: true });
                },
                onError: (errors) => {
                    showModificableAlert('Error al añadir el almacén', `Error: ${errors}`, 'error');
                }
            });
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
            <div className="bg-white p-6 rounded-lg w-96 space-y-4 shadow-xl">
                <h2 className="text-lg font-bold text-slate-700">Añadir Almacén</h2>
                <input
                    name="nombre"
                    placeholder="Nombre"
                    onChange={handleChange}
                    className="w-full p-2 border rounded"
                />
                <input
                    name="direccion"
                    placeholder="Dirección"
                    onChange={handleChange}
                    className="w-full p-2 border rounded"
                />
                <div className="flex justify-end space-x-2">
                    <button onClick={onClose} className="text-red-500">Cancelar</button>
                    <button
                        onClick={handleSubmit}
                        className="bg-slate-600 text-white px-4 py-2 rounded"
                    >
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    );
}

function DeleteAlmacenModal({ isOpen, onClose, almacenes, onDelete }) {
    const [selected, setSelected] = useState(null);

    const handleDelete = () => {
        if (selected !== null) {
            onDelete(selected);
            onClose();
            router.delete(route('inventario.delete'), {
                data: { id: selected, redireccion: true },
                onSuccess: () => {
                    showModificableAlert('Almacén eliminado', `Almacén eliminado del inventario.`, 'success');
                    router.visit(route('inventario.index'), { preserveScroll: true });
                },
                onError: (errors) => {
                    showModificableAlert('Error al eliminar el almacén', `${JSON.stringify(errors)}`, 'error');
                }
            });
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
            <div className="bg-white p-6 rounded-lg w-96 shadow-xl">
                <h2 className="text-lg font-bold text-slate-700 mb-4">Eliminar Almacén</h2>
                <select
                    className="w-full p-2 border rounded mb-4"
                    value={selected ?? ""}
                    onChange={(e) => setSelected(Number(e.target.value))}
                >
                    <option value="" disabled>Selecciona un almacén</option>
                    {almacenes.map((a) => (
                        <option key={a.id} value={a.id}>{a.nombre} - {a.direccion}</option>
                    ))}
                </select>
                <div className="flex justify-end space-x-2">
                    <button onClick={onClose} className="text-slate-600">Cancelar</button>
                    <button
                        onClick={handleDelete}
                        className="bg-red-500 text-white px-4 py-2 rounded"
                    >
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    );
}

export default function CarruselAlmacenes({ arrayAlmacenes, selected, setSelected }) {
    const almacenes = arrayAlmacenes;
    const [showAddModal, setShowAddModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);

    const toggleSelect = (id) => {
        setSelected((prev) =>
            prev.includes(id)
                ? prev.filter((selectedId) => selectedId !== id)
                : [...prev, id]
        );
    };

    const filtrarAlmacenes = () => {
        router.post(route(''), {
            data: { id: selected },
            onSuccess: () => {
                console.log('Almacenes filtrados');
            },
            onError: (errors) => {
                showModificableAlert('Error al filtrar los almacenes', `${JSON.stringify(errors)}`, 'error');
            }
        });
    };

    const handleAddAlmacen = () => {};
    const handleDeleteAlmacen = () => {};

    return (
        <div className="w-full">
            {/* Carrusel */}
            <div className="overflow-x-auto w-full py-2">
                <div className="flex space-x-4 px-1">
                    {almacenes.map((almacen, index) => (
                        <div
                            key={index}
                            onClick={() => toggleSelect(almacen.id)}
                            className={`min-w-[250px] rounded-xl p-4 shadow-md flex-shrink-0 cursor-pointer
                                ${selected.includes(almacen.id)
                                    ? "bg-slate-300 shadow-inner"
                                    : "bg-slate-100 shadow-md"
                                }`}
                        >
                            <div className="flex items-center space-x-2 mb-2">
                                <span className="material-icons text-slate-600 text-xl">
                                    warehouse
                                </span>
                                <span className="font-bold text-slate-800">
                                    {almacen.nombre}
                                </span>
                            </div>
                            <div className="text-xs font-bold text-slate-600">
                                <div>{almacen.productos_count} productos</div>
                                <div className="text-green-600">{almacen.precio_total.toFixed(2)}€</div>
                                <div className="text-xs text-slate-500 mt-1">
                                    {almacen.direccion}
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>

            {/* Botones debajo del carrusel */}
            <div className="flex justify-start gap-3 items-center mt-4 px-2">
                <button
                    onClick={() => setShowAddModal(true)}
                    className="bg-slate-600 text-white px-3 py-1 rounded-md hover:bg-slate-700 text-sm flex items-center gap-1"
                >
                    <span className="material-icons text-xl">add</span>
                    Añadir Almacén
                </button>

                <button
                    onClick={() => setShowDeleteModal(true)}
                    className="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-sm flex items-center gap-1"
                >
                    <span className="material-icons text-xl">delete</span>
                    Eliminar Almacén
                </button>
            </div>

            {/* Modales */}
            <AddAlmacenModal
                isOpen={showAddModal}
                onClose={() => setShowAddModal(false)}
                onAdd={handleAddAlmacen}
            />
            <DeleteAlmacenModal
                isOpen={showDeleteModal}
                onClose={() => setShowDeleteModal(false)}
                almacenes={almacenes}
                onDelete={handleDeleteAlmacen}
            />
        </div>
    );
}
