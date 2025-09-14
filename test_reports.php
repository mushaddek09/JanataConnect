<?php
/**
 * Test file for the Report Generation System
 * 
 * This file tests the basic functionality of the report system
 * to ensure everything is working correctly.
 * 
 * @author JanataConnect Team
 * @version 1.0.0
 * @since 2025-01-01
 */

// Start session for testing
session_start();

// Define application paths
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Include required files
require_once CONFIG_PATH . '/database.php';
require_once CONFIG_PATH . '/config.php';
require_once APP_PATH . '/models/BaseModel.php';
require_once APP_PATH . '/models/User.php';
require_once APP_PATH . '/models/Submission.php';
require_once APP_PATH . '/models/Department.php';
require_once APP_PATH . '/models/ReportModel.php';

// Test user session (simulate logged in user)
$_SESSION['user'] = [
    'id' => 1,
    'name' => 'Test User',
    'email' => 'test@example.com',
    'role' => 'admin'
];
$_SESSION['user_role'] = 'admin';

echo "<h1>JanataConnect Report System Test</h1>";
echo "<p>Testing the report generation system...</p>";

try {
    // Test 1: Create ReportModel instance
    echo "<h2>Test 1: Creating ReportModel instance</h2>";
    $reportModel = new ReportModel();
    echo "✅ ReportModel created successfully<br>";

    // Test 2: Test basic statistics
    echo "<h2>Test 2: Testing basic statistics</h2>";
    $basicStats = $reportModel->getBasicStatistics();
    echo "✅ Basic statistics retrieved successfully<br>";
    echo "Total submissions: " . $basicStats['total_submissions'] . "<br>";
    echo "Pending: " . $basicStats['pending'] . "<br>";
    echo "Completed: " . $basicStats['completed'] . "<br>";

    // Test 3: Test department statistics
    echo "<h2>Test 3: Testing department statistics</h2>";
    $deptStats = $reportModel->getDepartmentStatistics();
    echo "✅ Department statistics retrieved successfully<br>";
    echo "Number of departments: " . count($deptStats) . "<br>";

    // Test 4: Test status statistics for charts
    echo "<h2>Test 4: Testing status statistics for charts</h2>";
    $statusStats = $reportModel->getSubmissionStatusStatistics('admin', 0);
    echo "✅ Status statistics retrieved successfully<br>";
    echo "Status labels: " . implode(', ', $statusStats['labels']) . "<br>";
    echo "Status data: " . implode(', ', $statusStats['data']) . "<br>";

    // Test 5: Test priority statistics
    echo "<h2>Test 5: Testing priority statistics</h2>";
    $priorityStats = $reportModel->getPriorityStatistics('admin', 0);
    echo "✅ Priority statistics retrieved successfully<br>";
    echo "Priority labels: " . implode(', ', $priorityStats['labels']) . "<br>";
    echo "Priority data: " . implode(', ', $priorityStats['data']) . "<br>";

    // Test 6: Test monthly statistics
    echo "<h2>Test 6: Testing monthly statistics</h2>";
    $monthlyStats = $reportModel->getMonthlyStatistics('admin', 0);
    echo "✅ Monthly statistics retrieved successfully<br>";
    echo "Monthly labels: " . implode(', ', $monthlyStats['labels']) . "<br>";
    echo "Monthly data: " . implode(', ', $monthlyStats['data']) . "<br>";

    // Test 7: Test comprehensive statistics
    echo "<h2>Test 7: Testing comprehensive statistics</h2>";
    $comprehensiveStats = $reportModel->getComprehensiveStatistics();
    echo "✅ Comprehensive statistics retrieved successfully<br>";
    echo "All statistics loaded: " . (isset($comprehensiveStats['basic']) ? 'Yes' : 'No') . "<br>";

    echo "<h2>🎉 All tests passed successfully!</h2>";
    echo "<p>The report generation system is working correctly.</p>";
    echo "<p><a href='/JanataConnect/reports'>Go to Reports Dashboard</a></p>";

} catch (Exception $e) {
    echo "<h2>❌ Test failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
}

echo "<hr>";
echo "<h3>System Information</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>MySQL Extension: " . (extension_loaded('mysqli') ? 'Loaded' : 'Not loaded') . "</p>";
echo "<p>Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive') . "</p>";
echo "<p>Current User: " . ($_SESSION['user']['name'] ?? 'Not set') . "</p>";
echo "<p>User Role: " . ($_SESSION['user']['role'] ?? 'Not set') . "</p>";
?>
