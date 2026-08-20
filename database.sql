CREATE DATABASE IF NOT EXISTS medilink;
USE medilink;

CREATE TABLE users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(50) UNIQUE NOT NULL,
 password VARCHAR(255) NOT NULL,
 role VARCHAR(30) DEFAULT 'admin',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE hospitals (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 address VARCHAR(255) NOT NULL,
 phone VARCHAR(30),
 email VARCHAR(120)
);

CREATE TABLE doctors (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 specialization VARCHAR(100) NOT NULL,
 symptoms VARCHAR(255),
 phone VARCHAR(30),
 email VARCHAR(120),
 hospital_id INT NULL,
 FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE SET NULL
);

CREATE TABLE patients (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 dob DATE,
 gender VARCHAR(20),
 phone VARCHAR(30),
 address VARCHAR(255),
 blood_group VARCHAR(10),
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE consultations (
 id INT AUTO_INCREMENT PRIMARY KEY,
 patient_id INT NOT NULL,
 doctor_id INT NOT NULL,
 consultation_date DATE NOT NULL,
 diagnosis TEXT,
 notes TEXT,
 FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
 FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

CREATE TABLE prescriptions (
 id INT AUTO_INCREMENT PRIMARY KEY,
 patient_id INT NOT NULL,
 doctor_id INT NOT NULL,
 prescription_date DATE NOT NULL,
 medicine_name VARCHAR(120) NOT NULL,
 dosage VARCHAR(100),
 instructions TEXT,
 FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
 FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

CREATE TABLE pharmacies (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 address VARCHAR(255) NOT NULL,
 phone VARCHAR(30)
);

CREATE TABLE medicines (
 id INT AUTO_INCREMENT PRIMARY KEY,
 pharmacy_id INT NOT NULL,
 name VARCHAR(120) NOT NULL,
 category VARCHAR(80),
 quantity INT DEFAULT 0,
 price DECIMAL(10,2) DEFAULT 0,
 FOREIGN KEY (pharmacy_id) REFERENCES pharmacies(id) ON DELETE CASCADE
);

CREATE TABLE blood_donors (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 blood_group VARCHAR(10) NOT NULL,
 phone VARCHAR(30) NOT NULL,
 location VARCHAR(150) NOT NULL,
 last_donation_date DATE,
 available TINYINT(1) DEFAULT 1
);

INSERT INTO users(username,password,role) VALUES
('admin',SHA2('admin123',256),'admin');

INSERT INTO hospitals(name,address,phone,email) VALUES
('MediLink General Hospital','Dhaka, Bangladesh','01700000000','info@medilink.local'),
('City Care Hospital','Dhanmondi, Dhaka','01800000000','citycare@example.com');

INSERT INTO doctors(name,specialization,symptoms,phone,email,hospital_id) VALUES
('Dr. Rahman','Cardiology','chest pain, hypertension','01711111111','rahman@example.com',1),
('Dr. Sultana','Dermatology','rash, acne, skin allergy','01822222222','sultana@example.com',2);

INSERT INTO pharmacies(name,address,phone) VALUES
('MediLink Pharmacy','Dhaka','01933333333'),
('City Pharmacy','Dhanmondi','01644444444');

INSERT INTO medicines(pharmacy_id,name,category,quantity,price) VALUES
(1,'Paracetamol 500mg','Pain Relief',120,2.50),
(1,'Amoxicillin 500mg','Antibiotic',50,8.00),
(2,'Cetirizine 10mg','Antihistamine',80,3.00);

INSERT INTO blood_donors(name,blood_group,phone,location,last_donation_date) VALUES
('Arif Hasan','A+','01755555555','Dhaka','2026-05-10'),
('Nusrat Jahan','O+','01866666666','Dhanmondi','2026-04-15');
