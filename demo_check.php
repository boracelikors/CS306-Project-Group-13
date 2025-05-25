<?php
/**
 * CS306 Demo Verification Script
 * Run this script to check if your system is demo-ready
 */

echo "<h1>CS306 Demo Readiness Check</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .check { margin: 10px 0; padding: 10px; border-radius: 5px; }
    .pass { background: #d4edda; color: #155724; }
    .fail { background: #f8d7da; color: #721c24; }
    .warning { background: #fff3cd; color: #856404; }
</style>";

$checks = [];

// 1. Check PHP Version
$phpVersion = phpversion();
$checks[] = [
    'name' => 'PHP Version',
    'status' => version_compare($phpVersion, '7.4', '>='),
    'message' => "PHP $phpVersion " . (version_compare($phpVersion, '7.4', '>=') ? '✅' : '❌ Need 7.4+')
];

// 2. Check MySQL Connection
try {
    require_once 'scripts/config/mysql.php';
    $conn = getMySQLConnection();
    $checks[] = [
        'name' => 'MySQL Connection',
        'status' => true,
        'message' => '✅ MySQL connected successfully'
    ];
    
    // Check if database exists
    $result = $conn->query("SELECT COUNT(*) as count FROM Drones");
    $row = $result->fetch_assoc();
    $checks[] = [
        'name' => 'Database Schema',
        'status' => $row['count'] > 0,
        'message' => $row['count'] > 0 ? "✅ Found {$row['count']} drones in database" : '❌ Database schema missing'
    ];
    
} catch (Exception $e) {
    $checks[] = [
        'name' => 'MySQL Connection',
        'status' => false,
        'message' => '❌ MySQL connection failed: ' . $e->getMessage()
    ];
}

// 3. Check MongoDB Extension
$mongoLoaded = extension_loaded('mongodb');
$checks[] = [
    'name' => 'MongoDB Extension',
    'status' => $mongoLoaded,
    'message' => $mongoLoaded ? '✅ MongoDB extension loaded' : '❌ MongoDB extension not found'
];

// 4. Check MongoDB Connection
if ($mongoLoaded) {
    try {
        $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
        $command = new MongoDB\Driver\Command(['ping' => 1]);
        $manager->executeCommand('admin', $command);
        $checks[] = [
            'name' => 'MongoDB Connection',
            'status' => true,
            'message' => '✅ MongoDB connected successfully'
        ];
    } catch (Exception $e) {
        $checks[] = [
            'name' => 'MongoDB Connection',
            'status' => false,
            'message' => '❌ MongoDB connection failed: ' . $e->getMessage()
        ];
    }
}

// 5. Check File Structure
$requiredFiles = [
    'scripts/user/index.php' => 'User Homepage',
    'scripts/admin/index.php' => 'Admin Homepage',
    'scripts/user/triggers/trigger1.php' => 'Trigger 1',
    'scripts/user/triggers/trigger2.php' => 'Trigger 2',
    'scripts/user/triggers/trigger3.php' => 'Trigger 3',
    'scripts/user/triggers/trigger4.php' => 'Trigger 4',
    'scripts/user/triggers/trigger5.php' => 'Trigger 5',
    'scripts/user/procedures/assign_operator.php' => 'Procedure 1',
    'scripts/user/procedures/reserve_vehicle.php' => 'Procedure 2',
    'scripts/user/procedures/agent_reports.php' => 'Procedure 3',
    'scripts/user/procedures/generate_report.php' => 'Procedure 4',
    'scripts/user/procedures/order_supply.php' => 'Procedure 5',
    'scripts/user/support/tickets.php' => 'Support Tickets',
];

foreach ($requiredFiles as $file => $name) {
    $exists = file_exists($file);
    $checks[] = [
        'name' => $name,
        'status' => $exists,
        'message' => $exists ? "✅ $name exists" : "❌ $name missing"
    ];
}

// Display Results
$passCount = 0;
$totalCount = count($checks);

foreach ($checks as $check) {
    $class = $check['status'] ? 'pass' : 'fail';
    if ($check['status']) $passCount++;
    
    echo "<div class='check $class'>";
    echo "<strong>{$check['name']}:</strong> {$check['message']}";
    echo "</div>";
}

// Summary
echo "<h2>Summary</h2>";
$percentage = round(($passCount / $totalCount) * 100);
$summaryClass = $percentage >= 90 ? 'pass' : ($percentage >= 70 ? 'warning' : 'fail');

echo "<div class='check $summaryClass'>";
echo "<strong>Overall Status:</strong> $passCount/$totalCount checks passed ($percentage%)";

if ($percentage >= 90) {
    echo "<br>🎉 <strong>DEMO READY!</strong> Your system is ready for CS306 demonstration.";
} elseif ($percentage >= 70) {
    echo "<br>⚠️ <strong>ALMOST READY</strong> - Fix remaining issues before demo.";
} else {
    echo "<br>❌ <strong>NOT READY</strong> - Major issues need to be resolved.";
}
echo "</div>";

// Next Steps
echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Fix any failed checks above</li>";
echo "<li>Run <code>start_webapp.bat</code></li>";
echo "<li>Test user interface: <a href='http://localhost:8000/user'>localhost:8000/user</a></li>";
echo "<li>Test admin interface: <a href='http://localhost:8000/admin'>localhost:8000/admin</a></li>";
echo "<li>Test all 5 triggers and 5 procedures</li>";
echo "<li>Test support ticket system</li>";
echo "</ol>";

echo "<h2>Demo URLs</h2>";
echo "<ul>";
echo "<li><strong>User Interface:</strong> <a href='http://localhost:8000/user'>http://localhost:8000/user</a></li>";
echo "<li><strong>Admin Interface:</strong> <a href='http://localhost:8000/admin'>http://localhost:8000/admin</a></li>";
echo "</ul>";
?> 