// resources/js/Pages/Dashboard/Index.jsx
import Sidebar from "@/Components/Sidebar";
import { usePage } from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import Footer from "@/Components/Footer";
import { useState } from "react";
import { Bar, Doughnut, Line } from "react-chartjs-2";
import Main from "@/Components/SeccionesPrincipales/Main";
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    Filler,
} from "chart.js";

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    Filler
);

export default function Index() {
    const { props } = usePage();
    const [timeRange, setTimeRange] = useState("este-mes");

    return (
        <div className="flex flex-col w-full">
           <div className="flex w-full bg-slate-100 h-screen overflow-y-auto">
                <div className="sticky top-0 left-0 h-screen">
                    <Sidebar active={"dashboard"} />
                </div>
                <AuthenticatedLayout>
                    <div className=" overflow-y-auto bg-slate-100  min-h-screen">
                        <Main props={props} />
                    </div>
                    <Footer />
                </AuthenticatedLayout>
            </div>
        </div>
    );
}
