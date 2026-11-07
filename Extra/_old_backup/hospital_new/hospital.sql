CREATE TABLE hospitals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hospital_name VARCHAR(255) NOT NULL,
    authorized_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    contact_number VARCHAR(15) NOT NULL,
    city VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO hospitals (hospital_name, authorized_name, email, contact_number, city, password, address) 
VALUES ('Aga Khan University Hospital', 'Dr. Ahmed', 'info@aku.edu', '923001234567', 'Karachi East', '12345678', 'Stadium Road, Karachi');



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

CREATE TABLE hospital_vaccine_stock (
    stock_id INT AUTO_INCREMENT PRIMARY KEY,
    hospital_id INT NOT NULL,
    vaccine_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT hospital_vaccine
        UNIQUE (hospital_id, vaccine_id),

    CONSTRAINT stock_hospital
        FOREIGN KEY (hospital_id)
        REFERENCES hospitals(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT stock_vaccine
        FOREIGN KEY (vaccine_id)
        REFERENCES vaccines(vaccine_ID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);