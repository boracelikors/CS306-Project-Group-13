-- Ensure DroneStatus table exists
CREATE TABLE IF NOT EXISTS DroneStatus (
    status_id INT AUTO_INCREMENT PRIMARY KEY,
    drone_id INT NOT NULL,
    missile_id INT NOT NULL,
    status VARCHAR(50) DEFAULT 'Armed',
    assignment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (drone_id) REFERENCES Drones(drone_id),
    FOREIGN KEY (missile_id) REFERENCES Missiles(missile_id)
);

DELIMITER //
CREATE TRIGGER LogMissileAssignment
AFTER INSERT ON Drone_Missile_Usage
FOR EACH ROW
BEGIN
    DECLARE v_drone_count INT;
    DECLARE v_missile_count INT;
    
    -- Validate that the drone exists in Drones
    SELECT COUNT(*) INTO v_drone_count FROM Drones WHERE drone_id = NEW.drone_id;
    IF v_drone_count = 0 THEN
         SIGNAL SQLSTATE '45000'
         SET MESSAGE_TEXT = 'Invalid drone ID. Drone does not exist.';
    END IF;
    
    -- Validate that the missile exists in Missiles
    SELECT COUNT(*) INTO v_missile_count FROM Missiles WHERE missile_id = NEW.missile_id;
    IF v_missile_count = 0 THEN
         SIGNAL SQLSTATE '45000'
         SET MESSAGE_TEXT = 'Invalid missile ID. Missile does not exist.';
    END IF;
    
    INSERT INTO DroneStatus (drone_id, missile_id)
    VALUES (NEW.drone_id, NEW.missile_id);
END//
DELIMITER ;
