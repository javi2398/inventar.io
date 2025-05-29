export default function EditClienteModal({ entity, onClose }) {
  return (
    <div className="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
      <div className="bg-white p-6 rounded-xl shadow-lg max-w-md w-full">
        <h2 className="text-xl font-bold mb-4">Editar Comprador</h2>
        <form className="space-y-4">
          <input type="text" defaultValue={entity.nombre} className="w-full border rounded p-2" placeholder="Nombre" />
          <input type="text" defaultValue={entity.identificacion} className="w-full border rounded p-2" placeholder="Identificación" />
          <input type="text" defaultValue={entity.telefono} className="w-full border rounded p-2" placeholder="Teléfono" />
          <input type="email" defaultValue={entity.email} className="w-full border rounded p-2" placeholder="Email" />
          <input type="text" defaultValue={entity.direccion} className="w-full border rounded p-2" placeholder="Dirección" />
          <input type="text" defaultValue={entity.tipo_comprador} className="w-full border rounded p-2" placeholder="Tipo de comprador" />
          <div className="flex justify-end gap-2">
            <button type="button" onClick={onClose} className="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
            <button type="submit" className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  );
}
