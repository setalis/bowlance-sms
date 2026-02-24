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

        isOrdersEnabled() {
            return typeof window !== 'undefined' && window.siteOrdersEnabled === true;
        },

        // Добавить блюдо в корзину
        addDish(dish) {
            if (!this.isOrdersEnabled()) {
                this.showNotification(window.ordersUnavailableMessage || 'Заказы временно недоступны', 'error');
                return;
            }
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
                    carbs: dish.carbs || 0,
                    sauce_name: dish.sauce_name || null,
                    sauce_weight: dish.sauce_weight || null,
                    sauce_calories: dish.sauce_calories || 0,
                    sauce_proteins: dish.sauce_proteins || 0,
                    sauce_fats: dish.sauce_fats || 0,
                    sauce_carbs: dish.sauce_carbs || 0
                });
            }
            
            this.saveCart();
            this.showNotification('Блюдо добавлено в корзину');
        },

        // Добавить напиток в корзину
        addDrink(drink) {
            if (!this.isOrdersEnabled()) {
                this.showNotification(window.ordersUnavailableMessage || 'Заказы временно недоступны', 'error');
                return;
            }
            const existingItem = this.items.find(item => item.type === 'drink' && item.id === drink.id);
            
            if (existingItem) {
                existingItem.quantity++;
            } else {
                this.items.push({
                    type: 'drink',
                    id: drink.id,
                    name: drink.name,
                    price: parseFloat(drink.price),
                    image: drink.image,
                    quantity: 1,
                    volume: drink.volume || null,
                    calories: drink.calories || 0,
                    proteins: drink.proteins || 0,
                    fats: drink.fats || 0,
                    carbs: drink.carbs || 0
                });
            }
            
            this.saveCart();
            this.showNotification('Напиток добавлен в корзину');
        },

        // Добавить собранный боул в корзину
        addBowl(products) {
            if (!this.isOrdersEnabled()) {
                this.showNotification(window.ordersUnavailableMessage || 'Заказы временно недоступны', 'error');
                return;
            }
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
                calories: this.items.reduce((sum, item) => {
                    const dishCalories = (item.calories || 0) * item.quantity;
                    const sauceCalories = (item.sauce_calories || 0) * item.quantity;
                    return sum + dishCalories + sauceCalories;
                }, 0),
                proteins: this.items.reduce((sum, item) => {
                    const dishProteins = (item.proteins || 0) * item.quantity;
                    const sauceProteins = (item.sauce_proteins || 0) * item.quantity;
                    return sum + dishProteins + sauceProteins;
                }, 0),
                fats: this.items.reduce((sum, item) => {
                    const dishFats = (item.fats || 0) * item.quantity;
                    const sauceFats = (item.sauce_fats || 0) * item.quantity;
                    return sum + dishFats + sauceFats;
                }, 0),
                carbs: this.items.reduce((sum, item) => {
                    const dishCarbs = (item.carbs || 0) * item.quantity;
                    const sauceCarbs = (item.sauce_carbs || 0) * item.quantity;
                    return sum + dishCarbs + sauceCarbs;
                }, 0)
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
            if (!this.isOrdersEnabled()) {
                this.showNotification(window.ordersUnavailableMessage || 'Заказы временно недоступны', 'error');
                return;
            }
            this.isOpen = true;
        },

        // Закрыть drawer
        closeDrawer() {
            this.isOpen = false;
        },

        // Оформить заказ
        async checkout(customerData) {
            if (!this.isOrdersEnabled()) {
                this.showNotification(window.ordersUnavailableMessage || 'Заказы временно недоступны', 'error');
                return false;
            }
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
                const deliveryType = customerData.deliveryType ?? customerData.delivery_type ?? 'delivery';
                const deliveryCity = (customerData.deliveryCity ?? customerData.delivery_city ?? '').toString().trim() || null;
                const deliveryStreet = (customerData.deliveryStreet ?? customerData.delivery_street ?? '').toString().trim() || null;
                const deliveryHouse = (customerData.deliveryHouse ?? customerData.delivery_house ?? '').toString().trim() || null;

                const orderData = {
                    customer_name: customerData.name,
                    customer_phone: customerData.phone,
                    customer_email: customerData.email || null,
                    delivery_type: deliveryType,
                    delivery_city: deliveryCity,
                    delivery_street: deliveryStreet,
                    delivery_house: deliveryHouse,
                    entrance: customerData.entrance || null,
                    floor: customerData.floor || null,
                    apartment: customerData.apartment || null,
                    intercom: customerData.intercom || null,
                    courier_comment: customerData.courierComment || null,
                    receiver_phone: customerData.receiverPhone || null,
                    leave_at_door: customerData.leaveAtDoor || false,
                    comment: customerData.comment || null,
                    verification_request_id: customerData.verification_request_id,
                    confirm_switch_user: customerData.confirm_switch_user || false,
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

                // Получаем JSON ответ
                const result = await response.json();
                
                // Логируем для отладки
                console.log('Ответ сервера:', result);

                if (result.success) {
                    this.items = [];
                    this.saveCart();
                    this.closeDrawer();

                    return result.order;
                } else if (result.requires_confirmation) {
                    // Return special object indicating confirmation is needed
                    const error = new Error(result.message || 'Требуется подтверждение');
                    error.requires_confirmation = true;
                    error.target_user = result.target_user;
                    throw error;
                } else {
                    throw new Error(result.message || 'Ошибка при оформлении заказа');
                }
            } catch (error) {
                // Re-throw if it has requires_confirmation flag
                if (error.requires_confirmation) {
                    throw error;
                }
                this.showNotification(error.message || 'Ошибка при оформлении заказа', 'error');
                return false;
            }
        }
    }
}
