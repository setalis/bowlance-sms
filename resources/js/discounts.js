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

export function resolveDiscountForType(deliveryType, subtotal, pickupDiscount, cartTotalDiscounts = []) {
    if (deliveryType === 'pickup') {
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

export function getDiscountConfig() {
    return window.discountConfig ?? { pickup: null, cartTotal: [] };
}

export function getDiscountLabels() {
    return window.discountLabels ?? {};
}
