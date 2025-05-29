// resources/js/Pages/Pedidos/Index.jsx
import Sidebar from "@/Components/Sidebar";
import Header from "@/Components/Header";
import { usePage } from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import Footer from "@/Components/Footer";
import VentasTable from "@/Components/SeccionesPrincipales/VentasTable";

export default function Index() {
    const { props } = usePage();
    console.log(props);

    return (
        <div className="flex flex-col w-full">
            <div className="flex w-full bg-slate-100">
                <div className="sticky top-0 left-0 h-screen">
                    <Sidebar active={"ventas"}/>
                </div>
                <AuthenticatedLayout>
                    <div className=" overflow-y-auto bg-slate-100  min-h-screen">
                        <VentasTable props={props} />
                    </div>
                    <Footer/>
                </AuthenticatedLayout>
            </div>
        </div>
    );
}
