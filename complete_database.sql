-- =====================================================
-- SmartClinic Complete Database Setup
-- =====================================================
-- This file contains all database structure and data
-- Run this file to set up the complete database
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- =====================================================
-- PART 1: CREATE BASIC TABLES
-- =====================================================

-- Table: admin
DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `aemail` varchar(255) NOT NULL,
  `apassword` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`aemail`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Table: specialties
DROP TABLE IF EXISTS `specialties`;
CREATE TABLE IF NOT EXISTS `specialties` (
  `id` int(2) NOT NULL,
  `sname` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Table: doctor
DROP TABLE IF EXISTS `doctor`;
CREATE TABLE IF NOT EXISTS `doctor` (
  `docid` int(11) NOT NULL AUTO_INCREMENT,
  `docemail` varchar(255) DEFAULT NULL,
  `docname` varchar(255) DEFAULT NULL,
  `docpassword` varchar(255) DEFAULT NULL,
  `docnic` varchar(15) DEFAULT NULL,
  `doctel` varchar(15) DEFAULT NULL,
  `specialties` int(2) DEFAULT NULL,
  PRIMARY KEY (`docid`),
  KEY `specialties` (`specialties`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Table: patient
DROP TABLE IF EXISTS `patient`;
CREATE TABLE IF NOT EXISTS `patient` (
  `pid` int(11) NOT NULL AUTO_INCREMENT,
  `pemail` varchar(255) DEFAULT NULL,
  `pname` varchar(255) DEFAULT NULL,
  `ppassword` varchar(255) DEFAULT NULL,
  `paddress` varchar(255) DEFAULT NULL,
  `pnic` varchar(15) DEFAULT NULL,
  `pdob` date DEFAULT NULL,
  `gender` varchar(10) NOT NULL DEFAULT '',
  `ptel` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Table: schedule
DROP TABLE IF EXISTS `schedule`;
CREATE TABLE IF NOT EXISTS `schedule` (
  `scheduleid` int(11) NOT NULL AUTO_INCREMENT,
  `docid` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `scheduledate` date DEFAULT NULL,
  `scheduletime` time DEFAULT NULL,
  `nop` int(4) DEFAULT NULL,
  PRIMARY KEY (`scheduleid`),
  KEY `docid` (`docid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Table: appointment
DROP TABLE IF EXISTS `appointment`;
CREATE TABLE IF NOT EXISTS `appointment` (
  `appoid` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(10) DEFAULT NULL,
  `apponum` int(3) DEFAULT NULL,
  `scheduleid` int(10) DEFAULT NULL,
  `appodate` date DEFAULT NULL,
  PRIMARY KEY (`appoid`),
  KEY `pid` (`pid`),
  KEY `scheduleid` (`scheduleid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Table: webuser
DROP TABLE IF EXISTS `webuser`;
CREATE TABLE IF NOT EXISTS `webuser` (
  `email` varchar(255) NOT NULL,
  `usertype` char(1) DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Table: messages
DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(1) DEFAULT 0,
  `sender_type` varchar(10) DEFAULT 'patient',
  `receiver_type` varchar(10) DEFAULT 'doctor',
  PRIMARY KEY (`id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`),
  KEY `is_read` (`is_read`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1;

-- =====================================================
-- PART 2: CREATE ADDITIONAL TABLES
-- =====================================================

-- Table: admin_messages
DROP TABLE IF EXISTS `admin_messages`;
CREATE TABLE IF NOT EXISTS `admin_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `receiver_type` enum('doctor','patient') NOT NULL COMMENT 'نوع المستقبل: طبيب أو مريض',
  `receiver_id` int(11) NOT NULL COMMENT 'معرف المستقبل (docid أو pid)',
  `patient_id` int(11) DEFAULT NULL COMMENT 'معرف المريض إذا كان المستقبل مريض',
  `message` text NOT NULL COMMENT 'نص الرسالة',
  `is_read` tinyint(1) DEFAULT 0 COMMENT 'حالة القراءة: 0 = غير مقروء، 1 = مقروء',
  `sent_at` datetime DEFAULT current_timestamp() COMMENT 'وقت الإرسال',
  PRIMARY KEY (`id`),
  KEY `receiver_type` (`receiver_type`),
  KEY `receiver_id` (`receiver_id`),
  KEY `is_read` (`is_read`),
  KEY `patient_id` (`patient_id`),
  KEY `sent_at` (`sent_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='جدول رسائل الإدارة للأطباء والمرضى';

-- Table: chat_messages
DROP TABLE IF EXISTS `chat_messages`;
CREATE TABLE IF NOT EXISTS `chat_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctor_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `sender` enum('doctor','patient') NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `patient_id` (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: medicalrecords
DROP TABLE IF EXISTS `medicalrecords`;
CREATE TABLE IF NOT EXISTS `medicalrecords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `docid` int(11) NOT NULL,
  `record_date` datetime DEFAULT current_timestamp(),
  `diagnosis` text DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `weight` varchar(10) DEFAULT NULL,
  `height` varchar(10) DEFAULT NULL,
  `allergy` varchar(255) DEFAULT NULL,
  `surgical_history` text DEFAULT NULL,
  `diabetes` enum('Yes','No') DEFAULT 'No',
  `hypertension` enum('Yes','No') DEFAULT 'No',
  PRIMARY KEY (`id`),
  KEY `fk_patient` (`pid`),
  KEY `fk_doctor` (`docid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: message_read_status
DROP TABLE IF EXISTS `message_read_status`;
CREATE TABLE IF NOT EXISTS `message_read_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_id` int(11) NOT NULL,
  `user_type` enum('d','p') NOT NULL,
  `user_id` int(11) NOT NULL,
  `read_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_read` (`message_id`,`user_type`,`user_id`),
  KEY `message_id` (`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- PART 3: INSERT BASIC DATA
-- =====================================================

-- Insert Specialties
INSERT INTO `specialties` (`id`, `sname`) VALUES
(1, 'Accident and emergency medicine'),
(2, 'Paediatrics'),
(3, 'Clinical radiology'),
(4, 'Dental, oral and maxillo-facial surgery'),
(5, 'Cardiology'),
(6, 'Internal medicine'),
(7, 'General surgery'),
(8, 'Gastroenterology'),
(9, 'Endocrinology'),
(10, 'Nephrology'),
(11, 'Neuro-psychiatry'),
(12, 'Neurosurgery'),
(13, 'Obstetrics and gynecology'),
(14, 'Ophthalmology'),
(15, 'Orthopaedics'),
(16, 'Otorhinolaryngology');

-- Add Missing Specialties
INSERT INTO `specialties` (`id`, `sname`) 
SELECT 17, 'Dermatology' WHERE NOT EXISTS (SELECT 1 FROM specialties WHERE sname = 'Dermatology' OR id = 17);

INSERT INTO `specialties` (`id`, `sname`) 
SELECT 18, 'Neurology' WHERE NOT EXISTS (SELECT 1 FROM specialties WHERE sname = 'Neurology' OR id = 18);

INSERT INTO `specialties` (`id`, `sname`) 
SELECT 19, 'Psychiatry' WHERE NOT EXISTS (SELECT 1 FROM specialties WHERE sname = 'Psychiatry' OR id = 19);

INSERT INTO `specialties` (`id`, `sname`) 
SELECT 20, 'Oncology' WHERE NOT EXISTS (SELECT 1 FROM specialties WHERE sname = 'Oncology' OR id = 20);

INSERT INTO `specialties` (`id`, `sname`) 
SELECT 21, 'Respiratory' WHERE NOT EXISTS (SELECT 1 FROM specialties WHERE sname = 'Respiratory' OR id = 21);

INSERT INTO `specialties` (`id`, `sname`) 
SELECT 22, 'Infectious Diseases' WHERE NOT EXISTS (SELECT 1 FROM specialties WHERE sname = 'Infectious Diseases' OR id = 22);

INSERT INTO `specialties` (`id`, `sname`) 
SELECT 23, 'Genetic Disorders' WHERE NOT EXISTS (SELECT 1 FROM specialties WHERE sname = 'Genetic Disorders' OR id = 23);

INSERT INTO `specialties` (`id`, `sname`) 
SELECT 24, 'Urology' WHERE NOT EXISTS (SELECT 1 FROM specialties WHERE sname = 'Urology' OR id = 24);

-- Insert Admin
INSERT INTO `admin` (`aemail`, `apassword`) VALUES
('admin@gmail.com', '123'),
('admin1@gmail.com', '123456'),
('admin2@gmail.com', '123456'),
('admin3@gmail.com', '123456'),
('admin4@gmail.com', '123456'),
('admin5@gmail.com', '123456');

-- Insert Basic Doctor
INSERT INTO `doctor` (`docid`, `docemail`, `docname`, `docpassword`, `docnic`, `doctel`, `specialties`) VALUES
(1, 'doctor@gmail.com', 'Test Doctor', '123', '0123456789', '07812345677', 1);

-- Insert Additional Doctors (100 doctors)
INSERT INTO `doctor` (`docid`, `docemail`, `docname`, `docpassword`, `docnic`, `doctel`, `specialties`) VALUES
(2,'ahmed.ali.doc@gmail.com','Ahmed Ali','123456','1998012902','0778879991',3),
(3,'omar.alkurdi.doc@gmail.com','Omar AlKurdi','123456','1996228714','0789558127',8),
(4,'sara.alkhatib.doc@gmail.com','Sara AlKhatib','123456','1991103700','0786725529',12),
(5,'lina.almasri.doc@gmail.com','Lina AlMasri','123456','1990727315','0773549272',5),
(6,'mohammad.nour.doc@gmail.com','Mohammad Nour','123456','2001666787','0780825943',1),
(7,'yazan.alqawasmi.doc@gmail.com','Yazan AlQawasmi','123456','1999290616','0791121072',14),
(8,'layla.salem.doc@gmail.com','Layla Salem','123456','1993176703','0773824065',7),
(9,'hussain.alqawasmi.doc@gmail.com','Hussain AlQawasmi','123456','2007722446','0788719664',2),
(10,'rania.hassan.doc@gmail.com','Rania Hassan','123456','1999993708','0779162590',11),
(11,'laith.altarawneh.doc@gmail.com','Laith AlTarawneh','123456','1993492638','0774084446',4),
(12,'yasmeen.khaled.doc@gmail.com','Yasmeen Khaled','123456','1991947247','0779273169',15),
(13,'anas.altarawneh.doc@gmail.com','Anas AlTarawneh','123456','2001452825','0797727880',9),
(14,'nourhan.omar.doc@gmail.com','Nourhan Omar','123456','2007428854','0792953862',16),
(15,'rami.alhusseini.doc@gmail.com','Rami AlHusseini','123456','2008848381','0788918664',6),
(16,'fadi.ammar.doc@gmail.com','Fadi Ammar','123456','2006696424','0791214365',10),
(17,'saif.alhusseini.doc@gmail.com','Saif AlHusseini','123456','2009854615','0775557681',13),
(18,'khaled.hamdan.doc@gmail.com','Khaled Hamdan','123456','1995775797','0779126182',3),
(19,'hassan.almasri.doc@gmail.com','Hassan AlMasri','123456','2002699836','0785556318',8),
(20,'maram.faisal.doc@gmail.com','Maram Faisal','123456','2006057261','0779357597',5),
(21,'yousef.almasri.doc@gmail.com','Yousef AlMasri','123456','1994625188','0794605936',1),
(22,'farah.haj.doc@gmail.com','Farah Haj','123456','1990005663','0789191496',14),
(23,'ibrahim.alazzam.doc@gmail.com','Ibrahim AlAzzam','123456','1998427385','0785760421',7),
(24,'noor.alzain.doc@gmail.com','Noor AlZain','123456','1995149862','0790411082',2),
(25,'basel.alazzam.doc@gmail.com','Basel AlAzzam','123456','2000556147','0773472501',11),
(26,'dalia.latif.doc@gmail.com','Dalia Latif','123456','1992964050','0771499812',4),
(27,'jihad.alaryan.doc@gmail.com','Jihad AlAryan','123456','1997583888','0791902264',15),
(28,'reem.jawad.doc@gmail.com','Reem Jawad','123456','1993331433','0794625297',9),
(29,'alaa.alaryan.doc@gmail.com','Alaa AlAryan','123456','1994609430','0771564750',16),
(30,'hana.ayoub.doc@gmail.com','Hana Ayoub','123456','2003736233','0793839726',6),
(31,'ahmed.alzoubi.doc@gmail.com','Ahmed AlZoubi','123456','1992252233','0786463320',10),
(32,'mayad.ahmed.doc@gmail.com','Mayad Ahmed','123456','1990985387','0793530979',13),
(33,'mohammad.alzoubi.doc@gmail.com','Mohammad AlZoubi','123456','1992256652','0784671777',3),
(34,'lina.abdallah.doc@gmail.com','Lina Abdallah','123456','1992733217','0777096861',8),
(35,'yazan.alqadi.doc@gmail.com','Yazan AlQadi','123456','1991023788','0778684525',5),
(36,'amal.qasim.doc@gmail.com','Amal Qasim','123456','1993123810','0791709238',1),
(37,'hussain.alqadi.doc@gmail.com','Hussain AlQadi','123456','1991871614','0793007267',14),
(38,'samar.hussein.doc@gmail.com','Samar Hussein','123456','2008645192','0781340679',7),
(39,'mahmoud.alnimer.doc@gmail.com','Mahmoud AlNimer','123456','1990808864','0772923711',2),
(40,'nadina.jaber.doc@gmail.com','Nadina Jaber','123456','1998571149','0774099255',11),
(41,'tariq.alnimer.doc@gmail.com','Tariq AlNimer','123456','1998669395','0779593009',4),
(42,'bayan.fouad.doc@gmail.com','Bayan Fouad','123456','2005372715','0770143943',15),
(43,'saeed.alnajjar.doc@gmail.com','Saeed AlNajjar','123456','1998259032','0772706518',9),
(44,'saja.alharbi.doc@gmail.com','Saja AlHarbi','123456','2004438098','0775533391',16),
(45,'fares.alnajjar.doc@gmail.com','Fares AlNajjar','123456','2000324004','0779501979',6),
(46,'hanan.saleh.doc@gmail.com','Hanan Saleh','123456','2007397385','0780999323',10),
(47,'laith.almansi.doc@gmail.com','Laith AlMansi','123456','2009193743','0784858053',13),
(48,'mona.abuomar.doc@gmail.com','Mona AbuOmar','123456','2008034559','0787753432',3),
(49,'anas.almansi.doc@gmail.com','Anas AlMansi','123456','1994760868','0791123494',8),
(50,'dina.alzoubi.doc@gmail.com','Dina AlZoubi','123456','1990182025','0786393104',5),
(51,'rami.alomari.doc@gmail.com','Rami AlOmari','123456','2004813893','0797684446',1),
(52,'amal.shatti.doc@gmail.com','Amal Shatti','123456','1996721122','0772987641',14),
(53,'saif.alomari.doc@gmail.com','Saif AlOmari','123456','1991827585','0776713042',7),
(54,'ruwayda.omar.doc@gmail.com','Ruwayda Omar','123456','1997269332','0780184624',2),
(55,'hassan.alrahahleh.doc@gmail.com','Hassan AlRahahleh','123456','1999077532','0788175050',11),
(56,'tasneem.saed.doc@gmail.com','Tasneem Saed','123456','1996055143','0788073272',4),
(57,'yousef.alrahahleh.doc@gmail.com','Yousef AlRahahleh','123456','1997074134','0773249620',15),
(58,'sawsan.qatar.doc@gmail.com','Sawsan Qatar','123456','1999268515','0788047829',9),
(59,'ibrahim.alqaraan.doc@gmail.com','Ibrahim AlQaraan','123456','1998699165','0796361332',16),
(60,'noura.shami.doc@gmail.com','Noura Shami','123456','2002522321','0771486681',6),
(61,'basel.alqaraan.doc@gmail.com','Basel AlQaraan','123456','2001838958','0786299355',10),
(62,'salma.said.doc@gmail.com','Salma Said','123456','1999435408','0786626766',13),
(63,'jihad.almomani.doc@gmail.com','Jihad AlMomani','123456','1997705913','0777675378',3),
(64,'fatima.khouri.doc@gmail.com','Fatima Khouri','123456','1995417614','0776579423',8),
(65,'alaa.almomani.doc@gmail.com','Alaa AlMomani','123456','2002875878','0774256600',5),
(66,'manar.abbas.doc@gmail.com','Manar Abbas','123456','1993165606','0782078812',1),
(67,'ahmed.alrawashdeh.doc@gmail.com','Ahmed AlRawashdeh','123456','1998435793','0798493293',14),
(68,'maimouna.hasan.doc@gmail.com','Maimouna Hasan','123456','1990741897','0780611983',7),
(69,'mohammad.alrawashdeh.doc@gmail.com','Mohammad AlRawashdeh','123456','2009120545','0788738098',2),
(70,'suhad.abed.doc@gmail.com','Suhad Abed','123456','1991918749','0782928282',11),
(71,'yazan.alnimer.doc@gmail.com','Yazan AlNimer','123456','1991653039','0790842180',4),
(72,'marwa.samir.doc@gmail.com','Marwa Samir','123456','1999084641','0790736360',15),
(73,'hussain.alnimer.doc@gmail.com','Hussain AlNimer','123456','1999173406','0798200982',9),
(74,'rawan.adel.doc@gmail.com','Rawan Adel','123456','2009650932','0789689199',16),
(75,'mahmoud.alfayez.doc@gmail.com','Mahmoud AlFayez','123456','1993277057','0780214272',6),
(76,'shorouk.alani.doc@gmail.com','Shorouk AlAni','123456','1990398144','0781835808',10),
(77,'tariq.alfayez.doc@gmail.com','Tariq AlFayez','123456','1996359588','0787851889',13),
(78,'maysa.alsharif.doc@gmail.com','Maysa AlSharif','123456','2006851557','0778770271',3),
(79,'saeed.alrawashdeh.doc@gmail.com','Saeed AlRawashdeh','123456','1992642561','0797193676',8),
(80,'hana.gaber.doc@gmail.com','Hana Gaber','123456','1991182370','0775725061',5),
(81,'fares.alrawashdeh.doc@gmail.com','Fares AlRawashdeh','123456','1990180991','0796396616',1),
(82,'mais.jodeh.doc@gmail.com','Mais Jodeh','123456','1999941605','0777840081',14),
(83,'laith.alnajdawi.doc@gmail.com','Laith AlNajdawi','123456','1998329132','0778145676',7),
(84,'dalia.kayed.doc@gmail.com','Dalia Kayed','123456','2006675911','0781655843',2),
(85,'anas.alnajdawi.doc@gmail.com','Anas AlNajdawi','123456','1990478472','0782687154',11),
(86,'saja.alawi.doc@gmail.com','Saja AlAwi','123456','1997506123','0791238441',4),
(87,'rami.aljaberi.doc@gmail.com','Rami AlJaberi','123456','2006169806','0780559815',15),
(88,'nour.hamdan.doc@gmail.com','Nour Hamdan','123456','1992257966','0773224066',9),
(89,'saif.aljaberi.doc@gmail.com','Saif AlJaberi','123456','1995659615','0782316409',16),
(90,'rania.samir.doc@gmail.com','Rania Samir','123456','1997984892','0796946603',6),
(91,'hassan.alnatsheh.doc@gmail.com','Hassan AlNatsheh','123456','2001951022','0785392153',10),
(92,'dalal.mouath.doc@gmail.com','Dalal Mouath','123456','2002150952','0775866085',13),
(93,'yousef.alnatsheh.doc@gmail.com','Yousef AlNatsheh','123456','1991901180','0799786849',3),
(94,'lina.ayyash.doc@gmail.com','Lina Ayyash','123456','2001739155','0790313349',8),
(95,'ibrahim.alkhashman.doc@gmail.com','Ibrahim AlKhashman','123456','1994376328','0775221114',5),
(96,'tamara.assi.doc@gmail.com','Tamara Assi','123456','2005316849','0770076616',1),
(97,'basel.alkhashman.doc@gmail.com','Basel AlKhashman','123456','2008661330','0779463874',14),
(98,'hala.abunaim.doc@gmail.com','Hala AbuNaim','123456','1990708168','0782233376',7),
(99,'jihad.alghazawi.doc@gmail.com','Jihad AlGhazawi','123456','2005595241','0794228199',2),
(100,'sohaib.faleh.doc@gmail.com','Sohaib Faleh','123456','1992011874','0777757256',11),
(101,'alaa.alghazawi.doc@gmail.com','Alaa AlGhazawi','123456','1999852973','0778733266',4);

-- Insert Patients (200 patients)
INSERT INTO `patient` (`pid`, `pemail`, `pname`, `ppassword`, `paddress`, `pnic`, `pdob`, `gender`, `ptel`) VALUES
(1,'ahmedalkhatib@gmail.com','Ahmed Alkhatib','123456','Amman','2007480023','1995-07-29','Male','0787475863'),
(2,'mohammadalkhatib@gmail.com','Mohammad Alkhatib','123456','Zarqa','2009655435','1997-02-18','Male','0794527081'),
(3,'omaralhamouri@gmail.com','Omar Alhamouri','123456','Irbid','2005639554','2002-04-29','Male','0789020849'),
(4,'khaledalhamouri@gmail.com','Khaled Alhamouri','123456','Salt','2000670708','2001-06-09','Male','0797477456'),
(5,'yazanalqawasmi@gmail.com','Yazan Alqawasmi','123456','Aqaba','2009181831','2003-03-26','Male','0786536261'),
(6,'hussainalqawasmi@gmail.com','Hussain Alqawasmi','123456','Jarash','2004848052','1999-09-30','Male','0792027445'),
(7,'mahmoudalsaadi@gmail.com','Mahmoud Alsaadi','123456','Madaba','2001916759','2003-06-04','Male','0781308321'),
(8,'tariqalsaadi@gmail.com','Tariq Alsaadi','123456','Amman','2005589321','2002-12-17','Male','0791737823'),
(9,'saeedalbdour@gmail.com','Saeed Albdour','123456','Zarqa','2000020597','2002-08-11','Male','0789677242'),
(10,'faresalbdour@gmail.com','Fares Albdour','123456','Irbid','2004817841','1996-10-17','Male','0799805621'),
(11,'laithaltarawneh@gmail.com','Laith Altarawneh','123456','Salt','2001797823','1996-01-01','Male','0783517996'),
(12,'anasaltarawneh@gmail.com','Anas Altarawneh','123456','Aqaba','2008407849','1996-07-02','Male','0792107353'),
(13,'ramialhusseini@gmail.com','Rami Alhusseini','123456','Jarash','2006962686','2002-02-18','Male','0789844208'),
(14,'saifalhusseini@gmail.com','Saif Alhusseini','123456','Madaba','2000572192','2002-01-02','Male','0795840524'),
(15,'hassanalmasri@gmail.com','Hassan Almasri','123456','Amman','2002479841','1999-11-03','Male','0789674049'),
(16,'yousefalmasri@gmail.com','Yousef Almasri','123456','Zarqa','2008209276','1999-04-04','Male','0798650853'),
(17,'ibrahimalazzam@gmail.com','Ibrahim Alazzam','123456','Irbid','2000219313','1995-08-08','Male','0785381885'),
(18,'baselalazzam@gmail.com','Basel Alazzam','123456','Salt','2008372187','2000-04-17','Male','0798227551'),
(19,'jihadalaryan@gmail.com','Jihad Alaryan','123456','Aqaba','2006391634','1999-07-28','Male','0781839053'),
(20,'alaaalaryan@gmail.com','Alaa Alaryan','123456','Jarash','2009559550','2002-02-09','Male','0794483140'),
(21,'saraalzoubi@gmail.com','Sara Alzoubi','123456','Madaba','2005487573','1994-01-12','Female','0787297957'),
(22,'nooralzoubi@gmail.com','Noor Alzoubi','123456','Amman','2009470760','2000-05-06','Female','0799702206'),
(23,'layanalsharif@gmail.com','Layan Alsharif','123456','Zarqa','2009208658','2002-06-10','Female','0789831726'),
(24,'ranaalsharif@gmail.com','Rana Alsharif','123456','Irbid','2004603317','1993-12-03','Female','0791790867'),
(25,'raghadalqaisi@gmail.com','Raghad Alqaisi','123456','Salt','2007716109','1998-04-09','Female','0783943982'),
(26,'malakalqaisi@gmail.com','Malak Alqaisi','123456','Aqaba','2005941758','2000-12-13','Female','0790910325'),
(27,'maishaldamin@gmail.com','Mais Aldamin','123456','Jarash','2005505923','1997-04-14','Female','0781926839'),
(28,'hadeelaldamin@gmail.com','Hadeel Aldamin','123456','Madaba','2009324192','1996-07-06','Female','0792556526'),
(29,'danaalmahairat@gmail.com','Dana Almahairat','123456','Amman','2002995837','1999-05-09','Female','0789709960'),
(30,'leilaalmahairat@gmail.com','Leila Almahairat','123456','Zarqa','2007293457','2000-07-03','Female','0799988174'),
(31,'hanaalbushnaq@gmail.com','Hana Albushnaq','123456','Irbid','2002714863','1997-10-12','Female','0783995156'),
(32,'abeeralbushnaq@gmail.com','Abeer Albushnaq','123456','Salt','2008329940','1999-03-13','Female','0793703432'),
(33,'nasreenalsalman@gmail.com','Nasreen Alsalman','123456','Aqaba','2004691108','1993-09-01','Female','0780692939'),
(34,'janaalsalman@gmail.com','Jana Alsalman','123456','Jarash','2008367557','2000-05-14','Female','0790468935'),
(35,'mahaalfayez@gmail.com','Maha Alfayez','123456','Madaba','2000925073','1997-07-04','Female','0785974519'),
(36,'ayaalfayez@gmail.com','Aya Alfayez','123456','Amman','2005047067','1998-03-19','Female','0792164045'),
(37,'rahmaalghazawi@gmail.com','Rahma Alghazawi','123456','Zarqa','2008368125','1996-10-22','Female','0786386704'),
(38,'halaalghazawi@gmail.com','Hala Alghazawi','123456','Irbid','2006960070','1996-03-30','Female','0793106708'),
(39,'tamaraalsawaeer@gmail.com','Tamara Alsawaeer','123456','Salt','2002436563','1995-11-27','Female','0783979752'),
(40,'linaalsawaeer@gmail.com','Lina Alsawaeer','123456','Aqaba','2005057590','1997-07-13','Female','0790013873'),
(41,'salmaalkhashman@gmail.com','Salma Alkhashman','123456','Jarash','2009252220','1998-01-21','Female','0784307818'),
(42,'reemalkhashman@gmail.com','Reem Alkhashman','123456','Madaba','2008796429','1997-02-26','Female','0795749231'),
(43,'nouralkhalidi@gmail.com','Nour Alkhalidi','123456','Amman','2004212953','2001-12-12','Female','0783394374'),
(44,'laylaalkhalidi@gmail.com','Layla Alkhalidi','123456','Zarqa','2003706877','1998-10-16','Female','0799672815'),
(45,'mariamalqawasmi@gmail.com','Mariam Alqawasmi','123456','Irbid','2009731311','1998-06-11','Female','0782411115'),
(46,'hibaalqawasmi@gmail.com','Hiba Alqawasmi','123456','Salt','2000420741','1997-06-23','Female','0791664451'),
(47,'daliaalkayyali@gmail.com','Dalia Alkayyali','123456','Aqaba','2006069903','1995-03-18','Female','0786077638'),
(48,'ritaalkayyali@gmail.com','Rita Alkayyali','123456','Jarash','2008917170','1996-10-20','Female','0792241045'),
(49,'nadaalrawashdeh@gmail.com','Nada Alrawashdeh','123456','Madaba','2002238586','1996-11-06','Female','0787167133'),
(50,'samaralrawashdeh@gmail.com','Samar Alrawashdeh','123456','Amman','2009653951','2000-04-03','Female','0796757702'),
(51,'ahmedalnimer@gmail.com','Ahmed Alnimer','123456','Zarqa','2002434154','1999-08-07','Male','0789220791'),
(52,'mohammadalnimer@gmail.com','Mohammad Alnimer','123456','Irbid','2000367705','1997-01-15','Male','0791908691'),
(53,'omeralhaddad@gmail.com','Omar Alhaddad','123456','Salt','2004525341','1997-04-04','Male','0786819913'),
(54,'khaledalhaddad@gmail.com','Khaled Alhaddad','123456','Aqaba','2007188132','1994-09-07','Male','0796812680'),
(55,'yazanalqadi@gmail.com','Yazan Alqadi','123456','Jarash','2002097756','2003-02-21','Male','0788328646'),
(56,'hussainalqadi@gmail.com','Hussain Alqadi','123456','Madaba','2007810604','1999-04-13','Male','0795261807'),
(57,'mahmoudalbashiti@gmail.com','Mahmoud Albashiti','123456','Amman','2009942813','1998-03-14','Male','0783756909'),
(58,'tariqalbashiti@gmail.com','Tariq Albashiti','123456','Zarqa','2003759700','1994-07-01','Male','0790424153'),
(59,'saeedalnajjar@gmail.com','Saeed Alnajjar','123456','Irbid','2003397626','1994-02-01','Male','0786876696'),
(60,'faresalnajjar@gmail.com','Fares Alnajjar','123456','Salt','2006885797','1998-04-28','Male','0797438274'),
(61,'laithalmansi@gmail.com','Laith Almansi','123456','Aqaba','2001247376','1996-08-05','Male','0786209633'),
(62,'anasalmansi@gmail.com','Anas Almansi','123456','Jarash','2006640042','1993-06-24','Male','0795377657'),
(63,'ramialomari@gmail.com','Rami Alomari','123456','Madaba','2005250903','1996-01-10','Male','0788928092'),
(64,'saifalomari@gmail.com','Saif Alomari','123456','Amman','2001066350','1994-04-15','Male','0797807452'),
(65,'hassanalatawneh@gmail.com','Hassan Alatawneh','123456','Zarqa','2004136496','1997-02-10','Male','0787979382'),
(66,'yousefalatawneh@gmail.com','Yousef Alatawneh','123456','Irbid','2005635140','1994-07-19','Male','0797890587'),
(67,'ibrahimalrahahleh@gmail.com','Ibrahim Alrahahleh','123456','Salt','2000517264','1993-11-14','Male','0789595674'),
(68,'baselalrahahleh@gmail.com','Basel Alrahahleh','123456','Aqaba','2003972897','1999-01-04','Male','0793328990'),
(69,'jihadalqaraan@gmail.com','Jihad Alqaraan','123456','Jarash','2007349027','1997-01-06','Male','0781298934'),
(70,'laithalqaraan@gmail.com','Laith Alqaraan','123456','Madaba','2003271133','1994-09-09','Male','0799521351'),
(71,'anasalajarmeh@gmail.com','Anas Alajarmeh','123456','Amman','2000211702','1995-02-27','Male','0781628347'),
(72,'ramialajarmeh@gmail.com','Rami Alajarmeh','123456','Zarqa','2003692408','1995-05-08','Male','0792522858'),
(73,'saifalmomani@gmail.com','Saif Almomani','123456','Irbid','2003746513','2001-09-01','Male','0785620058'),
(74,'hassanalmomani@gmail.com','Hassan Almomani','123456','Salt','2008650213','1996-04-30','Male','0793688245'),
(75,'yousefalsalman@gmail.com','Yousef Alsalman','123456','Aqaba','2005352075','1996-03-12','Male','0786950971'),
(76,'ibrahimaltabbah@gmail.com','Ibrahim Altabbah','123456','Jarash','2008774012','1995-06-26','Male','0799474179'),
(77,'baselalomari@gmail.com','Basel Alomari','123456','Madaba','2000841866','1996-11-30','Male','0786619356'),
(78,'jihadalatawneh@gmail.com','Jihad Alatawneh','123456','Amman','2007037740','1995-01-24','Male','0797779741'),
(79,'alaaalrahahleh@gmail.com','Alaa Alrahahleh','123456','Zarqa','2006309884','1996-08-23','Male','0787676428'),
(80,'saraalqaraan@gmail.com','Sara Alqaraan','123456','Irbid','2003422637','2002-10-27','Female','0790587570'),
(81,'nooralajarmeh@gmail.com','Noor Alajarmeh','123456','Salt','2008829051','1996-01-19','Female','0786125321'),
(82,'layanalmomani@gmail.com','Layan Almomani','123456','Aqaba','2007634684','2000-02-22','Female','0791965878'),
(83,'ranaalghazawi@gmail.com','Rana Alghazawi','123456','Jarash','2005245630','1999-11-09','Female','0784609904'),
(84,'raghadalsawaeer@gmail.com','Raghad Alsawaeer','123456','Madaba','2005141449','1993-10-04','Female','0796394877'),
(85,'malakalkhashman@gmail.com','Malak Alkhashman','123456','Amman','2006335013','1997-03-03','Female','0787060504'),
(86,'ayaaladwan@gmail.com','Aya Aladwan','123456','Zarqa','2001078194','1994-12-13','Female','0795453619'),
(87,'rahmaalrawashdeh@gmail.com','Rahma Alrawashdeh','123456','Irbid','2005876585','1999-06-08','Female','0786243379'),
(88,'halaalrawashdeh@gmail.com','Hala Alrawashdeh','123456','Salt','2003075336','1994-06-23','Female','0797110580'),
(89,'tamaraalnimer@gmail.com','Tamara Alnimer','123456','Aqaba','2006211081','1998-01-04','Female','0784719654'),
(90,'linaalnimer@gmail.com','Lina Alnimer','123456','Jarash','2008272574','1994-05-02','Female','0791005110'),
(91,'salmaalhaddad@gmail.com','Salma Alhaddad','123456','Madaba','2008755018','1999-10-13','Female','0788107131'),
(92,'reemalhaddad@gmail.com','Reem Alhaddad','123456','Amman','2005461497','1994-07-18','Female','0797585053'),
(93,'nouralqadi@gmail.com','Nour Alqadi','123456','Zarqa','2007942738','2002-02-09','Female','0786006884'),
(94,'laylaalqadi@gmail.com','Layla Alqadi','123456','Irbid','2003490168','1997-03-12','Female','0798570011'),
(95,'mariamalbashiti@gmail.com','Mariam Albashiti','123456','Salt','2007741604','2003-05-10','Female','0780169959'),
(96,'hibaalbashiti@gmail.com','Hiba Albashiti','123456','Aqaba','2002371622','2000-09-13','Female','0798304369'),
(97,'daliaalnajjar@gmail.com','Dalia Alnajjar','123456','Jarash','2003856744','1994-08-02','Female','0784000117'),
(98,'ritaalnajjar@gmail.com','Rita Alnajjar','123456','Madaba','2009635400','1993-10-09','Female','0795216514'),
(99,'nadaalmansi@gmail.com','Nada Almansi','123456','Amman','2009335150','1998-06-25','Female','0785254840'),
(100,'samaralmansi@gmail.com','Samar Almansi','123456','Zarqa','2009824027','2001-08-27','Female','0798632439'),
(101,'ahmedalhaj@gmail.com','Ahmed AlHaj','123456','Amman','1998904064','1995-05-24','Male','0782451214'),
(102,'mohammedqasem@gmail.com','Mohammed Qasem','123456','Zarqa','1993333882','2001-12-26','Male','0796921841'),
(103,'omarhamdan@gmail.com','Omar Hamdan','123456','Irbid','1992927972','1993-09-20','Male','0780283645'),
(104,'khaledalmasri@gmail.com','Khaled AlMasri','123456','Salt','1992661672','1997-03-17','Male','0793418710'),
(105,'yazanalkurdi@gmail.com','Yazan AlKurdi','123456','Aqaba','1991808510','1996-07-16','Male','0784149759'),
(106,'hussainalattar@gmail.com','Hussain AlAttar','123456','Jarash','1996629271','2000-10-06','Male','0795047554'),
(107,'mahmoudalsabah@gmail.com','Mahmoud AlSabah','123456','Madaba','1993331604','1999-04-02','Male','0780756413'),
(108,'tariqalhilali@gmail.com','Tariq AlHilali','123456','Amman','1993753410','1994-11-09','Male','0797306925'),
(109,'saeedalmurad@gmail.com','Saeed AlMurad','123456','Zarqa','1995038613','1996-09-07','Male','0783375102'),
(110,'faresalhabash@gmail.com','Fares AlHabash','123456','Irbid','1993822984','1998-02-16','Male','0794594902'),
(111,'laithalhussein@gmail.com','Laith AlHussein','123456','Salt','1998282821','1994-07-19','Male','0789682666'),
(112,'anasalhaddad@gmail.com','Anas AlHaddad','123456','Aqaba','1990029473','2000-05-25','Male','0799015760'),
(113,'ramialfawwaz@gmail.com','Rami AlFawwaz','123456','Jarash','1998930805','2003-01-15','Male','0789762948'),
(114,'saifalmansour@gmail.com','Saif AlMansour','123456','Madaba','1996412868','2001-03-10','Male','0798906503'),
(115,'hassanalsharif@gmail.com','Hassan AlSharif','123456','Amman','1999568119','1997-09-15','Male','0785586272'),
(116,'yousefalghanem@gmail.com','Yousef AlGhanem','123456','Zarqa','1998127873','1998-03-04','Male','0799636195'),
(117,'ibrahimalzayed@gmail.com','Ibrahim AlZayed','123456','Irbid','1990625624','1994-01-22','Male','0780541291'),
(118,'baselalqarni@gmail.com','Basel AlQarni','123456','Salt','1993645993','1995-04-30','Male','0799599529'),
(119,'jihadalsaeed@gmail.com','Jihad AlSaeed','123456','Aqaba','1992537700','1997-11-09','Male','0788976225'),
(120,'alaaalkhatib@gmail.com','Alaa AlKhatib','123456','Jarash','1993792692','2001-08-28','Male','0796128016'),
(121,'saraaljamal@gmail.com','Sara AlJamal','123456','Madaba','1992774625','1998-12-06','Female','0783482196'),
(122,'nooralhanan@gmail.com','Noor AlHanan','123456','Amman','1997397968','2001-01-19','Female','0795908600'),
(123,'layanalhamwi@gmail.com','Layan AlHamwi','123456','Zarqa','1999962017','1994-06-23','Female','0782212972'),
(124,'ranaalramahi@gmail.com','Rana AlRamahi','123456','Irbid','1990285208','1999-03-01','Female','0791678466'),
(125,'raghadaljaberi@gmail.com','Raghad AlJaberi','123456','Salt','1999010424','1996-07-21','Female','0785331814'),
(126,'malakalhajali@gmail.com','Malak AlHajAli','123456','Aqaba','1992634284','1999-11-11','Female','0799147933'),
(127,'maishalabadi@gmail.com','Mais AlAbadi','123456','Jarash','1990669738','2000-04-08','Female','0788237685'),
(128,'hadeelalawadi@gmail.com','Hadeel AlAwadi','123456','Madaba','1998375727','1998-02-19','Female','0794922443'),
(129,'danaalrashdan@gmail.com','Dana AlRashdan','123456','Amman','1997501515','1997-03-03','Female','0783105782'),
(130,'leilaalfaouri@gmail.com','Leila AlFaouri','123456','Zarqa','1993610408','2000-10-27','Female','0792906971'),
(131,'hanaalhalabi@gmail.com','Hana AlHalabi','123456','Irbid','1990994997','1995-12-18','Female','0786005545'),
(132,'abeeralqaisi@gmail.com','Abeer AlQaisi','123456','Salt','1992115586','1993-02-14','Female','0797024793'),
(133,'nasreenalhajri@gmail.com','Nasreen AlHajri','123456','Aqaba','1995855757','2001-09-27','Female','0787743181'),
(134,'janaalrabadi@gmail.com','Jana AlRabadi','123456','Jarash','1996252453','2002-03-28','Female','0798325731'),
(135,'mahaalomari@gmail.com','Maha AlOmari','123456','Madaba','1999166301','1997-05-18','Female','0787797483'),
(136,'ayaalnassar@gmail.com','Aya AlNassar','123456','Amman','1993463130','1996-01-06','Female','0795854312'),
(137,'rahmaalhariri@gmail.com','Rahma AlHariri','123456','Zarqa','1991757139','1998-11-16','Female','0786731307'),
(138,'halaalnajjar@gmail.com','Hala AlNajjar','123456','Irbid','1992553849','1997-08-22','Female','0798382559'),
(139,'tamaraalshami@gmail.com','Tamara AlShami','123456','Salt','1995141659','1999-04-26','Female','0782370402'),
(140,'linaalrashid@gmail.com','Lina AlRashid','123456','Aqaba','1997855148','2000-06-15','Female','0799062484'),
(141,'salmaalabdullah@gmail.com','Salma AlAbdullah','123456','Jarash','1993900335','1993-04-09','Female','0786387336'),
(142,'reemalhaj@gmail.com','Reem AlHaj','123456','Madaba','1995068275','1997-10-03','Female','0790558996'),
(143,'nouralyousef@gmail.com','Nour AlYousef','123456','Amman','1993392152','1998-02-23','Female','0784489005'),
(144,'laylaalnaser@gmail.com','Layla AlNaser','123456','Zarqa','1999740200','1999-06-07','Female','0796912057'),
(145,'mariamalnajdawi@gmail.com','Mariam AlNajdawi','123456','Irbid','1991605198','1996-10-05','Female','0783197862'),
(146,'hibaalarafat@gmail.com','Hiba AlArafat','123456','Salt','1993222760','1997-12-29','Female','0798314559'),
(147,'daliaalhamidi@gmail.com','Dalia AlHamidi','123456','Aqaba','1994460087','1994-03-13','Female','0782722964'),
(148,'ritaalshaheen@gmail.com','Rita AlShaheen','123456','Jarash','1996640448','2001-07-18','Female','0794640408'),
(149,'nadaalhusban@gmail.com','Nada AlHusban','123456','Madaba','1997815009','1995-10-22','Female','0780663177'),
(150,'samaralabsi@gmail.com','Samar AlAbsi','123456','Amman','1997756034','2001-01-14','Female','0796270472'),
(151,'ahmedalrami@gmail.com','Ahmed AlRami','123456','Zarqa','1992085514','1994-09-08','Male','0781486930'),
(152,'mohammedaljbour@gmail.com','Mohammed AlJbour','123456','Irbid','1990122447','1996-03-29','Male','0796815591'),
(153,'omaralhusayni@gmail.com','Omar AlHusayni','123456','Salt','1998581882','1997-10-03','Male','0789982555'),
(154,'khaledalmaani@gmail.com','Khaled AlMaani','123456','Aqaba','1997771241','1999-01-27','Male','0799615522'),
(155,'yazanalhaddad@gmail.com','Yazan AlHaddad','123456','Jarash','1990020074','1995-11-01','Male','0786129340'),
(156,'hussainalfares@gmail.com','Hussain AlFares','123456','Madaba','1991159403','1998-03-09','Male','0799007092'),
(157,'mahmoudalhajjaj@gmail.com','Mahmoud AlHajjaj','123456','Amman','1992924697','1999-12-24','Male','0782211843'),
(158,'tariqalsharari@gmail.com','Tariq AlSharari','123456','Zarqa','1993646640','1997-10-12','Male','0797879469'),
(159,'saeedalrawashdeh@gmail.com','Saeed AlRawashdeh','123456','Irbid','1991279812','1995-06-15','Male','0785518026'),
(160,'faresalatiyat@gmail.com','Fares AlAtiyat','123456','Salt','1995093436','1994-08-18','Male','0795385691'),
(161,'laithalyamani@gmail.com','Laith AlYamani','123456','Aqaba','1996642147','1997-09-04','Male','0782680299'),
(162,'anasalhawamdeh@gmail.com','Anas AlHawamdeh','123456','Jarash','1990194573','1996-01-11','Male','0792269971'),
(163,'ramialfayez@gmail.com','Rami AlFayez','123456','Madaba','1998640937','1998-10-21','Male','0784346735'),
(164,'saifalnatsheh@gmail.com','Saif AlNatsheh','123456','Amman','1993454565','1996-02-05','Male','0799916150'),
(165,'hassanalhunaiti@gmail.com','Hassan AlHunaiti','123456','Zarqa','1990490203','1999-07-14','Male','0785770596'),
(166,'yousefalyacoub@gmail.com','Yousef AlYacoub','123456','Irbid','1993045798','1995-01-20','Male','0799144166'),
(167,'ibrahimalagha@gmail.com','Ibrahim AlAgah','123456','Salt','1995058073','1998-05-03','Male','0784943642'),
(168,'baselalqaralleh@gmail.com','Basel AlQaralleh','123456','Aqaba','1993703168','1997-02-16','Male','0794057975'),
(169,'jihadalhabahbeh@gmail.com','Jihad AlHabahbeh','123456','Jarash','1990655283','2000-01-07','Male','0783975950'),
(170,'laithaljawarneh@gmail.com','Laith AlJawarneh','123456','Madaba','1990314385','1994-05-20','Male','0791349443'),
(171,'anasalrabee@gmail.com','Anas AlRabee','123456','Amman','1991302551','1994-03-07','Male','0784062012'),
(172,'ramialshwayat@gmail.com','Rami AlShwayat','123456','Zarqa','1990071973','1999-08-11','Male','0797731710'),
(173,'saifalkhatatbeh@gmail.com','Saif AlKhatatbeh','123456','Irbid','1996506653','1993-07-19','Male','0780834818'),
(174,'hassanalmasalmeh@gmail.com','Hassan AlMasalmeh','123456','Salt','1996020448','1998-08-09','Male','0795973188'),
(175,'yousefalzeid@gmail.com','Yousef AlZeid','123456','Aqaba','1999762453','1999-12-28','Male','0789981630'),
(176,'ibrahimalghanayem@gmail.com','Ibrahim AlGhanayem','123456','Jarash','1990359177','1994-11-11','Male','0793152070'),
(177,'baselalqadi@gmail.com','Basel AlQadi','123456','Madaba','1993140916','1997-02-26','Male','0789596194'),
(178,'jihadalnajadat@gmail.com','Jihad AlNajadat','123456','Amman','1998243561','1996-04-07','Male','0792218043'),
(179,'alaaalyousef@gmail.com','Alaa AlYousef','123456','Zarqa','1996449958','1993-03-04','Male','0784056278'),
(180,'saraalnaser@gmail.com','Sara AlNaser','123456','Irbid','1995581304','2001-11-15','Female','0790686217'),
(181,'nooralrabi@gmail.com','Noor AlRabi','123456','Salt','1992094687','2000-06-12','Female','0781127684'),
(182,'layanalkatib@gmail.com','Layan AlKatib','123456','Aqaba','1994320781','1994-03-22','Female','0796159970'),
(183,'ranaalhusayni@gmail.com','Rana AlHusayni','123456','Jarash','1994405955','1995-09-09','Female','0783107334'),
(184,'raghadalrahman@gmail.com','Raghad AlRahman','123456','Madaba','1997919072','1997-12-01','Female','0791852038'),
(185,'malakaladwan@gmail.com','Malak AlAdwan','123456','Amman','1991842479','1999-01-13','Female','0780954288'),
(186,'ayaaljarrar@gmail.com','Aya AlJarrar','123456','Zarqa','1990262951','2000-02-29','Female','0796903381'),
(187,'rahmaalshaer@gmail.com','Rahma AlShaer','123456','Irbid','1998419955','2001-04-03','Female','0787251324'),
(188,'halaalnatsheh@gmail.com','Hala AlNatsheh','123456','Salt','1993643778','2002-09-22','Female','0797582912'),
(189,'tamaraalsharari@gmail.com','Tamara AlSharari','123456','Aqaba','1999612044','1997-10-10','Female','0783859144'),
(190,'linaalhabbash@gmail.com','Lina AlHabbash','123456','Jarash','1998134172','2000-03-07','Female','0793182467'),
(191,'salmaalabbadi@gmail.com','Salma AlAbbadi','123456','Madaba','1999660225','1998-08-24','Female','0787023149'),
(192,'reemalameer@gmail.com','Reem AlAmeer','123456','Amman','1991258944','1997-01-28','Female','0799544740'),
(193,'nouralabed@gmail.com','Nour AlAbed','123456','Zarqa','1997812907','2001-10-06','Female','0782156955'),
(194,'laylaalghanem@gmail.com','Layla AlGhanem','123456','Irbid','1994602701','1996-04-04','Female','0797416382'),
(195,'mariamalnajjari@gmail.com','Mariam AlNajjari','123456','Salt','1998670937','1998-06-29','Female','0781009173'),
(196,'hibaalrawahneh@gmail.com','Hiba AlRawahneh','123456','Aqaba','1994302611','1999-05-25','Female','0797350025'),
(197,'daliaalqudah@gmail.com','Dalia AlQudah','123456','Jarash','1993449005','1997-12-17','Female','0786508133'),
(198,'ritaalnassar@gmail.com','Rita AlNassar','123456','Madaba','1999538031','1993-10-03','Female','0793534482'),
(199,'nadaalrabadi@gmail.com','Nada AlRabadi','123456','Amman','1992104077','2002-12-02','Female','0786108270'),
(200,'samaralmubarak@gmail.com','Samar AlMubarak','123456','Zarqa','1995702024','2002-04-16','Female','0795908600');

-- Insert Web Users
INSERT INTO `webuser` (`email`, `usertype`) VALUES
('admin@gmail.com', 'a'),
('doctor@gmail.com', 'd'),
('admin1@gmail.com', 'a'),
('admin2@gmail.com', 'a'),
('admin3@gmail.com', 'a'),
('admin4@gmail.com', 'a'),
('admin5@gmail.com', 'a'),
('ahmed.ali.doc@gmail.com', 'd'),
('omar.alkurdi.doc@gmail.com', 'd'),
('sara.alkhatib.doc@gmail.com', 'd'),
('lina.almasri.doc@gmail.com', 'd'),
('mohammad.nour.doc@gmail.com', 'd'),
('yazan.alqawasmi.doc@gmail.com', 'd'),
('layla.salem.doc@gmail.com', 'd'),
('hussain.alqawasmi.doc@gmail.com', 'd'),
('rania.hassan.doc@gmail.com', 'd'),
('laith.altarawneh.doc@gmail.com', 'd'),
('yasmeen.khaled.doc@gmail.com', 'd'),
('anas.altarawneh.doc@gmail.com', 'd'),
('nourhan.omar.doc@gmail.com', 'd'),
('rami.alhusseini.doc@gmail.com', 'd'),
('fadi.ammar.doc@gmail.com', 'd'),
('saif.alhusseini.doc@gmail.com', 'd'),
('khaled.hamdan.doc@gmail.com', 'd'),
('hassan.almasri.doc@gmail.com', 'd'),
('maram.faisal.doc@gmail.com', 'd'),
('yousef.almasri.doc@gmail.com', 'd'),
('farah.haj.doc@gmail.com', 'd'),
('ibrahim.alazzam.doc@gmail.com', 'd'),
('noor.alzain.doc@gmail.com', 'd'),
('basel.alazzam.doc@gmail.com', 'd'),
('dalia.latif.doc@gmail.com', 'd'),
('jihad.alaryan.doc@gmail.com', 'd'),
('reem.jawad.doc@gmail.com', 'd'),
('alaa.alaryan.doc@gmail.com', 'd'),
('hana.ayoub.doc@gmail.com', 'd'),
('ahmed.alzoubi.doc@gmail.com', 'd'),
('mayad.ahmed.doc@gmail.com', 'd'),
('mohammad.alzoubi.doc@gmail.com', 'd'),
('lina.abdallah.doc@gmail.com', 'd'),
('yazan.alqadi.doc@gmail.com', 'd'),
('amal.qasim.doc@gmail.com', 'd'),
('hussain.alqadi.doc@gmail.com', 'd'),
('samar.hussein.doc@gmail.com', 'd'),
('mahmoud.alnimer.doc@gmail.com', 'd'),
('nadina.jaber.doc@gmail.com', 'd'),
('tariq.alnimer.doc@gmail.com', 'd'),
('bayan.fouad.doc@gmail.com', 'd'),
('saeed.alnajjar.doc@gmail.com', 'd'),
('saja.alharbi.doc@gmail.com', 'd'),
('fares.alnajjar.doc@gmail.com', 'd'),
('hanan.saleh.doc@gmail.com', 'd'),
('laith.almansi.doc@gmail.com', 'd'),
('mona.abuomar.doc@gmail.com', 'd'),
('anas.almansi.doc@gmail.com', 'd'),
('dina.alzoubi.doc@gmail.com', 'd'),
('rami.alomari.doc@gmail.com', 'd'),
('amal.shatti.doc@gmail.com', 'd'),
('saif.alomari.doc@gmail.com', 'd'),
('ruwayda.omar.doc@gmail.com', 'd'),
('hassan.alrahahleh.doc@gmail.com', 'd'),
('tasneem.saed.doc@gmail.com', 'd'),
('yousef.alrahahleh.doc@gmail.com', 'd'),
('sawsan.qatar.doc@gmail.com', 'd'),
('ibrahim.alqaraan.doc@gmail.com', 'd'),
('noura.shami.doc@gmail.com', 'd'),
('basel.alqaraan.doc@gmail.com', 'd'),
('salma.said.doc@gmail.com', 'd'),
('jihad.almomani.doc@gmail.com', 'd'),
('fatima.khouri.doc@gmail.com', 'd'),
('alaa.almomani.doc@gmail.com', 'd'),
('manar.abbas.doc@gmail.com', 'd'),
('ahmed.alrawashdeh.doc@gmail.com', 'd'),
('maimouna.hasan.doc@gmail.com', 'd'),
('mohammad.alrawashdeh.doc@gmail.com', 'd'),
('suhad.abed.doc@gmail.com', 'd'),
('yazan.alnimer.doc@gmail.com', 'd'),
('marwa.samir.doc@gmail.com', 'd'),
('hussain.alnimer.doc@gmail.com', 'd'),
('rawan.adel.doc@gmail.com', 'd'),
('mahmoud.alfayez.doc@gmail.com', 'd'),
('shorouk.alani.doc@gmail.com', 'd'),
('tariq.alfayez.doc@gmail.com', 'd'),
('maysa.alsharif.doc@gmail.com', 'd'),
('saeed.alrawashdeh.doc@gmail.com', 'd'),
('hana.gaber.doc@gmail.com', 'd'),
('fares.alrawashdeh.doc@gmail.com', 'd'),
('mais.jodeh.doc@gmail.com', 'd'),
('laith.alnajdawi.doc@gmail.com', 'd'),
('dalia.kayed.doc@gmail.com', 'd'),
('anas.alnajdawi.doc@gmail.com', 'd'),
('saja.alawi.doc@gmail.com', 'd'),
('rami.aljaberi.doc@gmail.com', 'd'),
('nour.hamdan.doc@gmail.com', 'd'),
('saif.aljaberi.doc@gmail.com', 'd'),
('rania.samir.doc@gmail.com', 'd'),
('hassan.alnatsheh.doc@gmail.com', 'd'),
('dalal.mouath.doc@gmail.com', 'd'),
('yousef.alnatsheh.doc@gmail.com', 'd'),
('lina.ayyash.doc@gmail.com', 'd'),
('ibrahim.alkhashman.doc@gmail.com', 'd'),
('tamara.assi.doc@gmail.com', 'd'),
('basel.alkhashman.doc@gmail.com', 'd'),
('hala.abunaim.doc@gmail.com', 'd'),
('jihad.alghazawi.doc@gmail.com', 'd'),
('sohaib.faleh.doc@gmail.com', 'd'),
('alaa.alghazawi.doc@gmail.com', 'd'),
('ahmedalkhatib@gmail.com','p'),
('mohammadalkhatib@gmail.com','p'),
('omaralhamouri@gmail.com','p'),
('khaledalhamouri@gmail.com','p'),
('yazanalqawasmi@gmail.com','p'),
('hussainalqawasmi@gmail.com','p'),
('mahmoudalsaadi@gmail.com','p'),
('tariqalsaadi@gmail.com','p'),
('saeedalbdour@gmail.com','p'),
('faresalbdour@gmail.com','p'),
('laithaltarawneh@gmail.com','p'),
('anasaltarawneh@gmail.com','p'),
('ramialhusseini@gmail.com','p'),
('saifalhusseini@gmail.com','p'),
('hassanalmasri@gmail.com','p'),
('yousefalmasri@gmail.com','p'),
('ibrahimalazzam@gmail.com','p'),
('baselalazzam@gmail.com','p'),
('jihadalaryan@gmail.com','p'),
('alaaalaryan@gmail.com','p'),
('saraalzoubi@gmail.com','p'),
('nooralzoubi@gmail.com','p'),
('layanalsharif@gmail.com','p'),
('ranaalsharif@gmail.com','p'),
('raghadalqaisi@gmail.com','p'),
('malakalqaisi@gmail.com','p'),
('maishaldamin@gmail.com','p'),
('hadeelaldamin@gmail.com','p'),
('danaalmahairat@gmail.com','p'),
('leilaalmahairat@gmail.com','p'),
('hanaalbushnaq@gmail.com','p'),
('abeeralbushnaq@gmail.com','p'),
('nasreenalsalman@gmail.com','p'),
('janaalsalman@gmail.com','p'),
('mahaalfayez@gmail.com','p'),
('ayaalfayez@gmail.com','p'),
('rahmaalghazawi@gmail.com','p'),
('halaalghazawi@gmail.com','p'),
('tamaraalsawaeer@gmail.com','p'),
('linaalsawaeer@gmail.com','p'),
('salmaalkhashman@gmail.com','p'),
('reemalkhashman@gmail.com','p'),
('nouralkhalidi@gmail.com','p'),
('laylaalkhalidi@gmail.com','p'),
('mariamalqawasmi@gmail.com','p'),
('hibaalqawasmi@gmail.com','p'),
('daliaalkayyali@gmail.com','p'),
('ritaalkayyali@gmail.com','p'),
('nadaalrawashdeh@gmail.com','p'),
('samaralrawashdeh@gmail.com','p'),
('ahmedalnimer@gmail.com','p'),
('mohammadalnimer@gmail.com','p'),
('omeralhaddad@gmail.com','p'),
('khaledalhaddad@gmail.com','p'),
('yazanalqadi@gmail.com','p'),
('hussainalqadi@gmail.com','p'),
('mahmoudalbashiti@gmail.com','p'),
('tariqalbashiti@gmail.com','p'),
('saeedalnajjar@gmail.com','p'),
('faresalnajjar@gmail.com','p'),
('laithalmansi@gmail.com','p'),
('anasalmansi@gmail.com','p'),
('ramialomari@gmail.com','p'),
('saifalomari@gmail.com','p'),
('hassanalatawneh@gmail.com','p'),
('yousefalatawneh@gmail.com','p'),
('ibrahimalrahahleh@gmail.com','p'),
('baselalrahahleh@gmail.com','p'),
('jihadalqaraan@gmail.com','p'),
('laithalqaraan@gmail.com','p'),
('anasalajarmeh@gmail.com','p'),
('ramialajarmeh@gmail.com','p'),
('saifalmomani@gmail.com','p'),
('hassanalmomani@gmail.com','p'),
('yousefalsalman@gmail.com','p'),
('ibrahimaltabbah@gmail.com','p'),
('baselalomari@gmail.com','p'),
('jihadalatawneh@gmail.com','p'),
('alaaalrahahleh@gmail.com','p'),
('saraalqaraan@gmail.com','p'),
('nooralajarmeh@gmail.com','p'),
('layanalmomani@gmail.com','p'),
('ranaalghazawi@gmail.com','p'),
('raghadalsawaeer@gmail.com','p'),
('malakalkhashman@gmail.com','p'),
('ayaaladwan@gmail.com','p'),
('rahmaalrawashdeh@gmail.com','p'),
('halaalrawashdeh@gmail.com','p'),
('tamaraalnimer@gmail.com','p'),
('linaalnimer@gmail.com','p'),
('salmaalhaddad@gmail.com','p'),
('reemalhaddad@gmail.com','p'),
('nouralqadi@gmail.com','p'),
('laylaalqadi@gmail.com','p'),
('mariamalbashiti@gmail.com','p'),
('hibaalbashiti@gmail.com','p'),
('daliaalnajjar@gmail.com','p'),
('ritaalnajjar@gmail.com','p'),
('nadaalmansi@gmail.com','p'),
('samaralmansi@gmail.com','p'),
('ahmedalhaj@gmail.com','p'),
('mohammedqasem@gmail.com','p'),
('omarhamdan@gmail.com','p'),
('khaledalmasri@gmail.com','p'),
('yazanalkurdi@gmail.com','p'),
('hussainalattar@gmail.com','p'),
('mahmoudalsabah@gmail.com','p'),
('tariqalhilali@gmail.com','p'),
('saeedalmurad@gmail.com','p'),
('faresalhabash@gmail.com','p'),
('laithalhussein@gmail.com','p'),
('anasalhaddad@gmail.com','p'),
('ramialfawwaz@gmail.com','p'),
('saifalmansour@gmail.com','p'),
('hassanalsharif@gmail.com','p'),
('yousefalghanem@gmail.com','p'),
('ibrahimalzayed@gmail.com','p'),
('baselalqarni@gmail.com','p'),
('jihadalsaeed@gmail.com','p'),
('alaaalkhatib@gmail.com','p'),
('saraaljamal@gmail.com','p'),
('nooralhanan@gmail.com','p'),
('layanalhamwi@gmail.com','p'),
('ranaalramahi@gmail.com','p'),
('raghadaljaberi@gmail.com','p'),
('malakalhajali@gmail.com','p'),
('maishalabadi@gmail.com','p'),
('hadeelalawadi@gmail.com','p'),
('danaalrashdan@gmail.com','p'),
('leilaalfaouri@gmail.com','p'),
('hanaalhalabi@gmail.com','p'),
('abeeralqaisi@gmail.com','p'),
('nasreenalhajri@gmail.com','p'),
('janaalrabadi@gmail.com','p'),
('mahaalomari@gmail.com','p'),
('ayaalnassar@gmail.com','p'),
('rahmaalhariri@gmail.com','p'),
('halaalnajjar@gmail.com','p'),
('tamaraalshami@gmail.com','p'),
('linaalrashid@gmail.com','p'),
('salmaalabdullah@gmail.com','p'),
('reemalhaj@gmail.com','p'),
('nouralyousef@gmail.com','p'),
('laylaalnaser@gmail.com','p'),
('mariamalnajdawi@gmail.com','p'),
('hibaalarafat@gmail.com','p'),
('daliaalhamidi@gmail.com','p'),
('ritaalshaheen@gmail.com','p'),
('nadaalhusban@gmail.com','p'),
('samaralabsi@gmail.com','p'),
('ahmedalrami@gmail.com','p'),
('mohammedaljbour@gmail.com','p'),
('omaralhusayni@gmail.com','p'),
('khaledalmaani@gmail.com','p'),
('yazanalhaddad@gmail.com','p'),
('hussainalfares@gmail.com','p'),
('mahmoudalhajjaj@gmail.com','p'),
('tariqalsharari@gmail.com','p'),
('saeedalrawashdeh@gmail.com','p'),
('faresalatiyat@gmail.com','p'),
('laithalyamani@gmail.com','p'),
('anasalhawamdeh@gmail.com','p'),
('ramialfayez@gmail.com','p'),
('saifalnatsheh@gmail.com','p'),
('hassanalhunaiti@gmail.com','p'),
('yousefalyacoub@gmail.com','p'),
('ibrahimalagha@gmail.com','p'),
('baselalqaralleh@gmail.com','p'),
('jihadalhabahbeh@gmail.com','p'),
('laithaljawarneh@gmail.com','p'),
('anasalrabee@gmail.com','p'),
('ramialshwayat@gmail.com','p'),
('saifalkhatatbeh@gmail.com','p'),
('hassanalmasalmeh@gmail.com','p'),
('yousefalzeid@gmail.com','p'),
('ibrahimalghanayem@gmail.com','p'),
('baselalqadi@gmail.com','p'),
('jihadalnajadat@gmail.com','p'),
('alaaalyousef@gmail.com','p'),
('saraalnaser@gmail.com','p'),
('nooralrabi@gmail.com','p'),
('layanalkatib@gmail.com','p'),
('ranaalhusayni@gmail.com','p'),
('raghadalrahman@gmail.com','p'),
('malakaladwan@gmail.com','p'),
('ayaaljarrar@gmail.com','p'),
('rahmaalshaer@gmail.com','p'),
('halaalnatsheh@gmail.com','p'),
('tamaraalsharari@gmail.com','p'),
('linaalhabbash@gmail.com','p'),
('salmaalabbadi@gmail.com','p'),
('reemalameer@gmail.com','p'),
('nouralabed@gmail.com','p'),
('laylaalghanem@gmail.com','p'),
('mariamalnajjari@gmail.com','p'),
('hibaalrawahneh@gmail.com','p'),
('daliaalqudah@gmail.com','p'),
('ritaalnassar@gmail.com','p'),
('nadaalrabadi@gmail.com','p'),
('samaralmubarak@gmail.com','p');

-- Insert Basic Schedule
INSERT INTO `schedule` (`scheduleid`, `docid`, `title`, `scheduledate`, `scheduletime`, `nop`) VALUES
(1, '1', 'Test Session', '2025-01-01', '18:00:00', 4);

-- =====================================================
-- PART 4: AUTO GENERATE SCHEDULES FOR 2026
-- =====================================================

INSERT INTO schedule (docid, title, scheduledate, scheduletime, nop)
WITH RECURSIVE
date_series AS (
  SELECT DATE('2026-01-01') AS d
  UNION ALL
  SELECT DATE_ADD(d, INTERVAL 1 DAY)
  FROM date_series
  WHERE d < '2026-01-07'
),
slot_series AS (
  SELECT 0 AS n
  UNION ALL
  SELECT n + 1 FROM slot_series WHERE n < 14
),
docs_ranked AS (
  SELECT
    docid,
    specialties,
    ROW_NUMBER() OVER (PARTITION BY specialties ORDER BY docid) AS rn
  FROM doctor
),
docs_with_shift AS (
  SELECT
    docid,
    CASE WHEN rn % 2 = 1 THEN 'morning' ELSE 'evening' END AS shift_type
  FROM docs_ranked
)
SELECT
  CAST(dws.docid AS CHAR(10)) AS docid,
  CONCAT('Auto Slot - ', dws.shift_type),
  ds.d,
  CASE
    WHEN dws.shift_type = 'morning' THEN
      CASE
        WHEN ss.n < 8
          THEN ADDTIME('08:00:00', SEC_TO_TIME(ss.n * 1800))
        ELSE
          ADDTIME('12:30:00', SEC_TO_TIME((ss.n - 8) * 1800))
      END
    ELSE
      CASE
        WHEN ss.n < 8
          THEN ADDTIME('16:00:00', SEC_TO_TIME(ss.n * 1800))
        ELSE
          ADDTIME('20:30:00', SEC_TO_TIME((ss.n - 8) * 1800))
      END
  END AS scheduletime,
  1 AS nop
FROM docs_with_shift dws
CROSS JOIN date_series ds
CROSS JOIN slot_series ss;

-- =====================================================
-- PART 5: ADD FOREIGN KEY CONSTRAINTS
-- =====================================================

ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctor` (`docid`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_messages_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`pid`) ON DELETE CASCADE;

ALTER TABLE `medicalrecords`
  ADD CONSTRAINT `fk_doctor` FOREIGN KEY (`docid`) REFERENCES `doctor` (`docid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_patient` FOREIGN KEY (`pid`) REFERENCES `patient` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `message_read_status`
  ADD CONSTRAINT `message_read_status_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE;

-- Add Foreign Key for admin_messages
ALTER TABLE `admin_messages`
  ADD CONSTRAINT `fk_admin_messages_patient` 
  FOREIGN KEY (`patient_id`) 
  REFERENCES `patient` (`pid`) 
  ON DELETE CASCADE 
  ON UPDATE CASCADE;

-- =====================================================
-- PART 6: CREATE EVENTS
-- =====================================================

DELIMITER $$
CREATE EVENT IF NOT EXISTS `delete_old_chat_messages` 
ON SCHEDULE EVERY 1 DAY 
STARTS CURRENT_DATE + INTERVAL 1 DAY
ON COMPLETION NOT PRESERVE 
ENABLE 
DO 
  DELETE FROM chat_messages WHERE created_at < NOW() - INTERVAL 7 DAY$$
DELIMITER ;

-- =====================================================
-- COMMIT TRANSACTION
-- =====================================================

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

