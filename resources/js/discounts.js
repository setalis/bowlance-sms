export function formatDiscountLabel(discount) {
    if (!discount) {
        return '';
    }

    if (discount.type === 'percent') {
        return `−${parseFloat(discount.size)}%`;
    }

    return `−${parseFloat(discount.size).toFixed(2)} ₾`;
}

export function calculateDiscountAmount(discount, subtotal) {
    if (!discount) {
        return 0;
    }

    if (discount.type === 'percent') {
        return subtotal * (parseFloat(discount.size) / 100);
    }

    return Math.min(parseFloat(discount.size), subtotal);
}

export function roundMoney(value) {
    return Math.max(0, Math.round(value * 100) / 100);
}

export function calculateSubtotalFromItems(items = []) {
    return roundMoney(
        items.reduce((sum, item) => sum + parseFloat(item.price) * item.quantity, 0)
    );
}

export function isOnPremise(deliveryType) {
    return deliveryType === 'pickup' || deliveryType === 'dine_in';
}

export function resolveDiscountForType(deliveryType, subtotal, pickupDiscount, cartTotalDiscounts = []) {
    if (isOnPremise(deliveryType)) {
        return pickupDiscount ?? null;
    }

    if (deliveryType !== 'delivery' || !cartTotalDiscounts?.length) {
        return null;
    }

    return [...cartTotalDiscounts]
        .filter((discount) => subtotal >= discount.min_cart_total)
        .sort((a, b) => b.min_cart_total - a.min_cart_total)[0] ?? null;
}

export function calculateTotalForType(deliveryType, subtotal, pickupDiscount, cartTotalDiscounts = []) {
    const discount = resolveDiscountForType(deliveryType, subtotal, pickupDiscount, cartTotalDiscounts);
    const discountAmount = calculateDiscountAmount(discount, subtotal);
    const deliveryFee = calculateDeliveryFee(subtotal, deliveryType);

    return roundMoney(subtotal - discountAmount + deliveryFee);
}

export function getDeliveryConfig() {
    return window.deliveryConfig ?? { fee: 5, freeFrom: 50 };
}

export function calculateDeliveryFee(subtotal, deliveryType = 'delivery') {
    if (deliveryType !== 'delivery') {
        return 0;
    }

    const { fee, freeFrom } = getDeliveryConfig();

    if (subtotal >= freeFrom) {
        return 0;
    }

    return fee;
}

export function buildDeliveryFeeHint(subtotal, labels = {}) {
    const { fee, freeFrom } = getDeliveryConfig();
    const currentFee = calculateDeliveryFee(subtotal, 'delivery');

    if (currentFee === 0) {
        return {
            label: labels.deliveryFeeFree ?? 'Доставка бесплатно',
            fee: 0,
            isFree: true,
        };
    }

    const remaining = roundMoney(freeFrom - subtotal);

    return {
        label: fillTemplate(
            labels.deliveryFeeHint ?? 'Доставка :fee ₾, бесплатно от :freeFrom ₾',
            {
                fee: fee.toFixed(2),
                freeFrom: freeFrom.toFixed(0),
            }
        ),
        fee: currentFee,
        isFree: false,
        remaining,
        addMoreLabel: fillTemplate(
            labels.deliveryFeeAddMore ?? 'Бесплатная доставка от :freeFrom ₾. Добавьте ещё :remaining ₾',
            {
                freeFrom: freeFrom.toFixed(0),
                remaining: remaining.toFixed(2),
            }
        ),
    };
}

function fillTemplate(template, replacements) {
    return Object.entries(replacements).reduce(
        (text, [key, value]) => text.replaceAll(`:${key}`, value),
        template ?? ''
    );
}

function getNextDeliveryTier(subtotal, cartTotalDiscounts = []) {
    if (!cartTotalDiscounts?.length) {
        return null;
    }

    const sorted = [...cartTotalDiscounts].sort((a, b) => a.min_cart_total - b.min_cart_total);
    const applicable = resolveDiscountForType('delivery', subtotal, null, cartTotalDiscounts);

    if (applicable) {
        return applicable;
    }

    return sorted.find((discount) => subtotal < discount.min_cart_total) ?? null;
}

export function buildPickupHint(subtotal, pickupDiscount, labels = {}) {
    if (!pickupDiscount) {
        return null;
    }

    const total = calculateTotalForType('pickup', subtotal, pickupDiscount, []);
    const discountLabel = formatDiscountLabel(pickupDiscount);
    const template = labels.pickupHint ?? 'Скидка :discount при самовывозе → :total ₾';

    return {
        label: fillTemplate(template, {
            discount: discountLabel,
            total: total.toFixed(2),
        }),
        total,
        applied: true,
    };
}

export function buildDeliveryHint(subtotal, cartTotalDiscounts = [], labels = {}) {
    if (!cartTotalDiscounts?.length) {
        return null;
    }

    const applicable = resolveDiscountForType('delivery', subtotal, null, cartTotalDiscounts);
    const tier = applicable ?? getNextDeliveryTier(subtotal, cartTotalDiscounts);

    if (!tier) {
        return null;
    }

    const discountLabel = formatDiscountLabel(tier);
    const threshold = parseFloat(tier.min_cart_total).toFixed(2);

    if (applicable) {
        const total = calculateTotalForType('delivery', subtotal, null, cartTotalDiscounts);
        const template = labels.deliveryApplied ?? 'Скидка :discount при заказе от :threshold ₾ → :total ₾';

        return {
            label: fillTemplate(template, {
                discount: discountLabel,
                threshold,
                total: total.toFixed(2),
            }),
            total,
            applied: true,
            remaining: 0,
        };
    }

    const remaining = roundMoney(parseFloat(tier.min_cart_total) - subtotal);
    const template = labels.deliveryAddMore ?? 'Скидка :discount при заказе от :threshold ₾. Добавьте ещё :remaining ₾';

    return {
        label: fillTemplate(template, {
            discount: discountLabel,
            threshold,
            remaining: remaining.toFixed(2),
        }),
        total: subtotal,
        applied: false,
        remaining,
    };
}

export function buildPickupBadge(pickupDiscount, labels = {}) {
    if (!pickupDiscount) {
        return null;
    }

    const discountLabel = formatDiscountLabel(pickupDiscount);

    return {
        text: discountLabel,
        title: fillTemplate(labels.badgePickup ?? ':discount самовывоз', { discount: discountLabel }),
        tone: 'emerald',
    };
}

export function buildDeliveryBadge(subtotal, cartTotalDiscounts = [], labels = {}) {
    if (!cartTotalDiscounts?.length) {
        return null;
    }

    const applicable = resolveDiscountForType('delivery', subtotal, null, cartTotalDiscounts);
    const tier = applicable ?? getNextDeliveryTier(subtotal, cartTotalDiscounts);

    if (!tier) {
        return null;
    }

    const discountLabel = formatDiscountLabel(tier);
    const threshold = parseFloat(tier.min_cart_total).toFixed(0);
    const hint = buildDeliveryHint(subtotal, cartTotalDiscounts, labels);

    if (applicable) {
        return {
            text: discountLabel,
            title: hint?.label ?? '',
            tone: 'emerald',
        };
    }

    return {
        text: fillTemplate(labels.badgeDeliveryThreshold ?? ':discount от :threshold ₾', {
            discount: discountLabel,
            threshold,
        }),
        title: hint?.label ?? '',
        tone: 'amber',
    };
}

export function buildDeliveryFeeBadge(subtotal, labels = {}) {
    const hint = buildDeliveryFeeHint(subtotal, labels);
    const { fee } = getDeliveryConfig();

    if (hint.isFree) {
        return {
            text: labels.badgeDeliveryFreeShort ?? '0 ₾',
            title: hint.label,
            tone: 'emerald',
        };
    }

    return {
        text: fillTemplate(labels.badgeDeliveryFeeShort ?? ':fee ₾', { fee: fee.toFixed(0) }),
        title: hint.label,
        tone: 'sky',
    };
}

export function buildSummaryChips(subtotal, labels = {}, scope = 'all') {
    const { pickup, cartTotal } = getDiscountConfig();
    const chips = [];

    const pickupBadge = buildPickupBadge(pickup, labels);
    if (pickupBadge && (scope === 'all' || scope === 'pickup')) {
        chips.push({ ...pickupBadge, key: 'pickup' });
    }

    const deliveryFeeBadge = buildDeliveryFeeBadge(subtotal, labels);
    if (deliveryFeeBadge && (scope === 'all' || scope === 'delivery')) {
        chips.push({ ...deliveryFeeBadge, key: 'delivery-fee' });
    }

    const deliveryBadge = buildDeliveryBadge(subtotal, cartTotal, labels);
    if (deliveryBadge && (scope === 'all' || scope === 'delivery')) {
        chips.push({ ...deliveryBadge, key: 'delivery-discount' });
    }

    return chips;
}

function emptyMethodFigures(labels = {}) {
    return [{ text: labels.summaryNoDiscount ?? '—', tone: 'muted' }];
}

function buildOnPremiseMethodSummary(labels = {}, caption = '') {
    const { pickup } = getDiscountConfig();
    const pickupBadge = buildPickupBadge(pickup, labels);

    return {
        figures: pickupBadge
            ? [{ text: pickupBadge.text, tone: pickupBadge.tone }]
            : emptyMethodFigures(labels),
        caption,
    };
}

export function buildDeliveryMethodSummary(subtotal, labels = {}) {
    const { cartTotal } = getDiscountConfig();
    const figures = [];
    const feeBadge = buildDeliveryFeeBadge(subtotal, labels);

    if (feeBadge) {
        figures.push({ text: feeBadge.text, tone: feeBadge.tone });
    }

    const deliveryBadge = buildDeliveryBadge(subtotal, cartTotal, labels);

    if (deliveryBadge) {
        const applicable = resolveDiscountForType('delivery', subtotal, null, cartTotal);
        const tier = applicable ?? getNextDeliveryTier(subtotal, cartTotal);

        if (tier) {
            figures.push({
                text: formatDiscountLabel(tier),
                tone: deliveryBadge.tone,
            });
        }
    }

    return {
        figures: figures.length > 0 ? figures : emptyMethodFigures(labels),
        caption: labels.deliveryProviderWolt ?? 'Wolt Drive',
    };
}

export function buildPickupMethodSummary(subtotal, labels = {}) {
    return buildOnPremiseMethodSummary(labels, labels.pickupFromStore ?? 'Из заведения');
}

export function buildDineInMethodSummary(subtotal, labels = {}) {
    return buildOnPremiseMethodSummary(labels, labels.dineInFromVenue ?? 'На месте');
}

export function getDiscountAmountForType(deliveryType, subtotal) {
    const { pickup, cartTotal } = getDiscountConfig();
    const discount = resolveDiscountForType(deliveryType, subtotal, pickup, cartTotal);

    return roundMoney(calculateDiscountAmount(discount, subtotal));
}

export function getFooterDeliveryFeeBeforeSelection(subtotal) {
    return calculateDeliveryFee(subtotal, 'delivery');
}

export function getFooterDiscountAmountBeforeSelection() {
    return 0;
}

export function getFooterTotalBeforeSelection(subtotal) {
    return roundMoney(subtotal + calculateDeliveryFee(subtotal, 'delivery'));
}

export function getDiscountConfig() {
    return window.discountConfig ?? { pickup: null, cartTotal: [] };
}

export function getDiscountLabels() {
    return window.discountLabels ?? {};
}
