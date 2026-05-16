CREATE DATABASE IF NOT EXISTS cti_dashboard CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE cti_dashboard;

DROP TABLE IF EXISTS uploads;
DROP TABLE IF EXISTS reports;
DROP TABLE IF EXISTS threat_intel;
DROP TABLE IF EXISTS investigations;
DROP TABLE IF EXISTS indicators;
DROP TABLE IF EXISTS incidents;
DROP TABLE IF EXISTS alerts;
DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(40),
    organization VARCHAR(160),
    status ENUM('Active', 'Blocked') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(160) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) DEFAULT 0,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    message TEXT,
    status ENUM('Pending', 'Approved', 'Rejected', 'Completed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);

CREATE TABLE alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    source VARCHAR(120),
    severity ENUM('Low', 'Medium', 'High', 'Critical') DEFAULT 'Medium',
    status ENUM('Open', 'In Progress', 'Closed') DEFAULT 'Open',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE incidents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    severity ENUM('Low', 'Medium', 'High', 'Critical') DEFAULT 'Medium',
    status ENUM('Open', 'Investigating', 'Resolved') DEFAULT 'Open',
    assigned_to VARCHAR(120),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE indicators (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('IP', 'Domain', 'Hash', 'URL') NOT NULL,
    value VARCHAR(255) NOT NULL,
    threat_type VARCHAR(120),
    confidence INT DEFAULT 50,
    source VARCHAR(120),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE investigations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    incident_id INT,
    analyst_id INT,
    title VARCHAR(180) NOT NULL,
    notes TEXT,
    result VARCHAR(160),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (incident_id) REFERENCES incidents(id) ON DELETE SET NULL,
    FOREIGN KEY (analyst_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE threat_intel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    feed_name VARCHAR(160) NOT NULL,
    source_url VARCHAR(255),
    category VARCHAR(120),
    summary TEXT,
    published_at DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    type VARCHAR(80),
    content TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    file_name VARCHAR(255) NOT NULL,
    original_name VARCHAR(255),
    mime_type VARCHAR(120),
    related_table VARCHAR(80),
    related_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

INSERT INTO roles (id, name) VALUES
(1, 'User / SOC Analyst'),
(2, 'Admin'),
(3, 'SOC Leader'),
(4, 'Threat Intelligence Researcher'),
(5, 'System Administrator');

INSERT INTO users (role_id, name, email, password, phone, organization) VALUES
(2, 'Admin Demo', 'admin@cti.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi', '+216 00 000 000', 'CTI Lab'),
(1, 'SOC Analyst Demo', 'analyst@cti.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi', '+216 11 111 111', 'Blue Team'),
(3, 'SOC Leader Demo', 'leader@cti.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi', '+216 22 222 222', 'SOC'),
(4, 'Researcher Demo', 'researcher@cti.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi', '+216 33 333 333', 'Threat Intel'),
(5, 'System Admin Demo', 'sysadmin@cti.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi', '+216 44 444 444', 'IT');

INSERT INTO services (name, description, price, status) VALUES
('Threat Intelligence Report', 'Monthly CTI report with actor tracking, campaigns, and recommended mitigations.', 300.00, 'Active'),
('Incident Investigation', 'Investigation support for malware, phishing, intrusion, or suspicious activity.', 600.00, 'Active'),
('IOC Enrichment Pack', 'Enrichment of IP, domain, URL, and hash indicators with confidence scoring.', 150.00, 'Active');

INSERT INTO reservations (user_id, service_id, message, status) VALUES
(2, 2, 'Need help investigating suspicious PowerShell activity.', 'Pending'),
(3, 1, 'Request a report about ransomware activity targeting finance.', 'Approved');

INSERT INTO alerts (title, source, severity, status, description) VALUES
('Suspicious outbound connection', 'SIEM', 'High', 'Open', 'Endpoint contacted a known command and control IP.'),
('Phishing campaign detected', 'Email Gateway', 'Medium', 'In Progress', 'Multiple users received similar credential harvesting emails.'),
('Critical malware hash match', 'EDR', 'Critical', 'Open', 'EDR detected a hash associated with ransomware tooling.');

INSERT INTO incidents (title, severity, status, assigned_to, description) VALUES
('Ransomware suspicion on HR workstation', 'Critical', 'Investigating', 'SOC Analyst Demo', 'Unusual encryption activity and malicious hash match.'),
('Credential phishing attempt', 'Medium', 'Open', 'Researcher Demo', 'Fake login page reported by users.'),
('Port scanning from external IP', 'Low', 'Resolved', 'SOC Analyst Demo', 'Blocked by firewall rule.');

INSERT INTO indicators (type, value, threat_type, confidence, source) VALUES
('IP', '185.199.110.153', 'C2', 85, 'Open feed'),
('Domain', 'login-secure-example.com', 'Phishing', 78, 'Email Gateway'),
('Hash', '44d88612fea8a8f36de82e1278abb02f', 'Malware', 92, 'EDR'),
('URL', 'http://malicious-example.test/payload', 'Payload Delivery', 88, 'Sandbox');

INSERT INTO investigations (incident_id, analyst_id, title, notes, result) VALUES
(1, 2, 'Initial ransomware triage', 'Collected EDR timeline, isolated host, and exported suspicious files.', 'Containment started'),
(2, 4, 'Phishing infrastructure review', 'Reviewed domains, headers, and landing page kit.', 'Indicators published');

INSERT INTO threat_intel (feed_name, source_url, category, summary, published_at) VALUES
('Abuse.ch URLhaus', 'https://urlhaus.abuse.ch/', 'Malware URLs', 'Malicious URLs feed used for IOC enrichment.', '2026-05-01'),
('CISA Known Exploited Vulnerabilities', 'https://www.cisa.gov/known-exploited-vulnerabilities-catalog', 'Vulnerabilities', 'Known exploited vulnerabilities for patch prioritization.', '2026-05-02');

INSERT INTO reports (title, type, content, created_by) VALUES
('Weekly Threat Brief', 'Threat Brief', 'Summary of phishing, malware, and scanning activity observed this week.', 4),
('Incident Report - HR Workstation', 'Incident Report', 'Initial investigation notes, evidence, impact, and containment actions.', 2);
