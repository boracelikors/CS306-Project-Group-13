DELIMITER //
CREATE PROCEDURE AssignOperatorToDrone(
    IN p_operator_id INT,
    IN p_drone_id INT,
    IN p_rank VARCHAR(50)
)
BEGIN
    DECLARE v_operator_exists INT;
    DECLARE v_drone_exists INT;
    
    -- Check if operator exists using op_id in Operator table
    SELECT COUNT(*) INTO v_operator_exists FROM Operator WHERE op_id = p_operator_id;
    
    IF v_operator_exists = 0 THEN
        INSERT INTO Operator (op_id, rank)
        VALUES (p_operator_id, p_rank);
    ELSE
        UPDATE Operator
        SET rank = p_rank
        WHERE op_id = p_operator_id;
    END IF;
    
    -- Check if drone exists
    SELECT COUNT(*) INTO v_drone_exists FROM Drones WHERE drone_id = p_drone_id;
    IF v_drone_exists = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Invalid drone ID. Drone does not exist.';
    END IF;
    
    -- Update the drone's operator assignment (set op_id)
    UPDATE Drones
    SET op_id = p_operator_id
    WHERE drone_id = p_drone_id;
    
    SELECT CONCAT('Operator ', p_operator_id, ' assigned to drone ', p_drone_id) AS result;
END//
DELIMITER ;
