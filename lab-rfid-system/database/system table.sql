CREATE TABLE systems (
    system_id INT AUTO_INCREMENT PRIMARY KEY,
    system_name VARCHAR(20),
    status VARCHAR(20) DEFAULT 'FREE'
);