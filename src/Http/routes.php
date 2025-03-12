<?php

$router->get('auth/nfdi/redirect', [
   'as'   => 'nfdi-redirect',
   'uses' => 'NfdiLoginController@redirect',
]);

$router->get('auth/iam4nfdi/callback', [
   'as'   => 'nfdi-callback',
   'uses' => 'NfdiLoginController@callback',
]);

$router->get('auth/nfdi/register', [
   'as'   => 'nfdi-register-form',
   'uses' => 'RegisterController@showRegistrationForm',
]);

$router->post('auth/nfdi/register', [
   'as'   => 'nfdi-register',
   'uses' => 'RegisterController@register',
]);
