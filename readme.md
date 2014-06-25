## Shopzoo
Data provider implementation for the [TaskReward] (/boyhagemann/taskreward) application.

### Requirements
- Laravel Homestead with Beanstalkd installed

### Install
* create a new database named `shopzoo`
* use `php artisan migrate` to migrate and the database
* add alias in the `Homestead.yml` file pointing to the public application folder.
* update the Windows hosts file (e.g. shopzoo.app 127.0.0.1)

### How to use
Navigate to the Shopzoo homepage to perform different tasks.

### Environment variables
There are a few environment variables that needs to be present:
* TRADETRACKER_USER : the client id
* TRADETRACKER_KEY  : the client secret key


