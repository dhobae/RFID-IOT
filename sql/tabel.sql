CREATE TABLE rfid_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rfid_id VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
);

-- dummy data
INSERT INTO rfid_logs (rfid_id, name, created_at, updated_at) VALUES
('1234567890', 'Alice Johnson', '2024-11-07 08:15:00', NULL),  -- Entry only, no exit yet
('0987654321', 'Bob Smith', '2024-11-07 09:30:00', '2024-11-07 11:45:00'),  -- Entry and exit
('5678901234', 'Charlie Brown', '2024-11-07 07:00:00', NULL),  -- Entry only, no exit yet
('2345678901', 'David Wilson', '2024-11-06 14:00:00', '2024-11-06 18:00:00');  -- Entry and exit


CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'user') DEFAULT 'user',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tambahkan pengguna admin dengan password hashed
INSERT INTO `users` (`username`, `password`, `role`) VALUES
('admin', '$2y$10$ttNxLIqnaJNt1.7eFbeXcuiCP430RAyerNyvCPZY0yDDPZlFT1r9K', 'admin');

