<?php

$router->get('auth/nfdi/redirect', [
   'as'   => 'nfdi-redirect',
   'uses' => 'NFDILoginController@redirect',
]);

$router->get('auth/nfdi/callback', [
   'as'   => 'nfdi-callback',
   'uses' => 'NFDILoginController@callback',
]);

$router->get('auth/nfdi/register', [
   'as'   => 'nfdi-register-form',
   'uses' => 'RegisterController@showRegistrationForm',
]);

$router->post('auth/nfdi/register', [
   'as'   => 'nfdi-register',
   'uses' => 'RegisterController@register',
]);
