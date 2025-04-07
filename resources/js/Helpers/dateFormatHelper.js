import moment from 'moment-timezone';

export const formatDateTime = (dateTime, outputFormat = 'DD/MM/YYYY HH:mm', timezone = moment.tz.guess()) => {
    return moment.utc(dateTime).tz(timezone).format(outputFormat);
};

export const formatUtcDateTime = (dateTime, outputFormat = 'DD/MM/YYYY HH:mm') => {
    return moment.utc(dateTime).format(outputFormat);
};

export const date_time_utc = (date, time) => {
    const momentTime = moment.tz(`${date} ${time}`, 'DD.MM.YY HH:mm', moment.tz.guess());
    const utcTime = momentTime.clone().utc().format('YYYYMMDDTHHmmss') + 'Z';
    const endHourUtc = momentTime.clone().add(3, 'hour').utc().format('YYYYMMDDTHHmmss') + 'Z';
    return { utcTime, endHourUtc };
};


export const toIso8601WithEnd = (date, time) => {
    const guessedTimeZone = moment.tz.guess(); // Определяем локальную временную зону
    const momentStart = moment.tz(`${date} ${time}`, 'DD.MM.YY HH:mm', guessedTimeZone);
    const isoStart = momentStart.format();
    const isoEnd = momentStart.clone().add(3, 'hours').format();

    return { start: isoStart, end: isoEnd };
};
