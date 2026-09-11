<?php

it('renders the personal data consent page', function () {
    $response = $this->get(route('legal.personal-data-consent'));

    $response->assertSuccessful();
    $response->assertSee('Согласие на обработку персональных данных', false);
    $response->assertSee('https://www.bowlance.ge/', false);
    $response->assertSee('I/E Vladyslav Kravchenko', false);
    $response->assertSee('543811345', false);
    $response->assertSee('Батуми, ул. Парнаваз Мепе 162/174', false);
    $response->assertSee('bowlance.ge@gmail.com', false);
    $response->assertSee('mailto:bowlance.ge@gmail.com', false);
    $response->assertSee('+995 500 700 877', false);
    $response->assertDontSee('[указать', false);
});

it('links to the consent page from the footer and checkout form', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee(route('legal.personal-data-consent'), false);
    $response->assertSee(__('frontend.personal_data_consent_prefix'), false);
    $response->assertSee(__('frontend.personal_data_consent_link'), false);
    $response->assertSee('personal-data-consent-checkout', false);
    $response->assertDontSee('personal-data-consent-pickup', false);
    $response->assertDontSee('personal-data-consent-verified', false);
    $response->assertDontSee('personal-data-consent-callback', false);
});

it('renders a three-step delivery checkout with a thank-you phone link', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('Шаг 1 из 3 — контакты и время', false);
    $response->assertSee('Шаг 2 из 3 — адрес доставки', false);
    $response->assertSee('Шаг 3 из 3 — как вы оплатите', false);
    $response->assertDontSee('Шаг 1 из 4', false);
    $response->assertDontSee('goToStep4()', false);
    $response->assertSee(__('frontend.order_thanks'), false);
    $response->assertSee('tel:+995500700877', false);
    $response->assertSee('verification_method: isOnPremise ? null : \'callback\'', false);
});
