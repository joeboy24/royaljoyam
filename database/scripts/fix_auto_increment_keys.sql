-- One-time fix for databases imported without AUTO_INCREMENT on id columns.
-- Run this in MySQL (MAMP phpMyAdmin or mysql CLI) BEFORE `php artisan migrate`
-- if migrate fails with: Field 'id' doesn't have a default value

-- 1) Fix Laravel's migrations table (required for migrate to work)
ALTER TABLE migrations
  MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY;

SET @next_migrations_id = (SELECT IFNULL(MAX(id), 0) + 1 FROM migrations);
SET @sql = CONCAT('ALTER TABLE migrations AUTO_INCREMENT = ', @next_migrations_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2) Fix inventory tables (safe to re-run; skip if already AUTO_INCREMENT)
ALTER TABLE item_images
  MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;

SET @next_item_images_id = (SELECT IFNULL(MAX(id), 0) + 1 FROM item_images);
SET @sql = CONCAT('ALTER TABLE item_images AUTO_INCREMENT = ', @next_item_images_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

ALTER TABLE items
  MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;

SET @next_items_id = (SELECT IFNULL(MAX(id), 0) + 1 FROM items);
SET @sql = CONCAT('ALTER TABLE items AUTO_INCREMENT = ', @next_items_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 3) If migrate already ran but failed to record, insert the row once:
-- INSERT INTO migrations (migration, batch)
-- SELECT '2026_07_02_055509_fix_item_images_id_auto_increment', IFNULL(MAX(batch), 0) + 1 FROM migrations
-- WHERE NOT EXISTS (
--   SELECT 1 FROM migrations WHERE migration = '2026_07_02_055509_fix_item_images_id_auto_increment'
-- );
