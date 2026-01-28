export const ENTITY_TYPES = {
    USERS: 'users',
    BANDS: 'bands',
    EVENTS: 'events',
    GALLERIES: 'galleries'
};

export const SORT_OPTIONS = {
    NEWEST: 'newest',
    OLDEST: 'oldest'
};

export const USER_FILTERS = {
    BLOCKED: 'blocked',
    BOT: 'bot',
    WEB: 'web'
};

export const DEFAULT_IMAGES = {
    USER: '/images/user.jpg',
    BAND: '/images/band.jpg',
    EVENT: '/images/event.jpg',
    GALLERY: '/images/gallery.jpg',
    PLACEHOLDER: '/images/placeholder.jpg'
};

export const getImageUrl = (type, item) => {
    const imageMap = {
        [ENTITY_TYPES.USERS]: item.image?.thumb ?? DEFAULT_IMAGES.USER,
        [ENTITY_TYPES.BANDS]: item.logo?.thumb ?? DEFAULT_IMAGES.BAND,
        [ENTITY_TYPES.EVENTS]: item.poster?.thumb ?? DEFAULT_IMAGES.EVENT,
        [ENTITY_TYPES.GALLERIES]: item.cover_img?.thumb ?? DEFAULT_IMAGES.GALLERY
    };
    return imageMap[type] ?? DEFAULT_IMAGES.PLACEHOLDER;
};

export const getTitle = (type, item) => {
    const titleMap = {
        [ENTITY_TYPES.USERS]: item.name,
        [ENTITY_TYPES.BANDS]: item.name,
        [ENTITY_TYPES.EVENTS]: item.title,
        [ENTITY_TYPES.GALLERIES]: item.title || item.description
    };
    return titleMap[type] ?? 'Unknown';
};

export const getLink = (type, item, routeFn) => {
    switch (type) {
        case ENTITY_TYPES.USERS:
            return routeFn('profile.show', item.username);
        case ENTITY_TYPES.BANDS:
            return routeFn('bands.show', item.slug);
        case ENTITY_TYPES.EVENTS:
            return routeFn('events.show', item.id);
        case ENTITY_TYPES.GALLERIES:
            return routeFn('galleries.show', item.id);
        default:
            return '#';
    }
};
