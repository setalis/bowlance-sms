<?php

use App\Support\PhoneNumber;

it('нормализует грузинские номера в E.164', function (string $input, string $expected) {
    expect(PhoneNumber::toE164($input))->toBe($expected);
})->with([
    'local with leading zero' => ['0507082864', '+995507082864'],
    'nine digits' => ['507082864', '+995507082864'],
    'country code without plus' => ['995507082864', '+995507082864'],
    'formatted with spaces' => ['+995 507 08 28 64', '+995507082864'],
    'already e164' => ['+995507082864', '+995507082864'],
    'mask style' => ['+995 555 12 34 56', '+995555123456'],
    'mistaken zero after country' => ['+9950507082864', '+995507082864'],
    'incomplete mistaken zero' => ['+995050708286', ''],
    'empty' => ['', ''],
    'whitespace' => ['   ', ''],
]);

it('возвращает кандидаты для поиска пользователя по телефону', function () {
    expect(PhoneNumber::lookupCandidates('0507082864'))->toContain('+995507082864')
        ->toContain('995507082864')
        ->toContain('0507082864')
        ->toContain('507082864');
});

it('форматирует номер для отображения в маске', function () {
    expect(PhoneNumber::formatDisplay('0507082864'))->toBe('+995 507 08 28 64');
    expect(PhoneNumber::formatDisplay('+995555123456'))->toBe('+995 555 12 34 56');
});
