# BIIGLE NFDI Login Module

[![Test status](https://github.com/biigle/auth-nfdi/workflows/Tests/badge.svg)](https://github.com/biigle/auth-nfdi/actions?query=workflow%3ATests)

This is a BIIGLE module that provides authentication via [NFDI Login](https://nfdi-aai.de/).

Information on how to register your BIIGLE instance as a new service to NFDI Login can be found [here](https://nfdi-aai.de/infraproxy/).

## Installation

1. Run `composer require biigle/auth-nfdi`.
2. Run `php artisan vendor:publish --tag=public` to refresh the public assets of the modules. Do this for every update of this module.
3. Configure your NFDI Login credentials in `config/services.php` like this:
   ```php
   'nfdilogin' => [
       'client_id' => env('NFDILOGIN_CLIENT_ID'),
       'client_secret' => env('NFDILOGIN_CLIENT_SECRET'),
       'redirect' => '/auth/iam4nfdi/callback',
   ],
   ```
4. Run the database migrations with `php artisan migrate`.

## Developing

Take a look at the [development guide](https://github.com/biigle/core/blob/master/DEVELOPING.md) of the core repository to get started with the development setup.

Want to develop a new module? Head over to the [biigle/module](https://github.com/biigle/module) template repository.

## Contributions and bug reports

Contributions to BIIGLE are always welcome. Check out the [contribution guide](https://github.com/biigle/core/blob/master/CONTRIBUTING.md) to get started.
