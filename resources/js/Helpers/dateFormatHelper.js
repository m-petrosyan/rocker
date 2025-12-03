import moment from 'moment-timezone';

export const formatDateTime = (dateTime, outputFormat = 'DD/MM/YYYY HH:mm', toLocal = true) => {
  const m = moment.utc(dateTime);
  return toLocal ? m.tz(moment.tz.guess()).format(outputFormat) : m.format(outputFormat);
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

export const isPastDate = (dateTime) => {
  return moment(dateTime).isBefore(moment());
};