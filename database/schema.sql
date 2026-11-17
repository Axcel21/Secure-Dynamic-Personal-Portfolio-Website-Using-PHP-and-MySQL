CREATE DATABASE IF NOT EXISTS portfolio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE portfolio_db;

CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS login_attempts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ip_hash CHAR(64) NOT NULL,
  attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (ip_hash, attempted_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS settings (
  `key` VARCHAR(50) PRIMARY KEY,
  `value` TEXT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS skills (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  level TINYINT UNSIGNED NOT NULL DEFAULT 0,
  note VARCHAR(255) NOT NULL DEFAULT '',
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  description TEXT NOT NULL,
  tag VARCHAR(50) NOT NULL,
  tools VARCHAR(150) NOT NULL DEFAULT '',
  image VARCHAR(255) NOT NULL DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS resume_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  section VARCHAR(40) NOT NULL,
  title VARCHAR(150) NOT NULL DEFAULT '',
  org VARCHAR(150) NOT NULL DEFAULT '',
  body TEXT NOT NULL,
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  subject VARCHAR(150) NOT NULL,
  message TEXT NOT NULL,
  ip_hash CHAR(64) NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (ip_hash, created_at)
) ENGINE=InnoDB;

INSERT INTO settings (`key`,`value`) VALUES
('short_name','Axcel John'),
('role','BSINT Student'),
('email','axceljohn@example.com'),
('github','https://github.com/Axcel21'),
('location','Purok 2 Bayawan City'),
('hero_intro','Hello! Welcome to my personal portfolio website. My name is Axcel John Cañete, a passionate BSINT student at NORSU-BSC who enjoys learning about web development, programming, and modern technology. I am continuously improving my skills in HTML, CSS, JavaScript, and other IT-related fields. My goal is to become a skilled IT professional and contribute innovative solutions through technology.'),
('about_lead','I am Axcel John F. Cañete, a third-year Bachelor of Science in Information Technology student at Negros Oriental State University. I am passionate about technology and continuously improving my knowledge and skills in web development, programming, networking, and database management.'),
('about_background','I am a student currently pursuing a Bachelor of Science in Information Technology (BSINT). I have a strong interest in technology, computers, and digital innovation. Throughout my studies, I have been developing my knowledge and skills in programming, web development, database management, and computer systems.'),
('about_education','BS Information Technology, Negros Oriental State University · 2024–present'),
('interests','Hiking,Learning Programming,Movie,Mobile gaming,Coffee')
ON DUPLICATE KEY UPDATE `value`=VALUES(`value`);

INSERT INTO skills (name,level,note,sort_order) VALUES
('HTML5',25,'Semantic markup, accessibility basics, forms.',1),
('CSS3',25,'Flexbox, Grid, responsive layout, animations.',2),
('JavaScript',25,'DOM manipulation, events, async/await, fetch.',3),
('React',30,'Component design, hooks, basic state management.',4),
('SQL & Databases',50,'Schema design, joins, basic query tuning.',5),
('Git & GitHub',5,'Branching, pull requests, resolving conflicts.',6),
('UI/UX Design',5,'Wireframing in Figma, layout, accessible color.',7),
('Cisco Packet Tracer',50,'Network simulation tool for creating, configuring, and testing virtual networks.',8);

INSERT INTO projects (title,description,tag,tools,image) VALUES
('Rise Brew','Modern coffee shop that offers freshly brewed coffee, refreshing beverages, and delicious snacks in a welcoming environment.','Animation','Animation, Adobe Animate','images/rise brew.jpg'),
('Activity 2 Hybrid Ring and Star Configuration','Network simulation built to design, configure, and test computer networks in a virtual environment.','Cisco','Cisco Packet Tracer','images/cisco.jpg'),
('Power Point Project','A simple and colorful outdoor scene featuring a sunset, green landscape, a tree, and people walking on a road.','Power Point','Power Point','images/Power Point Project.jpg'),
('Activity 1 Small Office','Network layout diagram showing a router, switch, server, wireless access point, desktop computers, and laptops.','Adobe Illustrator','Visual Graphics & Design, Adobe Illustrator','images/adobe illustrator.jpg');

INSERT INTO resume_items (section,title,org,body,sort_order) VALUES
('Education','BSINT Student','Negros Oriental State University','Currently a third-year Bachelor of Science in Information Technology student, passionate about web development, programming, networking, and database management.',1),
('Education','Senior High School, HUMSS Strand','Bayawan National Senior High School','Developed critical thinking, research, problem-solving, and communication skills through social sciences, literature, politics, and human interactions.',2),
('Experience','','','Basic experience in computer hardware: assembling and disassembling components, installing RAM, hard drives, SSDs, and peripherals, troubleshooting common issues, and performing routine maintenance.',1),
('Training','','','Training in installing, configuring, and maintaining computer networks and systems: network setup, cable installation, IP addressing, troubleshooting, and basic hardware/software maintenance.',1),
('Certifications','TESDA NC II – Computer Systems Servicing','','Hands-on skills in configuring network connections, assembling hardware, installing operating systems, and maintaining IT equipment.',1);
