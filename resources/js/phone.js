export const DEFAULT_PHONE_COUNTRY = 'GE';

/**
 * trunk — префикс междугородного набора, который страна не включает в E.164.
 * placeholder — пример местного номера уже без trunk-префикса.
 */
export const PHONE_COUNTRIES = [
    { iso: 'GE', name: 'Грузия', dial: '995', trunk: '', placeholder: '555 12 34 56' },
    { iso: 'UA', name: 'Украина', dial: '380', trunk: '0', placeholder: '50 123 4567' },
    { iso: 'RU', name: 'Россия', dial: '7', trunk: '8', placeholder: '912 345 67 89' },
    { iso: 'KZ', name: 'Казахстан', dial: '7', trunk: '8', placeholder: '771 000 9998' },
    { iso: 'BY', name: 'Беларусь', dial: '375', trunk: '80', placeholder: '29 491 19 11' },
    { iso: 'AM', name: 'Армения', dial: '374', trunk: '0', placeholder: '77 123456' },
    { iso: 'AZ', name: 'Азербайджан', dial: '994', trunk: '0', placeholder: '40 123 45 67' },
    { iso: 'TR', name: 'Турция', dial: '90', trunk: '0', placeholder: '501 234 56 78' },
    { iso: 'IL', name: 'Израиль', dial: '972', trunk: '0', placeholder: '50 234 5678' },
    { iso: 'AE', name: 'ОАЭ', dial: '971', trunk: '0', placeholder: '50 123 4567' },
    { iso: 'US', name: 'США', dial: '1', trunk: '', placeholder: '201 555 0123' },
    { iso: 'CA', name: 'Канада', dial: '1', trunk: '', placeholder: '506 234 5678' },
    { iso: 'GB', name: 'Великобритания', dial: '44', trunk: '0', placeholder: '7400 123456' },
    { iso: 'DE', name: 'Германия', dial: '49', trunk: '0', placeholder: '1512 3456789' },
    { iso: 'FR', name: 'Франция', dial: '33', trunk: '0', placeholder: '6 12 34 56 78' },
    { iso: 'IT', name: 'Италия', dial: '39', trunk: '', placeholder: '312 345 6789' },
    { iso: 'ES', name: 'Испания', dial: '34', trunk: '', placeholder: '612 34 56 78' },
    { iso: 'PT', name: 'Португалия', dial: '351', trunk: '', placeholder: '912 345 678' },
    { iso: 'PL', name: 'Польша', dial: '48', trunk: '', placeholder: '512 345 678' },
    { iso: 'NL', name: 'Нидерланды', dial: '31', trunk: '0', placeholder: '6 12345678' },
    { iso: 'GR', name: 'Греция', dial: '30', trunk: '', placeholder: '691 234 5678' },
    { iso: 'CZ', name: 'Чехия', dial: '420', trunk: '', placeholder: '601 123 456' },
    { iso: 'AT', name: 'Австрия', dial: '43', trunk: '0', placeholder: '664 123456' },
    { iso: 'CH', name: 'Швейцария', dial: '41', trunk: '0', placeholder: '78 123 45 67' },
    { iso: 'RO', name: 'Румыния', dial: '40', trunk: '0', placeholder: '712 034 567' },
    { iso: 'BG', name: 'Болгария', dial: '359', trunk: '0', placeholder: '43 012 345' },
    { iso: 'RS', name: 'Сербия', dial: '381', trunk: '0', placeholder: '60 1234567' },
    { iso: 'SE', name: 'Швеция', dial: '46', trunk: '0', placeholder: '70 123 45 67' },
    { iso: 'NO', name: 'Норвегия', dial: '47', trunk: '', placeholder: '40 61 23 45' },
    { iso: 'FI', name: 'Финляндия', dial: '358', trunk: '0', placeholder: '41 2345678' },
    { iso: 'DK', name: 'Дания', dial: '45', trunk: '', placeholder: '34 41 23 45' },
    { iso: 'LT', name: 'Литва', dial: '370', trunk: '0', placeholder: '612 34567' },
    { iso: 'LV', name: 'Латвия', dial: '371', trunk: '', placeholder: '21 234 567' },
    { iso: 'EE', name: 'Эстония', dial: '372', trunk: '', placeholder: '5123 4567' },
    { iso: 'MD', name: 'Молдова', dial: '373', trunk: '0', placeholder: '621 12 345' },
    { iso: 'UZ', name: 'Узбекистан', dial: '998', trunk: '', placeholder: '91 234 56 78' },
    { iso: 'KG', name: 'Киргизия', dial: '996', trunk: '0', placeholder: '700 123 456' },
    { iso: 'IN', name: 'Индия', dial: '91', trunk: '0', placeholder: '81234 56789' },
    { iso: 'CN', name: 'Китай', dial: '86', trunk: '', placeholder: '131 2345 6789' },
];

export function countryFlag(iso) {
    return String(iso ?? '')
        .toUpperCase()
        .replace(/[^A-Z]/g, '')
        .replace(/./g, (letter) => String.fromCodePoint(0x1f1a5 + letter.charCodeAt(0)));
}

export function findPhoneCountry(iso) {
    const code = String(iso ?? '').toUpperCase();

    return PHONE_COUNTRIES.find((country) => country.iso === code) ?? null;
}

export function normalizePhone(phone) {
    const raw = String(phone ?? '').trim();
    const digits = raw.replace(/\D+/g, '');

    if (!digits) {
        return '';
    }

    if (raw.startsWith('+')) {
        return `+${digits}`;
    }

    if (raw.startsWith('00')) {
        return `+${digits.slice(2)}`;
    }

    return '';
}

export function composePhone(iso, local) {
    const country = findPhoneCountry(iso) ?? findPhoneCountry(DEFAULT_PHONE_COUNTRY);
    const raw = String(local ?? '').trim();

    if (raw.startsWith('+') || raw.startsWith('00')) {
        return normalizePhone(raw);
    }

    let digits = raw.replace(/\D+/g, '');

    if (country.trunk && digits.startsWith(country.trunk)) {
        digits = digits.slice(country.trunk.length);
    }

    return digits ? `+${country.dial}${digits}` : '';
}

export function splitPhone(phone, fallbackIso = DEFAULT_PHONE_COUNTRY) {
    const normalized = normalizePhone(phone);

    if (!normalized) {
        return { iso: fallbackIso, local: '' };
    }

    const digits = normalized.slice(1);
    const country = PHONE_COUNTRIES
        .filter((candidate) => digits.startsWith(candidate.dial))
        .sort((a, b) => b.dial.length - a.dial.length)[0];

    return country
        ? { iso: country.iso, local: digits.slice(country.dial.length) }
        : { iso: fallbackIso, local: digits };
}

export function formatPhoneDisplay(phone) {
    const normalized = normalizePhone(phone);

    if (!normalized) {
        return '';
    }

    const { iso, local } = splitPhone(normalized);
    const country = findPhoneCountry(iso);

    return country ? `+${country.dial} ${local}` : normalized;
}
