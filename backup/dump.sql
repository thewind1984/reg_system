
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `surname` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `date_registered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('ACTIVE','SUSPEND','DELETED') NOT NULL DEFAULT 'ACTIVE',
  `date_deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `user` (`id`, `username`, `password`, `email`, `name`, `surname`, `phone`, `birthday`, `date_registered`, `status`, `date_deleted`) VALUES
(1, 'user1', 'd01978ad33a53128f2bbc74c8d52e4e2', 'user1@example.net', 'User', 'Super', '1020304099', '1980-02-01', '2017-11-05 13:49:20', 'ACTIVE', NULL),
(2, 'user2', 'd01978ad33a53128f2bbc74c8d52e4e2', 'user2@example.net', NULL, NULL, NULL, NULL, '2017-11-05 13:49:57', 'SUSPEND', NULL);
