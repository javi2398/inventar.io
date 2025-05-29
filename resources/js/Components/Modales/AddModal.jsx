import { useState, useEffect, useRef } from "react";
import { Dialog } from "@headlessui/react";
import { router } from "@inertiajs/react";
import { showModificableAlert } from "@/utils/alerts";

export default function AddModal({
    isOpen,
    onClose,
    onAdd,
    context,
    almacenes = [],
    categorias = [],
    productos = [],
    proveedores = [],
}) {
    const [formData, setFormData] = useState({
        id_categoria: "",
        codigo: "",
        nombre: "",
        descripcion: "",
        imagen: "",
        id_almacen: "",
        precio_unitario: "",
        cantidad_actual: "",
        perecedero: false,
        fecha_vencimiento: "",
        id_proveedor: "",
        nombre_categoria: "",
        nombre_proveedor: "",
        telefono: "",
        email: "",
    });

    const [mostrarNuevaCategoria, setMostrarNuevaCategoria] = useState(false);
    const [mostrarNuevoProveedor, setMostrarNuevoProveedor] = useState(false);
    const [mostrarFecha, setMostrarFecha] = useState(false);
    const [mostrarFoto, setMostrarFoto] = useState("");
    const [imagenUpload, setImagenUpload] = useState("");
    const inputFileRef = useRef(null);
    const [isReady, setIsReady] = useState(false);

    useEffect(() => {
        if (!isOpen) {
            setFormData({
                id_categoria: "",
                codigo: "",
                nombre: "",
                descripcion: "",
                imagen: "",
                id_almacen: "",
                precio_unitario: "",
                cantidad_actual: "",
                perecedero: false,
                fecha_vencimiento: "",
                id_proveedor: "",
                nombre_categoria: "",
                nombre_proveedor: "",
                telefono: "",
                email: "",
            });
            setMostrarNuevaCategoria(false);
            setMostrarNuevoProveedor(false);
            setMostrarFecha(false);
        }
    }, [isOpen]);

    const handleInputChange = ({ target: { name, value, type, checked } }) => {
        const inputValue = type === "checkbox" ? checked : value;

        if (name === "perecedero") {
            setMostrarFecha(inputValue);
        }

        setFormData((prev) => ({
            ...prev,
            [name]: inputValue,
        }));
    };

    const handleCategoriaChange = ({ target: { value } }) => {
        setMostrarNuevaCategoria(value === "nueva");
        setFormData((prev) => ({
            ...prev,
            id_categoria: value === "nueva" ? "" : value,
        }));
    };

    const handleProveedorChange = ({ target: { value } }) => {
        setMostrarNuevoProveedor(value === "nuevo");
        setFormData((prev) => ({
            ...prev,
            id_proveedor: value === "nuevo" ? "" : value,
        }));
    };

    const mostrarFotoSeleccionada = (foto) => {
        // Esto muestra las fotos subidas en la pagina
        const urlImagen = foto.target.files[0];
        // setMostrarFoto(URL.createObjectURL(urlImagen))
        setMostrarFoto({
            url: URL.createObjectURL(urlImagen),
            nombre: urlImagen.name,
        });
        setImagenUpload(urlImagen);
        console.log(URL.createObjectURL(urlImagen));
    };

    const handleDeletePhoto = () => {
        setMostrarFoto("");
        setImagenUpload("");
        inputFileRef.current.value = ""; // Reseteas el input file
    };

    const handleUpload = async () => {
        if (!imagenUpload) {
            console.log("No hay imagen para subir");
            setIsReady(true);
            return;
        }

        const uploadData = new FormData();

        const uploadToCloudinary = async (image) => {
            uploadData.append("file", image);
            uploadData.append("upload_preset", "default");

            try {
                const response = await fetch(
                    `https://api.cloudinary.com/v1_1/dcdvxqsxn/image/upload`,
                    {
                        method: "POST",
                        body: uploadData,
                    }
                );

                const data = await response.json();

                if (response.ok) {
                    console.log("Imagen subida con éxito:", [
                        data,
                        data.url,
                        data.public_id,
                        data.original_filename,
                    ]);
                    return data; // Devolvemos los datos de la subida
                } else {
                    console.error("Error al subir imagen:", data.error.message);
                    showModificableAlert(
                        "Error",
                        `Error: ${JSON.stringify(data.error.message)}`,
                        "error"
                    );
                }
            } catch (error) {
                console.error("Error al conectar con Cloudinary:", error);
            }
            return null;
        };

        const result = await uploadToCloudinary(imagenUpload);

        console.log(result);

        if (result !== null) {
            setFormData((prev) => ({
                ...prev,
                imagen: result.secure_url, // Guardamos la info de la imagen subida
            }));
            setIsReady(true);
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        handleUpload();
    };

    // Enviar los datos solo cuando form haya actualizado los datos
    useEffect(() => {
        if (isReady) {
            router.post(route("pedidos.store"), formData, {
                onSuccess: () => {
                    showModificableAlert(
                        "Pedido añadido",
                        "Se agregó el pedido correctamente.",
                        "success"
                    );
                    onAdd && onAdd(formData);
                    onClose();
                    router.visit(route("pedidos.index"), {
                        preserveScroll: true,
                    });
                },
                onError: (errors) => {
                    showModificableAlert(
                        "Error al añadir el producto",
                        `Error: ${JSON.stringify(errors)}`,
                        "error"
                    );
                },
            });
            setIsReady(false);
        }
    }, [isReady]);

    return (
        <Dialog
            open={isOpen}
            onClose={onClose}
            className="fixed inset-0 z-50 overflow-y-auto"
        >
            <div className="fixed inset-0 bg-black bg-opacity-25" />
            <div className="bg-white rounded-lg shadow-lg p-6 w-2/3 mx-auto mt-20 mb-10 z-50 relative">
                {/* Botón de cerrar */}
                <button
                    type="button"
                    onClick={onClose}
                    className="absolute top-4 right-4 text-gray-500 hover:text-gray-700"
                    aria-label="Cerrar"
                >
                    <span className="material-icons text-slate-500 text-2xl">
                        close
                    </span>
                </button>

                <Dialog.Title className="text-lg font-semibold text-gray-800 mb-4">
                    Añadir Nuevo Pedido
                </Dialog.Title>

                <form
                    onSubmit={handleSubmit}
                    className="grid grid-cols-1 md:grid-cols-2 gap-4"
                >
                    {/* Código */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700">
                            Código
                        </label>
                        <input
                            type="text"
                            name="codigo"
                            value={formData.codigo}
                            onChange={handleInputChange}
                            className="w-full border rounded-lg py-2 px-4"
                        />
                    </div>

                    {/* Nombre */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700">
                            Producto
                        </label>
                        <input
                            type="text"
                            name="nombre"
                            value={formData.nombre}
                            onChange={handleInputChange}
                            className="w-full border rounded-lg py-2 px-4"
                            placeholder="Nombre del producto"
                        />
                    </div>

                    {/* Descripción */}
                    <div className="md:col-span-2">
                        <label className="block text-sm font-medium text-gray-700">
                            Descripción
                        </label>
                        <textarea
                            name="descripcion"
                            value={formData.descripcion}
                            onChange={handleInputChange}
                            className="w-full border rounded-lg py-2 px-4"
                            rows={3}
                            placeholder="Descripción del producto"
                        />
                    </div>

                    {/* Categoría */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700">
                            Categoría
                        </label>
                        <select
                            name="id_categoria"
                            value={formData.id_categoria}
                            onChange={handleCategoriaChange}
                            className="w-full border rounded-lg py-2 px-4"
                        >
                            <option value="">Seleccionar categoría</option>
                            <option value="nueva">Nueva categoría</option>
                            {categorias.map((categoria) => (
                                <option key={categoria.id} value={categoria.id}>
                                    {categoria.nombre}
                                </option>
                            ))}
                        </select>
                        {mostrarNuevaCategoria && (
                            <input
                                type="text"
                                name="nombre_categoria"
                                placeholder="Nueva categoría"
                                value={formData.nombre_categoria}
                                onChange={handleInputChange}
                                className="mt-2 w-full border rounded-lg py-2 px-4"
                            />
                        )}
                    </div>

                    {/* Almacén */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700">
                            Almacén
                        </label>
                        <select
                            name="id_almacen"
                            value={formData.id_almacen}
                            onChange={handleInputChange}
                            className="w-full border rounded-lg py-2 px-4"
                        >
                            <option value="">Seleccionar almacén</option>
                            {almacenes.map((a) => (
                                <option key={a.id} value={a.id}>
                                    {a.nombre}
                                </option>
                            ))}
                        </select>
                    </div>

                    {/* Precio */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700">
                            Precio unitario
                        </label>
                        <input
                            type="number"
                            name="precio_unitario"
                            step="0.01"
                            value={formData.precio_unitario}
                            onChange={handleInputChange}
                            className="w-full border rounded-lg py-2 px-4"
                        />
                    </div>

                    {/* Cantidad */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700">
                            Cantidad
                        </label>
                        <input
                            type="number"
                            name="cantidad_actual"
                            value={formData.cantidad_actual}
                            onChange={handleInputChange}
                            className="w-full border rounded-lg py-2 px-4"
                        />
                    </div>

                    {/* Perecedero */}
                    <div className="col-span-1 md:col-span-2 flex items-center gap-2">
                        <input
                            type="checkbox"
                            name="perecedero"
                            checked={formData.perecedero}
                            onChange={handleInputChange}
                        />
                        <label className="text-sm text-gray-700">
                            ¿Producto perecedero?
                        </label>
                    </div>

                    {/* Fecha de caducidad */}
                    {formData.perecedero && (
                        <div>
                            <label className="block text-sm font-medium text-gray-700">
                                Fecha de caducidad
                            </label>
                            <input
                                type="date"
                                name="fecha_vencimiento"
                                value={formData.fecha_vencimiento}
                                onChange={handleInputChange}
                                className="w-full border rounded-lg py-2 px-4"
                            />
                        </div>
                    )}

                    {/* Proveedor + Imagen en la misma fila */}
                    <div className="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                        {/* Proveedor */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700">
                                Proveedor
                            </label>
                            <select
                                name="id_proveedor"
                                value={formData.id_proveedor}
                                onChange={handleProveedorChange}
                                className="w-full border rounded-lg py-2 px-4"
                            >
                                <option value="">Seleccionar proveedor</option>
                                <option value="nuevo">Nuevo proveedor</option>
                                {proveedores.map((p) => (
                                    <option key={p.id} value={p.id}>
                                        {p.nombre}
                                    </option>
                                ))}
                            </select>
                        </div>

                        {/* Imagen */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700">
                                Imagen (subir archivo)
                            </label>
                            <input
                                type="file"
                                name="imagen"
                                accept="image/*"
                                ref={inputFileRef}
                                onChange={(e) => mostrarFotoSeleccionada(e)}
                                className="w-full border rounded-lg py-2 px-4"
                            />
                        </div>
                    </div>

                    {/* Datos de nuevo proveedor (si se muestra) */}
                    {mostrarNuevoProveedor && (
                        <div className="md:col-span-2 space-y-2 mt-2">
                            <input
                                type="text"
                                name="nombre_proveedor"
                                placeholder="Nombre del proveedor"
                                value={formData.nombre_proveedor}
                                onChange={handleInputChange}
                                className="w-full border rounded-lg py-2 px-4"
                            />
                            <input
                                type="tel"
                                name="telefono"
                                placeholder="Teléfono"
                                value={formData.telefono}
                                onChange={handleInputChange}
                                className="w-full border rounded-lg py-2 px-4"
                            />
                            <input
                                type="email"
                                name="email"
                                placeholder="Correo electrónico"
                                value={formData.email}
                                onChange={handleInputChange}
                                className="w-full border rounded-lg py-2 px-4"
                            />
                        </div>
                    )}

                    {/* Vista previa de imagen si existe */}
                    {mostrarFoto && mostrarFoto.url && (
                        <div className="md:col-span-2 mt-4">
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                Vista previa
                            </label>
                            <div className="relative inline-block bg-white border-black border b-2 rounded-md shadow-md p-2 mt-6">
                                <img
                                    src={mostrarFoto.url}
                                    alt={mostrarFoto.nombre}
                                    className="h-52 w-auto object-cover rounded-md"
                                />
                                <button
                                    type="button"
                                    onClick={handleDeletePhoto}
                                    className="absolute -top-4 -right-4 text-red-500  hover:text-red-600 rounded-full  flex items-center justify-center "
                                >
                                    <span className="material-icons text-4xl">
                                        delete
                                    </span>
                                </button>
                            </div>
                        </div>
                    )}

                    {/* Botón */}
                    <div className="md:col-span-2 text-right mt-4">
                        <button
                            type="submit"
                            className="bg-slate-600 hover:bg-slate-700 text-white font-semibold rounded-lg py-2 px-4"
                        >
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </Dialog>
    );
}
