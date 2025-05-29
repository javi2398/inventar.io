"use client";

import React from "react";
import { Bar, Doughnut, Line } from "react-chartjs-2";
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
	Filler
} from "chart.js";
import { router } from "@inertiajs/react";


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



const ProductOverview = ({ producto, productos = [], searchTerm, props}) => {

console.log(producto);

console.log(productos);

console.log(props);

const detalleProducto = (prod) => {
	console.log(prod);
	router.get(route("producto.index", {id: prod.id_producto}), {
		onSuccess: () => {
		},
		onError: (errors) => {
			showModificableAlert(
				"Error al mostar los detalles del producto",
				`Error: ${JSON.stringify(errors)}`,
				"error"
			);
		},
	});
};

if (!producto) {

	return (
		<div className="w-full bg-slate-100 p-6 h-[80vh]">
		<h2 className="text-xl font-bold text-slate-700 mb-2">Ningún Producto Seleccionado</h2>
		<p className="text-sm text-gray-600 mb-4">Primero selecciona un producto para su visualización</p>

		<div className="overflow-x-auto rounded-md shadow">
			<table className="min-w-full bg-white border border-slate-300 text-sm">
			<thead className="bg-slate-200 text-slate-700">
				<tr>
				<th className="px-4 py-2 text-left border-b">ID</th>
				<th className="px-4 py-2 text-left border-b">Nombre del Producto</th>
				</tr>
			</thead>
			<tbody>
				{productos
				.filter((prod) =>
					prod.nombre.toLowerCase().includes(searchTerm.toLowerCase()) // Filtro por nombre
				)
				.map((prod) => (
					<tr
					key={prod.id_producto}
					className="cursor-pointer hover:bg-slate-100 transition"
					onClick={() => detalleProducto(prod)}
					>
					<td className="px-4 py-2 border-b">{prod.id_producto}</td>
					<td className="px-4 py-2 border-b">{prod.nombre}</td>
					</tr>
				))}
			</tbody>
			</table>
		</div>
		</div>


	);
}


  // Si hay producto seleccionado, mostrar vista detallada
  const revenue = 12456;
  const netProfitEstimate = props.beneficio_estimado;
  const stock = props.stock;
  const recommendedReorder = 10;
  const estimatedSold = props.ventas_estimadas_mes;
  const ventastotales = props.total_vendido;

  const ventasSemanas = props.ventas_por_semana
  const stockTendencias = props.stock_tendencias

  const datosBarras = []
  ventasSemanas.forEach(venta => {
    datosBarras.push(venta.total)
  });

  const revenueData = {
    labels: ["Semana 1", "Semana 2", "Semana 3", "Semana 4"],
    datasets: [
      {
        label: "Ingresos mensuales (€)",
        data: datosBarras,
        backgroundColor: "#2c4360"
      }
    ]
  };

  const salesDistributionData = {
    labels: ["Vendido", "En Stock"],
    datasets: [
      {
        data: [ventastotales, stock],
        backgroundColor: ["#2c4360", "#a3bbd6"]
      }
    ]
  };

  const stockTendencia = props.stock_tendencia_chart
  const stockTrendData = {
    labels: stockTendencia.labels,
    datasets: [
      {
        label: "Nivel de Stock",
        data: stockTendencia.data,
        borderColor: "#324d72",
        backgroundColor: "#cedae9",
        fill: true
      }
    ]
  };

  return (
    <div className="flex flex-col lg:flex-row gap-12 w-full mt-4 border border-slate-200 rounded-lg b-2 p-4 ">
      {/* Tarjeta del producto con imagen y detalles */}
      <div className="w-2/3  p-4 flex flex-col items-center justify-around bg-slate-50 rounded-lg">
        <div className="text-center w-full">
          <h1 className="text-xl font-bold">{producto.nombre}</h1>
          <p className="text-gray-600 text-sm mt-1">{producto.descripcion}</p>
        </div>

        <img
          src={producto.imagen}
          alt={producto.nombre}
          className="w-full h-auto object-contain rounded-md mb-4"
        />
        </div>


      {/* Gráficos y estadísticas */}
      <div className="w-full lg:w-3/5 flex flex-col gap-6">
        <div className="flex flex-col md:flex-row gap-4">
          {/* Ingresos */}
          <div className="p-4 bg-white rounded-md shadow w-full md:w-1/2">
            <h2 className="text-sm font-bold mb-2">Ingresos Mensuales</h2>
            <div className="h-40">
              <Bar data={revenueData} options={{ responsive: true, maintainAspectRatio: false }} />
            </div>
          </div>

          {/* Distribución */}
          <div className="p-4 bg-white rounded-md shadow w-full md:w-1/2">
            <h2 className="text-sm font-bold mb-2">Distribución de Ventas</h2>
            <div className="h-40">
              <Doughnut data={salesDistributionData} options={{ responsive: true, maintainAspectRatio: false }} />
            </div>
          </div>
        </div>

        {/* Tendencia del stock */}
        <div className="p-4 bg-white rounded-md shadow">
          <h2 className="text-sm font-bold mb-2">Tendencia del Stock</h2>
          <div className="h-32">
            <Line data={stockTrendData} options={{ responsive: true, maintainAspectRatio: false }} />
          </div>
        </div>

        {/* Estadísticas */}
        <div className="flex flex-col md:flex-row gap-4">
          <div className="bg-white rounded-md shadow p-4 w-full md:w-1/3">
            <h2 className="text-sm font-bold">Beneficio Neto Estimado</h2>
            <p className="text-2xl font-semibold text-emerald-500 mt-1">{netProfitEstimate} €</p>
          </div>
          <div className="bg-white rounded-md shadow p-4 w-full md:w-1/3">
            <h2 className="text-sm font-bold">Stock Disponible</h2>
            <p className="text-2xl font-semibold text-yellow-600 mt-1">{stock} unidades</p>
            <p className="text-xs text-gray-500 mt-1">
              Reorden sugerido: <span className="font-medium">{recommendedReorder} unidades</span>
            </p>
          </div>
          <div className="bg-white rounded-md shadow p-4 w-full md:w-1/3">
            <h2 className="text-sm font-bold">Ventas Estimadas Este Mes</h2>
            <p className="text-2xl font-semibold text-cyan-600 mt-1">{estimatedSold} unidades</p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ProductOverview;
