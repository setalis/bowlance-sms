<?php

namespace App\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class OrderWindow
{
    public function timezone(): string
    {
        return (string) config('app.timezone');
    }

    public function opensAt(): string
    {
        return (string) config('ordering.opens_at');
    }

    public function closesAt(): string
    {
        return (string) config('ordering.closes_at');
    }

    public function slotMinutes(): int
    {
        return (int) config('ordering.slot_minutes');
    }

    public function morningPrepMinutes(): int
    {
        return (int) config('ordering.morning_prep_minutes');
    }

    public function firstSlotAt(): string
    {
        return $this->formatMinutes($this->firstSlotMinutes());
    }

    public function isOpen(?CarbonInterface $moment = null): bool
    {
        $moment = $this->moment($moment);
        $minutes = $this->minutesOfDay($moment);

        return $minutes >= $this->firstSlotMinutes() && $minutes <= $this->closesAtMinutes();
    }

    public function bookableDate(?CarbonInterface $moment = null): Carbon
    {
        $moment = $this->moment($moment);

        if ($this->minutesOfDay($moment) > $this->closesAtMinutes()) {
            return $moment->copy()->addDay()->startOfDay();
        }

        return $moment->copy()->startOfDay();
    }

    public function bookableDateLabel(?CarbonInterface $moment = null): string
    {
        return $this->bookableDate($moment)->locale('ru')->isoFormat('D MMMM');
    }

    /**
     * @return list<string>
     */
    public function allSlots(): array
    {
        $slots = [];

        for ($minutes = $this->firstSlotMinutes(); $minutes <= $this->closesAtMinutes(); $minutes += $this->slotMinutes()) {
            $slots[] = $this->formatMinutes($minutes);
        }

        return $slots;
    }

    /**
     * @return list<string>
     */
    public function slots(?CarbonInterface $moment = null): array
    {
        $moment = $this->moment($moment);

        if (! $this->isOpen($moment)) {
            return $this->allSlots();
        }

        $nowMinutes = $this->minutesOfDay($moment);

        return array_values(array_filter(
            $this->allSlots(),
            fn (string $slot): bool => $this->minutesFromTime($slot) >= $nowMinutes,
        ));
    }

    public function fulfillAt(?string $time, ?CarbonInterface $moment = null): ?Carbon
    {
        if (blank($time) || ! in_array($time, $this->allSlots(), true)) {
            return null;
        }

        return $this->bookableDate($moment)->setTimeFromTimeString($time);
    }

    public function accepts(?string $time, ?CarbonInterface $moment = null): bool
    {
        $moment = $this->moment($moment);

        if (blank($time)) {
            return $this->isOpen($moment);
        }

        if (! in_array($time, $this->allSlots(), true)) {
            return false;
        }

        if (! $this->isOpen($moment)) {
            return true;
        }

        return $this->minutesFromTime($time) >= $this->minutesOfDay($moment);
    }

    public function closedMessage(): string
    {
        return 'Заказ можно выполнить только в рабочее время с '.$this->firstSlotAt().' до '.$this->closesAt().'.';
    }

    /**
     * @return array{
     *     timezone: string,
     *     opensAt: string,
     *     closesAt: string,
     *     firstSlotAt: string,
     *     morningPrepMinutes: int,
     *     slotMinutes: int,
     *     serverNow: string,
     *     open: bool,
     *     bookableDate: string,
     *     bookableDateLabel: string,
     *     slots: list<string>
     * }
     */
    public function clientPayload(?CarbonInterface $moment = null): array
    {
        $moment = $this->moment($moment);

        return [
            'timezone' => $this->timezone(),
            'opensAt' => $this->opensAt(),
            'closesAt' => $this->closesAt(),
            'firstSlotAt' => $this->firstSlotAt(),
            'morningPrepMinutes' => $this->morningPrepMinutes(),
            'slotMinutes' => $this->slotMinutes(),
            'serverNow' => $moment->toIso8601String(),
            'open' => $this->isOpen($moment),
            'bookableDate' => $this->bookableDate($moment)->toDateString(),
            'bookableDateLabel' => $this->bookableDateLabel($moment),
            'slots' => $this->slots($moment),
        ];
    }

    protected function moment(?CarbonInterface $moment): Carbon
    {
        $moment ??= now();

        return Carbon::parse($moment)->timezone($this->timezone());
    }

    protected function opensAtMinutes(): int
    {
        return $this->minutesFromTime($this->opensAt());
    }

    protected function firstSlotMinutes(): int
    {
        return $this->opensAtMinutes() + $this->morningPrepMinutes();
    }

    protected function closesAtMinutes(): int
    {
        return $this->minutesFromTime($this->closesAt());
    }

    protected function minutesOfDay(CarbonInterface $moment): int
    {
        return ((int) $moment->format('H')) * 60 + (int) $moment->format('i');
    }

    protected function minutesFromTime(string $time): int
    {
        [$hour, $minute] = array_map(intval(...), explode(':', $time));

        return $hour * 60 + $minute;
    }

    protected function formatMinutes(int $minutes): string
    {
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }
}
