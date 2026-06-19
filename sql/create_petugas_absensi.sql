-- Migration: create table petugas_absensi
-- Dialect: MySQL (suitable for XAMPP)

DROP TABLE IF EXISTS `petugas_absensi`;

-- Ensure referenced column `users.id` is indexed and has compatible type
ALTER TABLE `users` ADD INDEX (`id`);

CREATE TABLE `petugas_absensi` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `foto_referensi` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX (`user_id`),
  CONSTRAINT `fk_petugas_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
