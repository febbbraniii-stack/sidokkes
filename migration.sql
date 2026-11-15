CREATE DATABASE IF NOT EXISTS sidokkes_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sidokkes_db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS pemeriksaan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nomor_tahanan VARCHAR(50) NOT NULL,
  nama_tahanan VARCHAR(100) NOT NULL,
  tanggal_lahir DATE NULL,
  tanggal_periksa DATE NOT NULL,
  jenis_kelamin ENUM('Laki-laki','Perempuan','Lainnya') NOT NULL DEFAULT 'Laki-laki',
  keluhan TEXT NOT NULL,
  tekanan_darah VARCHAR(30) DEFAULT '',
  suhu DECIMAL(4,1) NULL,
  berat_badan DECIMAL(5,2) NULL,
  diagnosis TEXT NOT NULL,
  tindak_lanjut TEXT DEFAULT '',
  petugas_id INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pemeriksaan_petugas FOREIGN KEY (petugas_id) REFERENCES users(id) ON DELETE SET NULL
);
