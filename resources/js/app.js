import './bootstrap';
import 'flyonui/flyonui';
import Alpine from 'alpinejs';
import mask from '@alpinejs/mask';
import { initCart } from './cart';
import {
    composePhone,
    countryFlag,
    DEFAULT_PHONE_COUNTRY,
    findPhoneCountry,
    formatPhoneDisplay,
    normalizePhone,
    PHONE_COUNTRIES,
    splitPhone,
} from './phone';

window.Alpine = Alpine;
window.normalizePhone = normalizePhone;
window.formatPhoneDisplay = formatPhoneDisplay;
window.phoneCountries = PHONE_COUNTRIES;
window.defaultPhoneCountry = DEFAULT_PHONE_COUNTRY;
window.findPhoneCountry = findPhoneCountry;
window.composePhone = composePhone;
window.splitPhone = splitPhone;
window.countryFlag = countryFlag;

Alpine.plugin(mask);

Alpine.data('phoneField', (initialPhone = '', initialCountry = DEFAULT_PHONE_COUNTRY) => ({
    countries: PHONE_COUNTRIES,
    country: initialCountry,
    local: '',

    init() {
        const normalized = normalizePhone(initialPhone);

        if (normalized) {
            const { iso, local } = splitPhone(normalized, initialCountry);
            this.country = iso;
            this.local = local;

            return;
        }

        this.local = String(initialPhone ?? '').replace(/\D+/g, '');
    },

    selected() {
        return findPhoneCountry(this.country) ?? findPhoneCountry(DEFAULT_PHONE_COUNTRY);
    },

    flag(iso) {
        return countryFlag(iso);
    },

    e164() {
        return composePhone(this.country, this.local);
    },

    onLocalInput() {
        const typed = String(this.local ?? '').trim();

        if (! typed.startsWith('+') && ! typed.startsWith('00')) {
            return;
        }

        const { iso, local } = splitPhone(typed, this.country);
        this.country = iso;
        this.local = local;
    },
}));

// Регистрируем глобальный store для корзины
Alpine.store('cart', initCart());

Alpine.start();
