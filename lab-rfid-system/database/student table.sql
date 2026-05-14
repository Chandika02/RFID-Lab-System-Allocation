CREATE TABLE studentss (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    reg_no VARCHAR(20) UNIQUE,
    rfid_uid VARCHAR(50) UNIQUE,
    year VARCHAR(10),
    class VARCHAR(10),
    section VARCHAR(10)
);