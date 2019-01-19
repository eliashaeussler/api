# User authentication
CREATE TABLE `slack_auth` (
  `user` VARCHAR(20) NOT NULL UNIQUE,
  `token` VARCHAR(255) NOT NULL DEFAULT '',
  `scope` VARCHAR(255) NOT NULL DEFAULT '',

  PRIMARY KEY (`user`)
);

# User data
CREATE TABLE `slack_userdata` (
  `user` VARCHAR(20) NOT NULL,
  `default_expiration` INT(11) NOT NULL DEFAULT 45
);
