DELIMITER //
CREATE PROCEDURE GetAgentReports(
    IN p_agent_id INT
)
BEGIN
    DECLARE agent_name VARCHAR(100);
    
    -- Get the agent's name
    SELECT name INTO agent_name FROM Agents WHERE agent_id = p_agent_id;
    
    -- Display agent info as header
    SELECT CONCAT('Agent: ', agent_name, ' (ID: ', p_agent_id, ')') AS agent_info;
    
    -- Retrieve the agent's reports from Intelligence_Reports
    SELECT 
        report_id,
        date_created,
        title,
        classification_level
    FROM Intelligence_Reports
    WHERE agent_id = p_agent_id
    ORDER BY date_created DESC;
    
    -- Provide summary statistics
    SELECT 
        COUNT(*) AS total_reports,
        MAX(date_created) AS most_recent_report,
        MIN(date_created) AS oldest_report
    FROM Intelligence_Reports
    WHERE agent_id = p_agent_id;
END//
DELIMITER ;
