<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use App\Support\PhoneNumber;
use Illuminate\Console\Command;

class AuditPhoneNumbers extends Command
{
    protected $signature = 'phones:audit
        {--fix : Привести к E.164 номера, которые libphonenumber распознаёт, но которые хранятся в другом формате}
        {--set=* : Ручная правка в формате user_id:+E164, например 14:+380507082864}';

    protected $description = 'Показать телефоны пользователей и заказов, которые не проходят проверку libphonenumber';

    public function handle(): int
    {
        if (! $this->applyManualFixes()) {
            return self::FAILURE;
        }

        $this->normalizeRecognizedNumbers();
        $this->reportBrokenNumbers();

        return self::SUCCESS;
    }

    private function normalizeRecognizedNumbers(): void
    {
        $rows = [];

        User::query()->whereNotNull('phone')->orderBy('id')->each(function (User $user) use (&$rows): void {
            $canonical = PhoneNumber::toE164($user->phone);

            if ($canonical === '' || $canonical === $user->phone) {
                return;
            }

            $previous = $user->phone;

            if ($this->phoneTakenByAnotherUser($canonical, $user->id)) {
                $rows[] = [$user->id, $previous, $canonical, 'занят другим пользователем'];

                return;
            }

            if ($this->option('fix')) {
                $user->update(['phone' => $canonical]);
            }

            $rows[] = [$user->id, $previous, $canonical, $this->option('fix') ? 'записано' : 'будет записано с --fix'];
        });

        $this->components->info('Номеров не в формате E.164: '.count($rows));

        if ($rows !== []) {
            $this->table(['user_id', 'было', 'E.164', 'результат'], $rows);
        }
    }

    private function phoneTakenByAnotherUser(string $phone, int $userId): bool
    {
        return User::query()->where('phone', $phone)->where('id', '!=', $userId)->exists();
    }

    private function reportBrokenNumbers(): void
    {
        $userRows = [];

        User::query()->whereNotNull('phone')->orderBy('id')->each(function (User $user) use (&$userRows): void {
            if (! PhoneNumber::isValid($user->phone)) {
                $userRows[] = [$user->id, $user->phone];
            }
        });

        $this->components->info('Пользователи с несуществующим номером: '.count($userRows));

        if ($userRows !== []) {
            $this->table(['user_id', 'phone'], $userRows);
        }

        $orderRows = [];

        Order::query()->orderBy('id')->each(function (Order $order) use (&$orderRows): void {
            if (! PhoneNumber::isValid($order->customer_phone)) {
                $orderRows[] = [$order->id, $order->order_number, $order->customer_phone];
            }
        });

        $this->components->info('Заказы с несуществующим номером: '.count($orderRows));

        if ($orderRows !== []) {
            $this->table(['order_id', 'order_number', 'customer_phone'], $orderRows);
        }
    }

    private function applyManualFixes(): bool
    {
        foreach ($this->option('set') as $pair) {
            [$userId, $phone] = array_pad(explode(':', (string) $pair, 2), 2, null);

            $canonical = PhoneNumber::toE164($phone);

            if (! ctype_digit((string) $userId) || $canonical === '') {
                $this->components->error("Не удалось разобрать --set={$pair}: нужен формат user_id:+E164 с существующим номером");

                return false;
            }

            $user = User::find((int) $userId);

            if (! $user) {
                $this->components->error("Пользователь {$userId} не найден");

                return false;
            }

            if ($this->phoneTakenByAnotherUser($canonical, $user->id)) {
                $this->components->error("Номер {$canonical} уже занят другим пользователем");

                return false;
            }

            $previousPhone = $user->phone;
            $user->update(['phone' => $canonical]);

            Order::query()
                ->where('user_id', $user->id)
                ->where('customer_phone', $previousPhone)
                ->update(['customer_phone' => $canonical]);

            $this->components->info("Пользователь {$userId} и его заказы переведены на {$canonical}");
        }

        return true;
    }
}
