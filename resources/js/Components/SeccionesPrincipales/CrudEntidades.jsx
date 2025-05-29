import { useState } from "react";

// Importamos los modales para agregar y editar cada tipo de entidad
import AddAlmacenModal from "../Modales/AddAlmacenModal";
import AddCategoriaModal from "../Modales/AddCategoriaModal";
import AddClienteModal from "../Modales/AddClienteModal";
import AddProveedorModal from "../Modales/AddProveedorModal";

import EditAlmacenModal from "../Modales/EditAlmacenModal";
import EditCategoriaModal from "../Modales/EditCategoriaModal";
import EditClienteModal from "../Modales/EditClienteModal";
import EditProveedorModal from "../Modales/EditProveedorModal";
import { router } from "@inertiajs/react";
import { showModificableAlert } from "@/utils/alerts";

// Componente principal para gestionar entidades (almacenes, categorías, clientes, proveedores)
export default function CrudEntidades({ props }) {
    // Definimos las entidades disponibles con sus etiquetas, íconos y datos
    const entidades = {
        almacenes: {
            label: "Almacenes",
            icon: "📦",
            data: props.all_almacenes,
        },
        categorias: {
            label: "Categorías",
            icon: "🗂️",
            data: props.all_categorias,
        },
        clientes: {
            label: "Clientes",
            icon: "🧑‍💼",
            data: props.all_clientes,
        },
        proveedores: {
            label: "Proveedores",
            icon: "🚚",
            data: props.all_proveedores,
        },
    };

    // Estado para saber qué tipo de entidad está seleccionada
    const [selectedType, setSelectedType] = useState("almacenes");

    // Estado con los datos de todas las entidades (se puede actualizar al agregar o eliminar)
    const [data, setData] = useState(entidades);

    // Estados para controlar acciones: editar, eliminar o agregar
    const [itemToEdit, setItemToEdit] = useState(null);
    const [itemToDelete, setItemToDelete] = useState(null);
    const [itemToAdd, setItemToAdd] = useState(false); // Si se está agregando una nueva entidad

    // Accedemos a los datos actuales de la entidad seleccionada
    const currentItems = data[selectedType].data;
    const iconoEntidad = data[selectedType].icon;
    const tipoEntidad = data[selectedType].label;

    // Función para iniciar la edición de una entidad
    const handleEdit = (item) => setItemToEdit(item);

    // Función para eliminar una entidad del listado (solo visualmente)
    const handleDelete = (idEntidad) => {

        const updated = {
            ...data,
            [selectedType]: {
                ...data[selectedType],
                data: data[selectedType].data.filter((item) => item.id !== idEntidad),
            },
        };
        setData(updated);
        setItemToDelete(null);

        switch (selectedType) {
            case "almacenes":
                router.delete(route('entidad.almacen.delete'), {
                    data: { id: idEntidad},
                    onSuccess: () => {
                        showModificableAlert('Almacén eliminado', `Almacén eliminado del inventario.`, 'success');
                        // router.visit(route('entidades.index'), { preserveScroll: true });
                    },
                    onError: (errors) => {
                        showModificableAlert('Error al eliminar el almacén', `${JSON.stringify(errors)}`, 'error');
                    }
                });

                break
            case "categorias":
                router.delete(route('entidad.categoria.delete'), {
                    data: { id_categoria: idEntidad, redireccion: false},
                    onSuccess: () => {
                        showModificableAlert('Categoría eliminada', `Categoría eliminada del sistema.`, 'success');
                    },
                    onError: (errors) => {
                        const msg = errors.message || "Error al eliminar la categoría";
                        showModificableAlert('Error', msg, 'error');
                    }
                });
                break;
            case "clientes":
                router.delete(route('XXXXXXXXXXXXXXXXXXXXXXX'), {
                    data: { id_cliente: idEntidad, redireccion: false},
                    onSuccess: () => {
                        showModificableAlert('Cliente eliminado', `Cliente eliminado del sistema.`, 'success');
                        // router.visit(route('entidades.index'), { preserveScroll: true });
                    },
                    onError: (errors) => {
                        showModificableAlert('Error al eliminar el cliente', `${JSON.stringify(errors)}`, 'error');
                    }
                });
                break
            case "proveedores":
                router.delete(route('proveedor.destroy'), {
                    data: { id_proveedor: idEntidad, redireccion: false},
                    onSuccess: () => {
                        showModificableAlert('Proveedor eliminado', `Proveedor eliminado del sistema.`, 'success');
                        // router.visit(route('entidades.index'), { preserveScroll: true });
                    },
                    onError: (errors) => {
                        showModificableAlert('Error al eliminar el proveedor', `${JSON.stringify(errors)}`, 'error');
                    }
                });
                break
            default:
                return null;
        }
    };

    // Renderiza el modal correspondiente para editar según el tipo seleccionado
    const renderEditModal = () => {
        if (!itemToEdit) return null;

        switch (selectedType) {
            case "almacenes":
                return <EditAlmacenModal entity={itemToEdit} onClose={() => setItemToEdit(null)} />;
            case "categorias":
                return <EditCategoriaModal entity={itemToEdit} onClose={() => setItemToEdit(null)} />;
            case "clientes":
                return <EditClienteModal entity={itemToEdit} onClose={() => setItemToEdit(null)} />;
            case "proveedores":
                return <EditProveedorModal entity={itemToEdit} onClose={() => setItemToEdit(null)} />;
            default:
                return null;
        }
    };

    // Renderiza el modal correspondiente para agregar según el tipo seleccionado
    const renderAddModal = () => {
        if (!itemToAdd) return null;

        const closeAndAdd = (typeKey, newItem) => {
            setData((prev) => ({
                ...prev,
                [typeKey]: {
                    ...prev[typeKey],
                    data: [...prev[typeKey].data, newItem],
                },
            }));
            setItemToAdd(false);
        };

        switch (selectedType) {
            case "almacenes":
                return <AddAlmacenModal isOpen={true} onClose={() => setItemToAdd(false)} onAdd={(item) => closeAndAdd("almacenes", item)} />;
            case "categorias":
                return <AddCategoriaModal isOpen={true} onClose={() => setItemToAdd(false)} onAdd={(item) => closeAndAdd("categorias", item)} />;
            case "clientes":
                return <AddClienteModal isOpen={true} onClose={() => setItemToAdd(false)} onAdd={(item) => closeAndAdd("clientes", item)} />;
            case "proveedores":
                return <AddProveedorModal isOpen={true} onClose={() => setItemToAdd(false)} onAdd={(item) => closeAndAdd("proveedores", item)} />;
            default:
                return null;
        }
    };

    return (
        <div className="flex h-full relative">
            {/* Menú lateral para seleccionar tipo de entidad */}
            <aside className="w-1/5 bg-white p-4">
                <h2 className="pt-2 text-sm font-semibold mb-4 text-slate-600">
                    Elige el catálogo que quieres modificar
                </h2>
                <hr />
                <br />
                {/* Botones por cada tipo de entidad */}
                {Object.entries(data).map(([key, val]) => (
                    <button
                        key={key}
                        onClick={() => setSelectedType(key)}
                        className={`block text-left p-2 mb-2 w-full rounded-lg text-sm ${
                            selectedType === key
                                ? "bg-slate-300 font-bold "
                                : "hover:bg-slate-100 font-medium text-slate-600 "
                        }`}
                    >
                        {val.label}
                    </button>
                ))}
            </aside>

            {/* Panel principal */}
            <main className="flex-1 p-6 overflow-y-auto">
                <h1 className="text-2xl font-bold mb-6">{tipoEntidad}</h1>

                {/* Grid de tarjetas */}
                <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    {/* Tarjeta para crear nueva entidad */}
                   {/*  <div
                        onClick={() => setItemToAdd(true)}
                        className="flex flex-col items-center justify-center p-6 bg-slate-100 border-2 border-dashed rounded-lg cursor-pointer hover:bg-slate-200"
                    >
                        <div className="w-16 h-16 bg-slate-300 rounded-full flex items-center justify-center text-3xl font-bold">
                            +
                        </div>
                        <p className="mt-4 text-sm font-medium text-slate-600">
                            Crear nuevo
                        </p>
                    </div> */}

                    {/* Tarjetas de entidades existentes */}
                    {currentItems.map((item) => (
                        <div
                            key={item.id}
                            className="relative p-4 bg-white rounded-lg shadow hover:shadow-md transition flex flex-col justify-between hover:cursor-pointer"
                            onClick={() => handleEdit(item)}
                        >
                            <div>
                                {/* Icono grande representativo */}
                                <div className="w-full h-32 bg-slate-200 rounded mb-4 flex items-center justify-center text-7xl text-slate-500">
                                    {iconoEntidad}
                                </div>

                                {/* Título y descripción */}
                                <h2 className="text-sm font-bold mb-1">
                                    {item.nombre || item.email || "Elemento"}
                                </h2>
                                <p className="text-xs text-slate-500">
                                    {item.direccion ||
                                        item.telefono ||
                                        item.identificacion ||
                                        item.tipo_comprador ||
                                        ""}
                                </p>
                            </div>

                            {/* Botones para editar y eliminar */}
                         {/*    <div className="flex justify-end gap-3 mt-4">
                                <button
                                    onClick={() => handleEdit(item)}
                                    className="text-slate-600 hover:text-slate-800"
                                    title="Editar"
                                >
                                    <span className="material-icons">edit</span>
                                </button>
                                <button
                                    onClick={() => setItemToDelete(item)}
                                    className="text-red-600 hover:text-red-800"
                                    title="Eliminar"
                                >
                                    <span className="material-icons">delete</span>
                                </button>
                            </div> */}
                        </div>
                    ))}
                </div>
            </main>

            {/* Modal para editar entidad */}
            {renderEditModal()}

            {/* Modal para crear nueva entidad */}
            {renderAddModal()}

            {/* Modal de confirmación para eliminar entidad */}
            {itemToDelete && (
                <div className="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
                    <div className="bg-white p-6 rounded-xl shadow-lg max-w-sm w-full text-center">
                        <h2 className="text-lg font-bold mb-4">
                            ¿Eliminar {tipoEntidad.slice(0, -2)}?
                        </h2>
                        <p className="text-sm mb-6 text-slate-600">
                            ¿Estás seguro que quieres eliminar{" "}
                            <strong>{itemToDelete.nombre}</strong>?
                        </p>
                        <div className="flex justify-center gap-4">
                            <button
                                onClick={() => setItemToDelete(null)}
                                className="px-4 py-2 bg-slate-200 rounded-lg hover:bg-slate-300 text-sm"
                            >
                                Cancelar
                            </button>
                            <button
                                onClick={() => handleDelete(itemToDelete.id)}
                                className="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 text-sm"
                            >
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
