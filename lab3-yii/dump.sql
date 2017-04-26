-- phpMyAdmin SQL Dump
-- version 3.5.5
-- http://www.phpmyadmin.net
--
-- Хост: sql11.freesqldatabase.com
-- Время создания: Мар 25 2017 г., 17:08
-- Версия сервера: 5.5.53-0ubuntu0.14.04.1
-- Версия PHP: 5.3.28

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `sql11164673`
--

-- --------------------------------------------------------

--
-- Структура таблицы `answer`
--

CREATE TABLE IF NOT EXISTS `answer` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `id_user` int(4) NOT NULL,
  `id_question` int(4) NOT NULL,
  `message` text NOT NULL,
  `pub_date` date NOT NULL,
  `count_likes` int(4) DEFAULT NULL,
  `count_comments` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `id_question` (`id_question`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Структура таблицы `category_question`
--

CREATE TABLE IF NOT EXISTS `category_question` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `id_question` int(4) NOT NULL,
  `name` char(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_question` (`id_question`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39 ;

-- --------------------------------------------------------

--
-- Структура таблицы `comment`
--

CREATE TABLE IF NOT EXISTS `comment` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `id_user` int(4) NOT NULL,
  `id_type_msg` int(4) NOT NULL,
  `message` text NOT NULL,
  `pub_date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `id_type_msg` (`id_type_msg`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Структура таблицы `question`
--

CREATE TABLE IF NOT EXISTS `question` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `id_user` int(4) NOT NULL,
  `header` char(50) NOT NULL,
  `message` text NOT NULL,
  `pub_date` date NOT NULL,
  `count_views` int(11) DEFAULT NULL,
  `count_subscribers` int(11) DEFAULT NULL,
  `count_comments` int(11) DEFAULT NULL,
  `count_solutions` int(11) DEFAULT NULL,
  `count_answers` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Структура таблицы `solution`
--

CREATE TABLE IF NOT EXISTS `solution` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `id_user` int(4) NOT NULL,
  `id_question` int(4) NOT NULL,
  `message` text NOT NULL,
  `pub_date` date NOT NULL,
  `count_likes` int(4) DEFAULT NULL,
  `count_comments` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `id_question` (`id_question`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Структура таблицы `toster`
--

CREATE TABLE IF NOT EXISTS `toster` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `toster_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Структура таблицы `type_message`
--

CREATE TABLE IF NOT EXISTS `type_message` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `name` char(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Дамп данных таблицы `type_message`
--

INSERT INTO `type_message` (`id`, `name`) VALUES
(1, 'question'),
(2, 'solution'),
(3, 'answer'),
(4, 'comment_question'),
(5, 'comment_solution'),
(6, 'comment_answer');

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `id_type_msg` int(4) NOT NULL,
  `name` char(20) NOT NULL,
  `nickname` char(20) NOT NULL,
  `description` char(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_type_msg` (`id_type_msg`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=145 ;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `answer`
--
ALTER TABLE `answer`
  ADD CONSTRAINT `answer_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `answer_ibfk_2` FOREIGN KEY (`id_question`) REFERENCES `question` (`id`);

--
-- Ограничения внешнего ключа таблицы `category_question`
--
ALTER TABLE `category_question`
  ADD CONSTRAINT `category_question_ibfk_1` FOREIGN KEY (`id_question`) REFERENCES `question` (`id`);

--
-- Ограничения внешнего ключа таблицы `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`id_type_msg`) REFERENCES `type_message` (`id`);

--
-- Ограничения внешнего ключа таблицы `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `question_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`);

--
-- Ограничения внешнего ключа таблицы `solution`
--
ALTER TABLE `solution`
  ADD CONSTRAINT `solution_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `solution_ibfk_2` FOREIGN KEY (`id_question`) REFERENCES `question` (`id`);

--
-- Ограничения внешнего ключа таблицы `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`id_type_msg`) REFERENCES `type_message` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
