import EventIcon from '@/Components/Icons/EventIcon.vue';
import GalleryIcon from '@/Components/Icons/GalleryIcon.vue';

export default [
    { name: 'Events', url: 'events.index', icon: EventIcon },
    { name: 'Galleries', url: 'galleries.index', icon: GalleryIcon },
    { name: 'Home', url: 'home', img: '/images/logo.png' },
    { name: 'Community', url: 'community.index', icon: EventIcon },
    { name: 'Metal Bot', url: 'https://t.me/RockMetalEventsbot', icon: GalleryIcon, external: true }
];
