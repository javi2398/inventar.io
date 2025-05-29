import ApplicationLogo from "@/Components/ApplicationLogo";
import { Link } from "@inertiajs/react";

export default function GuestLayout({ children }) {
    return (
        <div className="flex w-full h-screen bg-slate-100 overflow-hidden ">
            {/* izquierda - Logo */}
            <div className="flex w-3/5 justify-center items-center bg-slate-200">
                <Link href="/">
                    <ApplicationLogo className="w-125 h-auto fill-current text-slate-500" />
                </Link>
            </div>

            {/* derecha - Login */}
            <div className="w-2/5 flex items-center justify-center bg-slate-50 p-8 ">
                <div className="w-full max-w-md p-6 bg-white rounded-2xl shadow-md">
                    {children}
                </div>
            </div>
        </div>
    );
}
