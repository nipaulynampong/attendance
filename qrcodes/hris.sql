-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2025 at 05:21 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hris`
--

DELIMITER $$
--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `add_notification` (`p_message` VARCHAR(255), `p_type` VARCHAR(50), `p_ref_id` INT) RETURNS INT(11) DETERMINISTIC BEGIN
  INSERT INTO notifications (message, type, reference_id) 
  VALUES (p_message, p_type, p_ref_id);
  RETURN LAST_INSERT_ID();
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `is_holiday` (`check_date` DATE) RETURNS TINYINT(1) DETERMINISTIC BEGIN
    DECLARE is_holiday TINYINT(1) DEFAULT 0;
    
    SELECT COUNT(*) > 0 INTO is_holiday 
    FROM holidays 
    WHERE holiday_date = check_date;
    
    RETURN is_holiday;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `last_attempt` timestamp NULL DEFAULT NULL,
  `cooldown_until` timestamp NULL DEFAULT NULL,
  `security_question` varchar(255) DEFAULT NULL,
  `security_answer` varchar(255) DEFAULT NULL,
  `locked_until` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `archived_employees`
--

CREATE TABLE `archived_employees` (
  `ID` int(11) NOT NULL,
  `EmployeeID` int(11) NOT NULL,
  `Last Name` varchar(100) NOT NULL,
  `First Name` varchar(100) NOT NULL,
  `Middle Name` varchar(100) NOT NULL,
  `Suffix` varchar(11) NOT NULL,
  `Age` int(11) NOT NULL,
  `Birthday` date NOT NULL,
  `Address` varchar(100) NOT NULL,
  `Gender` varchar(11) NOT NULL,
  `Contact Number` int(11) NOT NULL,
  `Email Address` varchar(100) NOT NULL,
  `Department` varchar(100) NOT NULL,
  `Monday_Rest` tinyint(1) NOT NULL DEFAULT 0,
  `Tuesday_Rest` tinyint(1) NOT NULL DEFAULT 0,
  `Wednesday_Rest` tinyint(1) NOT NULL DEFAULT 0,
  `Thursday_Rest` tinyint(1) NOT NULL DEFAULT 0,
  `Friday_Rest` tinyint(1) NOT NULL DEFAULT 0,
  `Saturday_Rest` tinyint(1) NOT NULL DEFAULT 0,
  `Sunday_Rest` tinyint(1) NOT NULL DEFAULT 0,
  `Image` blob NOT NULL,
  `QRCode` longblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `ID` int(11) NOT NULL,
  `EMPLOYEEID` int(11) NOT NULL,
  `Last Name` varchar(100) NOT NULL,
  `First Name` varchar(100) NOT NULL,
  `Middle Name` varchar(100) NOT NULL,
  `Department` varchar(100) NOT NULL,
  `TIMEIN` varchar(250) DEFAULT NULL,
  `TIMEOUT` varchar(250) DEFAULT NULL,
  `LOGDATE` date NOT NULL,
  `STATUS` varchar(250) DEFAULT '0',
  `HolidayInfo` varchar(255) DEFAULT NULL,
  `EventInfo` varchar(255) DEFAULT NULL,
  `REASON_TYPE` varchar(50) DEFAULT NULL,
  `REASON_DETAILS` varchar(255) DEFAULT NULL,
  `MODIFIED_BY` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `attendance`
--
DELIMITER $$
CREATE TRIGGER `attendance_notification` AFTER INSERT ON `attendance` FOR EACH ROW BEGIN
  DECLARE emp_name VARCHAR(100);
  
  
  SELECT CONCAT(`First Name`, ' ', `Last Name`) INTO emp_name 
  FROM employee 
  WHERE EmployeeID = NEW.EMPLOYEEID;
  
  
  IF emp_name IS NULL THEN
    SET emp_name = CONCAT('Employee ID ', NEW.EMPLOYEEID);
  END IF;
  
  
  IF NEW.MODIFIED_BY IS NULL THEN
    
    INSERT INTO notifications (message, type, reference_id)
    VALUES (CONCAT(emp_name, ' marked attendance at ', NEW.TIMEIN), 'attendance', NEW.ID);
  ELSE
    
    INSERT INTO notifications (message, type, reference_id)
    VALUES (CONCAT('Admin ', NEW.MODIFIED_BY, ' manually recorded attendance for ', emp_name), 'attendance', NEW.ID);
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `manual_attendance_modification` AFTER UPDATE ON `attendance` FOR EACH ROW BEGIN
  DECLARE emp_name VARCHAR(100);
  
  
  IF NEW.MODIFIED_BY IS NOT NULL AND (OLD.MODIFIED_BY <> NEW.MODIFIED_BY OR OLD.MODIFIED_BY IS NULL) THEN
    
    SELECT CONCAT(`First Name`, ' ', `Last Name`) INTO emp_name 
    FROM employee 
    WHERE EmployeeID = NEW.EMPLOYEEID;
    
    
    IF emp_name IS NULL THEN
      SET emp_name = CONCAT('Employee ID ', NEW.EMPLOYEEID);
    END IF;
    
    
    SET @changes = '';
    
    IF OLD.TIMEIN <> NEW.TIMEIN THEN
      SET @changes = CONCAT(@changes, 'Time In, ');
    END IF;
    
    IF OLD.TIMEOUT <> NEW.TIMEOUT OR (OLD.TIMEOUT IS NULL AND NEW.TIMEOUT IS NOT NULL) THEN
      SET @changes = CONCAT(@changes, 'Time Out, ');
    END IF;
    
    IF OLD.STATUS <> NEW.STATUS OR (OLD.STATUS IS NULL AND NEW.STATUS IS NOT NULL) THEN
      SET @changes = CONCAT(@changes, 'Status, ');
    END IF;
    
    
    IF LENGTH(@changes) > 0 THEN
      SET @changes = CONCAT('Changed: ', LEFT(@changes, LENGTH(@changes) - 2));
    ELSE
      SET @changes = 'Modified attendance record';
    END IF;
    
    
    INSERT INTO notifications (message, type, reference_id)
    VALUES (CONCAT('Admin ', NEW.MODIFIED_BY, ' modified attendance record for ', emp_name, ' (', @changes, ')'), 'attendance', NEW.ID);
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `ID` int(11) NOT NULL,
  `Department` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL,
  `Head` varchar(100) NOT NULL,
  `Contact` varchar(100) DEFAULT NULL,
  `Status` varchar(50) NOT NULL,
  `Created At` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `ID` int(11) NOT NULL,
  `EmployeeID` int(11) NOT NULL,
  `Last Name` varchar(100) NOT NULL,
  `First Name` varchar(100) NOT NULL,
  `Middle Name` varchar(100) NOT NULL,
  `Suffix` varchar(11) NOT NULL,
  `Age` int(11) NOT NULL,
  `Birthday` date NOT NULL,
  `Address` varchar(100) NOT NULL,
  `Gender` varchar(11) NOT NULL,
  `Contact Number` varchar(15) NOT NULL,
  `Email Address` varchar(100) DEFAULT NULL,
  `Department` varchar(100) NOT NULL,
  `Monday_Rest` tinyint(1) NOT NULL DEFAULT 0,
  `Tuesday_Rest` tinyint(1) NOT NULL DEFAULT 0,
  `Wednesday_Rest` tinyint(1) NOT NULL DEFAULT 0,
  `Thursday_Rest` tinyint(1) NOT NULL DEFAULT 0,
  `Friday_Rest` tinyint(1) NOT NULL DEFAULT 0,
  `Saturday_Rest` tinyint(1) NOT NULL DEFAULT 0,
  `Sunday_Rest` tinyint(1) NOT NULL DEFAULT 0,
  `Image` blob DEFAULT NULL,
  `QRCode` longblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `employee`
--
DELIMITER $$
CREATE TRIGGER `new_employee_notification` AFTER INSERT ON `employee` FOR EACH ROW BEGIN
  INSERT INTO notifications (message, type, reference_id)
  VALUES (CONCAT('New employee added: ', NEW.`First Name`, ' ', NEW.`Last Name`), 'employee', NEW.ID);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `event_type` enum('Company','Department','Training','Other') NOT NULL DEFAULT 'Other',
  `location` varchar(255) DEFAULT NULL,
  `departments` varchar(255) DEFAULT 'All',
  `required_attendance` tinyint(1) NOT NULL DEFAULT 0,
  `contact_person` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `holidays`
--

CREATE TABLE `holidays` (
  `id` int(11) NOT NULL,
  `holiday_name` varchar(255) NOT NULL,
  `holiday_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `holiday_type` enum('Regular','Special') NOT NULL DEFAULT 'Regular',
  `is_paid` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `holidays`
--

INSERT INTO `holidays` (`id`, `holiday_name`, `holiday_date`, `description`, `holiday_type`, `is_paid`, `date_created`) VALUES
(79, 'Election Day', '2025-05-12', 'Electing Senators, Partylist, Mayors, Councilors, and Congressman', 'Regular', 1, '2025-05-11 08:44:08');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `ID` int(11) NOT NULL,
  `Department` varchar(100) NOT NULL,
  `TimeIn` time NOT NULL,
  `TimeOut` time NOT NULL,
  `Created At` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `archived_employees`
--
ALTER TABLE `archived_employees`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `EmployeeID` (`EmployeeID`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `EMPLOYEEID` (`EMPLOYEEID`),
  ADD KEY `Department` (`Department`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Department` (`Department`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `EmployeeID` (`EmployeeID`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_events_dates` (`start_date`,`end_date`),
  ADD KEY `idx_events_type` (`event_type`);

--
-- Indexes for table `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_holiday_date` (`holiday_date`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Department` (`Department`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `archived_employees`
--
ALTER TABLE `archived_employees`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `holidays`
--
ALTER TABLE `holidays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`EMPLOYEEID`) REFERENCES `employee` (`EmployeeID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`Department`) REFERENCES `department` (`Department`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `schedule_ibfk_1` FOREIGN KEY (`Department`) REFERENCES `department` (`Department`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
