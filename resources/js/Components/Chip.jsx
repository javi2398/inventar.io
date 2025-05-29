export default function Chip({ status }) {
  const chipStyle = status
    ? "bg-green-100 border-green-300 text-green-600"
    : "bg-orange-100 border-orange-300 text-orange-600";

  return (
    <span
      className={`inline-block px-3 py-1 text-sm font-semibold border rounded-full ${chipStyle}`}
    >
      {status ? "Recibido" : "Pendiente"}
    </span>
  );
}