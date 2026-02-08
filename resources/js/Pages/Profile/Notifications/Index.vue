<script setup>
import { Link } from '@inertiajs/vue3';
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import moment from 'moment-timezone';

defineProps({
  notifications: Object,
  unreadCount: Number
});

const getStatusBadge = (data) => {
  if (data.status === 'accepted') {
    return { text: 'Approved', class: 'bg-green ' };
  }
  if (data.status === 'rejected') {
    return { text: 'Rejected', class: 'bg-red' };
  }
  if (data.status === 'pending') {
    return { text: 'Pending', class: 'bg-yellow' };
  }
  return null;
};
</script>

<template>
  <ProfileLayout>
    <div class="max-w-4xl mx-auto py-8 px-4">
      <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
        <div class="relative">
          <span class="text-4xl text-white">Notifications</span>
        </div>
      </div>

      <div
        v-if="notifications.data.length === 0"
        class="text-center py-16 px-8 bg-white/5 rounded-2xl border border-white/5"
      >
        <p class="text-xl text-white/60 mb-2">No notifications yet</p>
        <span class="text-sm text-white/40"
        >When you create events, galleries, blogs or bands,
          you'll see updates here.</span>
      </div>

      <div v-else class="flex flex-col gap-3">
        <div
          v-for="notification in notifications.data"
          :key="notification.id"
          class="flex items-start gap-4 p-5 bg-white/5 rounded-xl border border-white/5 transition-all hover:bg-white/10 hover:border-white/10 relative"
          :class="{
            'bg-purple-500/5 border-purple-500/20 before:absolute before:left-0 before:top-1/2 before:-translate-y-1/2 before:w-1 before:h-3/5 before:bg-gradient-to-br before:from-purple-500 before:to-violet-600 before:rounded-r':
              !notification.read_at,
          }"
        >
          <div class="flex-1 min-w-0">
            <Link
              v-if="notification.data.entity_url"
              :href="notification.data.entity_url"
              class="text-white/90 text-base leading-relaxed mb-2 block hover:text-purple-400 transition-colors font-medium cursor-pointer"
            >
              {{ notification.data.message }}
            </Link>
            <p
              v-else
              class="text-white/90 text-base leading-relaxed mb-2"
            >
              {{ notification.data.message }}
            </p>

            <div class="flex items-center gap-3 flex-wrap">
              {{ moment(notification.created_at).fromNow() }}
              <span
                v-if="getStatusBadge(notification.data)"
                class="text-xs px-2 py-0.5 rounded-full font-medium"
                :class="getStatusBadge(notification.data).class"
              >
                {{ getStatusBadge(notification.data).text }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div
        v-if="notifications.last_page > 1"
        class="flex justify-center items-center gap-4 mt-8 pt-8 border-t border-white/5"
      >
        <Link
          v-if="notifications.prev_page_url"
          :href="notifications.prev_page_url"
          class="flex items-center justify-center w-10 h-10 bg-white/5 rounded-lg text-white/60 hover:bg-white/10 hover:text-white transition-colors"
        >
          <span class="material-symbols-outlined">chevron_left</span>
        </Link>
        <span class="text-sm text-white/50">
          Page {{ notifications.current_page }} of
          {{ notifications.last_page }}
        </span>
        <Link
          v-if="notifications.next_page_url"
          :href="notifications.next_page_url"
          class="flex items-center justify-center w-10 h-10 bg-white/5 rounded-lg text-white/60 hover:bg-white/10 hover:text-white transition-colors"
        >
          <span class="material-symbols-outlined">chevron_right</span>
        </Link>
      </div>
    </div>
  </ProfileLayout>
</template>
