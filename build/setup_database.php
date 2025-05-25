<?php
// Database connection parameters
$host = "localhost";
$username = "root";
$password = "2112";

try {
    // Create initial connection without database
    $conn = new mysqli($host, $username, $password);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "Connected successfully\n";
    
    // Drop and create database
    $conn->query("DROP DATABASE IF EXISTS military_intelligence");
    echo "Dropped existing database if it existed\n";
    
    $conn->query("CREATE DATABASE military_intelligence");
    echo "Created new database\n";
    
    $conn->query("USE military_intelligence");
    echo "Switched to military_intelligence database\n";

    // Create Countries table
    $sql = "CREATE TABLE Countries (
        country_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        region VARCHAR(100),
        political_status VARCHAR(50),
        PRIMARY KEY (country_id)
    )";
    $conn->query($sql);
    echo "Created Countries table\n";

    // Create Base table (no location column)
    $sql = "CREATE TABLE Base (
        base_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        capacity INT,
        country_id INT NOT NULL,
        PRIMARY KEY (base_id),
        FOREIGN KEY (country_id) REFERENCES Countries(country_id)
    )";
    $conn->query($sql);
    echo "Created Base table\n";

    // Create Supply table
    $sql = "CREATE TABLE Supply (
        supply_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        type VARCHAR(50),
        quantity INT,
        PRIMARY KEY (supply_id)
    )";
    $conn->query($sql);
    echo "Created Supply table\n";

    // Create Vehicles table
    $sql = "CREATE TABLE Vehicles (
        vehicle_id INT NOT NULL,
        type VARCHAR(50),
        capacity INT,
        operational_status VARCHAR(50),
        base_id INT NOT NULL,
        PRIMARY KEY (vehicle_id),
        FOREIGN KEY (base_id) REFERENCES Base(base_id)
    )";
    $conn->query($sql);
    echo "Created Vehicles table\n";

    // Create Operator table (only op_id and rank)
    $sql = "CREATE TABLE Operator (
        op_id INT NOT NULL,
        rank VARCHAR(50),
        PRIMARY KEY (op_id)
    )";
    $conn->query($sql);
    echo "Created Operator table\n";

    // Create Person table (only person_id and base_id)
    $sql = "CREATE TABLE Person (
        person_id INT NOT NULL,
        base_id INT NOT NULL,
        PRIMARY KEY (person_id),
        FOREIGN KEY (base_id) REFERENCES Base(base_id)
    )";
    $conn->query($sql);
    echo "Created Person table\n";

    // Create Soldier table using person_id (ISA inheritance)
    $sql = "CREATE TABLE Soldier (
        person_id INT NOT NULL,
        specialty VARCHAR(100),
        unit VARCHAR(100),
        rank VARCHAR(50),
        PRIMARY KEY (person_id),
        FOREIGN KEY (person_id) REFERENCES Person(person_id)
    )";
    $conn->query($sql);
    echo "Created Soldier table\n";

    // Create Civil table using person_id (ISA inheritance)
    $sql = "CREATE TABLE Civil (
        person_id INT NOT NULL,
        department VARCHAR(100),
        occupation VARCHAR(100),
        PRIMARY KEY (person_id),
        FOREIGN KEY (person_id) REFERENCES Person(person_id)
    )";
    $conn->query($sql);
    echo "Created Civil table\n";

    // Create Agents table
    $sql = "CREATE TABLE Agents (
        agent_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        rank VARCHAR(50),
        PRIMARY KEY (agent_id)
    )";
    $conn->query($sql);
    echo "Created Agents table\n";

    // Create Missiles table (without direct drone_id)
    $sql = "CREATE TABLE Missiles (
        missile_id INT NOT NULL,
        type VARCHAR(50),
        range INT,
        PRIMARY KEY (missile_id)
    )";
    $conn->query($sql);
    echo "Created Missiles table\n";

    // Create Drones table (with op_id as foreign key)
    $sql = "CREATE TABLE Drones (
        drone_id INT NOT NULL,
        range INT,
        max_altitude INT,
        model VARCHAR(100),
        op_id INT NOT NULL,
        PRIMARY KEY (drone_id),
        FOREIGN KEY (op_id) REFERENCES Operator(op_id)
    )";
    $conn->query($sql);
    echo "Created Drones table\n";

    // Create Satellites table (order: satellite_id, operational_status, name)
    $sql = "CREATE TABLE Satellites (
        satellite_id INT NOT NULL,
        operational_status VARCHAR(50),
        name VARCHAR(100) NOT NULL,
        PRIMARY KEY (satellite_id)
    )";
    $conn->query($sql);
    echo "Created Satellites table\n";

    // Create Targets table
    $sql = "CREATE TABLE Targets (
        target_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        type VARCHAR(50),
        priority_level INT,
        PRIMARY KEY (target_id)
    )";
    $conn->query($sql);
    echo "Created Targets table\n";

    // Create Intelligence_Reports table
    $sql = "CREATE TABLE Intelligence_Reports (
        report_id INT NOT NULL,
        title VARCHAR(200) NOT NULL,
        content TEXT,
        date_created DATETIME NOT NULL,
        classification_level VARCHAR(50),
        agent_id INT,
        PRIMARY KEY (report_id),
        FOREIGN KEY (agent_id) REFERENCES Agents(agent_id)
    )";
    $conn->query($sql);
    echo "Created Intelligence_Reports table\n";

    // Create relationship tables exactly as in the PDF

    // Target_Base_Radar
    $sql = "CREATE TABLE Target_Base_Radar (
        target_id INT NOT NULL,
        base_id INT NOT NULL,
        PRIMARY KEY (target_id, base_id),
        FOREIGN KEY (target_id) REFERENCES Targets(target_id),
        FOREIGN KEY (base_id) REFERENCES Base(base_id)
    )";
    $conn->query($sql);
    echo "Created Target_Base_Radar table\n";

    // Base_Stores_Supply
    $sql = "CREATE TABLE Base_Stores_Supply (
        base_id INT NOT NULL,
        supply_id INT NOT NULL,
        PRIMARY KEY (base_id, supply_id),
        FOREIGN KEY (base_id) REFERENCES Base(base_id),
        FOREIGN KEY (supply_id) REFERENCES Supply(supply_id)
    )";
    $conn->query($sql);
    echo "Created Base_Stores_Supply table\n";

    // Drone_Missile_Usage
    $sql = "CREATE TABLE Drone_Missile_Usage (
        drone_id INT NOT NULL,
        missile_id INT NOT NULL,
        PRIMARY KEY (drone_id, missile_id),
        FOREIGN KEY (drone_id) REFERENCES Drones(drone_id),
        FOREIGN KEY (missile_id) REFERENCES Missiles(missile_id)
    )";
    $conn->query($sql);
    echo "Created Drone_Missile_Usage table\n";

    // Drone_Target_Attacks
    $sql = "CREATE TABLE Drone_Target_Attacks (
        drone_id INT NOT NULL,
        target_id INT NOT NULL,
        PRIMARY KEY (drone_id, target_id),
        FOREIGN KEY (drone_id) REFERENCES Drones(drone_id),
        FOREIGN KEY (target_id) REFERENCES Targets(target_id)
    )";
    $conn->query($sql);
    echo "Created Drone_Target_Attacks table\n";

    // Satellite_Target_Watches
    $sql = "CREATE TABLE Satellite_Target_Watches (
        satellite_id INT NOT NULL,
        target_id INT NOT NULL,
        PRIMARY KEY (satellite_id, target_id),
        FOREIGN KEY (satellite_id) REFERENCES Satellites(satellite_id),
        FOREIGN KEY (target_id) REFERENCES Targets(target_id)
    )";
    $conn->query($sql);
    echo "Created Satellite_Target_Watches table\n";

    // Intelligence_Report_Decides_Target
    $sql = "CREATE TABLE Intelligence_Report_Decides_Target (
        report_id INT NOT NULL,
        target_id INT NOT NULL,
        PRIMARY KEY (report_id, target_id),
        FOREIGN KEY (report_id) REFERENCES Intelligence_Reports(report_id),
        FOREIGN KEY (target_id) REFERENCES Targets(target_id)
    )";
    $conn->query($sql);
    echo "Created Intelligence_Report_Decides_Target table\n";

    // Agent_Wrote_Report
    $sql = "CREATE TABLE Agent_Wrote_Report (
        agent_id INT NOT NULL,
        report_id INT NOT NULL,
        PRIMARY KEY (agent_id, report_id),
        FOREIGN KEY (agent_id) REFERENCES Agents(agent_id),
        FOREIGN KEY (report_id) REFERENCES Intelligence_Reports(report_id)
    )";
    $conn->query($sql);
    echo "Created Agent_Wrote_Report table\n";

    // Insert sample data into Countries
    $sql = "INSERT INTO Countries VALUES
        (1, 'Atlantis','North Sea','Stable Democracy'),
        (2, 'Azura','South Sea','Monarchy'),
        (3, 'Rivia','Eastern Continent','Federation'),
        (4, 'Arcadia','Western Isles','Republic'),
        (5, 'Novia','Northern Mainland','Confederation'),
        (6, 'Eldora','Eastern Mainland','Kingdom'),
        (7, 'Valoria','Valley Region','Stable Democracy'),
        (8, 'Terranova','Island Frontier','Republic'),
        (9, 'Sandora','Desert Region','Autocracy'),
        (10, 'Polaris','Polar Region','Stable Democracy')";
    $conn->query($sql);
    echo "Inserted Countries data\n";

    // Insert sample data into Base
    $sql = "INSERT INTO Base VALUES
        (1, 'Fort Eagle', 500, 1),
        (2, 'Camp Iron', 350, 2),
        (3, 'Station Echo', 600, 3),
        (4, 'Forward Ops Delta', 400, 4),
        (5, 'Redwood Garrison', 300, 5),
        (6, 'Desert Storm Post', 200, 6),
        (7, 'Skywatch Base', 800, 7),
        (8, 'Oceanic Outpost', 250, 8),
        (9, 'Ziggurat HQ', 750, 9),
        (10, 'Polar Command', 1000, 10)";
    $conn->query($sql);
    echo "Inserted Base data\n";

    // Insert sample data into Supply
    $sql = "INSERT INTO Supply VALUES
        (1, 'Ammo Crates', 'Ammunition', 500),
        (2, 'Ration Packs', 'Food', 300),
        (3, 'Medical Kits', 'Medical', 200),
        (4, 'Fuel Barrels', 'Fuel', 1000),
        (5, 'Spare Parts', 'Mechanic', 250),
        (6, 'Electronics', 'Electronic', 150),
        (7, 'Uniforms', 'Clothing', 400),
        (8, 'Water Tanks', 'Water', 600),
        (9, 'Satellite Parts', 'Components', 50),
        (10, 'Office Supplies', 'Stationery', 100)";
    $conn->query($sql);
    echo "Inserted Supply data\n";

    // Insert sample data into Vehicles
    $sql = "INSERT INTO Vehicles VALUES
        (1, 'Humvee', 5, 'Active', 1),
        (2, 'APC', 8, 'Active', 2),
        (3, 'Tank', 3, 'Maintenance', 3),
        (4, 'Jeep', 2, 'Active', 4),
        (5, 'Truck', 10, 'Active', 5),
        (6, 'Helicopter', 2, 'Repair', 6),
        (7, 'Artillery', 1, 'Active', 7),
        (8, 'Fighter Jet', 1, 'Active', 8),
        (9, 'Drone Carrier', 0, 'Maintenance', 9),
        (10, 'Transport Bus', 20, 'Active', 10)";
    $conn->query($sql);
    echo "Inserted Vehicles data\n";

    // Insert sample data into Operator
    // (Note: Only op_id and rank are provided per schema)
    $sql = "INSERT INTO Operator VALUES
        (1, 'Captain'),
        (2, 'Lieutenant'),
        (3, 'Major'),
        (4, 'Captain'),
        (5, 'Sergeant'),
        (6, 'Corporal'),
        (7, 'Chief Officer'),
        (8, 'Lieutenant'),
        (9, 'Sergeant'),
        (10, 'Major')";
    $conn->query($sql);
    echo "Inserted Operator data\n";

    // Insert sample data into Person
    // (Only person_id and base_id, corresponding one-to-one to a base assignment)
    $sql = "INSERT INTO Person VALUES
        (1, 1),
        (2, 2),
        (3, 3),
        (4, 4),
        (5, 5),
        (6, 6),
        (7, 7),
        (8, 8),
        (9, 9),
        (10, 10)";
    $conn->query($sql);
    echo "Inserted Person data\n";

    // Insert sample data into Soldier
    $sql = "INSERT INTO Soldier VALUES
        (1, 'Sniper Ops', 'Alpha Squad', 'Sergeant'),
        (2, 'Medical', 'Bravo Squad', 'Lieutenant'),
        (3, 'Infantry', 'Charlie Squad', 'Captain'),
        (4, 'Engineer', 'Delta Squad', 'Sergeant'),
        (5, 'Pilot', 'Echo Squad', 'Major')";
    $conn->query($sql);
    echo "Inserted Soldier data\n";

    // Insert sample data into Civil
    $sql = "INSERT INTO Civil VALUES
        (6, 'Logistics', 'Accountant'),
        (7, 'HR', 'Recruiter'),
        (8, 'IT', 'Technician'),
        (9, 'Maintenance', 'Supervisor'),
        (10, 'Transport', 'Driver')";
    $conn->query($sql);
    echo "Inserted Civil data\n";

    // Insert sample data into Agents
    $sql = "INSERT INTO Agents VALUES
        (1, 'John Gray', 'Captain'),
        (2, 'Sarah Black', 'Lieutenant'),
        (3, 'Derek White', 'Major'),
        (4, 'Lucy Green', 'Captain'),
        (5, 'Mia Brown', 'Sergeant'),
        (6, 'James Fox', 'Corporal'),
        (7, 'Nina Red', 'Chief Officer'),
        (8, 'Omar Blue', 'Lieutenant'),
        (9, 'Iris Golden', 'Sergeant'),
        (10, 'Ethan Silver', 'Major')";
    $conn->query($sql);
    echo "Inserted Agents data\n";

    // Insert sample data into Satellites
    $sql = "INSERT INTO Satellites VALUES
        (1, 'Operational', 'HawkEye-1'),
        (2, 'Standby', 'HawkEye-2'),
        (3, 'Operational', 'StratoView'),
        (4, 'Under Maintenance', 'CosmoTracker'),
        (5, 'Operational', 'SkyNet-Alpha'),
        (6, 'Standby', 'SkyNet-Beta'),
        (7, 'Operational', 'GeoSat-7'),
        (8, 'Under Maintenance', 'CloudWatcher'),
        (9, 'Operational', 'Orbiter-9'),
        (10, 'Operational', 'Zenith-X')";
    $conn->query($sql);
    echo "Inserted Satellites data\n";

    // Insert sample data into Missiles
    $sql = "INSERT INTO Missiles VALUES
        (1, 'Air-to-Air', 50),
        (2, 'Air-to-Ground', 60),
        (3, 'Short-Range', 70),
        (4, 'Long-Range', 80),
        (5, 'Cruise', 90),
        (6, 'Tactical', 110),
        (7, 'Surface-to-Air', 120),
        (8, 'Anti-Ship', 130),
        (9, 'Interceptor', 140),
        (10, 'Ballistic', 150)";
    $conn->query($sql);
    echo "Inserted Missiles data\n";

    // Insert sample data into Drones
    // (Ensure that each drone is assigned an operator (op_id))
    $sql = "INSERT INTO Drones VALUES
        (1, 100, 2000, 'Raven-X', 1),
        (2, 120, 2500, 'Falcon-A', 2),
        (3, 150, 3000, 'Eagle-B', 3),
        (4, 180, 4000, 'Condor-P', 4),
        (5, 200, 4500, 'Vulture-Q', 5),
        (6, 220, 4800, 'Hawk-V', 6),
        (7, 250, 5200, 'Buzzard-H', 7),
        (8, 270, 5500, 'Kestrel-R', 8),
        (9, 300, 6000, 'Harrier-T', 9),
        (10, 350, 6500, 'Phoenix-Z', 10)";
    $conn->query($sql);
    echo "Inserted Drones data\n";

    // Insert sample data into Intelligence_Reports
    // (date_created uses NOW() to obtain current datetime)
    $sql = "INSERT INTO Intelligence_Reports VALUES
        (1, 'Arctic Reconnaissance', 'Enemy force spotted in northern region', NOW(), 'Secret', 1),
        (2, 'Desert Operations', 'Suspicious movement detected', NOW(), 'Top Secret', 2),
        (3, 'Maritime Surveillance', 'Naval activity increased', NOW(), 'Confidential', 3),
        (4, 'Mountain Watch', 'Supply routes identified', NOW(), 'Secret', 4),
        (5, 'Urban Intelligence', 'Network infrastructure mapped', NOW(), 'Top Secret', 5),
        (6, 'Coastal Defense', 'Defensive positions analyzed', NOW(), 'Secret', 6),
        (7, 'Forest Operations', 'Hidden base discovered', NOW(), 'Top Secret', 7),
        (8, 'Desert Storm', 'Movement patterns recorded', NOW(), 'Confidential', 8)";
    $conn->query($sql);
    echo "Inserted Intelligence_Reports data\n";

    // Insert sample data into relationship tables

    // Intelligence_Report_Decides_Target
    $sql = "INSERT INTO Intelligence_Report_Decides_Target VALUES
        (1, 5),
        (1, 6),
        (2, 3),
        (2, 7),
        (3, 9),
        (4, 1),
        (5, 2),
        (6, 8),
        (7, 10),
        (8, 4)";
    $conn->query($sql);
    echo "Inserted Intelligence_Report_Decides_Target data\n";

    // Satellite_Target_Watches
    $sql = "INSERT INTO Satellite_Target_Watches VALUES
        (1, 1),
        (2, 2),
        (3, 3),
        (4, 4),
        (5, 5),
        (6, 6),
        (7, 7),
        (8, 8),
        (9, 9),
        (10, 10)";
    $conn->query($sql);
    echo "Inserted Satellite_Target_Watches data\n";

    // Drone_Missile_Usage
    $sql = "INSERT INTO Drone_Missile_Usage VALUES
        (1, 1),
        (2, 2),
        (3, 3),
        (4, 4),
        (5, 5),
        (6, 6),
        (7, 7),
        (8, 8),
        (9, 9),
        (10, 10)";
    $conn->query($sql);
    echo "Inserted Drone_Missile_Usage data\n";

    // Drone_Target_Attacks
    $sql = "INSERT INTO Drone_Target_Attacks VALUES
        (1, 1),
        (2, 4),
        (3, 7),
        (4, 10),
        (5, 8),
        (6, 9),
        (7, 2),
        (8, 3),
        (9, 5),
        (10, 6)";
    $conn->query($sql);
    echo "Inserted Drone_Target_Attacks data\n";

    // Target_Base_Radar
    $sql = "INSERT INTO Target_Base_Radar VALUES
        (1, 4),
        (2, 9),
        (3, 1),
        (4, 2),
        (5, 8),
        (6, 10),
        (7, 6),
        (8, 3),
        (9, 5),
        (10, 7)";
    $conn->query($sql);
    echo "Inserted Target_Base_Radar data\n";

    // Base_Stores_Supply
    $sql = "INSERT INTO Base_Stores_Supply VALUES
        (1, 3),
        (2, 1),
        (3, 2),
        (4, 4),
        (5, 8),
        (6, 5),
        (7, 6),
        (8, 7),
        (9, 9),
        (10, 10)";
    $conn->query($sql);
    echo "Inserted Base_Stores_Supply data\n";

    echo "\nDatabase setup completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
