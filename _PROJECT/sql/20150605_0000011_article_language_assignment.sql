ALTER TABLE `oxarticles` ADD `LVLANGABBR` VARCHAR(3) NOT NULL AFTER `LVIMPORTCOMPLETE`;
ALTER TABLE `oxarticles` ADD INDEX(`LVLANGABBR`);

ALTER TABLE `oxarticles` ADD `LVAMZSALESRANK` INT NOT NULL AFTER `LVLANGABBR`;
