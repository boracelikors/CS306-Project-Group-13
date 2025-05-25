DELIMITER //
CREATE PROCEDURE GenerateIntelligenceReport(
    IN p_agent_id INT,
    IN p_title VARCHAR(200),
    IN p_content TEXT,
    IN p_classification_level VARCHAR(50)
)
BEGIN
    DECLARE v_agent_exists INT;
    DECLARE v_report_id INT;
    
    -- Check if the agent exists
    SELECT COUNT(*) INTO v_agent_exists FROM Agents WHERE agent_id = p_agent_id;
    IF v_agent_exists = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Invalid agent ID. Agent does not exist.';
    END IF;
    
    IF p_title IS NULL OR TRIM(p_title) = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Report title cannot be empty.';
    END IF;
    
    -- Generate a new report ID based on the current maximum
    SELECT COALESCE(MAX(report_id), 0) + 1 INTO v_report_id FROM Intelligence_Reports;
    
    -- Insert the new report into Intelligence_Reports
    INSERT INTO Intelligence_Reports (
        report_id,
        date_created,
        title,
        content,
        classification_level,
        agent_id
    )
    VALUES (
        v_report_id,
        NOW(),
        p_title,
        p_content,
        p_classification_level,
        p_agent_id
    );
    
    SELECT CONCAT('Intelligence report created successfully. Report ID: ', v_report_id) AS message;
END//
DELIMITER ;
