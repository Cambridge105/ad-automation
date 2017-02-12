
--
-- Database: `adverts`
--

-- --------------------------------------------------------

--
-- Table structure for table `metadata`
--

CREATE TABLE IF NOT EXISTS `metadata` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `advertiser` varchar(60) DEFAULT NULL,
  `cart` int(10) DEFAULT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `plays_per_day` int(2) NOT NULL DEFAULT '1',
  `length` int(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Advertisers' AUTO_INCREMENT=1 ;


--
-- Table structure for table `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `break_time` datetime NOT NULL,
  `ad_time` datetime NOT NULL,
  `cart` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


--
-- Dumping data for table `ADS_LOG`
--

INSERT INTO `ADS_LOG` (`ID`, `COUNT`, `TYPE`, `SOURCE`, `START_TIME`, `GRACE_TIME`, `CART_NUMBER`, `TIME_TYPE`, `POST_POINT`, `TRANS_TYPE`, `START_POINT`, `END_POINT`, `FADEUP_POINT`, `FADEUP_GAIN`, `FADEDOWN_POINT`, `FADEDOWN_GAIN`, `SEGUE_START_POINT`, `SEGUE_END_POINT`, `SEGUE_GAIN`, `DUCK_UP_GAIN`, `DUCK_DOWN_GAIN`, `COMMENT`, `LABEL`, `ORIGIN_USER`, `ORIGIN_DATETIME`, `LINK_EVENT_NAME`, `LINK_START_TIME`, `LINK_LENGTH`, `LINK_START_SLOP`, `LINK_END_SLOP`, `LINK_ID`, `LINK_EMBEDDED`, `EXT_START_TIME`, `EXT_LENGTH`, `EXT_CART_NAME`, `EXT_DATA`, `EXT_EVENT_ID`, `EXT_ANNC_TYPE`) VALUES
(1, 0, 0, 0, 0, 0, 60108, 0, 'N', 0, -1, -1, -1, -3000, -1, -3000, -1, -1, -3000, 0, 0, '', '', '', '0000-00-00 00:00:00', '', 0, 0, 0, 0, -1, 'N', '00:00:00', -1, '', '', '', ''),
(2, 1, 0, 0, 0, 0, 60507, 0, 'N', 0, -1, -1, -1, -3000, -1, -3000, -1, -1, -3000, 0, 0, '', '', '', '0000-00-00 00:00:00', '', 0, 0, 0, 0, -1, 'N', '00:00:00', -1, '', '', '', ''),
(3, 2, 0, 0, 0, 0, 60501, 0, 'N', 0, -1, -1, -1, -3000, -1, -3000, -1, -1, -3000, 0, 0, '', '', '', '0000-00-00 00:00:00', '', 0, 0, 0, 0, -1, 'N', '00:00:00', -1, '', '', '', ''),
(4, 3, 0, 0, 0, 0, 60513, 0, 'N', 0, -1, -1, -1, -3000, -1, -3000, -1, -1, -3000, 0, 0, '', '', '', '0000-00-00 00:00:00', '', 0, 0, 0, 0, -1, 'N', '00:00:00', -1, '', '', '', ''),
(5, 4, 0, 0, 0, 0, 60136, 0, 'N', 0, -1, -1, -1, -3000, -1, -3000, -1, -1, -3000, 0, 0, '', '', '', '0000-00-00 00:00:00', '', 0, 0, 0, 0, -1, 'N', '00:00:00', -1, '', '', '', '');
