import EventIcon from '@/Components/Icons/EventIcon.vue';
import GalleryIcon from '@/Components/Icons/GalleryIcon.vue';
import BotIcon from '@/Components/Icons/BotIcon.vue';
import BandIcon from '@/Components/Icons/BandIcon.vue';
import Logo from '@/Components/Icons/LogoIcon.vue';

export default [
    { name: 'Events', url: 'events.index', icon: EventIcon, label: 'Events list' },
    { name: 'Bands', url: 'bands.index', icon: BandIcon, label: 'Bands list' },
    { name: 'Home', url: 'home', icon: Logo, label: 'Home page' },
    { name: 'Galleries', url: 'galleries.index', icon: GalleryIcon, label: 'Galleries lis' },
    // { name: 'Community', url: 'community.index', icon: EventIcon },
    {
        name: 'Bot',
        url: 'https://t.me/RockMetalEventsbot',
        icon: BotIcon,
        label: 'Rocker.am Telegram Bot',
        external: true
    }
];
