ALTER TABLE `oxattribute` ADD `LVICON` VARCHAR(255) NOT NULL AFTER `OXDISPLAYINBASKET`; 

INSERT INTO `oxattribute` (`OXID`, `OXSHOPID`, `OXTITLE`, `OXTITLE_1`, `OXTITLE_2`, `OXTITLE_3`, `OXPOS`, `OXTIMESTAMP`, `OXDISPLAYINBASKET`, `LVICON`) VALUES
('CompatibilityTypeWine', 'oxbaseshop', 'Kompatibilität Wine', 'Compatibility Wine', '', '', 6, '2015-05-09 13:39:40', 0, ''),
('CompatibilityTypeMac', 'oxbaseshop', 'Kompatibilität MacOSX', 'Compatibility MacOSX', '', '', 5, '2015-05-09 13:35:48', 0, ''),
('DRM', 'oxbaseshop', 'Kopierschutz (DRM)', 'Copy Protection (DRM)', '', '', 2, '2015-05-09 13:35:23', 0, ''),
('GameGenre', 'oxbaseshop', 'Genre', 'Genre', '', '', 0, '2015-05-09 13:31:45', 0, ''),
('GameLanguageInterface', 'oxbaseshop', 'Sprache Text', 'Language Interface', '', '', 9, '2015-05-09 14:52:32', 0, ''),
('GameLanguageSubtitles', 'oxbaseshop', 'Sprache Untertitel', 'Language Subtitles', '', '', 10, '2015-05-09 14:52:36', 0, ''),
('GameLanguageAudio', 'oxbaseshop', 'Sprache Ton', 'Language Audio', '', '', 8, '2015-05-09 14:52:29', 0, ''),
('ReleaseDate', 'oxbaseshop', 'Veröffentlichungsdatum', 'Release Date', '', '', 13, '2015-05-09 14:52:46', 0, ''),
('CompatibilityTypeLin', 'oxbaseshop', 'Kompatibilität Linux', 'Compatibility Linux', '', '', 4, '2015-05-09 13:35:36', 0, ''),
('GameType', 'oxbaseshop', 'Spieltyp', 'Game Type', '', '', 1, '2015-05-09 13:35:11', 0, ''),
('RecommendedAgeUsk', 'oxbaseshop', 'Altersempfehlung USK', 'Recommended Age USK', '', '', 11, '2015-05-09 14:52:40', 0, ''),
('RecommendedAgePegi', 'oxbaseshop', 'Altersempfehlung Pegi', 'Recommended Age Pegi', '', '', 12, '2015-05-09 14:52:43', 0, ''),
('CompatibilityTypeWin', 'oxbaseshop', 'Kompatibilität Windows', 'Compatibility Windows', '', '', 3, '2015-05-09 13:35:30', 0, ''),
('CompatibilityTypePOL', 'oxbaseshop', 'Kompatibilität PlayOnLinux', 'Compatibility PlayOnLinux', '', '', 7, '2015-05-09 14:52:26', 0, '');
