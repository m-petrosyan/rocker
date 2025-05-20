export const removePostalCode = (address) => {
    return address.replace(/\s+\d{4}$/, '');
};
