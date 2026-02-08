<script setup>
import { onMounted, ref } from 'vue';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

const props = defineProps({
  title: String,
  labels: Array,
  data: Array,
  datasets: Array,
  type: {
    type: String,
    default: 'line'
  },
  color: {
    type: String,
    default: '#FF5722'
  }
});

const chartCanvas = ref(null);

onMounted(() => {
  const chartDatasets = props.datasets || [{
    label: props.title,
    data: props.data,
    borderColor: props.color,
    backgroundColor: props.type === 'line' ? `${props.color}33` : props.color,
    fill: props.type === 'line',
    tension: 0.4,
    borderRadius: props.type === 'bar' ? 4 : 0
  }];

  new Chart(chartCanvas.value, {
    type: props.type,
    data: {
      labels: props.labels,
      datasets: chartDatasets
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: !!props.datasets,
          labels: {
             color: '#fff'
          }
        },
        tooltip: {
          backgroundColor: '#1a1a1a',
          titleColor: '#fff',
          bodyColor: '#fff',
          borderColor: props.color,
          borderWidth: 1
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: {
            color: 'rgba(255, 255, 255, 0.1)'
          },
          ticks: {
            color: '#fff'
          }
        },
        x: {
          grid: {
            display: false
          },
          ticks: {
            color: '#fff'
          }
        }
      }
    }
  });
});
</script>

<template>
  <div class="bg-black p-6 rounded-lg shadow-lg">
    <h3 class="text-center mb-4 text-white font-semibold">{{ title }}</h3>
    <div class="h-64">
      <canvas ref="chartCanvas"></canvas>
    </div>
  </div>
</template>
