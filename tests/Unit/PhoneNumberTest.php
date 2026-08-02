<?php

use App\Support\PhoneNumber;

it('нормализует номера в E.164 с регионом по умолчанию GE', function (string $input, string $expected) {
    expect(PhoneNumber::toE164($input))->toBe($expected);
})->with([
    'georgian local' => ['555948217', '+995555948217'],
    'georgian with country code' => ['995555948217', '+995555948217'],
    'georgian formatted' => ['+995 555 12 34 56', '+995555123456'],
    'georgian already e164' => ['+995555948217', '+995555948217'],
    'ukrainian e164' => ['+380507082864', '+380507082864'],
    'ukrainian with international prefix' => ['00380507082864', '+380507082864'],
    'ukrainian local without country code' => ['0507082864', ''],
    'nonexistent georgian prefix' => ['+995222222222', ''],
    'too short' => ['55050505', ''],
    'truncated' => ['+995050708286', ''],
    'empty' => ['', ''],
    'whitespace' => ['   ', ''],
]);

it('разбирает местный номер по указанному региону', function () {
    expect(PhoneNumber::toE164('0507082864', 'UA'))->toBe('+380507082864')
        ->and(PhoneNumber::toE164('0507082864', 'GE'))->toBe('');
});

it('не трогает регион по умолчанию когда номер задан в международном формате', function () {
    expect(PhoneNumber::toE164('+380507082864', 'GE'))->toBe('+380507082864');
});

it('определяет валидность номера', function () {
    expect(PhoneNumber::isValid('+380507082864'))->toBeTrue()
        ->and(PhoneNumber::isValid('555948217'))->toBeTrue()
        ->and(PhoneNumber::isValid('0507082864'))->toBeFalse()
        ->and(PhoneNumber::isValid(null))->toBeFalse();
});

it('определяет регион номера', function () {
    expect(PhoneNumber::regionCode('+380507082864'))->toBe('UA')
        ->and(PhoneNumber::regionCode('555948217'))->toBe('GE')
        ->and(PhoneNumber::regionCode('0507082864'))->toBeNull();
});

it('возвращает кандидаты для поиска пользователя по телефону', function () {
    expect(PhoneNumber::lookupCandidates('555948217'))->toContain('+995555948217')
        ->toContain('995555948217')
        ->toContain('555 94 82 17')
        ->toContain('555948217');
});

it('возвращает кандидаты для международного номера', function () {
    expect(PhoneNumber::lookupCandidates('+380507082864'))->toContain('+380507082864')
        ->toContain('380507082864')
        ->toContain('050 708 2864')
        ->toContain('0507082864');
});

it('для невалидного номера возвращает только исходные варианты', function () {
    expect(PhoneNumber::lookupCandidates('0507082864'))->toBe(['0507082864']);
});

it('форматирует номер для отображения в международном виде', function () {
    expect(PhoneNumber::formatDisplay('555948217'))->toBe('+995 555 94 82 17')
        ->and(PhoneNumber::formatDisplay('+380507082864'))->toBe('+380 50 708 2864')
        ->and(PhoneNumber::formatDisplay('0507082864'))->toBe('');
});
