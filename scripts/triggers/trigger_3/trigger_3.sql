-- Create Vehicle_Status_Log table for vehicle status changes
CREATE TABLE IF NOT EXISTS Vehicle_Status_Log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    old_status VARCHAR(50),
    new_status VARCHAR(50),
    change_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES Vehicles(vehicle_id)
);

DELIMITER //
CREATE TRIGGER LogVehicleStatusChange
AFTER UPDATE ON Vehicles
FOR EACH ROW
BEGIN
    DECLARE v_vehicle_count INT;
    
    -- Check that the vehicle exists
    SELECT COUNT(*) INTO v_vehicle_count FROM Vehicles WHERE vehicle_id = NEW.vehicle_id;
    IF v_vehicle_count = 0 THEN
         SIGNAL SQLSTATE '45000'
         SET MESSAGE_TEXT = 'Invalid vehicle ID. Vehicle does not exist.';
    END IF;
    
    IF NEW.operational_status <> OLD.operational_status THEN
       INSERT INTO Vehicle_Status_Log (vehicle_id, old_status, new_status)
       VALUES (NEW.vehicle_id, OLD.operational_status, NEW.operational_status);
    END IF;
END//
DELIMITER ;
