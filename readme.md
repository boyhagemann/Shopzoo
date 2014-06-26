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

### Strategy
- [Cron: 5 minutes] Import campaign information for all campaigns.
- [Cron: daily] Import product feeds
- [Cron: 5 minutes] Export tasks

When campaign information is changed, an event is triggered that will update all existing task data.
This will recalculate the lead and sales rewards for each task that belongs to the updated campaign.
This way the product information stays in sync with the exported tasks.

### Environment variables
There are a few environment variables that needs to be present:
* TRADETRACKER_USER : the client id
* TRADETRACKER_KEY  : the client secret key


