-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Vært: 127.0.0.1
-- Genereringstid: 09. 11 2016 kl. 13:20:51
-- Serverversion: 10.1.10-MariaDB
-- PHP-version: 7.0.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crud_produkter`
--

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `designere`
--

CREATE TABLE `designere` (
  `designer_id` tinyint(3) UNSIGNED NOT NULL,
  `designer_navn` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Data dump for tabellen `designere`
--

INSERT INTO `designere` (`designer_id`, `designer_navn`) VALUES
(1, 'Karl Rüdiger'),
(2, 'Hans J. Wegner'),
(3, 'Bruno Mathsson'),
(4, 'Morten Voss'),
(5, 'Kasper Salto');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `kategorier`
--

CREATE TABLE `kategorier` (
  `kategori_id` tinyint(3) UNSIGNED NOT NULL,
  `kategori_navn` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Data dump for tabellen `kategorier`
--

INSERT INTO `kategorier` (`kategori_id`, `kategori_navn`) VALUES
(1, 'Sofa'),
(2, 'Sofabord'),
(3, 'Spisebord'),
(4, 'Spisestol');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `produkter`
--

CREATE TABLE `produkter` (
  `produkt_id` mediumint(8) UNSIGNED NOT NULL,
  `produkt_navn` varchar(50) NOT NULL,
  `produkt_pris` decimal(8,2) NOT NULL,
  `produkt_design_aar` year(4) NOT NULL,
  `produkt_vare_nr` mediumint(8) UNSIGNED NOT NULL,
  `produkt_beskrivelse` text NOT NULL,
  `fk_designer_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `fk_kategori_id` tinyint(3) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `produkt_billeder`
--

CREATE TABLE `produkt_billeder` (
  `produkt_billede_id` mediumint(8) UNSIGNED NOT NULL,
  `produkt_billede_filnavn` varchar(50) NOT NULL,
  `produkt_billede_er_primaer` tinyint(1) UNSIGNED NOT NULL,
  `fk_produkt_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Begrænsninger for dumpede tabeller
--

--
-- Indeks for tabel `designere`
--
ALTER TABLE `designere`
  ADD PRIMARY KEY (`designer_id`);

--
-- Indeks for tabel `kategorier`
--
ALTER TABLE `kategorier`
  ADD PRIMARY KEY (`kategori_id`);

--
-- Indeks for tabel `produkter`
--
ALTER TABLE `produkter`
  ADD PRIMARY KEY (`produkt_id`),
  ADD KEY `fk_designer_id` (`fk_designer_id`),
  ADD KEY `fk_kategori_id` (`fk_kategori_id`);

--
-- Indeks for tabel `produkt_billeder`
--
ALTER TABLE `produkt_billeder`
  ADD PRIMARY KEY (`produkt_billede_id`),
  ADD KEY `fk_produkt_id` (`fk_produkt_id`);

--
-- Brug ikke AUTO_INCREMENT for slettede tabeller
--

--
-- Tilføj AUTO_INCREMENT i tabel `designere`
--
ALTER TABLE `designere`
  MODIFY `designer_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- Tilføj AUTO_INCREMENT i tabel `kategorier`
--
ALTER TABLE `kategorier`
  MODIFY `kategori_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- Tilføj AUTO_INCREMENT i tabel `produkter`
--
ALTER TABLE `produkter`
  MODIFY `produkt_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Tilføj AUTO_INCREMENT i tabel `produkt_billeder`
--
ALTER TABLE `produkt_billeder`
  MODIFY `produkt_billede_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Begrænsninger for dumpede tabeller
--

--
-- Begrænsninger for tabel `produkter`
--
ALTER TABLE `produkter`
  ADD CONSTRAINT `produkter_ibfk_1` FOREIGN KEY (`fk_designer_id`) REFERENCES `designere` (`designer_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `produkter_ibfk_2` FOREIGN KEY (`fk_kategori_id`) REFERENCES `kategorier` (`kategori_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Begrænsninger for tabel `produkt_billeder`
--
ALTER TABLE `produkt_billeder`
  ADD CONSTRAINT `produkt_billeder_ibfk_1` FOREIGN KEY (`fk_produkt_id`) REFERENCES `produkter` (`produkt_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
