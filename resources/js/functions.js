export let resetArrayValues = (array) => {
    for (const [key, value] of Object.entries(array)) {
        if (typeof value === 'string') array[key] = '';
        else if (typeof value === 'number') array[key] = 0;
        else if (typeof value === 'boolean') array[key] = false;
        else if (typeof value === 'object') array[key] = [];
    }
}