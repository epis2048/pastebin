CREATE TABLE `pastebin` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `poster` varchar(16) NOT NULL,
  `postText` longtext NOT NULL,
  `code` varchar(16) NOT NULL,
  `posted` datetime DEFAULT NULL,
  `expires` datetime DEFAULT NULL,
  `expiry_flag` varchar(8) NOT NULL DEFAULT 'm',
  PRIMARY KEY (`ID`),
  KEY `expires` (`expires`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
