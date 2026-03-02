USE customers_db;
ALTER TABLE menu_items ADD COLUMN is_active TINYINT(1) DEFAULT 1;
