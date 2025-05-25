DELIMITER //
CREATE PROCEDURE OrderSupply(
    IN p_supply_id INT,
    IN p_order_quantity INT
)
BEGIN
    DECLARE v_current_quantity INT;
    
    -- Get the current quantity of the supply item
    SELECT quantity INTO v_current_quantity
    FROM Supply
    WHERE supply_id = p_supply_id;
    
    IF v_current_quantity IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Invalid supply ID. Supply does not exist.';
    ELSE
        -- Update the supply quantity by adding the order quantity
        UPDATE Supply
        SET quantity = quantity + p_order_quantity
        WHERE supply_id = p_supply_id;
        
        SELECT CONCAT('Supply ', p_supply_id, ' updated. New quantity: ', v_current_quantity + p_order_quantity) AS message;
    END IF;
END//
DELIMITER ;
