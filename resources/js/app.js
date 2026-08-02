import './bootstrap';
import 'flyonui/flyonui';
import Alpine from 'alpinejs';
import mask from '@alpinejs/mask';
import { initCart } from './cart';
import { formatPhoneDisplay, normalizePhone } from './phone';

window.Alpine = Alpine;
window.normalizePhone = normalizePhone;
window.formatPhoneDisplay = formatPhoneDisplay;

Alpine.plugin(mask);

// Регистрируем глобальный store для корзины
Alpine.store('cart', initCart());

Alpine.start();
