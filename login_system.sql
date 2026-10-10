CREATE DATABASE IF NOT EXISTS `login_system`;
USE `login_system`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('user','admin') NOT NULL DEFAULT 'user'
);

-- Default account: admin / password
INSERT INTO `users` (`username`, `password`, `role`) VALUES
('admin', '$2y$10$MXSd58qxdq34zTUtFNccSe9lk4Pfxug4Ah9J2HXac6oKgEltQj/lS', 'user')
ON DUPLICATE KEY UPDATE `id`=`id`;