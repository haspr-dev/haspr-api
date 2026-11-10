# Contributing to Passbolt API

## Prerequisite

* git
* Docker provider ([see supported list](https://ddev.readthedocs.io/en/stable/users/install/docker-installation/))
* [ddev](https://ddev.com/get-started/)
* mkcert, nss library installed ([see](https://docs.ddev.com/en/stable/users/install/ddev-installation/))

## Setup

Quickly spin up development environment with default configuration:
1. Clone passbolt api repo
2. Start ddev
    ```sh
    ddev start
    ```
3. Initiate dev environment (run only once)
    ```sh
    ddev init_passbolt
    ```

All set! Navigate to https://passbolt-api.ddev.site/ and start contributing to passbolt 💪

## Running tests

The test database is already created for you.

To run full test suite:
```
ddev composer test
```

To run a subset of tests:
```
ddev exec -d /var/www/html "vendor/bin/phpunit --filter <TestName>"
```

## Run code quality checks

- Coding style violations: `ddev composer cs-check`
- Autofix coding style errors: `ddev composer cs-fix`
- Static analysis: `ddev composer stan` and `ddev composer psalm`
- All in one command (run PHPStan, Psalm and coding standards check): `ddev analyze`

## Contributing to the Clients

* Browser extension (Firefox, Edge & Chrome): https://github.com/pbolt-dev/pbolt-browser-extension
* Styleguide: https://github.com/pbolt-dev/pbolt-styleguide
* CLI: https://github.com/pbolt-dev/go-passbolt-cli

## How do I contribute to translations

Translations for this repository are managed directly within this repository under `resources/locales/<locale>/default.po`. You can propose translation improvements or new language translations by submitting a Pull Request.
