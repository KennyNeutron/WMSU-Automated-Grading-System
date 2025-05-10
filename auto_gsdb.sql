-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 19, 2025 at 05:17 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `auto_gsdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `colleges_table`
--

CREATE TABLE `colleges_table` (
  `college_id` int(11) NOT NULL,
  `college_name` varchar(255) NOT NULL,
  `departments` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `colleges_table`
--

INSERT INTO `colleges_table` (`college_id`, `college_name`, `departments`) VALUES
(7, 'Engineering', '19,20'),
(8, 'Computing Studies', '17,18'),
(12, 'Nursing', '22');

-- --------------------------------------------------------

--
-- Table structure for table `college_department_table`
--

CREATE TABLE `college_department_table` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `college_department_table`
--

INSERT INTO `college_department_table` (`department_id`, `department_name`) VALUES
(17, 'Information Technology'),
(18, 'Computer Science'),
(19, 'Civil Engineering'),
(20, 'Mechanical Engineering');

-- --------------------------------------------------------

--
-- Table structure for table `component_items`
--

CREATE TABLE `component_items` (
  `items_id` int(11) NOT NULL,
  `component_id` int(11) NOT NULL,
  `component_date` date NOT NULL,
  `component_no` int(11) NOT NULL,
  `component_quantity` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `component_items`
--

INSERT INTO `component_items` (`items_id`, `component_id`, `component_date`, `component_no`, `component_quantity`) VALUES
(1, 1, '2025-02-01', 1, 25),
(2, 1, '2025-03-15', 2, 26),
(3, 2, '2025-02-18', 1, 50),
(5, 7, '2025-02-18', 1, 15),
(6, 5, '2025-02-18', 1, 10),
(9, 2, '2025-02-18', 2, 60),
(10, 12, '2025-02-20', 1, 100),
(13, 16, '2025-02-28', 2, 50),
(17, 8, '2025-03-14', 1, 10),
(18, 9, '2025-03-16', 1, 50),
(19, 13, '2025-03-19', 1, 80);

-- --------------------------------------------------------

--
-- Table structure for table `component_scores`
--

CREATE TABLE `component_scores` (
  `score_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  `grades_id` int(11) NOT NULL,
  `score` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `component_scores`
--

INSERT INTO `component_scores` (`score_id`, `items_id`, `grades_id`, `score`) VALUES
(1, 1, 22, 25),
(2, 2, 22, 26),
(3, 3, 22, 50),
(4, 9, 22, 60),
(5, 6, 22, 10),
(8, 1, 24, 25),
(9, 2, 24, 26),
(10, 3, 24, 0),
(11, 9, 24, 0),
(12, 6, 24, 0),
(15, 10, 22, 95),
(16, 5, 22, 15),
(17, 10, 24, 0),
(18, 17, 22, 8),
(19, 18, 22, 50),
(20, 19, 22, 70);

-- --------------------------------------------------------

--
-- Table structure for table `course_curr`
--

CREATE TABLE `course_curr` (
  `college_course_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `degree_level` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_curr`
--

INSERT INTO `course_curr` (`college_course_id`, `name`, `degree_level`) VALUES
(11, 'Civil Engineering', 'Bachelor\'s Degree'),
(12, 'Nursing', 'Bachelor\'s Degree'),
(13, 'Information Technology', 'Doctoral\'s Degree'),
(14, 'Information Technology', 'Associate Degree'),
(15, 'Computer Science', 'Bachelor\'s Degree'),
(16, 'Information Technology', 'Bachelor\'s Degree'),
(17, 'Psychology', 'Bachelor\'s Degree'),
(20, 'Computer Engineering', 'Bachelor\'s Degree'),
(22, 'Mechanical Engineering', 'Bachelor\'s Degree');

-- --------------------------------------------------------

--
-- Table structure for table `curr_table`
--

CREATE TABLE `curr_table` (
  `curr_id` int(11) NOT NULL,
  `curr_year_id` int(11) NOT NULL,
  `college_course_id` int(11) NOT NULL,
  `time_id` int(11) NOT NULL,
  `year_level_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `sub_code` varchar(150) NOT NULL,
  `sub_name` varchar(250) NOT NULL,
  `sub_prerequisite` varchar(250) DEFAULT NULL,
  `lec` int(11) DEFAULT NULL,
  `lab` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curr_table`
--

INSERT INTO `curr_table` (`curr_id`, `curr_year_id`, `college_course_id`, `time_id`, `year_level_id`, `semester_id`, `sub_code`, `sub_name`, `sub_prerequisite`, `lec`, `lab`) VALUES
(34, 6, 15, 1, 1, 1, 'CC 101', 'Computer Programming 1', NULL, 1, 2),
(35, 6, 15, 1, 1, 1, 'CC 100', 'Introduction To Computing', NULL, 1, 2),
(37, 6, 15, 1, 1, 1, 'EUTH A', 'Euthenics A', NULL, 1, 0),
(38, 6, 15, 1, 1, 1, 'MATH 100', 'Mathematics in the Modern World', NULL, 1, 1),
(39, 6, 15, 1, 1, 1, 'PE 101', 'Physical Education 1', NULL, 1, 0),
(40, 6, 15, 1, 1, 2, 'CC102', 'Computer Programming 2', 'CC 101', 1, 2),
(41, 6, 15, 1, 1, 2, 'EUTH B', 'Euthenics B', 'EUTH A', 1, 0),
(42, 6, 15, 1, 1, 2, 'MATH 103', 'Calculus 1', 'MATH 100', 1, 1),
(43, 6, 15, 1, 1, 2, 'PE 102', 'Physical Education 2', 'PE 101', 1, 0),
(44, 6, 15, 1, 1, 2, 'STS 100', 'Science, Technology, and Society', NULL, 1, 0),
(46, 6, 11, 1, 1, 1, 'MATH 105', 'Calculus 1', NULL, 1, 2),
(47, 6, 22, 1, 1, 1, 'MATH 105', 'Calculus 1', NULL, 1, 2),
(48, 6, 16, 1, 1, 1, 'CS 135', 'Software Engineering', NULL, 1, 2),
(49, 6, 16, 1, 1, 1, 'PE 102', 'Physical Education 1', NULL, 3, 0),
(50, 6, 16, 1, 3, 1, 'CC 104', 'Computer Programming 2', NULL, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `curr_time`
--

CREATE TABLE `curr_time` (
  `time_id` int(11) NOT NULL,
  `time_name` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curr_time`
--

INSERT INTO `curr_time` (`time_id`, `time_name`) VALUES
(1, 'Present'),
(2, 'Former');

-- --------------------------------------------------------

--
-- Table structure for table `curr_year`
--

CREATE TABLE `curr_year` (
  `curr_year_id` int(11) NOT NULL,
  `year_start` int(4) NOT NULL,
  `year_end` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curr_year`
--

INSERT INTO `curr_year` (`curr_year_id`, `year_start`, `year_end`) VALUES
(1, 2019, 2020),
(2, 2020, 2021),
(3, 2021, 2022),
(4, 2022, 2023),
(5, 2023, 2024),
(6, 2024, 2025);

-- --------------------------------------------------------

--
-- Table structure for table `faculty_schedule`
--

CREATE TABLE `faculty_schedule` (
  `sched_id` int(11) NOT NULL,
  `profiling_id` int(11) NOT NULL,
  `release_time` tinyint(1) NOT NULL,
  `hrs_per_week` int(11) NOT NULL,
  `school_yr` varchar(255) NOT NULL,
  `semester` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_schedule`
--

INSERT INTO `faculty_schedule` (`sched_id`, `profiling_id`, `release_time`, `hrs_per_week`, `school_yr`, `semester`) VALUES
(5, 30, 0, 40, '2024 - 2025', 1),
(7, 31, 0, 6, '2024 - 2025', 2),
(13, 29, 0, 35, '2023 - 2026', 3),
(14, 35, 0, 46, '2021 - 2025', 2),
(15, 31, 0, 48, '2024 - 2025', 1),
(16, 31, 0, 67, '2023 - 2024', 1);

-- --------------------------------------------------------

--
-- Table structure for table `faculty_subjects`
--

CREATE TABLE `faculty_subjects` (
  `faculty_sub_id` int(11) NOT NULL,
  `sched_id` int(11) NOT NULL,
  `curr_id` int(11) NOT NULL,
  `yr_sec` varchar(255) NOT NULL,
  `no_students` int(11) NOT NULL,
  `lec_days` varchar(255) DEFAULT NULL,
  `lab_days` varchar(255) DEFAULT NULL,
  `lec_time` varchar(255) DEFAULT NULL,
  `lab_time` varchar(255) DEFAULT NULL,
  `lec_room` varchar(255) DEFAULT NULL,
  `lab_room` varchar(255) DEFAULT NULL,
  `lec_units` int(11) DEFAULT NULL,
  `lab_units` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_subjects`
--

INSERT INTO `faculty_subjects` (`faculty_sub_id`, `sched_id`, `curr_id`, `yr_sec`, `no_students`, `lec_days`, `lab_days`, `lec_time`, `lab_time`, `lec_room`, `lab_room`, `lec_units`, `lab_units`) VALUES
(18, 16, 50, 'BSIT 3A', 32, 'M', 'M', '3:51 AM - 3:51 AM', '3:51 AM - 3:51 AM', 'LR 2', 'LAB 2', 1, 2),
(50, 7, 49, 'BSIT 3A', 34, 'M', '', '9:29 AM - 9:29 AM', '', 'LR 2', '', 3, 0),
(51, 15, 50, 'BSIT 3A', 23, 'M', '', '7:52 AM - 7:52 AM', '', 'Gymnasium', '', 1, 2),
(52, 15, 48, 'BSIT 1A', 23, '', 'M', '', '7:53 AM - 7:53 AM', '', 'LAB2', 1, 2),
(53, 15, 48, 'BSCS 2A', 32, '', 'MW', '', '9:21 AM - 12:56 PM', '', 'Reprehenderit sed ni', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notif_id` int(11) NOT NULL,
  `user_role` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notif_id`, `user_role`, `user_id`, `message`, `created_at`, `updated_at`) VALUES
(16, 1, 12, 'The account <span style=\"color:blue;\">cvbxvdfbf@gmail.com(2021-000023)</span> has successfully changed its password.', '2024-11-25 06:13:21', '2024-11-25 07:11:53');

-- --------------------------------------------------------

--
-- Table structure for table `periods`
--

CREATE TABLE `periods` (
  `period_id` int(11) NOT NULL,
  `faculty_sub_id` int(11) NOT NULL,
  `period_type` varchar(255) NOT NULL,
  `weight` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `periods`
--

INSERT INTO `periods` (`period_id`, `faculty_sub_id`, `period_type`, `weight`) VALUES
(4, 50, 'Midterm', 40),
(5, 50, 'Final Term', 60),
(6, 51, 'Midterm', 40),
(7, 51, 'Final Term', 60),
(8, 52, 'Midterm', 40),
(9, 52, 'Final Term', 60),
(10, 53, 'Midterm', 40),
(11, 53, 'Final Term', 60);

-- --------------------------------------------------------

--
-- Table structure for table `point_equivalent`
--

CREATE TABLE `point_equivalent` (
  `point_eqv_id` int(11) NOT NULL,
  `faculty_sub_id` int(11) NOT NULL,
  `1_00` double NOT NULL,
  `1_25` double NOT NULL,
  `1_50` double NOT NULL,
  `1_75` double NOT NULL,
  `2_00` double NOT NULL,
  `2_25` double NOT NULL,
  `2_50` double NOT NULL,
  `2_75` double NOT NULL,
  `3_00` double NOT NULL,
  `5_00` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `point_equivalent`
--

INSERT INTO `point_equivalent` (`point_eqv_id`, `faculty_sub_id`, `1_00`, `1_25`, `1_50`, `1_75`, `2_00`, `2_25`, `2_50`, `2_75`, `3_00`, `5_00`) VALUES
(1, 53, 97, 94, 91, 88, 85, 79, 76, 75, 60, 40),
(2, 52, 97, 94, 91, 88, 85, 79, 76, 75, 60, 40),
(3, 51, 97, 94, 91, 88, 85, 79, 76, 75, 60, 40),
(4, 50, 96, 94, 91, 88, 85, 79, 76, 73, 60, 40),
(5, 18, 97, 94, 91, 88, 85, 79, 76, 75, 60, 40);

-- --------------------------------------------------------

--
-- Table structure for table `posted_grades`
--

CREATE TABLE `posted_grades` (
  `posted_grade_id` int(11) NOT NULL,
  `student_data_id` int(11) NOT NULL,
  `faculty_sub_id` int(11) NOT NULL,
  `midterm_grade` double NOT NULL,
  `finalterm_grade` double NOT NULL,
  `remarks` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profiling_table`
--

CREATE TABLE `profiling_table` (
  `profiling_id` int(11) NOT NULL,
  `emp_id` text NOT NULL,
  `f_name` varchar(50) NOT NULL,
  `l_name` varchar(50) NOT NULL,
  `m_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `start_service` varchar(50) NOT NULL,
  `end_service` varchar(50) DEFAULT NULL,
  `acad_type` varchar(100) NOT NULL,
  `faculty_type` varchar(100) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `department_id` int(11) NOT NULL,
  `is_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profiling_table`
--

INSERT INTO `profiling_table` (`profiling_id`, `emp_id`, `f_name`, `l_name`, `m_name`, `email`, `start_service`, `end_service`, `acad_type`, `faculty_type`, `designation`, `department_id`, `is_created`, `is_updated`) VALUES
(29, '2021-00089', 'Sairyl', 'Manabat', '', 'sonarshironji@gmail.com', '2025-01-03', '2025-02-28', 'Adjunct Faculty', 'Visiting Lecturer', 'Professor', 18, '2025-01-26 13:12:59', '2025-02-10 21:18:56'),
(30, '2021-000009', 'Alco', 'Plus', 'Iso', 'email@email.com', '2025-01-04', '2025-03-19', 'Professor II', 'Regular Lecturer', 'Professor', 20, '2025-01-26 13:54:43', '2025-01-28 11:21:01'),
(31, '2021-000023', 'Raf', 'Saludo', '', 'saludoraf@gmail.com', '2025-01-03', '2025-01-30', 'Instructor', 'Visiting Lecturer', 'Assistant Professor', 17, '2025-01-26 14:32:56', '2025-01-26 14:32:56'),
(35, '2021-00150', 'Franklin', 'Oliveros', 'Ituralde', 'ofranklinjames@gmail.com', '2025-02-01', '2025-02-25', 'Adjunct Faculty', 'Visiting Lecturer', 'Professor', 18, '2025-02-10 21:21:38', '2025-02-10 21:21:38');

-- --------------------------------------------------------

--
-- Table structure for table `semester`
--

CREATE TABLE `semester` (
  `semester_id` int(11) NOT NULL,
  `semester` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `semester`
--

INSERT INTO `semester` (`semester_id`, `semester`) VALUES
(1, '1st Semester'),
(2, '2nd Semester'),
(3, 'Summer');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_data_id` int(11) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `student_firstname` varchar(255) NOT NULL,
  `student_middlename` varchar(255) DEFAULT NULL,
  `student_lastname` varchar(255) NOT NULL,
  `suffix` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `year_section` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_data_id`, `student_id`, `student_firstname`, `student_middlename`, `student_lastname`, `suffix`, `email`, `year_section`) VALUES
(31, '2021334234', 'Rylee Rhodes', 'Dela', 'Floyd', '', 'bbrightwin12xx@wmsu.edu.ph', 'BSIT 3A'),
(32, '7456756', 'Patrick', 'Nicholas', 'Floyd', 'Jr.', 'bbrightwin12xx@wmsu.edu.ph', 'BSIT 3A'),
(33, '645645', 'Calista', 'Dela', 'P', '', 'bbrightwin12xx@wmsu.edu.ph', 'BSIT 3A'),
(34, '42365646', 'Calistax', 'Tre', 'Free', 'Jr.', 'bbrightwin12xx@wmsu.edu.ph', 'BSIT 3A');

-- --------------------------------------------------------

--
-- Table structure for table `student_grades`
--

CREATE TABLE `student_grades` (
  `grades_id` int(8) NOT NULL,
  `student_data_id` int(11) NOT NULL,
  `faculty_sub_id` int(11) NOT NULL,
  `midterm_grade` varchar(255) NOT NULL,
  `final_grade` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_grades`
--

INSERT INTO `student_grades` (`grades_id`, `student_data_id`, `faculty_sub_id`, `midterm_grade`, `final_grade`) VALUES
(22, 34, 50, '93.5', '93'),
(23, 33, 50, '0', '0'),
(24, 32, 50, '0', '0'),
(25, 31, 51, '0', '0'),
(26, 32, 51, '0', '0'),
(27, 34, 51, '0', '0');

-- --------------------------------------------------------

--
-- Table structure for table `sub_components`
--

CREATE TABLE `sub_components` (
  `component_id` int(11) NOT NULL,
  `period_id` int(11) NOT NULL,
  `component_type` varchar(255) NOT NULL,
  `weight` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sub_components`
--

INSERT INTO `sub_components` (`component_id`, `period_id`, `component_type`, `weight`) VALUES
(1, 4, 'Quiz', 20),
(2, 4, 'Performance Task', 30),
(5, 4, 'Recitation', 15),
(7, 5, 'Quiz', 20),
(8, 5, 'Recitations', 10),
(9, 5, 'Project', 30),
(10, 8, 'Activities', 20),
(11, 9, 'Projects', 20),
(12, 4, 'Major Exam', 30),
(13, 5, 'Major Exam', 40),
(16, 6, 'Recitation', 30),
(20, 10, 'Recitations', 25),
(21, 10, 'Quiz', 15),
(22, 10, 'Major Exam', 40),
(23, 10, 'Project', 20),
(24, 11, 'Major Exam', 45),
(25, 11, 'Quiz', 25),
(26, 11, 'Recitation', 10),
(27, 11, 'Project', 20);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(100) NOT NULL,
  `emp_id` text NOT NULL,
  `user_role` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `f_name` varchar(155) NOT NULL,
  `l_name` varchar(155) NOT NULL,
  `m_name` varchar(155) DEFAULT NULL,
  `faculty_type` varchar(250) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `is_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `emp_id`, `user_role`, `email`, `password`, `f_name`, `l_name`, `m_name`, `faculty_type`, `profile_image`, `is_created`, `is_updated`) VALUES
(1, '2021-00150', 1, 'ofranklinjames@gmail.com', '$2y$10$oefvsQpyW0dBtXpNOWlFjOsiNaeTV8Nj0JskhRZzrJBLiy68Y6jSa', 'Franklin', 'Oliveros', 'Ituralde', 'Visiting Lecturer', NULL, '2024-01-25 14:07:56', '2024-01-28 08:19:11'),
(2, '2021-00151', 2, 'admin@gmail.com', '$2y$10$SnuD42TMnd7XB1t/UULH0eWd3d9r9KodyflG5Lp.MslLebbY9Z0V6', 'Admin', 'Main', '', 'regular ', 'admin-profile.png', '2024-01-28 04:49:03', '2025-01-09 13:11:49'),
(3, '2021-000009', 1, 'email@email.com', '$2y$10$g37cVIDEYRsCfYmIPMwybOLTd/JFqCR6Y48T2MQ4muzx/WCnpYI6u', 'Alco', 'Plus', 'Iso', 'Regular Lecturer', NULL, '2024-01-30 01:29:48', '2024-11-20 06:38:26'),
(12, '2021-000023', 1, 'saludoraf@gmail.com', '$2y$10$x1CiJCUkNZ1WttyHuwSbH.2ZZmT1QHpFx60id3H5Lkuy8gRXlYEcm', 'Raf', 'Saludo', '', 'Visiting Lecturer', NULL, '2024-11-21 22:30:34', '2025-02-10 20:26:05'),
(13, '2021-00089', 1, 'sonarshironji@gmail.com', '$2y$10$lReSRFWPxTJFHwrM1p3PWOXW5/izol3xtvzdsLBX0nlZ85lYiql4e', 'Sairyl', 'Manabat', '', 'Visiting Lecturer', NULL, '2024-11-24 19:49:16', '2024-11-24 19:49:16'),
(15, '2021-1111', 1, 'bbrightwin12xx@wmsu.edu.ph', '$2y$10$DWOwmSYd8kPxYAzSR/6wxeJZVLZsWMJS.nDwtwej6MjYym/BDk3jS', 'First', 'Last', 'Middle', 'Regular Lecturer', NULL, '2025-02-11 03:41:57', '2025-02-23 02:45:39');

-- --------------------------------------------------------

--
-- Table structure for table `year_lvl`
--

CREATE TABLE `year_lvl` (
  `year_level_id` int(11) NOT NULL,
  `year_level` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `year_lvl`
--

INSERT INTO `year_lvl` (`year_level_id`, `year_level`) VALUES
(1, '1st year'),
(2, '2nd year'),
(3, '3rd year'),
(4, '4th year');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `colleges_table`
--
ALTER TABLE `colleges_table`
  ADD PRIMARY KEY (`college_id`);

--
-- Indexes for table `college_department_table`
--
ALTER TABLE `college_department_table`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `component_items`
--
ALTER TABLE `component_items`
  ADD PRIMARY KEY (`items_id`),
  ADD KEY `component_id` (`component_id`);

--
-- Indexes for table `component_scores`
--
ALTER TABLE `component_scores`
  ADD PRIMARY KEY (`score_id`),
  ADD KEY `component_scores_ibfk_1` (`items_id`),
  ADD KEY `component_scores_ibfk_2` (`grades_id`);

--
-- Indexes for table `course_curr`
--
ALTER TABLE `course_curr`
  ADD PRIMARY KEY (`college_course_id`);

--
-- Indexes for table `curr_table`
--
ALTER TABLE `curr_table`
  ADD PRIMARY KEY (`curr_id`),
  ADD KEY `college_course_id` (`college_course_id`),
  ADD KEY `time_id` (`time_id`),
  ADD KEY `year_level_id` (`year_level_id`),
  ADD KEY `semester_id` (`semester_id`),
  ADD KEY `curr_year_id` (`curr_year_id`);

--
-- Indexes for table `curr_time`
--
ALTER TABLE `curr_time`
  ADD PRIMARY KEY (`time_id`);

--
-- Indexes for table `curr_year`
--
ALTER TABLE `curr_year`
  ADD PRIMARY KEY (`curr_year_id`);

--
-- Indexes for table `faculty_schedule`
--
ALTER TABLE `faculty_schedule`
  ADD PRIMARY KEY (`sched_id`),
  ADD KEY `semester` (`semester`),
  ADD KEY `faculty_schedule_ibfk_3` (`profiling_id`);

--
-- Indexes for table `faculty_subjects`
--
ALTER TABLE `faculty_subjects`
  ADD PRIMARY KEY (`faculty_sub_id`),
  ADD KEY `sched_id` (`sched_id`),
  ADD KEY `curr_id` (`curr_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notif_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `user_role` (`user_role`);

--
-- Indexes for table `periods`
--
ALTER TABLE `periods`
  ADD PRIMARY KEY (`period_id`),
  ADD KEY `periods_ibfk_1` (`faculty_sub_id`);

--
-- Indexes for table `point_equivalent`
--
ALTER TABLE `point_equivalent`
  ADD PRIMARY KEY (`point_eqv_id`),
  ADD KEY `faculty_sub_id` (`faculty_sub_id`);

--
-- Indexes for table `posted_grades`
--
ALTER TABLE `posted_grades`
  ADD PRIMARY KEY (`posted_grade_id`),
  ADD KEY `student_data_id` (`student_data_id`),
  ADD KEY `faculty_sub_id` (`faculty_sub_id`);

--
-- Indexes for table `profiling_table`
--
ALTER TABLE `profiling_table`
  ADD PRIMARY KEY (`profiling_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `semester`
--
ALTER TABLE `semester`
  ADD PRIMARY KEY (`semester_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_data_id`);

--
-- Indexes for table `student_grades`
--
ALTER TABLE `student_grades`
  ADD PRIMARY KEY (`grades_id`),
  ADD KEY `student_grades_ibfk_2` (`student_data_id`),
  ADD KEY `student_grades_ibfk_1` (`faculty_sub_id`);

--
-- Indexes for table `sub_components`
--
ALTER TABLE `sub_components`
  ADD PRIMARY KEY (`component_id`),
  ADD KEY `sub_components_ibfk_1` (`period_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `year_lvl`
--
ALTER TABLE `year_lvl`
  ADD PRIMARY KEY (`year_level_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `colleges_table`
--
ALTER TABLE `colleges_table`
  MODIFY `college_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `college_department_table`
--
ALTER TABLE `college_department_table`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `component_items`
--
ALTER TABLE `component_items`
  MODIFY `items_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `component_scores`
--
ALTER TABLE `component_scores`
  MODIFY `score_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `course_curr`
--
ALTER TABLE `course_curr`
  MODIFY `college_course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `curr_table`
--
ALTER TABLE `curr_table`
  MODIFY `curr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT for table `curr_time`
--
ALTER TABLE `curr_time`
  MODIFY `time_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `curr_year`
--
ALTER TABLE `curr_year`
  MODIFY `curr_year_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `faculty_schedule`
--
ALTER TABLE `faculty_schedule`
  MODIFY `sched_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `faculty_subjects`
--
ALTER TABLE `faculty_subjects`
  MODIFY `faculty_sub_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notif_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `periods`
--
ALTER TABLE `periods`
  MODIFY `period_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `point_equivalent`
--
ALTER TABLE `point_equivalent`
  MODIFY `point_eqv_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `posted_grades`
--
ALTER TABLE `posted_grades`
  MODIFY `posted_grade_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profiling_table`
--
ALTER TABLE `profiling_table`
  MODIFY `profiling_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `semester`
--
ALTER TABLE `semester`
  MODIFY `semester_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_data_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `student_grades`
--
ALTER TABLE `student_grades`
  MODIFY `grades_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `sub_components`
--
ALTER TABLE `sub_components`
  MODIFY `component_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `year_lvl`
--
ALTER TABLE `year_lvl`
  MODIFY `year_level_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `component_items`
--
ALTER TABLE `component_items`
  ADD CONSTRAINT `component_items_ibfk_1` FOREIGN KEY (`component_id`) REFERENCES `sub_components` (`component_id`) ON DELETE CASCADE;

--
-- Constraints for table `component_scores`
--
ALTER TABLE `component_scores`
  ADD CONSTRAINT `component_scores_ibfk_1` FOREIGN KEY (`items_id`) REFERENCES `component_items` (`items_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `component_scores_ibfk_2` FOREIGN KEY (`grades_id`) REFERENCES `student_grades` (`grades_id`);

--
-- Constraints for table `curr_table`
--
ALTER TABLE `curr_table`
  ADD CONSTRAINT `college_course_id` FOREIGN KEY (`college_course_id`) REFERENCES `course_curr` (`college_course_id`),
  ADD CONSTRAINT `curr_year_id` FOREIGN KEY (`curr_year_id`) REFERENCES `curr_year` (`curr_year_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `semester_id` FOREIGN KEY (`semester_id`) REFERENCES `semester` (`semester_id`),
  ADD CONSTRAINT `time_id` FOREIGN KEY (`time_id`) REFERENCES `curr_time` (`time_id`),
  ADD CONSTRAINT `year_lvl_id` FOREIGN KEY (`year_level_id`) REFERENCES `year_lvl` (`year_level_id`);

--
-- Constraints for table `faculty_schedule`
--
ALTER TABLE `faculty_schedule`
  ADD CONSTRAINT `faculty_schedule_ibfk_2` FOREIGN KEY (`semester`) REFERENCES `semester` (`semester_id`),
  ADD CONSTRAINT `faculty_schedule_ibfk_3` FOREIGN KEY (`profiling_id`) REFERENCES `profiling_table` (`profiling_id`) ON DELETE CASCADE;

--
-- Constraints for table `faculty_subjects`
--
ALTER TABLE `faculty_subjects`
  ADD CONSTRAINT `faculty_subjects_ibfk_2` FOREIGN KEY (`sched_id`) REFERENCES `faculty_schedule` (`sched_id`),
  ADD CONSTRAINT `faculty_subjects_ibfk_3` FOREIGN KEY (`curr_id`) REFERENCES `curr_table` (`curr_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `user_role` FOREIGN KEY (`user_role`) REFERENCES `user` (`user_role`);

--
-- Constraints for table `periods`
--
ALTER TABLE `periods`
  ADD CONSTRAINT `periods_ibfk_1` FOREIGN KEY (`faculty_sub_id`) REFERENCES `faculty_subjects` (`faculty_sub_id`) ON DELETE CASCADE;

--
-- Constraints for table `point_equivalent`
--
ALTER TABLE `point_equivalent`
  ADD CONSTRAINT `point_equivalent_ibfk_1` FOREIGN KEY (`faculty_sub_id`) REFERENCES `faculty_subjects` (`faculty_sub_id`) ON DELETE CASCADE;

--
-- Constraints for table `posted_grades`
--
ALTER TABLE `posted_grades`
  ADD CONSTRAINT `posted_grades_ibfk_1` FOREIGN KEY (`student_data_id`) REFERENCES `students` (`student_data_id`),
  ADD CONSTRAINT `posted_grades_ibfk_2` FOREIGN KEY (`faculty_sub_id`) REFERENCES `faculty_subjects` (`faculty_sub_id`);

--
-- Constraints for table `student_grades`
--
ALTER TABLE `student_grades`
  ADD CONSTRAINT `student_grades_ibfk_1` FOREIGN KEY (`faculty_sub_id`) REFERENCES `faculty_subjects` (`faculty_sub_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_grades_ibfk_2` FOREIGN KEY (`student_data_id`) REFERENCES `students` (`student_data_id`) ON DELETE CASCADE;

--
-- Constraints for table `sub_components`
--
ALTER TABLE `sub_components`
  ADD CONSTRAINT `sub_components_ibfk_1` FOREIGN KEY (`period_id`) REFERENCES `periods` (`period_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
