CREATE TABLE `user` (
    `userID` int(10) unsigned not NULL AUTO_INCREMENT,
    `username` varchar(100) NOT NULL,
    `password` varchar(100) NOT NULL,
    PRIMARY KEY (`userID`)
);