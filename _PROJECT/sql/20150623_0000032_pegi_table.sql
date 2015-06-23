CREATE TABLE IF NOT EXISTS `lvpegi` (
  `OXID` char(32) COLLATE latin1_general_ci NOT NULL,
  `OXOBJECTID` char(32) COLLATE latin1_general_ci NOT NULL,
  `LVURN` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `LVGAMETITLE` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `LVRELEASEDATE` date NOT NULL,
  `LVWEBADDRESS` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `LVPLATFORM` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `LVGAMESPUBLISHER` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `LVBASEAGECATEGORY` int(11) NOT NULL,
  `LVVIOLENCE` varchar(3) COLLATE latin1_general_ci NOT NULL,
  `LVSEX` varchar(3) COLLATE latin1_general_ci NOT NULL,
  `LVDRUGS` varchar(3) COLLATE latin1_general_ci NOT NULL,
  `LVFEAR` varchar(3) COLLATE latin1_general_ci NOT NULL,
  `LVDISCRIMINATION` varchar(3) COLLATE latin1_general_ci NOT NULL,
  `LVBADLANGUAGE` varchar(3) COLLATE latin1_general_ci NOT NULL,
  `LVGAMBLING` varchar(3) COLLATE latin1_general_ci NOT NULL,
  `LVONLINEGAMEPLAY` varchar(3) COLLATE latin1_general_ci NOT NULL,
  `LVHORROR` varchar(3) COLLATE latin1_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

ALTER TABLE `lvpegi`
  ADD PRIMARY KEY (`OXID`),
  ADD KEY `OXOBJECTID` (`OXOBJECTID`),
  ADD KEY `LVURN` (`LVURN`),
  ADD KEY `LVGAMETITLE` (`LVGAMETITLE`),
  ADD KEY `LVRELEASEDATE` (`LVRELEASEDATE`);
