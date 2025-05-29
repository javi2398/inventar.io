import Sidebar from "@/Components/Sidebar";
import Header from "@/Components/Header";
import { usePage } from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import Footer from "@/Components/Footer";
import CrudEntidades from "@/Components/SeccionesPrincipales/CrudEntidades";

export default function Index() {
    const { props } = usePage();
    console.log(props);

    return (
        <div className="flex flex-col w-full">
          <div className="flex w-full bg-slate-100 h-screen overflow-y-hidden">
                <div className="sticky top-0 left-0 h-screen">
                    <Sidebar active={"Entidades"}/>
                </div>
                <AuthenticatedLayout>
                    <div className=" bg-slate-100  h-[77vh] ">
                        <CrudEntidades props={props}/>
                    </div>
                    <Footer/>
                </AuthenticatedLayout>
            </div>
        </div>
    );
}
