import './bootstrap';
import 'flyonui/flyonui';
import Alpine from 'alpinejs';
import mask from '@alpinejs/mask';
import { initCart } from './cart';
import {
    buildDeliveryHint,
    buildDeliveryFeeHint,
    buildDeliveryMethodSummary,
    buildPickupHint,
    buildPickupMethodSummary,
    buildDineInMethodSummary,
    buildSummaryChips,
    calculateDiscountAmount,
    calculateDeliveryFee,
    calculateSubtotalFromItems,
    calculateTotalForType,
    formatDiscountLabel,
    getDiscountConfig,
    getFooterTotalBeforeSelection,
    getDiscountLabels,
    resolveDiscountForType,
    roundMoney,
    formatFooterDeliveryFeeLabel,
    buildFooterDeliveryRule,
    buildFooterDiscountRule,
} from './discounts';
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
window.buildDeliveryFeeHint = buildDeliveryFeeHint;
window.buildSummaryChips = buildSummaryChips;
window.buildDeliveryMethodSummary = buildDeliveryMethodSummary;
window.buildPickupMethodSummary = buildPickupMethodSummary;
window.buildDineInMethodSummary = buildDineInMethodSummary;
window.getFooterTotalBeforeSelection = getFooterTotalBeforeSelection;
window.formatFooterDeliveryFeeLabel = formatFooterDeliveryFeeLabel;
window.buildFooterDeliveryRule = buildFooterDeliveryRule;
window.buildFooterDiscountRule = buildFooterDiscountRule;
window.calculateDeliveryFee = calculateDeliveryFee;
window.calculateSubtotalFromItems = calculateSubtotalFromItems;
window.buildDeliveryHint = buildDeliveryHint;
window.calculateDiscountAmount = calculateDiscountAmount;
window.calculateTotalForType = calculateTotalForType;
window.formatDiscountLabel = formatDiscountLabel;
window.resolveDiscountForType = resolveDiscountForType;
window.roundMoney = roundMoney;
window.getDiscountConfig = getDiscountConfig;
window.getDiscountLabels = getDiscountLabels;

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
