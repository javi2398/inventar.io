import { useState, useRef, useEffect } from "react";
import { router } from "@inertiajs/react";
import { showModificableAlert } from "@/utils/alerts";

export default function ConcactoProveedores({ proveedores }) {
    const [selectedProveedor, setSelectedProveedor] = useState(
        proveedores[0] ?? null
    );
    const [mensaje, setMensaje] = useState("");
    const textareaRef = useRef(null);

    useEffect(() => {
        if (textareaRef.current) {
            textareaRef.current.style.height = "auto";
            textareaRef.current.style.height = `${textareaRef.current.scrollHeight}px`;
        }
    }, [mensaje]);

    const enviarMail = () => {
        router.post(
            route("proveedor.email"),
            {
                to: selectedProveedor.email,
                subject: `Mensaje para ${selectedProveedor.nombre}`,
                message: mensaje,
            },
            {
                onSuccess: () => {
                    showModificableAlert(
                        "Mail enviado",
                        `Mail enviado correctamente a: ${selectedProveedor.nombre}`,
                        "success"
                    );
                },
                onError: (errors) => {
                    showModificableAlert(
                        "Error al enviar el mail",
                        `Error: ${JSON.stringify(errors)}`,
                        "error"
                    );
                },
            }
        );
        setMensaje("");
    };

    return (
        <div className="grid grid-cols-1 md:grid-cols-5 gap-6 px-6 py-8 min-h-screen">
            {/* Columna izquierda (1/3): Lista + Detalles */}
            <div className="flex flex-col gap-6 col-span-2">
                {/* Lista */}
                <div className="bg-white border h-[40vh] border-slate-200 rounded-xl shadow-md p-4">
                    <div className="w-full h-full overflow-y-auto">
                    <h2 className="text-lg font-semibold text-slate-700 mb-4">
                        Proveedores
                    </h2>
                    <ul className="divide-y divide-slate-100">
                        {proveedores.map((prov) => (
                            <li
                                key={prov.id}
                                onClick={() => setSelectedProveedor(prov)}
                                className={`p-2 cursor-pointer rounded-md text-sm ${
                                    selectedProveedor?.id === prov.id
                                        ? "bg-slate-100 text-slate-800 font-medium"
                                        : "text-slate-600 hover:bg-slate-50"
                                }`}
                            >
                                {prov.nombre}
                            </li>
                        ))}
                    </ul>
                    </div>
                </div>

                {/* Detalles del proveedor */}
                <div className="bg-white h-[30vh] border border-slate-200 rounded-xl shadow-md p-4 overflow-y-auto">
                    <h2 className="text-lg font-semibold text-slate-700 mb-4">
                        Detalles
                    </h2>


                    {selectedProveedor ? (
                        <div className="space-y-2 text-sm text-slate-700">
                            <div>
                                <p className="text-slate-300 font-medium">
                                    Nombre
                                </p>
                                <p>{selectedProveedor.nombre}</p>
                            </div>
                            <hr />
                            <div>
                                <p className="text-slate-300 font-medium">
                                    Teléfono
                                </p>
                                <p>{selectedProveedor.telefono}</p>
                            </div>
                            <hr />
                            <div>
                                <p className="text-slate-300 font-medium">
                                    Email
                                </p>
                                <p>{selectedProveedor.email}</p>
                            </div>
                        </div>
                    ) : (
                        <p className="text-sm text-slate-500">
                            Selecciona un proveedor para ver los detalles.
                        </p>
                    )}
                </div>
            </div>

            {/* Columna derecha (2/3): Formulario de contacto */}

            <div className="bg-white border border-slate-200 rounded-xl shadow-md p-6 col-span-3 h-[73vh] grid grid-rows-[auto_1fr_auto] gap-4">
                <div className="text-center">
                    <span className="material-icons text-6xl text-slate-300 mb-2 block">
                        badge
                    </span>
                    <h2 className="text-xl font-semibold text-slate-700">
                        Contacto
                    </h2>
                </div>

                {selectedProveedor ? (
                    <>
                        {/* Área de mensaje ocupa todo el espacio disponible */}
                        <div className="flex flex-col">
                            <p className="text-sm text-slate-600 mb-3">
                                Escribe un mensaje a{" "}
                                <span className="font-medium text-slate-800">
                                    {selectedProveedor.nombre}
                                </span>
                                :
                            </p>
                            <textarea
                                className="flex-1 min-h-[200px] max-h-full border border-slate-300 rounded-lg p-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-400 resize-none overflow-auto"
                                placeholder="Escribe tu mensaje..."
                                value={mensaje}
                                onChange={(e) => setMensaje(e.target.value)}
                            />
                        </div>

                        {/* Botón fijo al final */}
                        <div className="flex justify-end">
                            <button
                                onClick={enviarMail}
                                disabled={!mensaje}
                                className="flex items-center gap-2 bg-slate-600 hover:bg-slate-700 text-white text-sm py-2 px-4 rounded-md transition disabled:opacity-50"
                            >
                                <span className="material-icons text-base">
                                    send
                                </span>
                                <span>Enviar</span>
                            </button>
                        </div>
                    </>
                ) : (
                    <p className="text-sm text-slate-500 row-span-2 self-center text-center">
                        Selecciona un proveedor para contactar.
                    </p>
                )}
            </div>
        </div>
    );
}
