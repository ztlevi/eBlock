-- phpMyAdmin SQL Dump
-- version 4.4.15.5
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 10, 2017 at 03:05 PM
-- Server version: 5.6.34-log
-- PHP Version: 7.0.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `e_guard`
--

-- --------------------------------------------------------

--
-- Table structure for table `block_category`
--

CREATE TABLE IF NOT EXISTS `block_category` (
  `Category` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `block_category`
--

INSERT INTO `block_category` (`Category`) VALUES
('Abortion'),
('Search Engines/Portals'),
('Spam'),
('Sports/Recreation'),
('Suspicious'),
('Uncategorized'),
('Violence/Hate/Racism'),
('Weapons'),
('Alcohol'),
('Audio/Video Clips'),
('Entertainment'),
('Hacking'),
('Newsgroups/Forums');

-- --------------------------------------------------------

--
-- Table structure for table `eguard_user`
--

CREATE TABLE IF NOT EXISTS `eguard_user` (
  `Username` varchar(50) NOT NULL,
  `Password` varchar(50) NOT NULL,
  `Email` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `eguard_user`
--

INSERT INTO `eguard_user` (`Username`, `Password`, `Email`) VALUES
('ztlevi', 'ztlevi', 'ztlevi1993@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `timer`
--

CREATE TABLE IF NOT EXISTS `timer` (
  `Limitation` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `website_black_list`
--

CREATE TABLE IF NOT EXISTS `website_black_list` (
  `URL` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `website_black_list`
--

INSERT INTO `website_black_list` (`URL`) VALUES
('www.facebook.com');

-- --------------------------------------------------------

--
-- Table structure for table `website_category`
--

CREATE TABLE IF NOT EXISTS `website_category` (
  `URL` varchar(50) NOT NULL,
  `Category` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `website_category`
--

INSERT INTO `website_category` (`URL`, `Category`) VALUES
('punchbaby.com', 'Adult/Mature Content'),
('www.abortionfacts.com', 'Abortion'),
('www.asahibeer.co.jp', 'Alcohol'),
('www.budweiser.com', 'Alcohol'),
('www.carabal.es', 'Alcohol'),
('www.carlsberg.com', 'Alcohol'),
('www.coors.com', 'Alcohol'),
('www.gynpages.com', 'Abortion'),
('www.heineken.com', 'Alcohol'),
('www.jackdaniels.com', 'Alcohol'),
('www.johnniewalker.com', 'Alcohol'),
('www.landoverbaptist.org', 'Adult/Mature Content'),
('www.paulaner.de', 'Alcohol'),
('www.tsingtao.com.cn', 'Alcohol'),
('www.bilibili.com', 'Entertainment'),
('www.bilibili.com', 'Audio/Video Clips'),
('www.bilibili.com', 'Entertainment'),
('www.bilibili.com', 'Audio/Video Clips'),
('www.bilibili.com', 'Entertainment'),
('www.bilibili.com', 'Audio/Video Clips'),
('www.bilibili.com', 'Entertainment'),
('www.bilibili.com', 'Audio/Video Clips'),
('www.bilibili.com', 'Entertainment'),
('www.bilibili.com', 'Audio/Video Clips'),
('www.bilibili.com', 'Entertainment'),
('www.bilibili.com', 'Audio/Video Clips'),
('http://www.heineken.com/', 'Alcohol'),
('stackoverflow.com', 'Newsgroups/Forums'),
('stackoverflow.com', 'Technology/Internet'),
('www.cnblogs.com', 'Technology/Internet'),
('www.w3school.com.cn', 'Informational'),
('www.w3school.com.cn', 'Technology/Internet'),
('php.net', 'Technology/Internet'),
('qa.helplib.com', 'Technology/Internet'),
('litemta.jimdo.com', 'Technology/Internet'),
('blog.wpjam.com', 'Technology/Internet'),
('blog.csdn.net', 'Technology/Internet'),
('blog.csdn.net', 'News/Media'),
('angli.me', 'Technology/Internet'),
('mail.google.com', 'Email'),
('www.umich.edu', 'Education'),
('accounts.google.com', 'Technology/Internet'),
('email.about.com', 'Reference'),
('pear.php.net', 'Technology/Internet'),
('www.solagirl.net', 'Technology/Internet'),
('outofmemory.cn', 'Informational'),
('outofmemory.cn', 'Technology/Internet'),
('www.dictionary.com', 'Reference'),
('www.fdshuaighauihugia.com', 'Suspicious'),
('www.aaaaaaaaa.com', 'Suspicious'),
('www.zhanqi.tv', 'Games'),
('www.acfun.tv', 'Entertainment'),
('bbs.ncar.cc', 'Search Engines/Portals'),
('tv.sohu.com', 'TV/Video Streams'),
('www.le.com', 'Technology/Internet'),
('v.qq.com', 'Audio/Video Clips'),
('v.qq.com', 'Mixed Content/Potentially Adult'),
('www.bing.com', 'Search Engines/Portals'),
('getbootstrap.com', 'Technology/Internet'),
('76km.wordpress.com', 'Technology/Internet'),
('www.360doc.com', 'Reference'),
('www.programgo.com', 'Technology/Internet'),
('wiki.jikexueyuan.com', 'Technology/Internet'),
('www.dilidili.com', 'Entertainment'),
('www.youku.com', 'Mixed Content/Potentially Adult'),
('www.youku.com', 'TV/Video Streams'),
('www.soku.com', 'Audio/Video Clips'),
('www.soku.com', 'Mixed Content/Potentially Adult'),
('v.youku.com', 'Audio/Video Clips'),
('v.youku.com', 'Mixed Content/Potentially Adult'),
('www.w3schools.com', 'Technology/Internet'),
('www.w3schools.com', 'Education'),
('www.devcurry.com', 'Technology/Internet'),
('developer.mozilla.org', 'Technology/Internet'),
('www.yisou.com', 'Search Engines/Portals'),
('developer.chrome.com', 'Technology/Internet'),
('campus.alibaba.com', 'Business/Economy'),
('leetcode.com', 'Technology/Internet'),
('shibboleth.umich.edu', 'Education'),
('www.umcu.org', 'Financial Services'),
('tryappservice.azure.com', 'Technology/Internet'),
('umdearborn.instructure.com', 'Education'),
('www.thefreedictionary.com', 'Reference'),
('www.wikipedia.org', 'Reference'),
('chrome.google.com', 'Technology/Internet'),
('www.merriam-webster.com', 'Reference'),
('www.umcu.org', 'Business/Economy'),
('www.youtube.com', 'Audio/Video Clips'),
('www.youtube.com', 'Mixed Content/Potentially Adult'),
('umich.edu', 'Education'),
('events.umich.edu', 'Education'),
('jingyan.baidu.com', 'Search Engines/Portals'),
('www.ask.com', 'Search Engines/Portals'),
('www.ichacha.net', 'Translation'),
('www.ichacha.net', 'Reference'),
('www.zaixian-fanyi.com', 'Translation'),
('web-sis.umd.umich.edu', 'Education'),
('forums.asp.net', 'Newsgroups/Forums'),
('forums.asp.net', 'Technology/Internet'),
('www.tutorialspoint.com', 'Technology/Internet'),
('www.tutorialspoint.com', 'Education'),
('programmers.stackexchange.com', 'Newsgroups/Forums'),
('programmers.stackexchange.com', 'Technology/Internet'),
('zh.wikipedia.org', 'Reference'),
('www.lucidchart.com', 'Office/Business Applications'),
('www.visual-paradigm.com', 'Technology/Internet'),
('abc.go.com', 'Entertainment'),
('dprtb.com', 'Suspicious'),
('dprtb.com', 'Web Ads/Analytics'),
('www.nba.com', 'Sports/Recreation'),
('productforums.google.com', 'Newsgroups/Forums'),
('productforums.google.com', 'Technology/Internet'),
('www.nfl.com', 'Sports/Recreation'),
('sitereview.bluecoat.com', 'Technology/Internet'),
('www.fhuaiwgheuai.com', 'Suspicious'),
('www.aaabsafds.com', 'Controlled Substances'),
('touch.facebook.com', 'Social Networking'),
('www.mamp.info', 'Technology/Internet'),
('www.google.com', 'Search Engines/Portals'),
('member.bilibili.com', 'Entertainment'),
('member.bilibili.com', 'Audio/Video Clips'),
('kochdavisjobs.com', 'Job Search/Careers'),
('www.dice.com', 'Job Search/Careers'),
('dnserrorassist.att.net', 'Search Engines/Portals'),
('www.u09upupoij.com', 'Malicious Outbound Data/Botnets'),
('www.344143dsfsdf.com', 'Suspicious'),
('www.8085tgdsg.com', 'Suspicious'),
('www.u0uiiuijkjk.com', 'Malicious Outbound Data/Botnets'),
('www.jo8uijiojojkl.com', 'Malicious Outbound Data/Botnets'),
('github.com', 'Office/Business Applications'),
('github.com', 'Technology/Internet'),
('www.huhohjkkljkjlkjlk.com', 'Malicious Outbound Data/Botnets'),
('www.huy8ijji.com', 'Suspicious'),
('www.224kjijiji.com', 'Suspicious'),
('www.32434jikjiji.com', 'Suspicious'),
('www.43243ijiji.com', 'Malicious Outbound Data/Botnets'),
('www.jij3443.com', 'Suspicious'),
('support.google.com', 'Technology/Internet'),
('www.baidu.com', 'Search Engines/Portals');

-- --------------------------------------------------------

--
-- Table structure for table `website_white_list`
--

CREATE TABLE IF NOT EXISTS `website_white_list` (
  `URL` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `website_white_list`
--

INSERT INTO `website_white_list` (`URL`) VALUES
('www.leetcode.com'),
('www.sina.com');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
