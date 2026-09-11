<?php

use App\Models\OrderItem;

it('умножает количество ингредиента на количество позиции', function () {
    $item = new OrderItem(['quantity' => 2]);
    $product = ['name' => 'Яйца пашот', 'price' => 5, 'quantity' => 1];

    expect($item->constructorProductQuantity($product))->toBe(2)
        ->and($item->constructorProductLinePrice($product))->toBe(10.0);
});

it('считает quantity ингредиента равным 1 если поле отсутствует', function () {
    $item = new OrderItem(['quantity' => 2]);
    $product = ['name' => 'Яйца пашот', 'price' => 5];

    expect($item->constructorProductQuantity($product))->toBe(2)
        ->and($item->constructorProductLinePrice($product))->toBe(10.0);
});

it('учитывает количество ингредиента внутри одного конструктора', function () {
    $item = new OrderItem(['quantity' => 2]);
    $product = ['name' => 'Яйца пашот', 'price' => 5, 'quantity' => 3];

    expect($item->constructorProductQuantity($product))->toBe(6)
        ->and($item->constructorProductLinePrice($product))->toBe(30.0);
});
