# Suomidigi

A Suomidigi Drupal 8 website.

## Environments

Env | Branch | Drush alias | URL
--- | ------ | ----------- | ---
development | * | - | http://suomidigi.fi.docker.amazee.io/
production | master | @master | TBD

## Requirements

You need to have these applications installed to operate on all environments:

- [Docker](https://github.com/druidfi/guidelines/blob/master/docs/docker.md)
- [Pygmy](https://github.com/druidfi/guidelines/blob/master/docs/pygmy.md)
- For the new person: Your SSH public key needs to be added to servers

## Setup a new local environment

By default we'll use Docker based environment.

- Change the hostname in `docker-compose.yml` file:
  E.g. `mysite.fi.docker.amazee.io` to `yoursite.fi.docker.amazee.io`

## Create and start the environment

For the first time (new project):

```
$ make new
```

And following times to create and start the environment:

```
$ make build-dev
```

Change these according of the state of your project.

## Login to Drupal container

This will log you inside the Drupal Docker container and in the `public` folder:

```
$ make shell
```

## Read more

- [Contrib modules included](https://github.com/druidfi/spell/blob/master/docs/contrib.md)
- [Project structure](https://github.com/druidfi/spell/blob/master/docs/structure.md)
- [Quality assurance](https://github.com/druidfi/spell/blob/master/docs/qa.md)
- [FAQ](https://github.com/druidfi/spell/blob/master/docs/faq.md)
