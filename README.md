# Suomidigi

A Suomidigi Drupal 9 website.

## Environments

| Env         | Branch | URL                          |
|-------------|--------|------------------------------|
| development | *      | https://suomidigi.docker.so/ |
| production  | dev    | https://testi.suomidigi.fi/  |
| production  | master | https://suomidigi.fi/        |

## Requirements

You need to have these applications installed to operate on all environments:

- [Docker](https://github.com/druidfi/guidelines/blob/master/docs/docker.md)
- [Stonehenge](https://github.com/druidfi/stonehenge)
- For the new person: access is granted via SUPO process

## Create and start the environment

For the first time:

```
make fresh
```

Ready! Now go to https://druid.docker.so/ to see your site.

## Login to Drupal container

This will log you inside the app container:

```
make shell
```
