import EventIcon from '@/Components/Icons/EventIcon.vue';
// import BandIcon from '@/Components/Icons/BandIcon.vue';
// import BlogIcon from '@/Components/Icons/BlogIcon.vue';
import GalleryIcon from '@/Components/Icons/GalleryIcon.vue';

export default [
    { name: 'Events', url: route('events.index'), icon: EventIcon },
    { name: 'Home', href: route('events.index'), img: '/images/logo.png' },
    { name: 'Galleries', url: route('gallery.index'), icon: GalleryIcon }
    // { name: 'Blog', url: route('events.index') , icon: BlogIcon},
    // { name: 'Bands', url: route('events.index'),icon:  BandIcon}
];
