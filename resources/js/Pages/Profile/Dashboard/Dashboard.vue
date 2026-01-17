<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import NavLink from '@/Components/NavLink.vue';
import BotIcon from '@/Components/Icons/BotIcon.vue';
import WebSiteIcon from '@/Components/Icons/WebSiteIcon.vue';
import Pagination from '@/Components/Elemtns/Pagination.vue';
import { formatDateTime } from '@/Helpers/dateFormatHelper.js';
import BandIcon from '@/Components/Icons/BandIcon.vue';
import EventIcon from '@/Components/Icons/EventIcon.vue';
import GalleryIcon from '@/Components/Icons/GalleryIcon.vue';
import { onMounted, ref } from 'vue';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const props = defineProps({
  users: {
    type: Object
  },
  statistics: {
    type: Object
  }
});

const usersChartCanvas = ref(null);
const eventsChartCanvas = ref(null);

onMounted(() => {
  if (props.statistics.charts) {
    new Chart(usersChartCanvas.value, {
      type: 'line',
      data: {
        labels: props.statistics.charts.users.labels,
        datasets: [{
          label: 'New Users',
          data: props.statistics.charts.users.data,
          borderColor: '#FF5722',
          backgroundColor: 'rgba(255, 87, 34, 0.2)',
          fill: true,
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              color: '#fff'
            }
          },
          x: {
            ticks: {
              color: '#fff'
            }
          }
        }
      }
    });

    new Chart(eventsChartCanvas.value, {
      type: 'bar',
      data: {
        labels: props.statistics.charts.events.labels,
        datasets: [{
          label: 'Added Events',
          data: props.statistics.charts.events.data,
          backgroundColor: '#4CAF50',
          borderRadius: 4
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              color: '#fff'
            }
          },
          x: {
            ticks: {
              color: '#fff'
            }
          }
        }
      }
    });
  }
});
</script>

<template>
  <ProfileLayout>
    <h2 class="text-center">Statistic</h2>
    <div class="mt-10">
      <div class="flex flex-wrap lg:flex-nowrap gap-4 items-center justify-center">
        <div class="text-center bg-black p-6 rounded-lg">
          <p>Users</p>
          <div class="flex flex-wrap gap-x-4">
            <div tooltip="Website users" class="flex items-center gap-x-2">
              <WebSiteIcon />
              <h2>{{ statistics.users_web }}</h2>
            </div>

            <div tooltip="Bot users" class="flex items-center gap-x-2">
              <BotIcon />
              <h2>{{ statistics.users_bot }}</h2>
            </div>
          </div>
        </div>
        <div class="text-center bg-black p-6 rounded-lg">
          <p>Events</p>
          <div class="flex">
            <h2 tooltip="all" class="opacity-30">{{ statistics.events }}</h2>
            <h2>/</h2>
            <h2 tooltip="active">{{ statistics.events_active }}</h2>
          </div>
        </div>
        <div class="text-center bg-black p-6 rounded-lg">
          <p>Bands</p>
          <h2>{{ statistics.bands }}</h2>
        </div>
        <div class="text-center bg-black p-6 rounded-lg">
          <p>Galleries</p>
          <h2>{{ statistics.galleries }}</h2>
        </div>
        <div class="text-center bg-black p-6 rounded-lg">
          <p>Blogs</p>
          <h2>{{ statistics.blogs }}</h2>
        </div>
        <div class="text-center bg-black p-6 rounded-lg">
          <p>PWA install</p>
          <h2>{{ statistics.pwa }}</h2>
        </div>
      </div>

      <div class="mt-10 grid lg:grid-cols-2 gap-8">
        <div class="bg-black p-6 rounded-lg shadow-lg">
          <h3 class="text-center mb-4">Registration Activity</h3>
          <canvas ref="usersChartCanvas"></canvas>
        </div>
        <div class="bg-black p-6 rounded-lg shadow-lg">
          <h3 class="text-center mb-4">Event Activity</h3>
          <canvas ref="eventsChartCanvas"></canvas>
        </div>
      </div>
    </div>
    <h2 class="mt-10 text-center">Users</h2>
    <div
      class="mt-10 grid md:grid-cols-2 gap-x-10 gap-y-20 lg:grid-cols-4 ">
      <NavLink :href="route('profile.show', user.username)" v-for="user in users.data"
               class="text-center flex flex-col items-center gap-4" href="">
        <div :style="{ backgroundImage: `url(${user.image?.thumb ?? '/images/user.jpg'})` }"
             class="relative h-48 w-48 bg-no-repeat bg-contain bg-cover rounded-full overflow-hidden">
          <div class="absolute w-full bottom-0 p-2 flex gap-4 justify-center items-center bg-blackTransparent">
            <BotIcon v-if="user.chat_count" />
            <WebSiteIcon v-if="user.email" />
          </div>
        </div>
        <div>
          <div v-if="user.is_blocked" class="bg-red text-white">Blocked</div>
          <div>
            <p>{{ user.name }}</p>
            <small
              tooltip="last acivity">{{ user.last_activity ? formatDateTime(user.last_activity, 'DD/MM/YY HH:mm') : 'No activity yet'
              }}</small>
          </div>
          <div class="mt-2 flex gap-4">
            <div class="flex flex-col">
              <b>{{ user.events_count }}</b>
              <EventIcon />
            </div>
            <div class="flex flex-col">
              <b>{{ user.bands_count }}</b>
              <BandIcon />
            </div>
            <div class="flex flex-col ">
              <b>{{ user.galleries_count }}</b>
              <GalleryIcon />
            </div>
            <!--            <div class="flex flex-col">-->
            <!--              <b>{{ user.blogs_count }}</b>-->
            <!--              <p>Blog</p>-->
            <!--            </div>-->
          </div>
        </div>
      </NavLink>
    </div>
    <Pagination :links="users?.links" />
  </ProfileLayout>
</template>
