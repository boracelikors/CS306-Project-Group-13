DELIMITER //
CREATE PROCEDURE ReserveVehicle(
    IN p_base_id INT,
    IN p_vehicle_type VARCHAR(50)
)
BEGIN
    DECLARE v_vehicle_id INT;
    
    -- Select an available vehicle from the specified base and type
    SELECT vehicle_id INTO v_vehicle_id
    FROM Vehicles
    WHERE base_id = p_base_id
      AND type = p_vehicle_type
      AND operational_status = 'Active'
    LIMIT 1;
    
    IF v_vehicle_id IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'No available vehicle found for reservation.';
    ELSE
        -- Update the vehicle status to 'Reserved'
        UPDATE Vehicles
        SET operational_status = 'Reserved'
        WHERE vehicle_id = v_vehicle_id;
        
        SELECT CONCAT('Vehicle ', v_vehicle_id, ' reserved successfully.') AS message;
    END IF;
END//
DELIMITER ;
