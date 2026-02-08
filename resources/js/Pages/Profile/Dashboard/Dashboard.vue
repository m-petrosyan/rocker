<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import Pagination from '@/Components/Elemtns/Pagination.vue';
import BotIcon from '@/Components/Icons/BotIcon.vue';
import WebSiteIcon from '@/Components/Icons/WebSiteIcon.vue';
import StatisticsChart from '@/Components/Elements/StatisticsChart.vue';
import UserCountryStats from '@/Components/Elements/UserCountryStats.vue';
import DiskUsage from '@/Components/Elements/DiskUsage.vue';
import DashboardFilterBar from '@/Components/Dashboard/DashboardFilterBar.vue';
import EntityCard from '@/Components/Dashboard/EntityCard.vue';
import { router } from '@inertiajs/vue3';
import { getImageUrl, getTitle, getLink } from '@/Constants/dashboardConstants.js';

const props = defineProps({
  users: {
    type: Object
  },
  statistics: {
    type: Object
  },
  filters: {
    type: Object,
    default: () => ({ type: 'users', filter: null, sort: 'newest', past: false })
  }
});

const applyFilters = (params) => {
  router.get(route('profile.dashboard'), {
    ...props.filters,
    ...params
  }, {
    preserveState: true,
    preserveScroll: true,
    only: ['users', 'filters']
  });
};

const handleTypeUpdate = (type) => applyFilters({ type, filter: null });
const handleSortUpdate = (sort) => applyFilters({ sort });
const handleFilterUpdate = (filter) => {
  applyFilters({
    filter: props.filters.filter === filter ? null : filter
  });
};
const handleTogglePast = () => applyFilters({ past: !props.filters.past });
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
            <UserCountryStats :countries="statistics.countries" />
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
        <div v-if="statistics.blogs" class="text-center bg-black p-6 rounded-lg">
          <p>Blogs</p>
          <h2>{{ statistics.blogs }}</h2>
        </div>
        <div class="text-center bg-black p-6 rounded-lg">
          <p>PWA install</p>
          <h2>{{ statistics.pwa }}</h2>
        </div>
      </div>

      <div class="mt-10 grid lg:grid-cols-2 gap-8" v-if="statistics.charts">
        <StatisticsChart
          title="Active users"
          :labels="statistics.charts.users.labels"
          :datasets="[
            {
              label: 'Active Users',
              data: statistics.charts.users.active,
              borderColor: '#FF5722',
              backgroundColor: '#FF572233',
              fill: true,
              tension: 0.4
            },
            {
              label: 'Registered Users',
              data: statistics.charts.users.registered,
              borderColor: '#2196F3',
              backgroundColor: '#2196F333',
              fill: true,
              tension: 0.4
            }
          ]"
          type="line"
        />
        <StatisticsChart
          title="New events"
          :labels="statistics.charts.events.labels"
          :data="statistics.charts.events.data"
          type="bar"
          color="#4CAF50"
        />
      </div>
    </div>

    <DiskUsage :disk="statistics.disk" />

    <DashboardFilterBar
      :filters="filters"
      @update:type="handleTypeUpdate"
      @update:sort="handleSortUpdate"
      @update:filter="handleFilterUpdate"
      @toggle:past="handleTogglePast"
    />

    <div class="mt-10 grid md:grid-cols-2 gap-x-10 gap-y-20 lg:grid-cols-4">
      <EntityCard
        v-for="item in users.data"
        :key="item.id"
        :item="item"
        :type="filters.type"
        :image-url="getImageUrl(filters.type, item)"
        :title="getTitle(filters.type, item)"
        :link="getLink(filters.type, item, route)"
      />
    </div>

    <div class="mt-10">
      <Pagination :links="users.links" />
    </div>
  </ProfileLayout>
</template>

<style scoped>
.bg-primary {
  background-color: var(--primary-color, #FFD700);
}

.border-primary {
  border-color: var(--primary-color, #FFD700);
}
</style>
