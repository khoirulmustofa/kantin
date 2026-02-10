
// Format Mata Uang (IDR)
export const formatCurrencyIndo = (value) => {
    if (!value && value !== 0) return 'Rp 0';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
};

// Format Tanggal
export const formatDateIndonesian = (value) => {
    if (!value) return '-';
    return new Date(value).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};