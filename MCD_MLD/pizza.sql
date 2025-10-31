-- ------------------------------------------------------------
-- MySQL 8+ — Pizza App (MVC, no ENUM)
-- ------------------------------------------------------------
-- Notes:
-- - Identifiants protégés avec des backticks (`user`, `size`).
-- - Valeurs monétaires en CENTIMES (INT UNSIGNED).
-- - Booléens en TINYINT(1) avec DEFAULT cohérents.
-- - FKs avec ON UPDATE CASCADE et politiques de suppression explicites.
-- - Ajout d'indexes pour les FKs et d'UNIQUE utiles.
-- - Correction de pizza_ingredient (PK composite + quantityUnit).
-- ------------------------------------------------------------

-- Charset/engine conseillés
SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- ------------------------------------------------------------
-- Table: user
-- ------------------------------------------------------------
CREATE TABLE `user` (
  `id`           INT AUTO_INCREMENT NOT NULL,
  `email`        VARCHAR(180) NOT NULL COMMENT 'Login + contact email.',
  `passwordHash` VARCHAR(255) NOT NULL COMMENT 'Password hash (BCrypt/Argon2).',
  `firstname`    VARCHAR(100) NOT NULL COMMENT 'First name.',
  `lastname`     VARCHAR(100) NOT NULL COMMENT 'Last name.',
  `address`      VARCHAR(255) NULL COMMENT 'Street address.',
  `postalCode`   VARCHAR(10)  NULL COMMENT 'Postal/ZIP.',
  `city`         VARCHAR(100) NULL,
  `phone`        VARCHAR(20)  NULL COMMENT 'Phone number.',
  `role`         VARCHAR(20)  NOT NULL COMMENT 'ADMIN or CUSTOMER (validated in app).',
  `createdAt`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Account creation date.',
  `lastLoginAt`  DATETIME NULL COMMENT 'Last successful login timestamp.',
  CONSTRAINT `user_PK` PRIMARY KEY (`id`),
  CONSTRAINT `user_email_UQ` UNIQUE (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Table: pizza
-- ------------------------------------------------------------
CREATE TABLE `pizza` (
  `id`              INT AUTO_INCREMENT NOT NULL,
  `name`            VARCHAR(255) NOT NULL COMMENT 'Pizza name (unique per catalog).',
  `description`     TEXT NULL COMMENT 'Marketing/recipe text.',
  `photo`           VARCHAR(255) NULL COMMENT 'Image path/URL.',
  `basePriceCents`  INT UNSIGNED NOT NULL COMMENT 'Base price (Large size).',
  `isActive`        TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Visible for sale if 1.',
  CONSTRAINT `pizza_PK` PRIMARY KEY (`id`),
  CONSTRAINT `pizza_name_UQ` UNIQUE (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Table: ingredient
-- ------------------------------------------------------------
CREATE TABLE `ingredient` (
  `id`               INT AUTO_INCREMENT NOT NULL,
  `name`             VARCHAR(100) NOT NULL COMMENT 'Ingredient name (unique).',
  `unit`             VARCHAR(10)  NOT NULL COMMENT 'GRAM | ML | PIECE (validated in app).',
  `photo`            VARCHAR(255) NULL COMMENT 'Image path/URL.',
  `costPerUnitCents` INT UNSIGNED NULL COMMENT 'Procurement cost per unit (optional).',
  `isVegetarian`     TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Vegetarian flag.',
  `isVegan`          TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Vegan flag.',
  `hasAllergens`     TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Contains allergens flag.',
  `isActive`         TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Available if 1.',
  CONSTRAINT `ingredient_PK` PRIMARY KEY (`id`),
  CONSTRAINT `ingredient_name_UQ` UNIQUE (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Table: size
-- ------------------------------------------------------------
CREATE TABLE `size` (
  `id`         INT AUTO_INCREMENT NOT NULL,
  `label`      VARCHAR(10) NOT NULL COMMENT 'Expected: M | L | XL',
  `diameterCm` DECIMAL(4,1) NOT NULL COMMENT 'Typical: 28.0 / 33.0 / 40.0',
  CONSTRAINT `size_PK` PRIMARY KEY (`id`),
  CONSTRAINT `size_label_UQ` UNIQUE (`label`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Table: purchase
-- ------------------------------------------------------------
CREATE TABLE `purchase` (
  `id`         INT AUTO_INCREMENT NOT NULL,
  `number`     VARCHAR(50) NOT NULL COMMENT 'Human-readable order number (e.g., ORD-2025-000123).',
  `createdAt`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Order date/time.',
  `status`     VARCHAR(20) NOT NULL COMMENT 'PENDING | PAID | CANCELLED | SHIPPED (validated in app).',
  `totalCents` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Order grand total in cents.',
  `id_user`    INT NOT NULL,
  CONSTRAINT `purchase_PK` PRIMARY KEY (`id`),
  CONSTRAINT `purchase_number_UQ` UNIQUE (`number`),
  KEY `purchase_user_IDX` (`id_user`),
  CONSTRAINT `purchase_user_FK`
    FOREIGN KEY (`id_user`) REFERENCES `user`(`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Table: purchase_item
-- ------------------------------------------------------------
CREATE TABLE `purchase_item` (
  `id`             INT AUTO_INCREMENT NOT NULL,
  `qty`            INT NOT NULL,
  `unitPriceCents` INT UNSIGNED NOT NULL,
  `lineTotalCents` INT UNSIGNED NOT NULL,
  `id_pizza`       INT NOT NULL,
  `id_size`        INT NOT NULL,
  `id_purchase`    INT NOT NULL,
  CONSTRAINT `purchase_item_PK` PRIMARY KEY (`id`),
  KEY `pi_purchase_IDX` (`id_purchase`),
  KEY `pi_pizza_IDX`    (`id_pizza`),
  KEY `pi_size_IDX`     (`id_size`),
  CONSTRAINT `purchase_item_purchase_FK`
    FOREIGN KEY (`id_purchase`) REFERENCES `purchase`(`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `purchase_item_pizza_FK`
    FOREIGN KEY (`id_pizza`) REFERENCES `pizza`(`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `purchase_item_size_FK`
    FOREIGN KEY (`id_size`) REFERENCES `size`(`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `purchase_item_qty_chk` CHECK (`qty` > 0),
  CONSTRAINT `purchase_item_line_chk` CHECK (`lineTotalCents` = `qty` * `unitPriceCents`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Table: pizza_ingredient (association N-N + quantité)
-- ------------------------------------------------------------
CREATE TABLE `pizza_ingredient` (
  `id_ingredient` INT NOT NULL,
  `id_pizza`      INT NOT NULL,
  `quantityUnit`  DECIMAL(8,2) NOT NULL COMMENT 'Quantity of ingredient in base recipe (in ingredient.unit)',
  CONSTRAINT `pizza_ingredient_PK` PRIMARY KEY (`id_ingredient`, `id_pizza`),
  KEY `pi_pizza_IDX` (`id_pizza`),
  CONSTRAINT `pi_ingredient_FK`
    FOREIGN KEY (`id_ingredient`) REFERENCES `ingredient`(`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `pi_pizza_FK`
    FOREIGN KEY (`id_pizza`) REFERENCES `pizza`(`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Données initiales — size
-- ------------------------------------------------------------
INSERT INTO `size` (`label`, `diameterCm`) VALUES
('M', 28.0), ('L', 33.0), ('XL', 40.0);
