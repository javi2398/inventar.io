import { showModificableAlert } from "@/utils/alerts";
import { useEffect, useRef, useState } from "react";
import { router } from "@inertiajs/react";

export default function EditproductoModal({ producto, onClose, context, almacenes = [], categorias = [], proveedores }) {
	console.log(producto)
	const [formData, setFormData] = useState({
		codigo: producto.codigo,
		nombre: producto.nombre,
		descripcion: producto.descripcion,
    id_producto: producto.id_producto,
		imagen: producto.imagen,
		id_categoria: producto.id_categoria,
		nombre_categoria: producto.categoria ?? producto.nombre_categoria,
    id_almacen: producto.id_almacen,
    id_almacen_antiguo: producto.id_almacen,
		precio_unitario: producto.precio_unitario,
		cantidad_actual: producto.cantidad_actual,
		perecedero: producto.fecha_vencimiento ? true : false,
		fecha_vencimiento: producto.fecha_vencimiento ?? "",
		id_proveedor: producto.proveedores[0].id,
		nombre_proveedor: producto.proveedores[0].nombre,
		id_detalle: producto.id_detalle ? producto.id_detalle : 1 ,
		telefono: producto.proveedores[0].telefono ,
		email: producto.proveedores[0].email,
		status: context === "orders" ? producto.status : undefined,
		almacen: context === "stock" ? producto.almacen_nombre : undefined,
	});
	console.log(producto)
  console.log(almacenes)

	const [mostrarNuevaCategoria, setMostrarNuevaCategoria] = useState(false);
	const [mostrarNuevoProveedor, setMostrarNuevoProveedor] = useState(false);
	const [mostrarFoto, setMostrarFoto] = useState({url: producto.imagen ?? ""});
	const [imagenUpload, setImagenUpload] = useState("");
	const inputFileRef = useRef(null);
	const [isReady, setIsReady] = useState(false);

  // const rutaDelete = context === 'orders' ? 'pedidos.patchInventario' : 'producto.delete';
  const rutaPatch = context === 'orders' ? 'pedidos.patchInventario' : 'producto.editar';
  const rutaRedirect = context === 'orders' ? 'pedidos.index' : 'inventario.index';

	const handleInputChange = ({ target: { name, value, type, checked } }) => {
		const inputValue = type === "checkbox" ? checked : value;
		if (name === "perecedero") {
		setFormData(prev => ({ ...prev, [name]: inputValue, fecha_vencimiento: "" }));
		} else {
		setFormData(prev => ({ ...prev, [name]: inputValue }));
		}

    if (name === "id_almacen") {
		setFormData(prev => ({ ...prev, [name]: Number(inputValue)}));
		}
	};

	const handleCategoriaChange = ({ target: { value } }) => {
		setMostrarNuevaCategoria(value === "nueva");
		setFormData(prev => ({ ...prev, id_categoria: value === "nueva" ? "" : value }));
	};

	const handleProveedorChange = ({ target: { value } }) => {
		setMostrarNuevoProveedor(value === "nuevo");
		setFormData(prev => ({ ...prev, id_proveedor: value === "nuevo" ? "" : value }));
	};


	if (producto.estado) {
		showModificableAlert("Pedido ya recibido", `No se puede editar un producto ya recibido`, "error");
		onClose();
		return;
	}


	const mostrarFotoSeleccionada = (foto) => {
		// Esto muestra las fotos subidas en la pagina
		const urlImagen = foto.target.files[0];
		// setMostrarFoto(URL.createObjectURL(urlImagen))
		setMostrarFoto({
			url: URL.createObjectURL(urlImagen)
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
		router.put(route(rutaPatch), formData, {
			onSuccess: () => {
			showModificableAlert("Pedido actualizado", `El producto ${producto.nombre} se ha actualizado.`, "success");
			onClose();
			router.visit(route(rutaRedirect), { preserveScroll: true });
			},
			onError: (error) =>
			showModificableAlert("Error al actualizar el producto", `Error: ${JSON.stringify(error)}`, "error"),
		});
			setIsReady(false);
		}
	}, [isReady]);

  return (
    <div className="fixed inset-0 bg-slate-800 bg-opacity-30 flex items-center justify-center z-50">
      <div className="bg-white text-black rounded-md shadow-md shadow-slate-400 p-6 w-full max-w-4xl max-h-[85vh] overflow-y-auto">
        <h2 className="text-2xl font-bold mb-4">
            {context === "orders" ? "Editar Pedido" : "Editar Producto"}
        </h2>

        <form onSubmit={handleSubmit} className="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
          <div>
            <label className="block font-medium text-black">Código</label>
            <input
              type="text"
              name="codigo"
              readOnly
              value={formData.codigo}
              onChange={handleInputChange}
              className="w-full border border-slate-300 rounded p-1.5 mt-1 bg-white focus:ring-2 focus:ring-slate-400 focus:outline-none text-black"
            />
          </div>

          <div>
            <label className="block font-medium text-black">Producto</label>
            <input
              type="text"
              name="nombre"
              readOnly
              value={formData.nombre}
              onChange={handleInputChange}
              className="w-full border border-slate-300 rounded p-1.5 mt-1 bg-white focus:ring-2 focus:ring-slate-400 focus:outline-none text-black"
            />
          </div>

          <div className="md:col-span-2">
            <label className="block font-medium text-black">Descripción</label>
            <textarea
              name="descripcion"
              value={formData.descripcion}
              readOnly
              onChange={handleInputChange}
              rows={2}
              className="w-full border border-slate-300 rounded p-1.5 mt-1 bg-white focus:ring-2 focus:ring-slate-400 focus:outline-none text-black"
              placeholder="Descripción del producto"
            />
          </div>

          <div>
            <label className="block font-medium text-black">Categoría</label>
            <select
              name="id_categoria"
              value={formData.id_categoria}
              onChange={handleCategoriaChange}
              className="w-full border border-slate-300 rounded p-1.5 mt-1 bg-white focus:ring-2 focus:ring-slate-400 focus:outline-none text-black"
            >
              <option value="nueva">Nueva categoría</option>
              {categorias.map(c => (
                <option key={c.id} value={c.id}>{c.nombre}</option>
              ))}
            </select>
            {mostrarNuevaCategoria && (
              <input
                type="text"
                name="nombre_categoria"
                placeholder="Nueva categoría"
                value={formData.nombre_categoria}
                onChange={handleInputChange}
                className="w-full border border-slate-300 rounded p-1.5 mt-2 bg-white focus:ring-2 focus:ring-slate-400 focus:outline-none text-black"
              />
            )}
          </div>

          <div>
            <label className="block font-medium text-black">Almacén</label>
            <select
              name="id_almacen"
              value={formData.id_almacen}
              onChange={handleInputChange}
              className="w-full border border-slate-300 rounded p-1.5 mt-1 bg-white focus:ring-2 focus:ring-slate-400 focus:outline-none text-black"
            >
              <option value="">Seleccionar almacén</option>
              {almacenes.map(a => (
                <option key={a.id} value={a.id}>{a.nombre}</option>
              ))}
            </select>
          </div>

          <div>
            <label className="block font-medium text-black">Precio unitario</label>
            <input
              type="number"
              name="precio_unitario"
              step="0.01"
              value={formData.precio_unitario}
              onChange={handleInputChange}
              className="w-full border border-slate-300 rounded p-1.5 mt-1 bg-white focus:ring-2 focus:ring-slate-400 focus:outline-none text-black"
            />
          </div>

          <div>
            <label className="block font-medium text-black">Cantidad</label>
            <input
              type="number"
              name="cantidad_actual"
              value={formData.cantidad_actual}
              onChange={handleInputChange}
              className="w-full border border-slate-300 rounded p-1.5 mt-1 bg-white focus:ring-2 focus:ring-slate-400 focus:outline-none text-black"
            />
          </div>

          <div className="flex items-center gap-2 md:col-span-2">
            <input
              type="checkbox"
              name="perecedero"
              checked={formData.perecedero}
              onChange={handleInputChange}
            />
            <label className="text-sm text-black">¿Producto perecedero?</label>
          </div>

          {formData.perecedero && (
            <div>
              <label className="block font-medium text-black">Fecha de caducidad</label>
              <input
                type="date"
                name="fecha_vencimiento"
                value={formData.fecha_vencimiento}
                onChange={handleInputChange}
                className="w-full border border-slate-300 rounded p-1.5 mt-1 bg-white focus:ring-2 focus:ring-slate-400 focus:outline-none text-black"
              />
            </div>
          )}

          <div>
            <label className="block font-medium text-black">Proveedor</label>
            <select
              name="id_proveedor"
              value={formData.id_proveedor}
              onChange={handleProveedorChange}
              className="w-full border border-slate-300 rounded p-1.5 mt-1 bg-white focus:ring-2 focus:ring-slate-400 focus:outline-none text-black"
            >
              <option value="nuevo">Nuevo proveedor</option>
              {proveedores.map(p => (
                <option key={p.id} value={p.id}>{p.nombre}</option>
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


          {mostrarNuevoProveedor && (
            <div className="md:col-span-2 grid gap-2">
              <input
                type="text"
                name="nombre_proveedor"
                placeholder="Nombre del proveedor"
                value={formData.nombre_proveedor}
                onChange={handleInputChange}
                className="w-full border border-slate-300 rounded p-1.5 bg-white focus:ring-2 focus:ring-slate-400 focus:outline-none text-black"
              />
              <input
                type="tel"
                name="telefono"
                placeholder="Teléfono"
                value={formData.telefono}
                onChange={handleInputChange}
                className="w-full border border-slate-300 rounded p-1.5 bg-white focus:ring-2 focus:ring-slate-400 focus:outline-none text-black"
              />
              <input
                type="email"
                name="email"
                placeholder="Correo electrónico"
                value={formData.email}
                onChange={handleInputChange}
                className="w-full border border-slate-300 rounded p-1.5 bg-white focus:ring-2 focus:ring-slate-400 focus:outline-none text-black"
              />
            </div>
          )}

          {/* Vista previa de imagen si existe */}
          {mostrarFoto && (
              <div className="md:col-span-2 mt-4">
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                      Vista previa
                  </label>
                  <div className="relative inline-block bg-white border-black border b-2 rounded-md shadow-md p-2 mt-6">
                      <img
                          src={mostrarFoto.url}
                          alt={"Imagen del producto"}
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

        </form>

        <div className="mt-4 flex justify-end space-x-3">
          <button
            className="px-4 py-2 rounded-md border border-gray-300 text-gray-600 hover:bg-gray-100 transition"
            onClick={onClose}
          >
            Cancelar
          </button>
          <button
            className="px-4 py-2 rounded-md text-white bg-slate-500 hover:bg-slate-700 transition"
            onClick={handleSubmit}
          >
            Guardar
          </button>
        </div>
      </div>
    </div>
  );
}
