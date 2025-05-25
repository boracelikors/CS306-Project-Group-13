DELIMITER //

-- Stored procedure to generate intelligence reports
CREATE PROCEDURE GenerateIntelligenceReport(
    IN p_agent_id INT,
    IN p_title VARCHAR(200),
    IN p_content TEXT,
    IN p_classification_level VARCHAR(50)
)
BEGIN
    DECLARE v_agent_exists INT;
    
    -- Check if agent exists
    SELECT COUNT(*) INTO v_agent_exists FROM Agents WHERE agent_id = p_agent_id;
    
    IF v_agent_exists = 0 THEN
        SELECT 'Error: Agent not found.' AS message;
    ELSE
        -- Insert the report
        INSERT INTO Intelligence_Reports (agent_id, title, content, classification_level)
        VALUES (p_agent_id, p_title, p_content, p_classification_level);
        
        SELECT 'Intelligence report generated successfully.' AS message;
    END IF;
END //

-- Stored procedure to order supplies
CREATE PROCEDURE OrderSupply(
    IN p_supply_id INT,
    IN p_order_quantity INT
)
BEGIN
    DECLARE v_current_quantity INT;
    DECLARE v_supply_name VARCHAR(100);
    
    -- Get current quantity and name
    SELECT quantity, name INTO v_current_quantity, v_supply_name 
    FROM Supply 
    WHERE supply_id = p_supply_id;
    
    IF v_current_quantity IS NULL THEN
        SELECT 'Error: Supply item not found.' AS message;
    ELSE
        -- Update quantity
        UPDATE Supply 
        SET quantity = quantity + p_order_quantity 
        WHERE supply_id = p_supply_id;
        
        SELECT CONCAT('Successfully ordered ', p_order_quantity, ' units of ', v_supply_name) AS message;
    END IF;
END //

-- Stored procedure to get agent reports
CREATE PROCEDURE GetAgentReports(
    IN p_agent_id INT
)
BEGIN
    DECLARE v_agent_name VARCHAR(100);
    DECLARE v_agent_rank VARCHAR(50);
    
    -- Get agent info
    SELECT CONCAT(name, ' (', rank, ')') INTO v_agent_name 
    FROM Agents 
    WHERE agent_id = p_agent_id;
    
    IF v_agent_name IS NULL THEN
        SELECT 'Error: Agent not found.' AS agent_info;
        SELECT NULL AS report_id, NULL AS date_created, NULL AS title, NULL AS classification_level;
        SELECT NULL AS total_reports, NULL AS most_recent_report, NULL AS oldest_report;
    ELSE
        -- Return agent info
        SELECT v_agent_name AS agent_info;
        
        -- Return reports
        SELECT report_id, created_at AS date_created, title, classification_level
        FROM Intelligence_Reports
        WHERE agent_id = p_agent_id
        ORDER BY created_at DESC;
        
        -- Return summary
        SELECT 
            COUNT(*) AS total_reports,
            MAX(created_at) AS most_recent_report,
            MIN(created_at) AS oldest_report
        FROM Intelligence_Reports
        WHERE agent_id = p_agent_id;
    END IF;
END //

-- Stored procedure to assign operator to drone
CREATE PROCEDURE AssignOperatorToDrone(
    IN p_operator_id INT,
    IN p_drone_id INT,
    IN p_rank VARCHAR(50)
)
BEGIN
    DECLARE v_drone_status VARCHAR(20);
    
    -- Check drone availability
    SELECT status INTO v_drone_status 
    FROM Drones 
    WHERE drone_id = p_drone_id;
    
    IF v_drone_status IS NULL THEN
        SELECT 'Error: Drone not found.' AS result;
    ELSEIF v_drone_status != 'Available' THEN
        SELECT 'Error: Drone is not available for assignment.' AS result;
    ELSE
        -- Update operator assignment and rank
        UPDATE Operator 
        SET drone_id = p_drone_id, rank = p_rank
        WHERE op_id = p_operator_id;
        
        -- Update drone status
        UPDATE Drones 
        SET status = 'Assigned' 
        WHERE drone_id = p_drone_id;
        
        SELECT 'Operator successfully assigned to drone.' AS result;
    END IF;
END //

DELIMITER ; 