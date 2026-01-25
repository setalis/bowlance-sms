// Глобальное управление корзиной
export function initCart() {
    return {
        items: [],
        isOpen: false,

        init() {
            // Загружаем корзину из localStorage при инициализации
            const savedCart = localStorage.getItem('bowlance_cart');
            if (savedCart) {
                try {
                    this.items = JSON.parse(savedCart);
                } catch (e) {
                    this.items = [];
                }
            }
        },

        // Добавить блюдо в корзину
        addDish(dish) {
            const existingItem = this.items.find(item => item.type === 'dish' && item.id === dish.id);
            
            if (existingItem) {
                existingItem.quantity++;
            } else {
                this.items.push({
                    type: 'dish',
                    id: dish.id,
                    name: dish.name,
                    price: parseFloat(dish.price),
                    image: dish.image,
                    quantity: 1,
                    weight: dish.weight || null,
                    calories: dish.calories || 0,
                    proteins: dish.proteins || 0,
                    fats: dish.fats || 0,
                    carbs: dish.carbs || 0
                });
            }
            
            this.saveCart();
            this.showNotification('Блюдо добавлено в корзину');
        },

        // Добавить собранный боул в корзину
        addBowl(products) {
            if (!products || products.length === 0) {
                this.showNotification('Выберите продукты для боула', 'error');
                return;
            }

            const bowlId = Date.now();
            const totalPrice = products.reduce((sum, p) => sum + parseFloat(p.price), 0);
            const totalCalories = products.reduce((sum, p) => sum + (p.calories || 0), 0);
            const totalProteins = products.reduce((sum, p) => sum + (p.proteins || 0), 0);
            const totalFats = products.reduce((sum, p) => sum + (p.fats || 0), 0);
            const totalCarbs = products.reduce((sum, p) => sum + (p.carbs || 0), 0);

            this.items.push({
                type: 'bowl',
                id: bowlId,
                name: 'Собранный боул',
                price: totalPrice,
                quantity: 1,
                products: products,
                calories: totalCalories,
                proteins: totalProteins,
                fats: totalFats,
                carbs: totalCarbs
            });

            this.saveCart();
            this.showNotification('Боул добавлен в корзину');
        },

        // Увеличить количество
        increaseQuantity(index) {
            if (this.items[index]) {
                this.items[index].quantity++;
                this.saveCart();
            }
        },

        // Уменьшить количество
        decreaseQuantity(index) {
            if (this.items[index] && this.items[index].quantity > 1) {
                this.items[index].quantity--;
                this.saveCart();
            }
        },

        // Удалить товар
        removeItem(index) {
            this.items.splice(index, 1);
            this.saveCart();
            this.showNotification('Товар удален из корзины');
        },

        // Очистить корзину
        clearCart() {
            if (confirm('Вы уверены, что хотите очистить корзину?')) {
                this.items = [];
                this.saveCart();
                this.showNotification('Корзина очищена');
            }
        },

        // Сохранить корзину в localStorage
        saveCart() {
            localStorage.setItem('bowlance_cart', JSON.stringify(this.items));
        },

        // Получить общее количество товаров
        get totalItems() {
            return this.items.reduce((sum, item) => sum + item.quantity, 0);
        },

        // Получить общую сумму
        get totalPrice() {
            return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },

        // Получить общую пищевую ценность
        get totalNutrition() {
            return {
                calories: this.items.reduce((sum, item) => sum + ((item.calories || 0) * item.quantity), 0),
                proteins: this.items.reduce((sum, item) => sum + ((item.proteins || 0) * item.quantity), 0),
                fats: this.items.reduce((sum, item) => sum + ((item.fats || 0) * item.quantity), 0),
                carbs: this.items.reduce((sum, item) => sum + ((item.carbs || 0) * item.quantity), 0)
            };
        },

        // Показать уведомление
        showNotification(message, type = 'success') {
            // Простое уведомление (можно заменить на toast-библиотеку)
            const event = new CustomEvent('cart-notification', {
                detail: { message, type }
            });
            window.dispatchEvent(event);
        },

        // Открыть drawer
        openDrawer() {
            this.isOpen = true;
        },

        // Закрыть drawer
        closeDrawer() {
            this.isOpen = false;
        },

        // Оформить заказ
        async checkout(customerData) {
            if (this.items.length === 0) {
                this.showNotification('Корзина пуста', 'error');
                return false;
            }

            // Проверка наличия verification_request_id
            if (!customerData.verification_request_id) {
                this.showNotification('Необходимо верифицировать номер телефона', 'error');
                return false;
            }

            try {
                // Подготовка данных заказа
                const orderData = {
                    customer_name: customerData.name,
                    customer_phone: customerData.phone,
                    customer_email: customerData.email || null,
                    delivery_address: customerData.address || null,
                    comment: customerData.comment || null,
                    verification_request_id: customerData.verification_request_id,
                    items: this.items.map(item => ({
                        type: item.type,
                        id: item.id,
                        name: item.name,
                        price: item.price,
                        quantity: item.quantity,
                        calories: item.calories,
                        proteins: item.proteins,
                        fats: item.fats,
                        carbs: item.carbs,
                        products: item.products || null,
                    })),
                };

                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                if (!csrfMeta) {
                    this.showNotification('Ошибка: отсутствует CSRF-токен. Обновите страницу.', 'error');
                    return false;
                }

                const response = await fetch('/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfMeta.getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(orderData),
                });

                if (response.status === 419) {
                    this.showNotification('Сессия истекла. Обновите страницу (F5) и повторите попытку.', 'error');
                    setTimeout(() => window.location.reload(), 1500);
                    return false;
                }

                const result = await response.json();

                if (result.success) {
                    this.items = [];
                    this.saveCart();
                    this.closeDrawer();

                    return result.order;
                } else {
                    throw new Error(result.message || 'Ошибка при оформлении заказа');
                }
            } catch (error) {
                this.showNotification(error.message || 'Ошибка при оформлении заказа', 'error');
                return false;
            }
        }
    }
}
