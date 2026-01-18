import './bootstrap';
import 'flyonui/flyonui';
import Alpine from 'alpinejs';
import { initCart } from './cart';

window.Alpine = Alpine;

// Регистрируем глобальный store для корзины
Alpine.store('cart', initCart());

Alpine.start();
