-- =====================================================================
--  PANASONIC ELECTRIC DISTRIBUTION - DATABASE SCHEMA
--  Engine: InnoDB | Charset: utf8mb4 | Tương thích MySQL 5.7+ / MariaDB 10.4+
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `panasonic_shop`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `panasonic_shop`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `quotes_requests`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `product_series`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `admins`;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- 1. Danh mục sản phẩm
-- ---------------------------------------------------------------------
CREATE TABLE `categories` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(150) NOT NULL,
  `slug`        VARCHAR(170) NOT NULL,
  `icon`        VARCHAR(50)  NOT NULL DEFAULT 'box',
  `description` TEXT NULL,
  `sort_order`  SMALLINT NOT NULL DEFAULT 0,
  `status`      TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_slug` (`slug`),
  KEY `idx_categories_status` (`status`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. Dòng sản phẩm (Series)
-- ---------------------------------------------------------------------
CREATE TABLE `product_series` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(120) NOT NULL,
  `slug`        VARCHAR(140) NOT NULL,
  `tagline`     VARCHAR(255) NULL,
  `color`       VARCHAR(20)  NOT NULL DEFAULT '#004098',
  `catalog_url` VARCHAR(255) NULL,
  `sort_order`  SMALLINT NOT NULL DEFAULT 0,
  `status`      TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_series_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. Sản phẩm
--    specs (JSON) : bảng thông số hiển thị dạng {"Nhãn": "Giá trị"}
--    rated_current / breaking_capacity / poles : cột số hoá để lọc nhanh có index
-- ---------------------------------------------------------------------
CREATE TABLE `products` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id`       INT UNSIGNED NOT NULL,
  `series_id`         INT UNSIGNED NULL,
  `name`              VARCHAR(255) NOT NULL,
  `slug`              VARCHAR(280) NOT NULL,
  `sku`               VARCHAR(60)  NOT NULL,
  `model_code`        VARCHAR(60)  NOT NULL,
  `short_desc`        VARCHAR(500) NULL,
  `description`       TEXT NULL,
  `price`             DECIMAL(15,0) NOT NULL DEFAULT 0,
  `wholesale_price`   DECIMAL(15,0) NOT NULL DEFAULT 0,
  `wholesale_min_qty` INT UNSIGNED NOT NULL DEFAULT 10,
  `stock`             INT NOT NULL DEFAULT 0,
  `unit`              VARCHAR(20) NOT NULL DEFAULT 'Cái',
  `thumbnail`         VARCHAR(255) NULL,
  `gallery`           JSON NULL,
  `specs`             JSON NULL,
  `rated_current`     DECIMAL(6,1) NULL COMMENT 'Dòng định mức (A)',
  `breaking_capacity` DECIMAL(5,1) NULL COMMENT 'Dòng cắt Icu/Icn (kA)',
  `poles`             TINYINT UNSIGNED NULL COMMENT 'Số cực',
  `pdf_catalog_url`   VARCHAR(255) NULL,
  `is_featured`       TINYINT(1) NOT NULL DEFAULT 0,
  `status`            TINYINT(1) NOT NULL DEFAULT 1,
  `views`             INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_products_slug` (`slug`),
  UNIQUE KEY `uq_products_sku` (`sku`),
  KEY `idx_products_model` (`model_code`),
  KEY `idx_products_cat` (`category_id`, `status`),
  KEY `idx_products_series` (`series_id`, `status`),
  KEY `idx_products_filter` (`rated_current`, `breaking_capacity`, `poles`),
  KEY `idx_products_price` (`price`),
  KEY `idx_products_featured` (`is_featured`, `status`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_products_series` FOREIGN KEY (`series_id`) REFERENCES `product_series` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. Yêu cầu báo giá / bóc tách vật tư công trình
-- ---------------------------------------------------------------------
CREATE TABLE `quotes_requests` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_name`  VARCHAR(150) NOT NULL,
  `company`        VARCHAR(200) NULL,
  `customer_type`  ENUM('contractor','dealer','investor','retail') NOT NULL DEFAULT 'contractor',
  `phone`          VARCHAR(20)  NOT NULL,
  `email`          VARCHAR(150) NULL,
  `project_name`   VARCHAR(255) NULL,
  `address`        VARCHAR(255) NULL,
  `file_bom_path`  VARCHAR(255) NULL COMMENT 'Đường dẫn tương đối trong storage/boms',
  `file_original`  VARCHAR(255) NULL,
  `note`           TEXT NULL,
  `status`         ENUM('new','processing','quoted','closed') NOT NULL DEFAULT 'new',
  `ip_address`     VARCHAR(45) NULL,
  `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_quotes_status` (`status`, `created_at`),
  KEY `idx_quotes_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 5. Đơn hàng & chi tiết đơn hàng
-- ---------------------------------------------------------------------
CREATE TABLE `orders` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_code`     VARCHAR(20)  NOT NULL,
  `customer_name`  VARCHAR(150) NOT NULL,
  `phone`          VARCHAR(20)  NOT NULL,
  `email`          VARCHAR(150) NULL,
  `company`        VARCHAR(200) NULL,
  `tax_code`       VARCHAR(20)  NULL,
  `address`        VARCHAR(255) NOT NULL,
  `note`           TEXT NULL,
  `order_type`     ENUM('retail','project') NOT NULL DEFAULT 'retail',
  `subtotal`       DECIMAL(15,0) NOT NULL DEFAULT 0 COMMENT 'Tổng theo giá bán lẻ',
  `discount`       DECIMAL(15,0) NOT NULL DEFAULT 0 COMMENT 'Chiết khấu công trình',
  `total`          DECIMAL(15,0) NOT NULL DEFAULT 0,
  `payment_method` ENUM('cod','bank_transfer') NOT NULL DEFAULT 'cod',
  `status`         ENUM('pending','confirmed','shipping','completed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_orders_code` (`order_code`),
  KEY `idx_orders_status` (`status`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order_items` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`     INT UNSIGNED NOT NULL,
  `product_id`   INT UNSIGNED NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `model_code`   VARCHAR(60)  NOT NULL,
  `price_type`   ENUM('retail','wholesale') NOT NULL DEFAULT 'retail',
  `unit_price`   DECIMAL(15,0) NOT NULL,
  `quantity`     INT UNSIGNED NOT NULL,
  `line_total`   DECIMAL(15,0) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_items_order` (`order_id`),
  CONSTRAINT `fk_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 6. Quản trị viên
-- ---------------------------------------------------------------------
CREATE TABLE `admins` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username`      VARCHAR(60)  NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name`     VARCHAR(150) NULL,
  `role`          ENUM('superadmin','editor','sales') NOT NULL DEFAULT 'editor',
  `last_login`    DATETIME NULL,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_admins_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  DỮ LIỆU MẪU
-- =====================================================================

-- Tài khoản: admin / Admin@123  (ĐỔI MẬT KHẨU NGAY SAU KHI CÀI ĐẶT)
INSERT INTO `admins` (`username`, `password_hash`, `full_name`, `role`) VALUES
('admin', '$2y$10$ZwAM9R1Ygz/rRszfJdHPKOq4WZECVrxfbLI1dh7bV57ssEr9DaRaK', 'Quản trị hệ thống', 'superadmin');

INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `description`, `sort_order`, `status`) VALUES
(1, 'Công tắc - Ổ cắm', 'cong-tac-o-cam', 'switch', 'Công tắc, ổ cắm, mặt che các dòng Wide, Halumie, Gen-X, Moderva, Full Color, Minerva.', 1, 1),
(2, 'Thiết bị đóng cắt MCB/RCBO', 'thiet-bi-dong-cat', 'breaker', 'Cầu dao tự động MCB, RCBO, ELCB, tủ điện Panasonic đạt chuẩn IEC 60898-1 / IEC 61009-1.', 2, 1),
(3, 'Đèn LED chiếu sáng', 'den-led-chieu-sang', 'bulb', 'Đèn LED Downlight âm trần, Panel, Tube, ốp trần tiết kiệm điện.', 3, 1),
(4, 'Quạt trần & Quạt hút', 'quat-tran-quat-hut', 'fan', 'Quạt trần DC Inverter, quạt hút âm trần, quạt thông gió gắn tường.', 4, 1),
(5, 'Ổ cắm âm sàn / Công nghiệp', 'o-cam-am-san-cong-nghiep', 'plug', 'Ổ cắm âm sàn chống nước, ổ cắm & phích cắm công nghiệp IP44/IP67.', 5, 1);

INSERT INTO `product_series` (`id`, `name`, `slug`, `tagline`, `color`, `catalog_url`, `sort_order`) VALUES
(1, 'Wide Series',  'wide',       'Thiết kế phẳng tràn viền – bền bỉ, phổ biến nhất', '#e8edf3', 'uploads/catalogs/panasonic-wide-series.pdf', 1),
(2, 'Halumie',      'halumie',    'Tinh tế, kinh tế – lựa chọn cho căn hộ hiện đại', '#f3f4f6', 'uploads/catalogs/panasonic-halumie.pdf', 2),
(3, 'Gen-X',        'gen-x',      'Mỏng nhẹ, viền nhôm cao cấp', '#d9dde3', 'uploads/catalogs/panasonic-gen-x.pdf', 3),
(4, 'Moderva',      'moderva',    'Xám ánh kim sang trọng – dòng cao cấp mới', '#6b7280', 'uploads/catalogs/panasonic-moderva.pdf', 4),
(5, 'Full Color',   'full-color', 'Đa sắc màu – bảo vệ an toàn trẻ nhỏ', '#ffffff', 'uploads/catalogs/panasonic-full-color.pdf', 5),
(6, 'Minerva',      'minerva',    'Dòng phổ thông, giá tốt cho công trình', '#f8fafc', 'uploads/catalogs/panasonic-minerva.pdf', 6);

INSERT INTO `products`
(`id`,`category_id`,`series_id`,`name`,`slug`,`sku`,`model_code`,`short_desc`,`description`,`price`,`wholesale_price`,`wholesale_min_qty`,`stock`,`unit`,`thumbnail`,`gallery`,`specs`,`rated_current`,`breaking_capacity`,`poles`,`pdf_catalog_url`,`is_featured`,`status`) VALUES

(1, 1, 4, 'Công tắc 1 chiều Moderva Xám Ánh Kim', 'cong-tac-1-chieu-moderva-xam-anh-kim', 'PN-MOD-501MYZ', 'WMF501MYZ',
 'Công tắc 1 chiều 16AX dòng Moderva, màu xám ánh kim cao cấp, bề mặt chống bám vân tay.',
 'Dòng Moderva mang phong cách tối giản Nhật Bản với tông Xám Ánh Kim (Metallic Grey), phím bấm êm, tuổi thọ cơ học trên 40.000 lần đóng ngắt. Phù hợp biệt thự, căn hộ cao cấp và khách sạn.',
 98000, 82000, 20, 450, 'Cái', 'uploads/products/switch-grey.svg',
 '["uploads/products/switch-grey.svg","uploads/products/switch-white.svg"]',
 '{"Dòng định mức":"16AX","Điện áp định mức":"250V~","Loại":"Công tắc 1 chiều","Màu sắc":"Xám Ánh Kim (MYZ)","Kích thước đế âm":"Đế vuông 86 x 86 mm","Vật liệu":"Nhựa PC chống cháy V-0","Tiêu chuẩn":"IEC 60669-1","Đóng gói":"10 cái/hộp – 200 cái/thùng","Bảo hành":"24 tháng"}',
 16.0, NULL, 1, 'uploads/catalogs/panasonic-moderva.pdf', 1, 1),

(2, 1, 1, 'Ổ cắm đôi có màn che Wide Series WEV1582SW', 'o-cam-doi-co-man-che-wide-wev1582sw', 'PN-WID-1582SW', 'WEV1582SW',
 'Ổ cắm đôi 3 chấu có màn che an toàn, 16A 250V, lắp mặt Wide Series.',
 'Ổ cắm đôi có dây nối đất với màn che bảo vệ chống trẻ em chọc vật kim loại. Tiếp điểm hợp kim đồng phosphor, kẹp chặt phích cắm, giảm phát nhiệt.',
 62000, 51000, 20, 1200, 'Cái', 'uploads/products/socket-white.svg',
 '["uploads/products/socket-white.svg"]',
 '{"Dòng định mức":"16A","Điện áp định mức":"250V~","Số chấu":"3 chấu (có tiếp địa)","Màn che an toàn":"Có","Màu sắc":"Trắng (SW)","Kích thước đế âm":"Đế chữ nhật WEV68xx / đế âm 2 thiết bị","Tiêu chuẩn":"IEC 60884-1","Đóng gói":"10 cái/hộp","Bảo hành":"24 tháng"}',
 16.0, NULL, NULL, 'uploads/catalogs/panasonic-wide-series.pdf', 1, 1),

(3, 1, 1, 'Công tắc B 1 chiều Wide Series WEV5001SW', 'cong-tac-b-1-chieu-wide-wev5001sw', 'PN-WID-5001SW', 'WEV5001SW',
 'Hạt công tắc 1 chiều cỡ S, 16AX 250V, dòng Wide phổ biến nhất.',
 'Hạt công tắc Wide Series lắp ghép linh hoạt 1–6 thiết bị trên mặt che. Thiết kế phím rộng, thao tác nhẹ, có thể gắn đèn báo dạ quang.',
 19000, 15500, 50, 5000, 'Cái', 'uploads/products/switch-white.svg',
 '["uploads/products/switch-white.svg"]',
 '{"Dòng định mức":"16AX","Điện áp định mức":"250V~","Loại":"1 chiều – size S","Màu sắc":"Trắng (SW)","Kích thước đế âm":"Đế chữ nhật 120 x 70 mm","Tiêu chuẩn":"IEC 60669-1","Đóng gói":"10 cái/hộp – 500 cái/thùng","Bảo hành":"24 tháng"}',
 16.0, NULL, 1, 'uploads/catalogs/panasonic-wide-series.pdf', 0, 1),

(4, 1, 1, 'Mặt che 1 thiết bị Wide Series WEG6801SW', 'mat-che-1-thiet-bi-wide-weg6801sw', 'PN-WID-6801SW', 'WEG6801SW',
 'Mặt che dùng cho 1 thiết bị Wide, nhựa trắng bóng chống ố vàng.',
 'Mặt che chuẩn Wide Series, khớp hoàn hảo với hạt công tắc và ổ cắm Wide, dễ tháo lắp bảo trì.',
 14000, 11000, 50, 3000, 'Cái', 'uploads/products/plate-white.svg', NULL,
 '{"Loại":"Mặt che 1 thiết bị","Màu sắc":"Trắng (SW)","Vật liệu":"Nhựa PC","Kích thước":"120 x 70 mm","Đóng gói":"10 cái/hộp","Bảo hành":"24 tháng"}',
 NULL, NULL, NULL, 'uploads/catalogs/panasonic-wide-series.pdf', 0, 1),

(5, 1, 2, 'Công tắc 1 chiều Halumie WEVH5531-7', 'cong-tac-1-chieu-halumie-wevh5531-7', 'PN-HAL-5531', 'WEVH5531-7',
 'Bộ công tắc đơn 1 chiều kèm mặt Halumie, 16A 250V, màu trắng.',
 'Halumie là dòng công tắc nguyên bộ (kèm mặt), thiết kế bo cong tinh tế, giá thành hợp lý cho dự án nhà phố và chung cư.',
 42000, 34000, 30, 800, 'Bộ', 'uploads/products/switch-white.svg', NULL,
 '{"Dòng định mức":"16A","Điện áp định mức":"250V~","Loại":"Công tắc đơn 1 chiều nguyên bộ","Kích thước đế âm":"Đế vuông 86 x 86 mm","Tiêu chuẩn":"IEC 60669-1","Đóng gói":"10 bộ/hộp","Bảo hành":"24 tháng"}',
 16.0, NULL, 1, 'uploads/catalogs/panasonic-halumie.pdf', 0, 1),

(6, 1, 3, 'Công tắc 1 chiều Gen-X viền nhôm WNG5001W', 'cong-tac-1-chieu-gen-x-wng5001w', 'PN-GNX-5001W', 'WNG5001W',
 'Công tắc Gen-X siêu mỏng, viền nhôm xước, 16AX 250V.',
 'Gen-X sở hữu mặt viền nhôm nguyên khối mỏng chỉ 8.7mm, phù hợp không gian hiện đại, văn phòng hạng A.',
 135000, 112000, 20, 260, 'Cái', 'uploads/products/switch-grey.svg', NULL,
 '{"Dòng định mức":"16AX","Điện áp định mức":"250V~","Loại":"Công tắc 1 chiều","Mặt viền":"Nhôm xước anod","Độ dày mặt":"8.7 mm","Kích thước đế âm":"Đế vuông 86 x 86 mm","Tiêu chuẩn":"IEC 60669-1","Bảo hành":"24 tháng"}',
 16.0, NULL, 1, 'uploads/catalogs/panasonic-gen-x.pdf', 1, 1),

(7, 1, 5, 'Ổ cắm đơn 3 chấu Full Color WEG1181', 'o-cam-don-3-chau-full-color-weg1181', 'PN-FCL-1181', 'WEG1181',
 'Hạt ổ cắm đơn 3 chấu Full Color 16A có màn che an toàn.',
 'Full Color đa dạng sắc màu, phối hợp nội thất linh hoạt, tích hợp màn che bảo vệ trẻ nhỏ.',
 38000, 31000, 30, 900, 'Cái', 'uploads/products/socket-white.svg', NULL,
 '{"Dòng định mức":"16A","Điện áp định mức":"250V~","Số chấu":"3 chấu","Màn che an toàn":"Có","Kích thước đế âm":"Đế chữ nhật 120 x 70 mm","Tiêu chuẩn":"IEC 60884-1","Bảo hành":"24 tháng"}',
 16.0, NULL, NULL, 'uploads/catalogs/panasonic-full-color.pdf', 0, 1),

(8, 1, 6, 'Công tắc 1 chiều Minerva WMT501', 'cong-tac-1-chieu-minerva-wmt501', 'PN-MIN-501', 'WMT501',
 'Công tắc Minerva phổ thông, 10A 250V, tối ưu chi phí công trình.',
 'Minerva là dòng thiết bị phổ thông với chất lượng tiêu chuẩn Panasonic, phù hợp dự án nhà ở xã hội, nhà xưởng.',
 26000, 20500, 50, 2500, 'Cái', 'uploads/products/switch-white.svg', NULL,
 '{"Dòng định mức":"10A","Điện áp định mức":"250V~","Loại":"Công tắc 1 chiều","Kích thước đế âm":"Đế vuông 86 x 86 mm","Tiêu chuẩn":"IEC 60669-1","Bảo hành":"12 tháng"}',
 10.0, NULL, 1, 'uploads/catalogs/panasonic-minerva.pdf', 0, 1),

(9, 2, NULL, 'Cầu dao tự động MCB 2P 16A 6kA BBD2161CNV', 'cau-dao-tu-dong-mcb-2p-16a-6ka-bbd2161cnv', 'PN-MCB-2161', 'BBD2161CNV',
 'MCB 2 cực 16A, dòng cắt 6kA, đường cong C – bảo vệ quá tải & ngắn mạch.',
 'Cầu dao tự động Panasonic BBD dạng thanh DIN 35mm, cơ cấu cắt nhanh, chỉ thị trạng thái tiếp điểm rõ ràng. Lý tưởng cho nhánh ổ cắm, điều hoà, bình nóng lạnh.',
 285000, 238000, 10, 640, 'Cái', 'uploads/products/mcb-2p.svg',
 '["uploads/products/mcb-2p.svg","uploads/products/mcb-1p.svg"]',
 '{"Dòng định mức (In)":"16A","Số cực":"2P","Dòng cắt ngắn mạch (Icn)":"6kA","Điện áp định mức (Ue)":"230/400V AC","Đặc tính ngắt":"Đường cong C","Tần số":"50/60Hz","Tuổi thọ cơ khí":"20.000 lần","Kiểu lắp":"Thanh DIN 35mm","Tiêu chuẩn":"IEC 60898-1","Đóng gói":"6 cái/hộp – 60 cái/thùng","Bảo hành":"24 tháng"}',
 16.0, 6.0, 2, 'uploads/catalogs/panasonic-mcb-rcbo.pdf', 1, 1),

(10, 2, NULL, 'Cầu dao tự động MCB 1P 20A 4.5kA BBD1201CNV', 'cau-dao-tu-dong-mcb-1p-20a-4-5ka-bbd1201cnv', 'PN-MCB-1201', 'BBD1201CNV',
 'MCB 1 cực 20A, dòng cắt 4.5kA, phù hợp mạch chiếu sáng & ổ cắm gia đình.',
 'MCB 1P Panasonic BBD với vỏ nhựa chống cháy, tay gạt chắc chắn, đáp ứng tiêu chuẩn IEC 60898-1.',
 135000, 112000, 10, 900, 'Cái', 'uploads/products/mcb-1p.svg', NULL,
 '{"Dòng định mức (In)":"20A","Số cực":"1P","Dòng cắt ngắn mạch (Icn)":"4.5kA","Điện áp định mức (Ue)":"230V AC","Đặc tính ngắt":"Đường cong C","Kiểu lắp":"Thanh DIN 35mm","Tiêu chuẩn":"IEC 60898-1","Đóng gói":"12 cái/hộp","Bảo hành":"24 tháng"}',
 20.0, 4.5, 1, 'uploads/catalogs/panasonic-mcb-rcbo.pdf', 0, 1),

(11, 2, NULL, 'Cầu dao tự động MCB 3P 32A 6kA BBD3323CNV', 'cau-dao-tu-dong-mcb-3p-32a-6ka-bbd3323cnv', 'PN-MCB-3323', 'BBD3323CNV',
 'MCB 3 cực 32A dòng cắt 6kA cho tủ điện tổng 3 pha.',
 'Thiết bị bảo vệ 3 pha cho tủ phân phối, motor nhỏ, hệ thống bơm. Thân máy chuẩn module 18mm/cực.',
 620000, 525000, 5, 210, 'Cái', 'uploads/products/mcb-3p.svg', NULL,
 '{"Dòng định mức (In)":"32A","Số cực":"3P","Dòng cắt ngắn mạch (Icn)":"6kA","Điện áp định mức (Ue)":"400V AC","Đặc tính ngắt":"Đường cong C","Kiểu lắp":"Thanh DIN 35mm","Tiêu chuẩn":"IEC 60898-1","Đóng gói":"4 cái/hộp","Bảo hành":"24 tháng"}',
 32.0, 6.0, 3, 'uploads/catalogs/panasonic-mcb-rcbo.pdf', 0, 1),

(12, 2, NULL, 'Cầu dao chống giật RCBO 2P 20A 30mA 6kA BBDE22031CNV', 'cau-dao-chong-giat-rcbo-2p-20a-30ma-bbde22031cnv', 'PN-RCBO-22031', 'BBDE22031CNV',
 'RCBO 2P 20A bảo vệ quá tải, ngắn mạch và dòng rò 30mA – chống giật cho phòng tắm, bình nóng lạnh.',
 'RCBO Panasonic tích hợp chức năng MCB và RCD trong một thiết bị, nút TEST kiểm tra định kỳ, độ nhạy 30mA đáp ứng an toàn con người.',
 890000, 760000, 5, 180, 'Cái', 'uploads/products/rcbo.svg', NULL,
 '{"Dòng định mức (In)":"20A","Số cực":"2P (1P+N)","Dòng cắt ngắn mạch (Icn)":"6kA","Dòng rò định mức (IΔn)":"30mA","Loại bảo vệ dòng rò":"Type AC","Điện áp định mức (Ue)":"230V AC","Kiểu lắp":"Thanh DIN 35mm","Tiêu chuẩn":"IEC 61009-1","Bảo hành":"24 tháng"}',
 20.0, 6.0, 2, 'uploads/catalogs/panasonic-mcb-rcbo.pdf', 1, 1),

(13, 4, NULL, 'Quạt trần 5 cánh Panasonic F-60UFN', 'quat-tran-5-canh-panasonic-f-60ufn', 'PN-FAN-60UFN', 'F-60UFN',
 'Quạt trần 5 cánh sải 150cm, động cơ DC tiết kiệm điện, remote điều khiển 9 tốc độ.',
 'Quạt trần F-60UFN sử dụng động cơ DC Inverter tiết kiệm đến 50% điện năng, chế độ gió tự nhiên 1/f Yuragi, hẹn giờ tắt và đèn LED tích hợp tuỳ chọn.',
 4890000, 4350000, 3, 45, 'Bộ', 'uploads/products/ceiling-fan.svg', NULL,
 '{"Sải cánh":"150 cm (60 inch)","Số cánh":"5 cánh","Động cơ":"DC Inverter","Công suất":"32W","Điện áp":"220-240V / 50Hz","Tốc độ gió":"9 cấp","Điều khiển":"Remote hồng ngoại","Hẹn giờ":"1/2/4 giờ","Bảo hành":"Động cơ 5 năm"}',
 NULL, NULL, NULL, 'uploads/catalogs/panasonic-quat-tran.pdf', 1, 1),

(14, 4, NULL, 'Quạt hút âm trần Panasonic FV-24CU7', 'quat-hut-am-tran-panasonic-fv-24cu7', 'PN-FAN-24CU7', 'FV-24CU7',
 'Quạt hút âm trần lưu lượng 216 m³/h, độ ồn thấp, dùng cho nhà tắm & phòng kín.',
 'Quạt thông gió âm trần Panasonic có van một chiều chống hồi gió, cánh Sirocco hoạt động êm, ống đầu ra Ø100mm.',
 1350000, 1150000, 5, 120, 'Cái', 'uploads/products/exhaust-fan.svg', NULL,
 '{"Lưu lượng gió":"216 m³/h","Công suất":"24W","Độ ồn":"32 dB","Điện áp":"220V / 50Hz","Kích thước lỗ khoét":"250 x 250 mm","Đường kính ống":"Ø100 mm","Bảo hành":"12 tháng"}',
 NULL, NULL, NULL, 'uploads/catalogs/panasonic-quat-hut.pdf', 0, 1),

(15, 3, NULL, 'Đèn LED âm trần Downlight Panasonic 9W NNV70093WE1A', 'den-led-am-tran-downlight-9w-nnv70093we1a', 'PN-LED-DL9W', 'NNV70093WE1A',
 'Downlight LED âm trần 9W, lỗ khoét Ø90mm, ánh sáng trung tính 4000K, CRI > 80.',
 'Đèn LED Downlight Panasonic tản nhiệt nhôm đúc, chip LED hiệu suất cao 90lm/W, không nhấp nháy (Flicker-free), bảo vệ mắt.',
 165000, 132000, 20, 1500, 'Cái', 'uploads/products/downlight.svg', NULL,
 '{"Công suất":"9W","Quang thông":"810 lm","Nhiệt độ màu":"4000K (trung tính)","Chỉ số hoàn màu":"CRI > 80","Kích thước lỗ khoét":"Ø90 mm","Điện áp":"220-240V / 50Hz","Tuổi thọ":"25.000 giờ","Bảo hành":"24 tháng"}',
 NULL, NULL, NULL, 'uploads/catalogs/panasonic-den-led.pdf', 1, 1),

(16, 5, NULL, 'Ổ cắm âm sàn chống nước Panasonic DU1102', 'o-cam-am-san-chong-nuoc-panasonic-du1102', 'PN-FLR-1102', 'DU1102',
 'Ổ cắm âm sàn nắp bật inox, 2 ổ cắm 16A + 1 cổng mạng, chuẩn IP44.',
 'Giải pháp cấp nguồn âm sàn cho văn phòng, sảnh hội nghị. Vỏ hợp kim chịu lực, nắp bật giảm chấn, chống nước tạt IP44.',
 1250000, 1050000, 5, 70, 'Bộ', 'uploads/products/floor-socket.svg', NULL,
 '{"Dòng định mức":"16A","Điện áp định mức":"250V~","Cấu hình":"2 ổ cắm 3 chấu + 1 RJ45","Cấp bảo vệ":"IP44","Vật liệu nắp":"Inox 304","Kích thước đế âm":"Hộp âm sàn 120 x 120 x 70 mm","Bảo hành":"24 tháng"}',
 16.0, NULL, NULL, 'uploads/catalogs/panasonic-o-cam-am-san.pdf', 0, 1);

-- Dữ liệu mẫu báo giá
INSERT INTO `quotes_requests` (`customer_name`,`company`,`customer_type`,`phone`,`email`,`project_name`,`address`,`note`,`status`) VALUES
('Nguyễn Văn Thành', 'Công ty TNHH Cơ Điện Thành Phát', 'contractor', '0912345678', 'thanhphat.me@gmail.com', 'Chung cư Sky Garden – Block B', 'Quận 7, TP.HCM', 'Cần báo giá thiết bị Wide Series + MCB cho 120 căn hộ.', 'new');
