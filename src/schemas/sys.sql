# Scheduler tasks
CREATE TABLE `sys_scheduled_tasks` (
  `uid` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `task` VARCHAR(255) NOT NULL DEFAULT '',
  `arguments` MEDIUMTEXT,
  `scheduled_execution` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_execution_time` TIMESTAMP,
  `status` TINYINT UNSIGNED NOT NULL DEFAULT 0,

  PRIMARY KEY (`uid`)
);
