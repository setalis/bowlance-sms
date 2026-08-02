export function normalizePhone(phone) {
    const raw = String(phone ?? '').trim();

    if (!raw) {
        return '';
    }

    const digits = raw.replace(/\D+/g, '');

    if (!digits) {
        return '';
    }

    if (digits.startsWith('995') && digits.length >= 12) {
        return `+${digits}`;
    }

    if (digits.startsWith('0') && digits.length === 10) {
        return `+995${digits.slice(1)}`;
    }

    if (digits.length === 9) {
        return `+995${digits}`;
    }

    if (raw.startsWith('+')) {
        return `+${digits}`;
    }

    return `+${digits}`;
}

export function formatPhoneDisplay(phone) {
    const canonical = normalizePhone(phone);
    const match = canonical.match(/^\+995(\d{9})$/);

    if (!match) {
        return canonical;
    }

    const local = match[1];

    return `+995 ${local.slice(0, 3)} ${local.slice(3, 5)} ${local.slice(5, 7)} ${local.slice(7, 9)}`;
}

export const GEORGIAN_PHONE_MASK = '+995 999 99 99 99';
