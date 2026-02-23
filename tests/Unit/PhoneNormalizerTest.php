<?php

use App\Services\PhoneNormalizer;

it('нормализует грузинский номер в формате +995...', function () {
    expect(PhoneNormalizer::normalize('+995555123456'))->toBe('+995555123456');
});

it('нормализует грузинский номер с пробелами', function () {
    expect(PhoneNormalizer::normalize('995 555 123 456'))->toBe('+995555123456');
});

it('нормализует грузинский номер с дефисами', function () {
    expect(PhoneNormalizer::normalize('0555-123-456'))->toBe('+995555123456');
});

it('нормализует 9 цифр с первой 5 как Грузию', function () {
    expect(PhoneNormalizer::normalize('555123456'))->toBe('+995555123456');
});

it('нормализует 10 цифр с ведущим 0 как Грузию', function () {
    expect(PhoneNormalizer::normalize('0555123456'))->toBe('+995555123456');
});

it('нормализует 12 цифр 995... в E.164', function () {
    expect(PhoneNormalizer::normalize('995555123456'))->toBe('+995555123456');
});

it('разные форматы дают один нормализованный номер', function () {
    $expected = '+995555123456';
    expect(PhoneNormalizer::normalize('+995555123456'))->toBe($expected);
    expect(PhoneNormalizer::normalize('995 555 123 456'))->toBe($expected);
    expect(PhoneNormalizer::normalize('0555-123-456'))->toBe($expected);
    expect(PhoneNormalizer::normalize('555 123 456'))->toBe($expected);
});

it('возвращает пустую строку для пустого или только нецифрового ввода', function () {
    expect(PhoneNormalizer::normalize(''))->toBe('');
    expect(PhoneNormalizer::normalize('   ---   '))->toBe('');
});
