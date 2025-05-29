export default function Navbar() {
  return (
    <div className="bg-gray-100 border-b flex items-center p-2">
      <div className="flex items-center text-gray-600">
        <a href="#" className="material-icons mx-1 cursor-pointer">arrow_back</a>
        <a href="#" className="material-icons mx-1 cursor-pointer">arrow_forward</a>
        <a href="#" className="material-icons mx-1 cursor-pointer">refresh</a>
        <a href="#" className="mx-1 flex items-center">
          <span className="bg-gray-300 rounded-full w-4 h-4 flex items-center justify-center text-xs mr-1">
            <span  className="material-icons text-xs ">info</span>
          </span>
          192.168.1.15/facturacion/bin/modules/productos/registro_productos.php
        </a>
      </div>
      <div className="ml-auto flex">
        <a href="#" className="material-icons mx-1 cursor-pointer">minimize</a>
        <a href="#" className="material-icons mx-1 cursor-pointer">crop_square</a>
        <a href="#" className="material-icons mx-1 cursor-pointer">close</a>
      </div>
    </div>
  );
}
