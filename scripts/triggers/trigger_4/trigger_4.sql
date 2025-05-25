-- Create Report_Update_Log table for capturing report title changes
CREATE TABLE IF NOT EXISTS Report_Update_Log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    report_id INT NOT NULL,
    old_title VARCHAR(200),
    new_title VARCHAR(200),
    update_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (report_id) REFERENCES Intelligence_Reports(report_id)
);

DELIMITER //
CREATE TRIGGER LogReportUpdate
AFTER UPDATE ON Intelligence_Reports
FOR EACH ROW
BEGIN
    DECLARE v_report_count INT;
    
    -- Validate that the report exists in Intelligence_Reports
    SELECT COUNT(*) INTO v_report_count FROM Intelligence_Reports WHERE report_id = NEW.report_id;
    IF v_report_count = 0 THEN
         SIGNAL SQLSTATE '45000'
         SET MESSAGE_TEXT = 'Invalid report ID. Report does not exist.';
    END IF;
    
    IF NEW.title <> OLD.title THEN
       INSERT INTO Report_Update_Log (report_id, old_title, new_title)
       VALUES (NEW.report_id, OLD.title, NEW.title);
    END IF;
END//
DELIMITER ;
