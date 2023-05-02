-- Role table
CREATE TABLE Role (
  role_id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL
);

-- Company table
CREATE TABLE Company (
  company_id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  website VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  address VARCHAR(255) NOT NULL
);

-- Menu table
CREATE TABLE Menu (
  menu_id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  url VARCHAR(255) NOT NULL,
  parent_id INT,
  is_top_menu BOOLEAN NOT NULL,
  FOREIGN KEY (parent_id) REFERENCES Menu(menu_id)
);

-- Permission table
CREATE TABLE Permission (
  permission_id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL
);

-- Group table
CREATE TABLE `Group` (
  group_id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL
);

-- User table
CREATE TABLE `User` (
  `user_id` INT PRIMARY KEY AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `status` ENUM('active', 'banned', 'unverified') NOT NULL,
  `last_login_date` DATETIME,
  `registration_date` DATETIME NOT NULL,
  `user_type` ENUM('internal', 'external') NOT NULL,
  `is_verified` BOOLEAN NOT NULL,
  `is_elevated` BOOLEAN NOT NULL,
  `role_id` INT NOT NULL,
  `company_id` INT,
  `kyc_status` ENUM('pending', 'approved', 'unverified') NOT NULL,
  `employee_id` INT,
  `is_active` BOOLEAN,
  FOREIGN KEY (`role_id`) REFERENCES `Role`(`role_id`),
  FOREIGN KEY (`company_id`) REFERENCES `Company`(`company_id`),
  CONSTRAINT `check_user_type` CHECK (`user_type` IN ('internal', 'external')),
  CONSTRAINT `check_employee_id` CHECK ((`user_type` = 'external' AND `employee_id` IS NOT NULL) OR `user_type` = 'internal')
);

-- UserGroup table
CREATE TABLE UserGroup (
  user_id INT NOT NULL,
  group_id INT NOT NULL,
  PRIMARY KEY (user_id, group_id),
  FOREIGN KEY (user_id) REFERENCES `User`(user_id),
  FOREIGN KEY (group_id) REFERENCES `Group`(group_id)
);

-- GroupPermission table
CREATE TABLE GroupPermission (
  group_id INT NOT NULL,
  permission_id INT NOT NULL,
  PRIMARY KEY (group_id, permission_id),
  FOREIGN KEY (group_id) REFERENCES `Group`(group_id),
  FOREIGN KEY (permission_id) REFERENCES Permission(permission_id)
);

-- RolePermission table
CREATE TABLE RolePermission (
  role_id INT NOT NULL,
  permission_id INT NOT NULL,
  PRIMARY KEY (role_id, permission_id),
  FOREIGN KEY (role_id) REFERENCES Role(role_id),
  FOREIGN KEY (permission_id) REFERENCES Permission(permission_id)
);

-- CompanyUser table
CREATE TABLE CompanyUser (
  company_id INT NOT NULL,
  user_id INT NOT NULL,
  PRIMARY KEY (company_id, user_id),
  FOREIGN KEY (company_id) REFERENCES Company(company_id),
  FOREIGN KEY (user_id) REFERENCES `User`(user_id)
);

CREATE TABLE MenuPermission (
  menu_id INT NOT NULL,
  permission_id INT NOT NULL,
  PRIMARY KEY (menu_id, permission_id),
  FOREIGN KEY (menu_id) REFERENCES Menu(menu_id),
  FOREIGN KEY (permission_id) REFERENCES Permission(permission_id)
);

CREATE TABLE OAuthProvider (
  provider_id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL
);

CREATE TABLE OAuthProviderConfiguration (
  provider_id INT NOT NULL,
  configuration_key VARCHAR(50) NOT NULL,
  configuration_value VARCHAR(255) NOT NULL,
  PRIMARY KEY (provider_id, configuration_key),
  FOREIGN KEY (provider_id) REFERENCES OAuthProvider(provider_id)
);

CREATE TABLE Configuration (
  configuration_id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  value VARCHAR(255) NOT NULL
);

CREATE TABLE NotificationTemplate (
  template_id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  subject VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  is_global BOOLEAN NOT NULL
);

CREATE TABLE NotificationType (
  type_id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL
);

CREATE TABLE Notification (
  notification_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  type_id INT NOT NULL,
  template_id INT NOT NULL,
  message TEXT NOT NULL,
  date_sent DATETIME NOT NULL,
  is_read BOOLEAN NOT NULL,
  FOREIGN KEY (user_id) REFERENCES `User`(user_id),
  FOREIGN KEY (type_id) REFERENCES NotificationType(type_id),
  FOREIGN KEY (template_id) REFERENCES NotificationTemplate(template_id)
);

CREATE TABLE BugReport (
  report_id INT PRIMARY KEY,
  user_id INT,
  title VARCHAR(255),
  description TEXT,
  date_submitted DATE,
  FOREIGN KEY (user_id) REFERENCES `User`(user_id)
);

CREATE TABLE FeatureRequest (
  request_id INT PRIMARY KEY,
  user_id INT,
  title VARCHAR(255),
  description TEXT,
  date_submitted DATE,
  FOREIGN KEY (user_id) REFERENCES `User`(user_id)
);

CREATE TABLE SupportRequest (
  request_id INT PRIMARY KEY,
  user_id INT,
  title VARCHAR(255),
  description TEXT,
  date_submitted DATE,
  FOREIGN KEY (user_id) REFERENCES `User`(user_id)
);

CREATE TABLE RestfulAPI (
  api_id INT PRIMARY KEY,
  name VARCHAR(255),
  url VARCHAR(255)
);

CREATE TABLE Support (
  support_id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  status ENUM('New', 'In Progress', 'Closed') DEFAULT 'New',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  user_id INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES `User`(user_id) ON DELETE CASCADE
);

CREATE TABLE OAuth (
  oauth_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  provider_id INT NOT NULL,
  provider_user_id VARCHAR(255) NOT NULL,
  access_token VARCHAR(255) NOT NULL,
  refresh_token VARCHAR(255),
  expires_in INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES `User`(user_id) ON DELETE CASCADE,
  FOREIGN KEY (provider_id) REFERENCES OAuthProvider(provider_id) ON DELETE CASCADE
);

CREATE TABLE NotificationSetting (
  setting_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  notification_type_id INT NOT NULL,
  is_enabled BOOLEAN NOT NULL,
  FOREIGN KEY (user_id) REFERENCES `User`(user_id) ON DELETE CASCADE,
  FOREIGN KEY (notification_type_id) REFERENCES NotificationType(type_id) ON DELETE CASCADE
);

-- Email table
CREATE TABLE email (
  email_id INT PRIMARY KEY AUTO_INCREMENT,
  email VARCHAR(255) NOT NULL,
  date_created DATETIME NOT NULL,
  user_id INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES `User`(user_id)
);

-- capi_phonenumbers table
CREATE TABLE capi_phonenumbers (
  phone_id INT PRIMARY KEY AUTO_INCREMENT,
  phone_number VARCHAR(20) NOT NULL,
  date_added DATETIME NOT NULL,
  active TINYINT NOT NULL,
  user_id INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES `User`(user_id)
);

-- Department table
CREATE TABLE department (
  id INT PRIMARY KEY,
  name VARCHAR(50) UNIQUE
);

-- Designation table
CREATE TABLE designation (
  id INT PRIMARY KEY,
  name VARCHAR(50) UNIQUE,
  department_id INT,
  FOREIGN KEY (department_id) REFERENCES department(id)
);

CREATE TABLE grade_level (
  grade_id INT PRIMARY KEY,
  grade_level VARCHAR(50),
  grade_step INT,
  status ENUM('active', 'inactive') NOT NULL,
  date_added DATETIME NOT NULL
);

-- Office table
CREATE TABLE office (
  office_id INT PRIMARY KEY,
  office_name VARCHAR(255) NOT NULL,
  office_description VARCHAR(255),
  office_location VARCHAR(255)
);

-- Personnel table
CREATE TABLE personnel (
  personnel_id INT PRIMARY KEY,
  first_name VARCHAR(50) NOT NULL,
  last_name VARCHAR(50) NOT NULL,
  date_of_birth DATE,
  marital_status VARCHAR(50),
  gender VARCHAR(50),
  department_id INT NOT NULL,
  designation_id INT NOT NULL,
  current_grade_id INT NOT NULL,
  date_of_employment DATE,
  office_id INT NOT NULL,
  FOREIGN KEY (department_id) REFERENCES department(id),
  FOREIGN KEY (designation_id) REFERENCES designation(id),
  FOREIGN KEY (current_grade_id) REFERENCES grade_level(grade_id),
  FOREIGN KEY (office_id) REFERENCES office(office_id)
);


-- PersonnelPosting table
CREATE TABLE PersonnelPosting (
  posting_id INT PRIMARY KEY AUTO_INCREMENT,
  date_of_posting DATETIME NOT NULL,
  personnel_id INT NOT NULL,
  department_id INT NOT NULL,
  designation_id INT NOT NULL,
  current_grade_id INT NOT NULL,
  FOREIGN KEY (personnel_id) REFERENCES personnel(personnel_id),
  FOREIGN KEY (department_id) REFERENCES department(id),
  FOREIGN KEY (designation_id) REFERENCES designation(id),
  FOREIGN KEY (current_grade_id) REFERENCES grade_level(grade_id)
);

-- Address table
CREATE TABLE addresses (
  id INT PRIMARY KEY,
  user_id INT UNIQUE,
  street_address VARCHAR(255),
  city VARCHAR(50),
  state VARCHAR(50),
  country VARCHAR(50),
  FOREIGN KEY (user_id) REFERENCES User(user_id)
);

-- Personal Information table
CREATE TABLE personal_information (
  id INT PRIMARY KEY,
  user_id INT UNIQUE,
  first_name VARCHAR(50),
  last_name VARCHAR(50),
  date_of_birth DATE,
  marital_status VARCHAR(50),
  gender VARCHAR(50),
  FOREIGN KEY (user_id) REFERENCES User(user_id)
);

-- Medical table
CREATE TABLE medical (
  id INT PRIMARY KEY,
  user_id INT,
  medical_condition VARCHAR(255),
  insurance VARCHAR(50) DEFAULT 'ASHIA',
  hospital VARCHAR(255),
  doctor VARCHAR(255),
  FOREIGN KEY (user_id) REFERENCES User(user_id)
);

-- Asset table
CREATE TABLE assets (
  id INT PRIMARY KEY,
  user_id INT,
  asset_type VARCHAR(50),
  asset_description VARCHAR(255),
  year_of_purchase INT,
  year_acquired INT,
  FOREIGN KEY (user_id) REFERENCES User(user_id)
);

-- Financial Activities table
CREATE TABLE financial_activities (
  id INT PRIMARY KEY,
  user_id INT,
  activity_type VARCHAR(50),
  amount DECIMAL(10,2),
  date_of_payment DATE,
  FOREIGN KEY (user_id) REFERENCES User(user_id)
);

-- Education table
CREATE TABLE education (
  id INT PRIMARY KEY,
  user_id INT,
  institution VARCHAR(255),
  degree VARCHAR(50),
  field_of_study VARCHAR(50),
  date_attended_from DATE,
  date_attended_to DATE,
  FOREIGN KEY (user_id) REFERENCES User(user_id)
);

-- Spouse table
CREATE TABLE spouses (
  id INT PRIMARY KEY,
  user_id INT,
  first_name VARCHAR(50),
  last_name VARCHAR(50),
  date_of_birth DATE,
  FOREIGN KEY (user_id) REFERENCES User(user_id)
);

-- Children table
CREATE TABLE children (
  id INT PRIMARY KEY,
  user_id INT,
  first_name VARCHAR(50),
  last_name VARCHAR(50),
  date_of_birth DATE,
  FOREIGN KEY (user_id) REFERENCES User(user_id)
);

-- Employment table
CREATE TABLE employment (
  id INT PRIMARY KEY,
  user_id INT,
  employer_name VARCHAR(255),
  job_title VARCHAR(50),
  start_date DATE,
  end_date DATE,
  FOREIGN KEY (user_id) REFERENCES User(user_id)
);

-- Secondary_Data table
CREATE TABLE Secondary_Data (
  id INT PRIMARY KEY AUTO_INCREMENT,
  data_value VARCHAR(255) NOT NULL,
  user_id INT NOT NULL,
  date_added DATETIME NOT NULL,
  status TINYINT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES User(user_id)
);
