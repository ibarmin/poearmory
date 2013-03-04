SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


-- -----------------------------------------------------
-- Table `league`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `league` ;

CREATE  TABLE IF NOT EXISTS `league` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `class`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `class` ;

CREATE  TABLE IF NOT EXISTS `class` (
  `id` INT NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `account`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `account` ;

CREATE  TABLE IF NOT EXISTS `account` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `username_UNIQUE` (`username` ASC) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `character`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `character` ;

CREATE  TABLE IF NOT EXISTS `character` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `level` INT(4) NULL ,
  `league_id` INT NOT NULL ,
  `class_id` INT NOT NULL ,
  `account_id` INT NOT NULL ,
  `url` VARCHAR(255) NOT NULL ,
  `verified` TINYINT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) ,
  INDEX `fk_character_league_idx` (`league_id` ASC) ,
  INDEX `fk_character_class_idx` (`class_id` ASC) ,
  INDEX `fk_character_account_idx` (`account_id` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `item_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `item_type` ;

CREATE  TABLE IF NOT EXISTS `item_type` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `frametype` INT(4) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `frametype_UNIQUE` (`frametype` ASC) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `inventory`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `inventory` ;

CREATE  TABLE IF NOT EXISTS `inventory` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `x` TINYINT NULL ,
  `y` TINYINT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `item`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `item` ;

CREATE  TABLE IF NOT EXISTS `item` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `w` TINYINT NOT NULL ,
  `h` TINYINT NOT NULL ,
  `icon` VARCHAR(512) NOT NULL ,
  `type_line` VARCHAR(255) NOT NULL ,
  `descr_text` VARCHAR(255) NULL ,
  `colour` VARCHAR(2) NULL ,
  `hash` VARCHAR(128) NOT NULL ,
  `flavour` TEXT NULL ,
  `item_type_id` INT NOT NULL ,
  `inventory_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `hash_UNIQUE` (`hash` ASC) ,
  INDEX `fk_item_type_idx` (`item_type_id` ASC) ,
  INDEX `fk_item_inventory_idx` (`inventory_id` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `property`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `property` ;

CREATE  TABLE IF NOT EXISTS `property` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `values` TEXT NULL ,
  `item_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_property_item_idx` (`item_id` ASC) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `requirement`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `requirement` ;

CREATE  TABLE IF NOT EXISTS `requirement` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `values` TEXT NOT NULL ,
  `display_mode` TINYINT NOT NULL ,
  `item_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_requirements_item_idx` (`item_id` ASC) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `socket`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `socket` ;

CREATE  TABLE IF NOT EXISTS `socket` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `attr` VARCHAR(2) NOT NULL ,
  `parent_id` INT NOT NULL ,
  `item_id` INT NOT NULL ,
  `group` SMALLINT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_socketed_item_idx` (`parent_id` ASC) ,
  INDEX `fk_socket_gem_idx` (`item_id` ASC) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `character_inventory`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `character_inventory` ;

CREATE  TABLE IF NOT EXISTS `character_inventory` (
  `character_id` INT NOT NULL ,
  `inventory_id` INT NOT NULL ,
  `item_id` INT NOT NULL ,
  `equipped` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `active` TINYINT NOT NULL DEFAULT 0 ,
  `x` TINYINT NOT NULL ,
  `y` TINYINT NOT NULL ,
  INDEX `fk_character_inventory_item_idx` (`item_id` ASC) ,
  INDEX `fk_character_inventory_idx` (`inventory_id` ASC) ,
  INDEX `fk_inventory_character_idx` (`character_id` ASC) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `item_mod`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `item_mod` ;

CREATE  TABLE IF NOT EXISTS `item_mod` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` TEXT NULL ,
  `item_id` INT NOT NULL ,
  `implicit` TINYINT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_implicitMod_item_idx` (`item_id` ASC) )
ENGINE = MyISAM;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
