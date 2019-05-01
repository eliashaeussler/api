# User authentication
CREATE TABLE `slack_auth` (
  `user` VARCHAR(20) NOT NULL UNIQUE,
  `token` VARCHAR(255) NOT NULL DEFAULT '',
  `scope` VARCHAR(255) NOT NULL DEFAULT '',

  PRIMARY KEY (`user`)
);

# User data
CREATE TABLE `slack_userdata` (
  `user` VARCHAR(20) NOT NULL UNIQUE,
  `default_expiration` INT(11) NOT NULL DEFAULT 45,

  PRIMARY KEY (`user`)
);

# Redmine API keys
CREATE TABLE `slack_redmine_api_keys` (
  `user` VARCHAR(20) NOT NULL UNIQUE,
  `api_key` VARCHAR(255) NOT NULL DEFAULT '',

  PRIMARY KEY (`user`)
);

# Spent beers
CREATE TABLE `slack_spent_beers` (
  `donor` VARCHAR(20) NOT NULL DEFAULT '',
  `receiver` VARCHAR(20) NOT NULL DEFAULT '',
  `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
