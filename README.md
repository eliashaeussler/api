[![license](https://img.shields.io/github/license/eliashaeussler/api.svg)](LICENSE)
[![release](https://img.shields.io/github/release/eliashaeussler/api.svg)](https://github.com/eliashaeussler/api/releases/)


# elias-haeussler.de API

A PHP-based API to handle custom requests sent to [api.elias-haeussler.de](https://api.elias-haeussler.de).
The API serves different endpoints which can be accessed from various clients.


## Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
    + [User-specific environment](#user-specific-environment)
        - [`.env`](#env)
        - [`remote.env`](#remoteenv)
    + [PHP-CS-Fixer](#php-cs-fixer)
    + [Initialize database](#initialize-database)
- [Usage](#usage)
    + [Controller](#controller)
    + [Route mapping](#route-mapping)
    + [Example: `SlackController`](#example-slackcontroller)
- [Deployment](#deployment)
    + [Prerequisites](#prerequisites)
    + [Process deployment](#process-deployment)
- [Console](#console)
    + [`database:export`](#databaseexport)
    + [`database:migrate`](#databasemigrate)
    + [`database:schema`](#databaseschema)
    + [`logfile:clear`](#logfileclear)
- [License](#license)


## Features

- Start or end your lunch break in Slack using slash command `/lunch`
- Display Redmine issue information in Slack using slash command `/issue` or `/redmine issue`
- Notify your team members in Slack that you're ready for stand-up using slash command `/standup`


## Requirements

- Apache web server
- MySQL database
- Composer


## Installation

Clone the repository first:

```bash
git clone git@github.com:eliashaeussler/api.git
```

Install dependencies (`composer` needs to be installed for this):

```bash
composer install
```

Now point your web server/virtual host to the `public` directory of your cloned project
and you should be able to access the API.

### User-specific environment

#### [`.env`](.env.dist)

In order to access the database and set some configuration settings, it's necessary to create
a user-specific environment file:

```bash
cp .env.dist .env
```

Adapt the following environment variables by your needs:

- `DB_HOST`: **localhost**: Host where your MySQL database is served
- `DB_USER`: Database user to be used for database connections
- `DB_PASSWORD`: Password of the database user
- `DB_NAME`: Name of the database
- `DB_PORT`: **3306**: Port which listens for incoming database connections
- `DEBUG_EXCEPTIONS`: **0**: Set to `1` to debug exceptions.
  **Do not use this in production mode as it may print sensitive data.**
- `MINIMUM_LOG_LEVEL`: **1**: The minimum level for logs printed to the log file.
  See [`LogService`](src/classes/Service/LogService.php) class for available log levels.
- `ROUTE_BLACKLIST`: **favicon.ico**: Comma-separated list of routes to be blocked on request

#### [`remote.env`](remote.env.dist)

If you want to deploy the API to a remote server, you need to create a custom `remote.env` file:

```bash
cp remove.env.dist remote.env
```

Adapt the following environment variables by your needs:

- `TARGET_HOST`: Remote host in SSH notation (`user@host`) serving the production API
- `TARGET_PATH`: Path on the remote host to the deployed API
- `TARGET_PORT`: **22**: Port of the remote host listening for incoming SSH connections

### PHP-CS-Fixer

Set up the PHP-CS-Fixer by defining a custom `.php_cs` file. You can also use the distributed file:

```bash
cp .php_cs.dist .php_cs
```

### Initialize database

Before using the API, the database schema needs to be updated. This can be done by using the
included console:

```bash
./console.php database:schema update
```


## Usage

The API awaits requests in the form

```
<API_HOST>/<controller>/<parameters>
```

`GET`-Parameters like `?param1=a&param2=b` will be extracted and stored in the concrete controller class.

### Controller

For each `<controller>` an appropriate class `EliasHaeussler\Api\Controller\<Controller>Controller`
is expected and instantiated, if available.

For example, requests with `<API_HOST>/slack/<parameters>` will result in an instantiation of the
[`SlackController`](src/classes/Controller/SlackController.php) class.

Each controller must extend the [`BaseController`](src/classes/Controller/BaseController.php) class.

### Route mapping

The controller parameters, displayed as `<parameters>` in the above example, will be redirected to
the controller. Each controller is then responsible for defining route mappings for each available
parameter.

Route mappings are defined as separate class `EliasHaeussler\Api\Routing\<controller>\<route>Route`.
They must extend the class [`BaseRoute`](src/classes/Routing/BaseRoute.php) and then registered
in the appropriate controller within the class constant `ROUTE_MAPPINGS`.

### Example: `SlackController`

![Slack slash command `/lunch`](docs/assets/slack-lunch.gif)

The [`SlackController`](src/classes/Controller/SlackController.php) will be used when sending
requests in the form `<API_HOST>/slack/<parameters>`. It currently allows three route mappings:

- `authenticate`: Process user authentication at Slack using
  [`AuthenticateRoute`](src/classes/Routing/Slack/AuthenticateRoute.php) class
- `lunch`: Process a request for the Slack slash command `/lunch` using
  [`LunchCommandRoute`](src/classes/Routing/Slack/LunchCommandRoute.php) class
- `redmine`: Process a request for the Slack slash commands `/redmine` or `/issue` using
  [`RedmineCommandRoute`](src/classes/Routing/Slack/RedmineCommandRoute.php) class
- `standup`: Process a request for the Slack slash command `/standup` using
  [`StandupCommandRoute`](src/classes/Routing/Slack/StandupCommandRoute.php) class


## Deployment

The API includes a basic deployment of the project to a remote host. Before processing the deployment,
make sure your server supports the required environment (see [Requirements](#requirements)).
Additionally, both your local machine and your deployment target server must support these packages:

- `rsync` to transfer files to the remote target server
- `ssh` to process the further deployment on the remote target server
- `php` with at least version 7.1 on the remote target server to run API console commands

### Prerequisites

First, configure the deployment settings in the `remote.env` file of your local installation
(see [`remote.env`](#remoteenv)).

On the target server, make sure that the following files exist in the deployment target path:

- `<TARGET_PATH>/local/.env` containing the database credentials and API configurations.
  **Make sure that the value of `DEBUG_EXCEPTIONS` is set to `0`!**
- `<TARGET_PATH>/local/slack.env` is only necessary if you want to support requests for `SlackController`

Your web server/virtual host should point to `<TARGET_PATH>/release/public`. Additionally, it is
recommended to set the value of `MINIMUM_LOG_LEVEL` in your remote `.env` file to a value not
lower than `1`, otherwise all debug messages will be logged.

### Process deployment

Before starting the deployment, make sure your Git working directory is clean. Otherwise, the
deployment won't start due to security reasons.

The deployment process can be started as follows:

```bash
./sbin/deploy.sh
```

On each deployment, the script will rsync the build files to a `cache` folder inside your
remote target path. If all files are ready on the server, the `release` directory will be
overridden by the `cache` directory which is overlain by the `local` directory.

Note that on each deployment, the log files on the server will be removed.


## Console

The API ships with a [console](console.php) which provides useful commands to keep the API up-to-date.
The console is based on Symfony console, so all known commands such as `list` or `help` are available.

### `database:export`

This command allows you to export the currently used database. It prints the database dump to `stdin`
which allows you to save it to a SQL file.

```bash
./console.php database:export
```

### `database:migrate`

This command allows you to migrate legacy SQLite databases to the new MySQL database. It's useful to
execute the `database:schema` command after calling this one.

```bash
./console.php database:migrate <file>
```

### `database:schema`

This command allows you to maintain the database schema. It can be used to update the database schema
or drop unused components such as fields or tables.

```bash
./console.php database:schema update [-s|--schema <schema>]
./console.php database:schema drop [--fields] [--tables] [--dry-run] [--force] [-s|--schema <schema>]
```

### `logfile:clear`

This command allows you to clear old log files. It is useful to run this command in a specific interval
to remove old logs and clear disk space as the log files might exceed in its size in case a low
`MINIMUM_LOG_LEVEL` is set.

```bash
./console.php logfile:clear [--keep-current]
```


## License

[GNU General Public License v3.0](LICENSE)
