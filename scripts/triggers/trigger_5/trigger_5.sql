-- Create Drone_Attack_Log table for tracking drone-target attacks
CREATE TABLE IF NOT EXISTS Drone_Attack_Log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    drone_id INT NOT NULL,
    target_id INT NOT NULL,
    attack_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (drone_id) REFERENCES Drones(drone_id),
    FOREIGN KEY (target_id) REFERENCES Targets(target_id)
);

DELIMITER //
CREATE TRIGGER LogDroneTargetAttack
AFTER INSERT ON Drone_Target_Attacks
FOR EACH ROW
BEGIN
    DECLARE v_drone_count INT;
    DECLARE v_target_count INT;
    
    -- Validate drone existence
    SELECT COUNT(*) INTO v_drone_count FROM Drones WHERE drone_id = NEW.drone_id;
    IF v_drone_count = 0 THEN
         SIGNAL SQLSTATE '45000'
         SET MESSAGE_TEXT = 'Invalid drone ID. Drone does not exist.';
    END IF;
    
    -- Validate target existence
    SELECT COUNT(*) INTO v_target_count FROM Targets WHERE target_id = NEW.target_id;
    IF v_target_count = 0 THEN
         SIGNAL SQLSTATE '45000'
         SET MESSAGE_TEXT = 'Invalid target ID. Target does not exist.';
    END IF;
    
    INSERT INTO Drone_Attack_Log (drone_id, target_id)
    VALUES (NEW.drone_id, NEW.target_id);
END//
DELIMITER ;
