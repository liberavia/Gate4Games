ALTER TABLE `oxattribute` ADD `LVICON` VARCHAR(255) NOT NULL AFTER `OXDISPLAYINBASKET`; 

INSERT INTO `oxattribute` (`OXID`, `OXSHOPID`, `OXTITLE`, `OXTITLE_1`, `OXTITLE_2`, `OXTITLE_3`, `OXPOS`, `OXTIMESTAMP`, `OXDISPLAYINBASKET`, `LVICON`) VALUES
('CompatibilityTypeWine', 'oxbaseshop', 'Kompatibilität Wine', 'Compatibility Wine', '', '', 6, '2015-05-09 13:39:40', 0, ''),
('CompatibilityTypeMac', 'oxbaseshop', 'Kompatibilität MacOSX', 'Compatibility MacOSX', '', '', 5, '2015-05-09 13:35:48', 0, ''),
('DRM', 'oxbaseshop', 'Kopierschutz (DRM)', 'Copy Protection (DRM)', '', '', 2, '2015-05-09 13:35:23', 0, ''),
('GameGenre', 'oxbaseshop', 'Genre', 'Genre', '', '', 0, '2015-05-09 13:31:45', 0, ''),
('GameLanguageInterface', 'oxbaseshop', 'Sprache Text', 'Language Interface', '', '', 8, '2015-05-09 13:39:54', 0, ''),
('GameLanguageSubtitles', 'oxbaseshop', 'Sprache Untertitel', 'Language Subtitles', '', '', 9, '2015-05-09 13:39:58', 0, ''),
('GameLanguageAudio', 'oxbaseshop', 'Sprache Ton', 'Language Audio', '', '', 7, '2015-05-09 13:39:50', 0, ''),
('ReleaseDate', 'oxbaseshop', 'Veröffentlichungsdatum', 'Release Date', '', '', 12, '2015-05-09 13:40:13', 0, ''),
('CompatibilityTypeLin', 'oxbaseshop', 'Kompatibilität Linux', 'Compatibility Linux', '', '', 4, '2015-05-09 13:35:36', 0, ''),
('GameType', 'oxbaseshop', 'Spieltyp', 'Game Type', '', '', 1, '2015-05-09 13:35:11', 0, ''),
('RecommendedAgeUsk', 'oxbaseshop', 'Altersempfehlung USK', 'Recommended Age USK', '', '', 10, '2015-05-09 13:40:03', 0, ''),
('RecommendedAgePegi', 'oxbaseshop', 'Altersempfehlung Pegi', 'Recommended Age Pegi', '', '', 11, '2015-05-09 13:40:09', 0, ''),
('CompatibilityTypeWin', 'oxbaseshop', 'Kompatibilität Windows', 'Compatibility Windows', '', '', 3, '2015-05-09 13:35:30', 0, '');

