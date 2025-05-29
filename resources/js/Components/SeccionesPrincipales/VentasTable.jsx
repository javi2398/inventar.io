import { useState } from "react";
import { usePage } from "@inertiajs/inertia-react";

export default function VentasTable({ props }) {
    const [searchTerm, setSearchTerm] = useState("");
    const ventas = props.detalles_ventas;

    const filteredVentas = ventas.filter((venta) =>
        venta.nombre.toLowerCase().includes(searchTerm.toLowerCase())
    );

    return (
        <div className="w-full flex flex-col align-middle justify-start p-12 pt-0npm r pb-34 h-full">
            <div className="bg-white rounded-lg overflow-hidden shadow-lg mt-4 p-10">
            <h2 className="text-xl font-semibold text-gray-700 mb-4">Ventas Realizadas</h2>

            <div className="flex justify-between items-center mb-6">
                <input
                    type="text"
                    placeholder="Buscar producto..."
                    className="border border-gray-300 rounded-lg py-2 px-4 w-64 focus:outline-none focus:ring-2 focus:ring-slate-500"
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                />
            </div>

            <div className="grid grid-cols-7 font-semibold text-gray-700 bg-slate-100 py-2 px-6 rounded-t-md">
                <div className="text-start">ID Producto</div>
                <div className="col-span-2 text-start">Nombre</div>
                <div className="text-center">Precio Unitario</div>
                <div className="text-center">Cantidad</div>
                <div className="text-center">Fecha Venta</div>
                <div className="text-center">Cliente</div>
            </div>

            {filteredVentas.length > 0 ? (
                <div className="divide-y divide-gray-200">
                    {filteredVentas.map((venta, index) => (
                        <div
                            key={index}
                            className="grid grid-cols-7 py-3 px-6 text-sm text-gray-700 hover:bg-gray-50"
                        >
                            <div>{venta.producto_id}</div>
                            <div className="col-span-2">{venta.nombre}</div>
                            <div className="text-center">{venta.precio_unitario}</div>
                            <div className="text-center">{venta.cantidad}</div>
                            <div className="text-center">{venta.fecha_venta}</div>
                            <div className="text-center">{venta.cliente || "N/A"}</div>
                        </div>
                    ))}
                </div>
            ) : (
                <div className="text-center py-8 text-gray-400">No se encontraron ventas</div>
            )}
        </div>
        </div>
    );
}
