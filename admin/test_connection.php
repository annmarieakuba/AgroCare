<?php
// Simple test file to check database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../settings/db_class.php';

echo "<h2>Database Connection Test</h2>";

try {
    $db = new db_connection();
    
    if (!$db->db_connect()) {
        die("<p style='color: red;'>❌ Database connection failed!</p>");
    }
    
    echo "<p style='color: green;'>✅ Database connected successfully!</p>";
    
    // Test query
    $testQuery = "SELECT COUNT(*) as count FROM customer";
    $result = mysqli_query($db->db, $testQuery);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo "<p>✅ Test query successful. Found {$row['count']} customers in database.</p>";
    } else {
        echo "<p style='color: red;'>❌ Test query failed: " . mysqli_error($db->db) . "</p>";
    }
    
    // Check if tables exist
    $tables = ['customer', 'orders', 'orderdetails', 'products'];
    echo "<h3>Checking Tables:</h3><ul>";
    foreach ($tables as $table) {
        $checkQuery = "SHOW TABLES LIKE '$table'";
        $checkResult = mysqli_query($db->db, $checkQuery);
        if ($checkResult && mysqli_num_rows($checkResult) > 0) {
            echo "<li style='color: green;'>✅ Table '$table' exists</li>";
        } else {
            echo "<li style='color: red;'>❌ Table '$table' NOT found</li>";
        }
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

