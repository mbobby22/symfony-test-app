# About

- Dockerized Symfony app
- Requests Yahoo finance API
- Caches responses using Redis
- Displays data in table format
- Displays data in chart format
- Emails send using async process (Mailer + Messenger + Mailtrap on local)
- Form fields generated using entity
- Form validation using entity validation (HTML5 gets generated for frontend)
- Tests included (partial for controller and services)

# How to run

1. Run `docker compose build --pull --no-cache` (it will take a while the 1st time)

2. Run `docker compose up`

3. Open https://localhost/form in your favorite web browser and play with the form

4. Run `docker-compose run php sh -c "php bin/console messenger:consume async”` to consume emails

5. Run tests using command `docker-compose run php sh -c "php bin/phpunit"`

6. Run `docker compose down --remove-orphans` to shut down and remove containers

**Enjoy!**

## Features

* Production, development and CI ready
* [Installation of extra Docker Compose services](docs/extra-services.md) with Symfony Flex
* Automatic HTTPS (in dev and in prod!)
* HTTP/2, HTTP/3 and [Preload](https://symfony.com/doc/current/web_link.html) support
* Built-in [Mercure](https://symfony.com/doc/current/mercure.html) hub
* [Vulcain](https://vulcain.rocks) support
* Native [XDebug](docs/xdebug.md) integration
* Just 2 services (PHP FPM and Caddy server)
* Super-readable configuration

## License

Symfony Docker is available under the MIT License.

## Credits

Symfony Docker Created by [Kévin Dunglas](https://dunglas.fr), co-maintained by [Maxime Helias](https://twitter.com/maxhelias) and sponsored by [Les-Tilleuls.coop](https://les-tilleuls.coop).
