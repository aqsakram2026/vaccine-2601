CREATE TABLE hospitals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hospital_name VARCHAR(255) NOT NULL,
    authorized_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    contact_number VARCHAR(15) NOT NULL,
    city VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    STATUS ENUM ('pending','accepted','rejected')
);

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hospital_name VARCHAR(255) NOT NULL,
    child_name VARCHAR(100) NOT NULL,
    parent_email VARCHAR(100) NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    vaccination_status VARCHAR(50) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE vaccines (
    Vaccine_ID INT AUTO_INCREMENT PRIMARY KEY,
    Vaccine_Name VARCHAR(100) NOT NULL,
    Dose INT NOT NULL,
    Age_Limit VARCHAR(50) NOT NULL,
    Status ENUM('Active', 'Inactive')
        DEFAULT 'Active'
);
--  foreign key
CREATE TABLE vaccine_doses (
    Dose_ID INT AUTO_INCREMENT PRIMARY KEY,
    Vaccine_ID INT NOT NULL UNIQUE,
    Hospital_ID INT NOT NULL UNIQUE,
    Dose_Number INT NOT NULL,
    Age_Limit VARCHAR(50) NOT NULL,

    FOREIGN KEY (Vaccine_ID)
        REFERENCES vaccines(Vaccine_ID),
    FOREIGN KEY (Hospital_ID)
        REFERENCES hospitals(id)
);

        -- vaccine table insertion

INSERT INTO vaccines
    (Vaccine_Name, Dose, Age_Limit, Status)
VALUES
    ('BCG', 1, 'At birth', 'Active'),
    ('OPV-0', 0, 'At birth', 'Active'),
    ('Hepatitis B', 1, 'At birth', 'Active'),
    ('Pentavalent-1', 1, '6 weeks', 'Active'),
    ('OPV-1', 1, '6 weeks', 'Active'),
    ('Rotavirus-1', 1, '6 weeks', 'Active'),
    ('PCV-1', 1, '6 weeks', 'Active'),
    ('Pentavalent-2', 2, '10 weeks', 'Active'),
    ('OPV-2', 2, '10 weeks', 'Active'),
    ('Pentavalent-3', 3, '14 weeks', 'Active'),
    ('OPV-3 / IPV', 3, '14 weeks', 'Active'),
    ('Measles-1', 1, '9 months', 'Active'),
    ('MMR', 1, '15 months', 'Active');


        -- update command in vaccine table
UPDATE vaccines
SET Dose = 1
WHERE Vaccine_Name = 'OPV-0';

-- child table

CREATE TABLE children (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NOT NULL,
    child_name VARCHAR(100) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    allergies VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT children_parent
        FOREIGN KEY (parent_id)
        REFERENCES parents(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

    -- children table update
ALTER TABLE children
ADD COLUMN vaccination_date DATE DEFAULT NULL,
ADD COLUMN status ENUM('Pending', 'Verified', 'Rejected')
NOT NULL DEFAULT 'Pending'
