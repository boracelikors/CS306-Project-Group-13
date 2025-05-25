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