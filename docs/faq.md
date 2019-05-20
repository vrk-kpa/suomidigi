# FAQ

## How to access Amazee instances of this site?

First access your local container and from there use drush to connect:

```
$ make shell
$ drush @amazeeio.master ssh
```

## How to connect to local database?

Add your database to e.g. Sequel Pro with these instructions:

https://docs.amazee.io/local_docker_development/connect_to_mysql_from_external.html

## How are emails sent?

Emails are sent with SendGrid SMTP configuration.

## Mailhog

If you are looking for the email sent by you local visit http://mailhog.docker.amazee.io/
