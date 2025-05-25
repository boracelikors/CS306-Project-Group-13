-- Ensure Supply_Audit table exists
CREATE TABLE IF NOT EXISTS Supply_Audit (
    audit_id INT AUTO_INCREMENT PRIMARY KEY,
    supply_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    old_quantity INT,
    new_quantity INT,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    action_type VARCHAR(10)
);

DELIMITER //
CREATE TRIGGER LogSupplyChanges
AFTER UPDATE ON Supply
FOR EACH ROW
BEGIN
    DECLARE v_supply_count INT;
    
    -- Validate that the supply item exists
    SELECT COUNT(*) INTO v_supply_count FROM Supply WHERE supply_id = NEW.supply_id;
    IF v_supply_count = 0 THEN
         SIGNAL SQLSTATE '45000'
         SET MESSAGE_TEXT = 'Invalid supply ID. Supply does not exist.';
    END IF;
    
    INSERT INTO Supply_Audit (supply_id, name, old_quantity, new_quantity, action_type)
    VALUES (NEW.supply_id, NEW.name, OLD.quantity, NEW.quantity, 'UPDATE');
END//
DELIMITER ;
