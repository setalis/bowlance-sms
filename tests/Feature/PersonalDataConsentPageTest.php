<?php

it('renders the personal data consent page', function () {
    $response = $this->get(route('legal.personal-data-consent'));

    $response->assertSuccessful();
    $response->assertSee('Согласие на обработку персональных данных', false);
    $response->assertSee('https://www.bowlance.ge/', false);
    $response->assertSee('info@bowlance.ge', false);
    $response->assertSee('+995 500 700 877', false);
});

it('links to the consent page from the footer and checkout form', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee(route('legal.personal-data-consent'), false);
    $response->assertSee(__('frontend.personal_data_consent_prefix'), false);
    $response->assertSee(__('frontend.personal_data_consent_link'), false);
    $response->assertSee('personal-data-consent-pickup', false);
    $response->assertSee('personal-data-consent-verified', false);
    $response->assertSee('personal-data-consent-callback', false);
});
