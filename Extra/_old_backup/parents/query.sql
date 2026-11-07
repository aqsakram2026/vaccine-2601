

CREATE TABLE parents(
    PID  int AUTO_INCREMENT  NOT null PRIMARY KEY,
    name varchar(255) NOT null,
    Relation_with_child ENUM('Mother','Father','Guardian') DEFAULT 'Guardian',
    Phone_no int not null UNIQUE,
    email varchar(255) not null UNIQUE,
    CNIC char(13) NOT null UNIQUE,
    City varchar(255),
    address varchar(255),
    Hashed_password varchar(255) Not null);

    INSERT INTO parents(name,Relation_with_child,Phone_no,email,CNIC,Hashed_password,City,address)
VALUES('iman imran','mother',03482907868,'iman@gmail.com','42201987654123','hashedpassword123','karachi','comm5');


-- booking update
ALTER TABLE bookings
ADD COLUMN parent_id INT(11) NOT NULL,
ADD COLUMN child_id INT(11) NOT NULL,
ADD CONSTRAINT appointments_parent
    FOREIGN KEY (parent_id)
    REFERENCES parents(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
ADD CONSTRAINT appointments_child
    FOREIGN KEY (child_id)
    REFERENCES children(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE;




































































































    