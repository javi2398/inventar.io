import Sidebar from "@/Components/Sidebar";
import { usePage } from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import Footer from "@/Components/Footer";
import InventarioTabla from "@/Components/SeccionesPrincipales/InventarioTabla";

export default function Index() {
    const { props } = usePage();
    console.log(props);

    return (
        <div className="flex flex-col w-full">
           <div className="flex w-full bg-slate-100 h-screen overflow-y-auto">
                <div className="sticky top-0 left-0 h-screen">
                    <Sidebar active={"inventario"}/>
                </div>
                <AuthenticatedLayout>
                    <div className=" overflow-y-auto bg-slate-100 min-h-screen">
                        <InventarioTabla props={props} />
                    </div>
                    <Footer/>
                </AuthenticatedLayout>
            </div>
        </div>
    );
}
