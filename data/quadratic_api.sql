  CREATE SCHEMA `quadratic` ;
  
  CREATE TABLE `quadratic`.`api_requests` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `remote_address` VARCHAR(45) NOT NULL,
  `token` VARCHAR(40) NOT NULL,
  `a_value` INT NOT NULL,
  `b_value` INT NOT NULL,
  `c_value` INT NOT NULL,
  `request_counter` INT NOT NULL DEFAULT 0,
  `date_requested` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `token_UNIQUE` (`token` ASC))
  ENGINE=InnoDB;
  
  CREATE TABLE `quadratic`.`api_responses` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `api_request_id` INT NOT NULL,
  `solution_one` VARCHAR(45) NOT NULL,
  `solution_two` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
  ENGINE=InnoDB;

