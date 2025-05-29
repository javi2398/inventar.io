// resources/js/Pages/Producto/Index.jsx
import Sidebar from "@/Components/Sidebar";
import Main from "@/Components/SeccionesPrincipales/Main";
import Header from "@/Components/Header";
import { usePage } from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import ProductOverview from "@/Components/SeccionesPrincipales/ProductOverview";
import { useState } from "react";
import Footer from "@/Components/Footer";

export default function Index() {
    const { props } = usePage();
    const [busqueda, setBusqueda] = useState("");
    const [producto, setProducto] = useState(props.producto);

    console.log(props);

    const handleBuscar = (e) => {
        e.preventDefault();
        // Aquí puedes hacer lógica para buscar producto en base a `busqueda`
        console.log("Buscando producto:", busqueda);

    };

    return (
        <div className="flex flex-col w-full">
           <div className="flex w-full bg-slate-100 h-screen overflow-y-auto">
                <div className="sticky top-0 left-0 h-screen">
                    <Sidebar active={"detalleProducto"}/>
                </div>

                <AuthenticatedLayout>
                    <div className="overflow-y-auto flex flex-col items-center w-full mt-8 px-6 gap-6 min-h-screen">
                        {/* Formulario de búsqueda */}
                        <div className="w-full  flex justify-start h-[24px]">
                            <form
                                onSubmit={handleBuscar}
                                className="w-full max-w-sm flex gap-2 items-center h-full"
                            >
                                <input
                                    type="text"
                                    placeholder="Buscar producto..."
                                    className="flex-1 px-3 py-1.5 text-sm rounded border border-slate-300 shadow-sm  focus:border-slate-400 h-full"
                                    value={busqueda}
                                    onChange={(e) =>
                                        setBusqueda(e.target.value)
                                    }
                                />
                                <button
                                    type="submit"
                                    className="px-2 text-sm bg-slate-400 text-white rounded hover:bg-slate-700 transition h-full"
                                >
                                    Buscar
                                </button>
                            </form>
                        </div>

                        {/* Vista del producto */}
                        <ProductOverview
                            producto={producto}
                            productos={props.all_products}
                            searchTerm={busqueda}
                            props={props}/>
                    </div>
                    <Footer/>
                </AuthenticatedLayout>
            </div>
        </div>
    );
}
