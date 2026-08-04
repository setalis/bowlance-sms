<?php

it('показывает реквизиты обеих карт в чекауте', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();

    $response->assertSee('Bank of Georgia');
    $response->assertSee('GE05BG0000000539887879');
    $response->assertSee('GE05 BG00 0000 0539 8878 79');

    $response->assertSee('TBC Bank');
    $response->assertSee('GE26TB7836436010100048');
    $response->assertSee('GE26 TB78 3643 6010 1000 48');
});
