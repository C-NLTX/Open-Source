-- This table definition is loaded and then executed when the OpenEMR interface's install button is clicked.
CREATE TABLE IF NOT EXISTS `jbpm_generic_callouts`(
    `id` INT(11)  PRIMARY KEY AUTO_INCREMENT NOT NULL
	,`date_entered` DATETIME
    ,`name` VARCHAR(255)
	,`description` VARCHAR(255)
	,`response_id` VARCHAR(255)
	,`response_status` VARCHAR(255)
);