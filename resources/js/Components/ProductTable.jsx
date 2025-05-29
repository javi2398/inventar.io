import { useState } from "react";
import ProductTableRow from "./ProductTableRow";
import CarruselAlmacenes from "./CarruselAlmacenes";
import AddModal from "./Modales/AddModal";
import DeleteProductModal from "./Modales/DeleteProductModal";
import { usePage } from "@inertiajs/inertia-react";
import { router } from "@inertiajs/react";

export default function ProductTable({props}) {
    console.log(props.data[0].productos);

    const [activeTab, setActiveTab] = useState("ordenes");
    const [products, setProducts] = useState(props.all_productos);
    const [searchTerm, setSearchTerm] = useState("");
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
    const [selectedProduct, setSelectedProduct] = useState(null);
    const [selected, setSelected] = useState([]);
    const [almacenes, setAlmacenes] = useState(props.data);
    const [categorias, setCategorias] = useState(props.categorias);
    const [proveedores, setProveedores] = useState(props.all_proveedores);

    console.log(props);

    const handleAddProduct = (newProduct) => {
        setProducts([...products, newProduct]);
    };

    const handleDeleteProduct = (product) => {
        setSelectedProduct(product);
        setIsDeleteModalOpen(true);
    };

    const limpiarFiltros = () => {
        setSearchTerm('')
        setSelected([])

        router.visit(route('inventario.index'), {
            method: 'get',
            onSuccess: (page) => {
                setSelected([]);  // Limpia selección
                setAlmacenes(page.props.data);  // Actualiza almacenes
            },
            preserveState: true,  // Mantiene la pestaña activa
            preserveScroll: true, // Mantiene el scroll
            only: ['data'],       // Solo actualiza los almacenes
        });

    }

    return (
        <div>
            <div className="flex space-x-2 px-4 pt-4 bg-slate-100">
                <button
                    onClick={() => setActiveTab("ordenes")}
                    className={`px-6 py-2 rounded-t-lg font-semibold transition-all duration-100 ${
                        activeTab === "ordenes"
                            ? "bg-white text-slate-700 shadow-none"
                            : "bg-slate-200 shadow-inner text-slate-600"
                    }`}
                >
                    Órdenes de Compra
                </button>
                <button
                    onClick={() => setActiveTab("stock")}
                    className={`px-6 py-2 rounded-t-lg font-semibold transition-all duration-300 ${
                        activeTab === "stock"
                            ? "bg-white text-slate-700 shadow-none"
                            : "bg-slate-200 shadow-inner text-slate-600"
                    }`}
                >
                    Inventario
                </button>
            </div>

            <div className="bg-white rounded-lg overflow-hidden shadow-lg">
                {activeTab === "ordenes" && (
                    <div className="shadow-slate-300 shadow-md">
                        <div className="flex justify-between items-center mb-4 p-6">
                            <h2 className="text-xl font-semibold text-gray-700">
                                Órdenes de Compra
                            </h2>
                            <button
                                onClick={() => setIsModalOpen(true)}
                                className="bg-slate-500 text-white px-4 py-2 rounded-md font-extrabold hover:bg-slate-600"
                            >
                                Nuevo
                            </button>
                        </div>
                        <div className="flex justify-between items-center mb-4 px-6">
                            <div className="relative">
                                <input
                                    type="text"
                                    placeholder="Buscar"
                                    className="border border-gray-300 rounded-lg py-2 px-4 w-64 focus:outline-none focus:ring-2 focus:ring-slate-500"
                                    value={searchTerm}
                                    onChange={(e) => setSearchTerm(e.target.value)}
                                />
                            </div>
                        </div>
                        <div className="grid grid-cols-8 items-center bg-slate-100 font-semibold text-gray-700 py-2 px-8 gap-2 mb-6 mt-10">
                           {/*  <div className="text-center">Imagen</div> */}
                            <div className="text-start">ID Producto</div>
                            <div className="text-start pl-4 col-span-2">Artículo</div>
                            <div className="text-center">Precio</div>
                            <div className="text-center">Cantidad</div>
                            <div className="text-center">Estado</div>
                            <div className="text-center">Fecha Recepción</div>
                            <div className="text-center">Acciones</div>
                        </div>
                        <div className="grid grid-cols-1 px-4 pb-4">
                            {products
                                .filter((product) =>
                                    product.nombre
                                        .toLowerCase()
                                        .includes(searchTerm.toLowerCase())
                                )
                                .map((product, index) => (
                                    <ProductTableRow
                                        key={index}
                                        product={product}
                                        context="orders"
                                        almacenes={almacenes}
                                        onDelete={() => handleDeleteProduct(product)}
                                    />
                                ))}
                        </div>
                    </div>
                )}

                {activeTab === "stock" && (
                    <div className="p-6 relative flex flex-col gap-6">
                        <h2 className="text-xl font-semibold text-gray-700 mb-4">
                            Inventario
                        </h2>
                        <button
                            onClick={() => setIsModalOpen(true)}
                            className="absolute top-6 right-6 bg-slate-500 text-white px-4 py-2 rounded-md font-semibold hover:bg-slate-600"
                        >
                            Añadir Producto
                        </button>

                        <div className="flex flex-col justify-start items-left gap-2 mb-4">
                            <input
                                type="text"
                                placeholder="Buscar"
                                className="border border-gray-300 rounded-lg py-2 px-4 w-64 focus:outline-none focus:ring-2 focus:ring-slate-500"
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                            />
                            <div>
                                <button className="mr-2 bg-slate-300 text-slate-600 px-4 py-2 rounded-md font-semibold hover:bg-slate-400 hover:text-white">
                                    Buscar
                                </button>
                                <button className="hover:underline text-sm text-red-400"
                                onClick={() => limpiarFiltros()}>
                                    Limpiar Filtros
                                </button>
                            </div>
                        </div>

                        <div className="text-right text-sm text-gray-500 mb-2">
                            <CarruselAlmacenes
                                arrayAlmacenes={almacenes}
                                selected={selected}
                                setSelected={setSelected}
                            />
                        </div>

                        <div className="grid grid-cols-8 items-center bg-slate-100 font-semibold text-gray-700 py-2 px-8 gap-2 mt-8 mb-4">
                            {/* <div className="text-center">Imagen</div> */}
                            <div className="text-start">ID Producto</div>
                            <div className="text-start pl-4 col-span-2">Artículo</div>
                            <div className="text-center">Precio</div>
                            <div className="text-center">Cantidad</div>
                            <div className="text-center">Almacén</div>
                            <div className="text-center">Fecha Recepción</div>
                            <div className="text-center">Acciones</div>
                        </div>

                        <div className="grid grid-cols-1 px-4 pb-4">
                            {products
                                .filter((product) =>
                                    product.nombre
                                        .toLowerCase()
                                        .includes(searchTerm.toLowerCase())
                                )
                                .map((product, index) => (
                                    <ProductTableRow
                                        key={index}
                                        product={product}
                                        context="stock"
                                        almacenes={almacenes}
                                        categorias={categorias}
                                        productos={products}
                                        proveedores={proveedores}
                                        onDelete={() => handleDeleteProduct(product)}
                                    />
                                ))}
                        </div>
                    </div>
                )}
            </div>

            <AddModal
                isOpen={isModalOpen}
                onClose={() => setIsModalOpen(false)}
                onAdd={handleAddProduct}
                context={activeTab === "ordenes" ? "orders" : "stock"}
                almacenes={almacenes}
                categorias={categorias}
                productos={products}
                proveedores={proveedores}
            />

            {isDeleteModalOpen && selectedProduct && (
                <DeleteProductModal
                    product={selectedProduct}
                    totalAmount={selectedProduct.existencias}
                    onClose={() => setIsDeleteModalOpen(false)}
                />
            )}
        </div>
    );
}
