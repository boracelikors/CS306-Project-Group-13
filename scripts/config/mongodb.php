<?php
// MongoDB connection settings
define('MONGODB_HOST', 'localhost');
define('MONGODB_PORT', '27017');
define('MONGODB_DATABASE', 'cs306');
define('MONGODB_COLLECTION', 'tickets');

// Helper function to get MongoDB connection
function getMongoDBConnection() {
    try {
        $manager = new MongoDB\Driver\Manager(
            "mongodb://" . MONGODB_HOST . ":" . MONGODB_PORT
        );
        return $manager;
    } catch (MongoDB\Driver\Exception\Exception $e) {
        die("Failed to connect to MongoDB: " . $e->getMessage());
    }
}

// Helper function to insert a document
function insertDocument($collection, $document) {
    $manager = getMongoDBConnection();
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->insert($document);
    
    try {
        $manager->executeBulkWrite(MONGODB_DATABASE . '.' . $collection, $bulk);
        return true;
    } catch (MongoDB\Driver\Exception\Exception $e) {
        return false;
    }
}

// Helper function to find documents
function findDocuments($collection, $filter = [], $options = []) {
    $manager = getMongoDBConnection();
    $query = new MongoDB\Driver\Query($filter, $options);
    
    try {
        $cursor = $manager->executeQuery(MONGODB_DATABASE . '.' . $collection, $query);
        return $cursor->toArray();
    } catch (MongoDB\Driver\Exception\Exception $e) {
        return [];
    }
}

// Helper function to update a document
function updateDocument($collection, $filter, $update) {
    $manager = getMongoDBConnection();
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->update($filter, ['$set' => $update]);
    
    try {
        $manager->executeBulkWrite(MONGODB_DATABASE . '.' . $collection, $bulk);
        return true;
    } catch (MongoDB\Driver\Exception\Exception $e) {
        return false;
    }
}

// Helper function to delete a document
function deleteDocument($collection, $filter) {
    $manager = getMongoDBConnection();
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->delete($filter);
    
    try {
        $manager->executeBulkWrite(MONGODB_DATABASE . '.' . $collection, $bulk);
        return true;
    } catch (MongoDB\Driver\Exception\Exception $e) {
        return false;
    }
}
?> 