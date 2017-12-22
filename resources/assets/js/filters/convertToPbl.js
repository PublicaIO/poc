import CONFIG from 'root/config';

export default (value, rate = CONFIG.conversionRate) => {
    if (value && !isNaN(value)) {
        return parseInt(value) / rate;
    }

    return 0;
}
