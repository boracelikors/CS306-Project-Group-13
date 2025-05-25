-- Master setup script for CS306 Project Phase III

-- First, create the database and tables
SOURCE database_setup.sql;

-- Then, create the stored procedures
SOURCE stored_procedures.sql;

-- Finally, create the triggers
SOURCE triggers.sql;

-- Verify setup
SELECT 'Database setup completed successfully.' AS message; 