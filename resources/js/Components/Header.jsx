export default function Header() {
  return (
    <div className="bg-green-100 text-slate-300 p-3 h-[8vh] w-full flex items-center shadow-md z-50 align-middle justify-between overflow-hidden">
      <img src="../public/images/logo.png" className="h-[18vh]" alt="imagen logo" />
      <span className="text-gray-600 font-bold">Usuario</span>
    </div>
  );
}
