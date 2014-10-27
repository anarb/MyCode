CREATE TABLE `db2691071-main`.`indexer` (
`id` INT NOT NULL AUTO_INCREMENT ,
`collection` VARCHAR( 4 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`words` VARCHAR( 120 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`frequency` INT( 5 ) NOT NULL ,
`documents` INT( 5 ) NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;