-- ===============================================================
-- Military Intelligence and Operations Database Setup - UPDATED VERSION
-- This script creates all tables, triggers, stored procedures, and sample data
-- according to the CS306.pdf schema.
-- ===============================================================

-- Create database (if it doesn't exist)
CREATE DATABASE IF NOT EXISTS military_intelligence;
USE military_intelligence;

-- =============================================
-- TABLE CREATION (Güncel Şema from 306Tables.pdf)
-- =============================================

-- Countries
CREATE TABLE IF NOT EXISTS Countries (
    country_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    region VARCHAR(100),
    political_status VARCHAR(50),
    PRIMARY KEY (country_id)
);

-- Base (no location column)
CREATE TABLE IF NOT EXISTS Base (
    base_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    capacity INT,
    country_id INT NOT NULL,
    PRIMARY KEY (base_id),
    FOREIGN KEY (country_id) REFERENCES Countries(country_id)
);

-- Supply
CREATE TABLE IF NOT EXISTS Supply (
    supply_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50),
    quantity INT,
    PRIMARY KEY (supply_id)
);

-- Vehicles
CREATE TABLE IF NOT EXISTS Vehicles (
    vehicle_id INT NOT NULL,
    type VARCHAR(50),
    capacity INT,
    operational_status VARCHAR(50),
    base_id INT NOT NULL,
    PRIMARY KEY (vehicle_id),
    FOREIGN KEY (base_id) REFERENCES Base(base_id)
);

-- Operator (only op_id and rank)
CREATE TABLE IF NOT EXISTS Operator (
    op_id INT NOT NULL,
    rank VARCHAR(50),
    PRIMARY KEY (op_id)
);

-- Person (only person_id and base_id)
CREATE TABLE IF NOT EXISTS Person (
    person_id INT NOT NULL,
    base_id INT NOT NULL,
    PRIMARY KEY (person_id),
    FOREIGN KEY (base_id) REFERENCES Base(base_id)
);

-- Soldier (ISA relationship using person_id)
CREATE TABLE IF NOT EXISTS Soldier (
    person_id INT NOT NULL,
    specialty VARCHAR(100),
    unit VARCHAR(100),
    rank VARCHAR(50),
    PRIMARY KEY (person_id),
    FOREIGN KEY (person_id) REFERENCES Person(person_id)
);

-- Civil (ISA relationship using person_id)
CREATE TABLE IF NOT EXISTS Civil (
    person_id INT NOT NULL,
    department VARCHAR(100),
    occupation VARCHAR(100),
    PRIMARY KEY (person_id),
    FOREIGN KEY (person_id) REFERENCES Person(person_id)
);

-- Agents
CREATE TABLE IF NOT EXISTS Agents (
    agent_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    rank VARCHAR(50),
    PRIMARY KEY (agent_id)
);

-- Missiles (no drone_id column)
CREATE TABLE IF NOT EXISTS Missiles (
    missile_id INT NOT NULL,
    type VARCHAR(50),
    range INT,
    PRIMARY KEY (missile_id)
);

-- Drones (includes op_id as foreign key)
CREATE TABLE IF NOT EXISTS Drones (
    drone_id INT NOT NULL,
    range INT,
    max_altitude INT,
    model VARCHAR(100),
    op_id INT NOT NULL,
    PRIMARY KEY (drone_id),
    FOREIGN KEY (op_id) REFERENCES Operator(op_id)
);

-- Satellites
CREATE TABLE IF NOT EXISTS Satellites (
    satellite_id INT NOT NULL,
    operational_status VARCHAR(50),
    name VARCHAR(100) NOT NULL,
    PRIMARY KEY (satellite_id)
);

-- Targets
CREATE TABLE IF NOT EXISTS Targets (
    target_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50),
    priority_level INT,
    PRIMARY KEY (target_id)
);

-- Intelligence_Reports 
-- (date_created is DATETIME; no operational_status or satellite_id columns)
CREATE TABLE IF NOT EXISTS Intelligence_Reports (
    report_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT,
    date_created DATETIME NOT NULL,
    classification_level VARCHAR(50),
    agent_id INT,
    PRIMARY KEY (report_id),
    FOREIGN KEY (agent_id) REFERENCES Agents(agent_id)
);

-- =============================================
-- İLİŞKİ TABLOLARI (Many-to-Many Relationships)
-- =============================================

-- Target_Base_Radar
CREATE TABLE IF NOT EXISTS Target_Base_Radar (
    target_id INT NOT NULL,
    base_id INT NOT NULL,
    PRIMARY KEY (target_id, base_id),
    FOREIGN KEY (target_id) REFERENCES Targets(target_id),
    FOREIGN KEY (base_id) REFERENCES Base(base_id)
);

-- Base_Stores_Supply
CREATE TABLE IF NOT EXISTS Base_Stores_Supply (
    base_id INT NOT NULL,
    supply_id INT NOT NULL,
    PRIMARY KEY (base_id, supply_id),
    FOREIGN KEY (base_id) REFERENCES Base(base_id),
    FOREIGN KEY (supply_id) REFERENCES Supply(supply_id)
);

-- Drone_Missile_Usage
CREATE TABLE IF NOT EXISTS Drone_Missile_Usage (
    drone_id INT NOT NULL,
    missile_id INT NOT NULL,
    PRIMARY KEY (drone_id, missile_id),
    FOREIGN KEY (drone_id) REFERENCES Drones(drone_id),
    FOREIGN KEY (missile_id) REFERENCES Missiles(missile_id)
);

-- Drone_Target_Attacks
CREATE TABLE IF NOT EXISTS Drone_Target_Attacks (
    drone_id INT NOT NULL,
    target_id INT NOT NULL,
    PRIMARY KEY (drone_id, target_id),
    FOREIGN KEY (drone_id) REFERENCES Drones(drone_id),
    FOREIGN KEY (target_id) REFERENCES Targets(target_id)
);

-- Satellite_Target_Watches
CREATE TABLE IF NOT EXISTS Satellite_Target_Watches (
    satellite_id INT NOT NULL,
    target_id INT NOT NULL,
    PRIMARY KEY (satellite_id, target_id),
    FOREIGN KEY (satellite_id) REFERENCES Satellites(satellite_id),
    FOREIGN KEY (target_id) REFERENCES Targets(target_id)
);

-- Intelligence_Report_Decides_Target
CREATE TABLE IF NOT EXISTS Intelligence_Report_Decides_Target (
    report_id INT NOT NULL,
    target_id INT NOT NULL,
    PRIMARY KEY (report_id, target_id),
    FOREIGN KEY (report_id) REFERENCES Intelligence_Reports(report_id),
    FOREIGN KEY (target_id) REFERENCES Targets(target_id)
);

-- Agent_Wrote_Report
CREATE TABLE IF NOT EXISTS Agent_Wrote_Report (
    agent_id INT NOT NULL,
    report_id INT NOT NULL,
    PRIMARY KEY (agent_id, report_id),
    FOREIGN KEY (agent_id) REFERENCES Agents(agent_id),
    FOREIGN KEY (report_id) REFERENCES Intelligence_Reports(report_id)
);

-- =============================================
-- ADDITIONAL TABLES FOR TRIGGERS
-- =============================================

-- DroneStatus: Logs drone-missile assignments (linked to Drone_Missile_Usage)
CREATE TABLE IF NOT EXISTS DroneStatus (
    status_id INT AUTO_INCREMENT PRIMARY KEY,
    drone_id INT NOT NULL,
    missile_id INT NOT NULL,
    status VARCHAR(50) DEFAULT 'Armed',
    assignment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (drone_id) REFERENCES Drones(drone_id),
    FOREIGN KEY (missile_id) REFERENCES Missiles(missile_id)
);

-- Supply_Audit: Logs changes in the Supply table
CREATE TABLE IF NOT EXISTS Supply_Audit (
    audit_id INT AUTO_INCREMENT PRIMARY KEY,
    supply_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    old_quantity INT,
    new_quantity INT,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    action_type VARCHAR(10)
);

-- =============================================
-- TRIGGERS
-- =============================================

-- Trigger 1: LogMissileAssignment
-- After inserting into Drone_Missile_Usage, log the assignment into DroneStatus
DELIMITER //
CREATE TRIGGER LogMissileAssignment
AFTER INSERT ON Drone_Missile_Usage
FOR EACH ROW
BEGIN
    INSERT INTO DroneStatus (drone_id, missile_id)
    VALUES (NEW.drone_id, NEW.missile_id);
END//
DELIMITER ;

-- Trigger 2: LogSupplyChanges
-- After an update on Supply, log the changes into Supply_Audit
DELIMITER //
CREATE TRIGGER LogSupplyChanges
AFTER UPDATE ON Supply
FOR EACH ROW
BEGIN
    INSERT INTO Supply_Audit (supply_id, name, old_quantity, new_quantity, action_type)
    VALUES (NEW.supply_id, NEW.name, OLD.quantity, NEW.quantity, 'UPDATE');
END//
DELIMITER ;

-- =============================================
-- STORED PROCEDURES
-- =============================================

-- Stored Procedure 1: AssignOperatorToDrone 
-- Updates a drone's operator assignment after ensuring the operator and drone exist.
DELIMITER //
CREATE PROCEDURE AssignOperatorToDrone(
    IN p_operator_id INT,
    IN p_drone_id INT,
    IN p_rank VARCHAR(50)
)
BEGIN
    DECLARE v_operator_exists INT;
    DECLARE v_drone_exists INT;
    
    -- Check if operator exists
    SELECT COUNT(*) INTO v_operator_exists FROM Operator WHERE op_id = p_operator_id;
    -- Check if drone exists
    SELECT COUNT(*) INTO v_drone_exists FROM Drones WHERE drone_id = p_drone_id;
    
    IF v_operator_exists = 0 THEN
        INSERT INTO Operator (op_id, rank) VALUES (p_operator_id, p_rank);
    ELSE
        UPDATE Operator SET rank = p_rank WHERE op_id = p_operator_id;
    END IF;
    
    IF v_drone_exists = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Invalid drone ID. Drone does not exist.';
    END IF;
    
    UPDATE Drones SET op_id = p_operator_id WHERE drone_id = p_drone_id;
    
    SELECT CONCAT('Operator ', p_operator_id, ' assigned to drone ', p_drone_id) AS result;
END//
DELIMITER ;

-- Stored Procedure 2: GetAgentReports 
-- Retrieves reports written by a given agent along with summary statistics.
DELIMITER //
CREATE PROCEDURE GetAgentReports(
    IN p_agent_id INT
)
BEGIN
    DECLARE agent_name VARCHAR(100);
    
    SELECT name INTO agent_name FROM Agents WHERE agent_id = p_agent_id;
    
    SELECT CONCAT('Agent: ', agent_name, ' (ID: ', p_agent_id, ')') AS agent_info;
    
    SELECT report_id, date_created, title, classification_level
    FROM Intelligence_Reports
    WHERE agent_id = p_agent_id
    ORDER BY date_created DESC;
    
    SELECT COUNT(*) AS total_reports,
           MAX(date_created) AS most_recent_report,
           MIN(date_created) AS oldest_report
    FROM Intelligence_Reports
    WHERE agent_id = p_agent_id;
END//
DELIMITER ;

-- =============================================
-- INSERT SAMPLE DATA (Order adjusted to satisfy FK constraints)
-- =============================================

-- 1. Insert into Countries
INSERT INTO Countries VALUES
(1, 'Atlantis', 'North Sea', 'Stable Democracy'),
(2, 'Azura', 'South Sea', 'Monarchy'),
(3, 'Rivia', 'Eastern Continent', 'Federation'),
(4, 'Arcadia', 'Western Isles', 'Republic'),
(5, 'Novia', 'Northern Mainland', 'Confederation'),
(6, 'Eldora', 'Eastern Mainland', 'Kingdom'),
(7, 'Valoria', 'Valley Region', 'Stable Democracy'),
(8, 'Terranova', 'Island Frontier', 'Republic'),
(9, 'Sandora', 'Desert Region', 'Autocracy'),
(10, 'Polaris', 'Polar Region', 'Stable Democracy');

-- 2. Insert into Base
INSERT INTO Base VALUES
(1, 'Fort Eagle', 500, 1),
(2, 'Camp Iron', 350, 2),
(3, 'Station Echo', 600, 3),
(4, 'Forward Ops Delta', 400, 4),
(5, 'Redwood Garrison', 300, 5),
(6, 'Desert Storm Post', 200, 6),
(7, 'Skywatch Base', 800, 7),
(8, 'Oceanic Outpost', 250, 8),
(9, 'Ziggurat HQ', 750, 9),
(10, 'Polar Command', 1000, 10);

-- 3. Insert into Operator
INSERT INTO Operator VALUES
(1, 'Sergeant'),
(2, 'Lieutenant'),
(3, 'Captain'),
(4, 'Major'),
(5, 'Colonel'),
(6, 'Sergeant'),
(7, 'Lieutenant'),
(8, 'Captain'),
(9, 'Colonel'),
(10, 'Major');

-- 4. Insert into Agents
INSERT INTO Agents VALUES
(1, 'John Gray', 'Captain'),
(2, 'Sarah Black', 'Lieutenant'),
(3, 'Derek White', 'Major'),
(4, 'Lucy Green', 'Captain'),
(5, 'Mia Brown', 'Sergeant'),
(6, 'James Fox', 'Corporal'),
(7, 'Nina Red', 'Chief Officer'),
(8, 'Omar Blue', 'Lieutenant'),
(9, 'Iris Golden', 'Sergeant'),
(10, 'Ethan Silver', 'Major');

-- 5. Insert into Satellites
INSERT INTO Satellites VALUES
(1, 'Operational', 'HawkEye-1'),
(2, 'Standby', 'HawkEye-2'),
(3, 'Operational', 'StratoView'),
(4, 'Under Maintenance', 'CosmoTracker'),
(5, 'Operational', 'SkyNet-Alpha'),
(6, 'Standby', 'SkyNet-Beta'),
(7, 'Operational', 'GeoSat-7'),
(8, 'Under Maintenance', 'CloudWatcher'),
(9, 'Operational', 'Orbiter-9'),
(10, 'Operational', 'Zenith-X');

-- 6. Insert into Drones (assign op_id matching valid Operator values)
INSERT INTO Drones VALUES
(1, 100, 2000, 'Raven-X', 1),
(2, 120, 2500, 'Falcon-A', 2),
(3, 150, 3000, 'Eagle-B', 3),
(4, 180, 4000, 'Condor-P', 4),
(5, 200, 4500, 'Vulture-Q', 5),
(6, 220, 4800, 'Hawk-V', 6),
(7, 250, 5200, 'Buzzard-H', 7),
(8, 270, 5500, 'Kestrel-R', 8),
(9, 300, 6000, 'Harrier-T', 9),
(10, 350, 6500, 'Phoenix-Z', 10);

-- 7. Insert into Missiles
INSERT INTO Missiles VALUES
(1, 'Air-to-Air', 50),
(2, 'Air-to-Ground', 60),
(3, 'Short-Range', 70),
(4, 'Long-Range', 80),
(5, 'Cruise', 90),
(6, 'Tactical', 110),
(7, 'Surface-to-Air', 120),
(8, 'Anti-Ship', 130),
(9, 'Interceptor', 140),
(10, 'Ballistic', 150);

-- 8. Insert into Targets
INSERT INTO Targets VALUES
(1, 'Bunker Bravo', 'Ground Installation', 5),
(2, 'Radar Station', 'Communications', 7),
(3, 'Supply Depot', 'Logistics', 4),
(4, 'Enemy Outpost', 'Forward Base', 8),
(5, 'Submarine Dock', 'Naval', 9),
(6, 'Convoy Route', 'Transport', 3),
(7, 'Rebel Camp', 'Hostile', 6),
(8, 'Aircraft Carrier', 'Naval', 10),
(9, 'Missile Silo', 'Strategic', 9),
(10, 'Mountain Hideout', 'Guerrilla', 7);

-- 9. Insert into Person (only person_id and base_id)
INSERT INTO Person VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 10);

-- 10. Insert into Soldier (for persons 1-5)
INSERT INTO Soldier VALUES
(1, 'Sniper Ops', 'Alpha Squad', 'Sergeant'),
(2, 'Medical', 'Bravo Squad', 'Lieutenant'),
(3, 'Infantry', 'Charlie Squad', 'Captain'),
(4, 'Engineer', 'Delta Squad', 'Sergeant'),
(5, 'Pilot', 'Echo Squad', 'Major');

-- 11. Insert into Civil (for persons 6-10)
INSERT INTO Civil VALUES
(6, 'Logistics', 'Accountant'),
(7, 'HR', 'Recruiter'),
(8, 'IT', 'Technician'),
(9, 'Maintenance', 'Supervisor'),
(10, 'Transport', 'Driver');

-- 12. Insert into Vehicles
INSERT INTO Vehicles VALUES
(1, 'Humvee', 5, 'Active', 1),
(2, 'APC', 8, 'Active', 2),
(3, 'Tank', 3, 'Maintenance', 3),
(4, 'Jeep', 2, 'Active', 4),
(5, 'Truck', 10, 'Active', 5),
(6, 'Helicopter', 2, 'Repair', 6),
(7, 'Artillery', 1, 'Active', 7),
(8, 'Fighter Jet', 1, 'Active', 8),
(9, 'Drone Carrier', 0, 'Maintenance', 9),
(10, 'Transport Bus', 20, 'Active', 10);

-- 13. Insert into Supply
INSERT INTO Supply VALUES
(1, 'Ammo Crates', 'Ammunition', 500),
(2, 'Ration Packs', 'Food', 300),
(3, 'Medical Kits', 'Medical', 200),
(4, 'Fuel Barrels', 'Fuel', 1000),
(5, 'Spare Parts', 'Mechanic', 250),
(6, 'Electronics', 'Electronic', 150),
(7, 'Uniforms', 'Clothing', 400),
(8, 'Water Tanks', 'Water', 600),
(9, 'Satellite Parts', 'Components', 50),
(10, 'Office Supplies', 'Stationery', 100);

-- 14. Insert into Intelligence_Reports
-- (report_id, date_created, title, content, classification_level, agent_id)
INSERT INTO Intelligence_Reports VALUES
(1, '2025-01-01 00:00:00', 'Scouting Operation', 'Arctic Recon', 'Secret', 1),
(2, '2025-01-02 00:00:00', 'Threat Analysis', 'Desert Intel', 'Classified', 2),
(3, '2025-01-03 00:00:00', 'Base Status', 'Camp Survey', 'TopSecret', 3),
(4, '2025-01-04 00:00:00', 'Forward Plans', 'Inshore Recon', 'Secret', 4),
(5, '2025-01-05 00:00:00', 'Unit Position', 'Air Patrol', 'Classified', 5),
(6, '2025-01-06 00:00:00', 'Alert Notice', 'Mountain Watch', 'Secret', 6),
(7, '2025-01-07 00:00:00', 'Rescue Mission', 'Night Ops', 'TopSecret', 7),
(8, '2025-01-08 00:00:00', 'Supply Drop', 'Outpost Refill', 'Secret', 8),
(9, '2025-01-09 00:00:00', 'Recon Summary', 'Base Threat', 'TopSecret', 9),
(10, '2025-01-10 00:00:00', 'Final Brief', 'Operation Dawn', 'Classified', 10);

-- 15. Insert into relationship tables

-- Target_Base_Radar
INSERT INTO Target_Base_Radar VALUES
(1,4),
(2,9),
(3,1),
(4,2),
(5,8),
(6,10),
(7,6),
(8,3),
(9,5),
(10,7);

-- Base_Stores_Supply
INSERT INTO Base_Stores_Supply VALUES
(1,3),
(2,1),
(3,2),
(4,4),
(5,8),
(6,5),
(7,6),
(8,7),
(9,9),
(10,10);

-- Drone_Missile_Usage (Sample: for trigger testing)
INSERT INTO Drone_Missile_Usage VALUES
(1,1),
(2,2),
(3,3),
(4,4),
(5,5),
(6,6),
(7,7),
(8,8),
(9,9),
(10,10);

-- Drone_Target_Attacks (Sample data)
INSERT INTO Drone_Target_Attacks VALUES
(1,1),
(2,4),
(3,7),
(4,10),
(5,8),
(6,9),
(7,2),
(8,3),
(9,5),
(10,6);

-- Satellite_Target_Watches (Sample data)
INSERT INTO Satellite_Target_Watches VALUES
(1,1),
(2,2),
(3,3),
(4,4),
(5,5),
(6,6),
(7,7),
(8,8),
(9,9),
(10,10);

-- Intelligence_Report_Decides_Target (Sample data)
INSERT INTO Intelligence_Report_Decides_Target VALUES
(1,5),
(1,6),
(2,3),
(2,7),
(3,9),
(4,1),
(5,2),
(6,8),
(7,10),
(8,4);

-- Agent_Wrote_Report (Sample data - can be added as needed)
-- For example:
-- INSERT INTO Agent_Wrote_Report VALUES (1, 1);

-- =============================================
-- EXAMPLE COMMANDS TO TEST TRIGGERS AND PROCEDURES
-- =============================================
-- Test Trigger 1: Missile assignment log
-- Example: Add a new missile and record an assignment via Drone_Missile_Usage
-- INSERT INTO Missiles VALUES (11, 'Anti-Tank', 200);
-- INSERT INTO Drone_Missile_Usage VALUES (3, 11);
-- SELECT * FROM DroneStatus;

-- Test Trigger 2: Supply changes log
-- UPDATE Supply SET quantity = 450 WHERE supply_id = 1;
-- SELECT * FROM Supply_Audit;

-- Test Stored Procedure 1: AssignOperatorToDrone
-- CALL AssignOperatorToDrone(11, 5, 'Major');
-- SELECT * FROM Operator WHERE op_id = 11;
-- SELECT * FROM Drones WHERE drone_id = 5;

-- Test Stored Procedure 2: GetAgentReports
-- CALL GetAgentReports(1);
