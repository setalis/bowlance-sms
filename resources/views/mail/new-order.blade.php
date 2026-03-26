<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новый заказ #{{ $order->order_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 640px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .header { background: #1a1a2e; color: #fff; padding: 24px 32px; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 4px 0 0; opacity: .7; font-size: 13px; }
        .body { padding: 28px 32px; }
        h2 { font-size: 15px; color: #555; text-transform: uppercase; letter-spacing: .5px; margin: 24px 0 12px; border-bottom: 1px solid #eee; padding-bottom: 6px; }
        h2:first-child { margin-top: 0; }
        .info-row { display: flex; margin-bottom: 8px; }
        .info-label { width: 160px; flex-shrink: 0; color: #888; }
        .info-value { font-weight: 600; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #f8f8f8; text-align: left; padding: 8px 10px; font-size: 12px; color: #666; }
        td { padding: 8px 10px; border-bottom: 1px solid #f0f0f0; }
        tr:last-child td { border-bottom: none; }
        .totals { margin-top: 16px; }
        .totals .info-row { margin-bottom: 6px; }
        .totals .total-final .info-label,
        .totals .total-final .info-value { font-size: 16px; color: #1a1a2e; font-weight: 700; }
        .comment-box { background: #fffbe6; border-left: 3px solid #f0c040; padding: 10px 14px; border-radius: 4px; margin-top: 8px; }
        .footer { background: #f8f8f8; padding: 16px 32px; text-align: center; color: #aaa; font-size: 12px; border-top: 1px solid #eee; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Новый заказ</h1>
        <p>#{{ $order->order_number }} &bull; {{ $order->created_at->format('d.m.Y H:i') }}</p>
    </div>

    <div class="body">
        <h2>Клиент</h2>
        <div class="info-row">
            <span class="info-label">Имя:</span>
            <span class="info-value">{{ $order->customer_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Телефон:</span>
            <span class="info-value">{{ $order->customer_phone }}</span>
        </div>
        @if($order->customer_email)
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span class="info-value">{{ $order->customer_email }}</span>
        </div>
        @endif

        <h2>Доставка</h2>
        <div class="info-row">
            <span class="info-label">Тип:</span>
            <span class="info-value">{{ $order->delivery_type?->label() }}</span>
        </div>
        @if($order->delivery_type?->value === 'delivery')
        <div class="info-row">
            <span class="info-label">Адрес:</span>
            <span class="info-value">{{ $order->delivery_address }}</span>
        </div>
        @endif
        @if($order->delivery_time)
        <div class="info-row">
            <span class="info-label">Время доставки:</span>
            <span class="info-value">{{ $order->delivery_time }}</span>
        </div>
        @endif
        @if($order->receiver_phone)
        <div class="info-row">
            <span class="info-label">Телефон получателя:</span>
            <span class="info-value">{{ $order->receiver_phone }}</span>
        </div>
        @endif

        <h2>Позиции</h2>
        <table>
            <thead>
                <tr>
                    <th>Наименование</th>
                    <th style="text-align:center">Кол-во</th>
                    <th style="text-align:right">Цена</th>
                    <th style="text-align:right">Сумма</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td style="text-align:center">{{ $item->quantity }}</td>
                    <td style="text-align:right">{{ number_format($item->price, 2) }} ₾</td>
                    <td style="text-align:right">{{ number_format($item->subtotal, 2) }} ₾</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="info-row">
                <span class="info-label">Подытог:</span>
                <span class="info-value">{{ number_format($order->subtotal, 2) }} ₾</span>
            </div>
            <div class="info-row">
                <span class="info-label">Доставка:</span>
                <span class="info-value">{{ $order->delivery_fee > 0 ? number_format($order->delivery_fee, 2).' ₾' : 'Бесплатно' }}</span>
            </div>
            <div class="info-row total-final">
                <span class="info-label">Итого:</span>
                <span class="info-value">{{ number_format($order->total, 2) }} ₾</span>
            </div>
        </div>

        <h2>Оплата</h2>
        <div class="info-row">
            <span class="info-label">Способ оплаты:</span>
            <span class="info-value">{{ $order->payment_method?->label() }}</span>
        </div>

        @if($order->comment)
        <h2>Комментарий</h2>
        <div class="comment-box">{{ $order->comment }}</div>
        @endif

        @if($order->courier_comment)
        <h2>Комментарий курьеру</h2>
        <div class="comment-box">{{ $order->courier_comment }}</div>
        @endif
    </div>

    <div class="footer">
        Bowlance &bull; Это автоматическое уведомление
    </div>
</div>
</body>
</html>
