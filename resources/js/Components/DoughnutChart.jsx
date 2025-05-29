// components/DoughnutChart.js
import { Doughnut } from 'react-chartjs-2';
import {
  Chart as ChartJS,
  ArcElement,
  Tooltip,
  Legend
} from 'chart.js';

ChartJS.register(ArcElement, Tooltip, Legend);

export default function DoughnutChart({ inStock, lowStock, outOfStock }) {
  const data = {
    labels: ['In Stock', 'Low Stock', 'Out of Stock'],
    datasets: [
      {
        data: [inStock, lowStock, outOfStock],
        backgroundColor: ['#14b8a6', '#f97316', '#ef4444'], // teal-500, orange-500, red-500
        borderWidth: 0,
      },
    ],
  };

  const options = {
    cutout: '80%',
    radius: '90%',
    plugins: {
        legend: {
          display: false,
        },
        tooltip: {
          enabled: false,

        },
      },
  };

  return (
    <div className="w-32 h-32">
      <Doughnut data={data} options={options} />
    </div>
  );
}
