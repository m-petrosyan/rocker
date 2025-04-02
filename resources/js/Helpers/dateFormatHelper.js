import moment from 'moment-timezone';

export const formatDateTime = (dateTime, outputFormat = 'DD/MM/YYYY HH:mm', timezone = moment.tz.guess()) => {
    return moment.utc(dateTime).tz(timezone).format(outputFormat)
};

export const formatUtcDateTime = (dateTime, outputFormat = 'DD/MM/YYYY HH:mm') => {
    return moment.utc(dateTime).format(outputFormat)
};

