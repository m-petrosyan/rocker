export const removePostalCode = (address, maxLength) => {
    let formatted = address.replace(/\s+\d{4}$/, '');
    if (maxLength && formatted.length > maxLength) {
        formatted = formatted.substring(0, maxLength - 3) + '...';
    }
    return formatted;
};
