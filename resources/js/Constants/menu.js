import EventIcon from '@/Components/Icons/EventIcon.vue';
import GalleryIcon from '@/Components/Icons/GalleryIcon.vue';
import BandIcon from '@/Components/Icons/BandIcon.vue';
import Logo from '@/Components/Icons/LogoIcon.vue';

export default [
    { name: 'Events', url: 'events.index', icon: EventIcon },
    { name: 'Bands', url: 'bands.index', icon: BandIcon },
    { name: 'Home', url: 'home', icon: Logo },
    { name: 'Galleries', url: 'galleries.index', icon: GalleryIcon }
    // { name: 'Community', url: 'community.index', icon: EventIcon },
    // { name: 'Bot', url: 'https://t.me/RockMetalEventsbot', icon: BotIcon, external: true }
];
