# LTI 1.3 Prototype

> LTI 1.3 prototype implementation

## Installation with symfony cli (with PHP installed locally)

First, install [symfony installer](https://symfony.com/download).

Then, install dependencies with [composer](https://getcomposer.org/download/):
```bash
$ composer install
```

Then, start the symfony local web server: 
```bash
$ symfony server:start --port 8008
```

**Note**: the application will be accessible on [http://localhost:8008](http://localhost:8008)

You can then list the available endpoints:

```bash
$ bin/console debug:router
```

## Installation with docker (without PHP installed locally)

Install dependencies with [composer docker image](https://hub.docker.com/_/composer):
```bash
$ docker run --rm --interactive --tty \
    --volume $PWD:/app \
    composer install
```

**Note**: if you don't have PHP locally, you can use the [docker composer official image](https://hub.docker.com/_/composer) to proceed to the installlation.

Then start the  application with [docker-compose](https://docs.docker.com/compose/)

```bash
$ docker-compose up -d
```

**Note**: the application will be accessible on [http://localhost:8008](http://localhost:8008)

You can then list the available endpoints:

```bash
$ docker exec -it lti1p3_phpfpm bin/console debug:router
```


## Configuration

You can:
- configure the LTI settings in the file [config/lti.yaml](config/lti.yaml)
- configure the users settings in the file [config/users.yaml](config/users.yaml)