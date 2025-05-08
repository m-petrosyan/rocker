import EventIcon from '@/Components/Icons/EventIcon.vue';
import GalleryIcon from '@/Components/Icons/GalleryIcon.vue';
import BotIcon from '@/Components/Icons/BotIcon.vue';

export default [
    { name: 'Events', url: 'events.index', icon: EventIcon },
    { name: 'Bands', url: 'bands.index', icon: EventIcon },
    { name: 'Home', url: 'home', img: '/images/logo.png' },
    { name: 'Galleries', url: 'galleries.index', icon: GalleryIcon },
    // { name: 'Community', url: 'community.index', icon: EventIcon },
    { name: 'Bot', url: 'https://t.me/RockMetalEventsbot', icon: BotIcon, external: true }
];
