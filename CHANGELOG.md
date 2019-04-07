# Changelog

All notable changes will be documented in this file.
Version numbers are based on [Semantic Versioning](https://semver.org).


## [Unreleased]

### Updated

- Switch from MIT License to [GNU General Public License v3.0](LICENSE)
- Improve PHP-CS-Fixer configuration



## [4.1.0] - 2019-04-07

### Added

- Environment variable `SERVER_NAME`
- SlackController: Routing for `/standup` slash command using [`StandupCommandRoute`](src/classes/Routing/Slack/StandupCommandRoute.php) class

### Updated

- Do not build documentation on pre-commit as it produces too many failures
- Allow multiline texts as localizations in `l10n` XML files
- Remove `DataUtility` class as data strings are provided by [`LocalizationUtility`](src/classes/Utility/LocalizationUtility.php) class
- SlackController: Allow adding more data when building message attachment footer
- SlackController: Allow definition of custom configuration when building bot message

### Fixed

- Generate documentation on commit only if PHP files were committed
- Exclude `docs` directory from being processed with PHP-CS-Fixer



## [4.0.2] - 2019-03-17

### Fixed

- SlackController: Show authentication uri again if OAuth token was revoked



## [4.0.1] - 2019-03-17

### Added

- License
- Instructions for usage of the console



## [4.0.0] - 2019-03-17

### Added

- Possibility to get and user current version from Git
- Install Composer dependencies before creating DDEV containers
- Possibility to drop unused database fields and tables from console
- Cache Dotenv loaders and hide all environment variables if debugging is enabled
- Provide custom SymfonyStyle for console commands
- Localize texts using [`LocalizationUtility::localize()`](src/classes/Utility/LocalizationUtility.php)
- Store `GET` and `POST` parameters in [`BaseController`](src/classes/Controller/BaseController.php) class
- Set minimum PHP version in [`composer.json`](composer.json) file
- File-based log service with [`LogService::log()`](src/classes/Service/LogService.php)
- Log warning if request is not secured with HTTPS
- Route blacklist to block unwanted requests
- Support of PHP-CS-Fixer for code sniffing
- Git Pre-Commit hook file
- Set correct HTTP response code on failures
- Possibility to define remote server port in [`remote.env`](remote.env.dist)
- Documentation
- **Breaking:** SlackController: Request user's locale when sending API request
- SlackController: Allow users to set default expiration time when using `/lunch` command in Slack
- SlackController: Introduce `SlackMessage` class for styling of Slack messages
- SlackController: Show link for re-authentication if requested scope is missing
- SlackController: Add Redmine connector to post issue information

### Updated

- **Breaking:** Restructure source paths in order to place all source files in the `src` folder
- **Breaking:** Rename `production.env` to [`remote.env`](remote.env.dist)
- **Breaking:** Remove strict mode when updating database schema
- **Breaking:** Move `cURL` request to new [`ConnectionUtility`](src/classes/Utility/ConnectionUtility.php) class
- Store data strings in JSON files and read them using [`DataUtility`](src/classes/Utility/DataUtility.php) class
- Use shorter file names for Bash scripts
- Do not catch errors in console commands
- Store database dumps from production in new directory `src/db-assets`
- Do not require necessary packages as `require-dev` in [`composer.json`](composer.json)
- SlackController: Define required scopes separately for each route
- SlackController: Define authentication route as own Route class [`AuthenticateRoute`](src/classes/Routing/Slack/AuthenticateRoute.php)

### Fixed

- Set correct path for console when dumping database from production
- Set correct configuration for Xdebug in DDEV container
- Ensure local database dump folder exists before dumping database from production
- Make local database available from outside of DDEV container
- Print correct file name in database migration method
- Pass through dumped database result instead of just executing it
- Allow `GET` requests when using [`ConnectionUtility::sendRequest()`](src/classes/Utility/ConnectionUtility.php) method
- Check if selected controller can be instantiated before trying to instantiate it
- Check if route mapping is available for current route
- SlackController: Fix argument order when building Slack authentication uri
- SlackController: Check if user already exists in database when trying to add authentication data
- SlackController: Show link for re-authentication if Slack API throws `not_authed` error
- SlackController: Add missing keys to `slack_userdata` table



## [3.1.0] - 2019-01-19

### Added

- Console command to export/dump the current database
- Bash script to import production database into DDEV
- Deployment with bash script

### Updated

- Use Apache instead of nginx in DDEV container

### Fixed

- Suppress warnings and errors when getting latest Git commit from console



## [3.0.0] - 2019-01-07

### Added

- Debugging of exceptions in order to get the stacktrace
- Custom exception handler for debugging

### Updated

- **Breaking:** Use MySQL database instead of SQLite and provide database migration command
- Rename database command in console to be more generic
- Enable Xdebug in DDEV by default

### Fixed

- Load environment variables when running console



## [2.0.0] - 2019-01-06

### Added

- Integrate SQLite database using Doctrine DBAL as connection helper
- Console to update database schema (based on Symfony console)
- Local development with DDEV
- Possibility to set default values for environment variables

### Updated

- **Breaking:** Rewrite RoutingUtility as service class.
- Store controller-specific environment variables in separate files
- SlackController: Store authentication data in database

### Fixed

- Always create lower-cased `.env` file names
- Handle invalid class names when reading controller name
- SlackController: Allow exception objects to be passed to Slack bot message



## [1.0.0] - 2018-12-28

Initial version
