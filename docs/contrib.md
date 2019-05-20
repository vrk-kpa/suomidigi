# Contrib modules included

These recommended modules are included in the `composer.json`, but you can remove them if not needed.

- [Admin Toolbar](https://www.drupal.org/project/admin_toolbar) - Improved Drupal Toolbar
- [CDN](https://www.drupal.org/project/cdn) - Easy CDN integration
- [GDPR](https://www.drupal.org/project/gdpr) - GPDR checklist and data sanitizers
- [Pathauto](https://www.drupal.org/project/pathauto) - Automated URL alias generating
- [Swift Mailer](https://www.drupal.org/project/swiftmailer) - Advanced mailer
- [System Status](https://www.drupal.org/project/system_status) - Lumturio monitoring

## Add or remove modules

First log into Drupal Docker container with:

```
$ make shell
$ cd ..
```

### Add a new module

Require the new module (e.g. Paragraphs) with:

```
$ composer require drupal/paragraphs
```

### Remove a new module

Remove the module (e.g. System status) with:

```
$ composer remove drupal/system_status
```
