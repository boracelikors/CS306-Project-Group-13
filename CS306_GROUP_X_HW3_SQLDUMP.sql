-- CS306 Project Phase III SQL Dump
-- Version: 1.0
-- Generation Time: May 4, 2025

-- Database creation and schema
-- Create database if not exists
CREATE DATABASE IF NOT EXISTS cs306;
USE cs306;

-- Create Agents table
CREATE TABLE IF NOT EXISTS Agents (
    agent_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    rank VARCHAR(50),
    status VARCHAR(20) DEFAULT 'Active'
);

-- Create Drones table
CREATE TABLE IF NOT EXISTS Drones (
    drone_id INT PRIMARY KEY AUTO_INCREMENT,
    model VARCHAR(50) NOT NULL,
    status VARCHAR(20) DEFAULT 'Available'
);

-- Create Missiles table
CREATE TABLE IF NOT EXISTS Missiles (
    missile_id INT PRIMARY KEY AUTO_INCREMENT,
    type VARCHAR(50) NOT NULL,
    status VARCHAR(20) DEFAULT 'Available'
);

-- Create Supply table
CREATE TABLE IF NOT EXISTS Supply (
    supply_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    quantity INT DEFAULT 0,
    min_quantity INT DEFAULT 10
);

-- Create DroneStatus table for missile assignments
CREATE TABLE IF NOT EXISTS DroneStatus (
    status_id INT PRIMARY KEY AUTO_INCREMENT,
    drone_id INT,
    missile_id INT,
    status VARCHAR(20) DEFAULT 'Assigned',
    assignment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (drone_id) REFERENCES Drones(drone_id),
    FOREIGN KEY (missile_id) REFERENCES Missiles(missile_id)
);

-- Create Supply_Audit table for supply changes
CREATE TABLE IF NOT EXISTS Supply_Audit (
    audit_id INT PRIMARY KEY AUTO_INCREMENT,
    supply_id INT,
    name VARCHAR(100),
    old_quantity INT,
    new_quantity INT,
    action_type VARCHAR(20),
    changed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (supply_id) REFERENCES Supply(supply_id)
);

-- Create Operator table
CREATE TABLE IF NOT EXISTS Operator (
    op_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    rank VARCHAR(50),
    drone_id INT,
    FOREIGN KEY (drone_id) REFERENCES Drones(drone_id)
);

-- Create Intelligence_Reports table
CREATE TABLE IF NOT EXISTS Intelligence_Reports (
    report_id INT PRIMARY KEY AUTO_INCREMENT,
    agent_id INT,
    title VARCHAR(200) NOT NULL,
    content TEXT,
    classification_level VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agent_id) REFERENCES Agents(agent_id)
);

-- Create Drone_Missile_Usage table for tracking assignments
CREATE TABLE IF NOT EXISTS Drone_Missile_Usage (
    usage_id INT PRIMARY KEY AUTO_INCREMENT,
    drone_id INT,
    missile_id INT,
    assignment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (drone_id) REFERENCES Drones(drone_id),
    FOREIGN KEY (missile_id) REFERENCES Missiles(missile_id)
);

-- Insert sample data for testing
INSERT INTO Agents (name, rank) VALUES 
('John Smith', 'Senior Agent'),
('Jane Doe', 'Field Agent'),
('Mike Johnson', 'Special Agent');

INSERT INTO Drones (model, status) VALUES 
('Recon-X1', 'Available'),
('Strike-F22', 'Available'),
('Scout-Y3', 'Maintenance');

INSERT INTO Missiles (type, status) VALUES 
('Air-to-Ground', 'Available'),
('Surface-to-Air', 'Available'),
('Tactical', 'Available');

INSERT INTO Supply (name, quantity, min_quantity) VALUES 
('Fuel', 1000, 200),
('Ammunition', 500, 100),
('Spare Parts', 300, 50);

INSERT INTO Operator (name, rank) VALUES 
('Tom Wilson', 'Senior'),
('Sarah Brown', 'Expert'),
('James Lee', 'Junior');

-- Stored Procedures

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

-- Triggers

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