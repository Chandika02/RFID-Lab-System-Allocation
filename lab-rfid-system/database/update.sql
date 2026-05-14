INSERT INTO studentss (name, reg_no, rfid_uid, year, class, section)
VALUES
('BALASUBRAMANIYAN S','927623BEC301','RFID301','III','ECE','A'),
('DARSAN SANJAI SR','927623BEC302','RFID302','III','ECE','A');
UPDATE studentss
SET reg_no = '927623BEC062', rfid_uid = 'RFID062'
WHERE name = 'GUGANATHAN R';
UPDATE studentss
SET reg_no = '927623BEC063', rfid_uid = 'RFID063'
WHERE name = 'HARI PRASATH R';