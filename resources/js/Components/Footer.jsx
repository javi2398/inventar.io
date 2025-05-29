import logoMedac from "../../images/logoMedac.png";

export default function Footer() {
    return (
        <footer className="bg-white py-4 px-8 text-sm text-gray-600 flex flex-col sm:flex-row items-center justify-between gap-4 ">
            {/* Sección izquierda */}
            <div className="text-center text-md font-bold sm:text-left">
                <p>Sistema de Facturación e Inventario Web</p>
                <p className="text-xs">Todos los derechos reservados</p>
                <p className="text-xs">Proyecto Final DAW - Versión 1.0</p>
            </div>

            {/* Logo al centro con recorte de espacio vacío */}
            <div className="h-12 w-28 overflow-hidden flex justify-center items-center mr-">
                <img
                    src={logoMedac}
                    alt="Logo Medac"
                    className="h-22 object-cover"
                />
            </div>

            {/* Sección derecha */}
            <div className="text-center sm:text-right">
                <div className="mt-1 text-xs">
                    <p className="font-bold text-xs">Autores</p>
                    <p>Javier Vigara Valentín</p>
                    <p>Jose Miguel Hernández</p>
                    <p>Julia Alcalde</p>
                    <p>Carlos </p>
                </div>
            </div>
        </footer>
    );
}
