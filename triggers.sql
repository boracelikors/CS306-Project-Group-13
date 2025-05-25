DELIMITER //

-- Trigger to log missile assignments
CREATE TRIGGER LogMissileAssignment
AFTER INSERT ON Drone_Missile_Usage
FOR EACH ROW
BEGIN
    DECLARE v_drone_exists INT;
    DECLARE v_missile_exists INT;
    
    -- Check if drone and missile exist
    SELECT COUNT(*) INTO v_drone_exists FROM Drones WHERE drone_id = NEW.drone_id;
    SELECT COUNT(*) INTO v_missile_exists FROM Missiles WHERE missile_id = NEW.missile_id;
    
    IF v_drone_exists > 0 AND v_missile_exists > 0 THEN
        -- Insert into DroneStatus
        INSERT INTO DroneStatus (drone_id, missile_id, status)
        VALUES (NEW.drone_id, NEW.missile_id, 'Assigned');
        
        -- Update missile status
        UPDATE Missiles 
        SET status = 'Assigned' 
        WHERE missile_id = NEW.missile_id;
        
        -- Update drone status
        UPDATE Drones 
        SET status = 'Armed' 
        WHERE drone_id = NEW.drone_id;
    END IF;
END //

-- Trigger to log supply changes
CREATE TRIGGER LogSupplyChanges
AFTER UPDATE ON Supply
FOR EACH ROW
BEGIN
    DECLARE v_action_type VARCHAR(20);
    
    -- Determine action type
    IF NEW.quantity > OLD.quantity THEN
        SET v_action_type = 'Increase';
    ELSE
        SET v_action_type = 'Decrease';
    END IF;
    
    -- Log the change
    INSERT INTO Supply_Audit (
        supply_id,
        name,
        old_quantity,
        new_quantity,
        action_type
    )
    VALUES (
        NEW.supply_id,
        (SELECT name FROM Supply WHERE supply_id = NEW.supply_id),
        OLD.quantity,
        NEW.quantity,
        v_action_type
    );
    
    -- Check if quantity is below minimum
    IF NEW.quantity < NEW.min_quantity THEN
        INSERT INTO Supply_Audit (
            supply_id,
            name,
            old_quantity,
            new_quantity,
            action_type
        )
        VALUES (
            NEW.supply_id,
            (SELECT name FROM Supply WHERE supply_id = NEW.supply_id),
            NEW.quantity,
            NEW.quantity,
            'Low Stock Alert'
        );
    END IF;
END //

DELIMITER ; 