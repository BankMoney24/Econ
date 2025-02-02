<?php
session_start();
require 'config.php';  // Include the database connection

// Function to check and limit requests from a single IP
function rateLimit() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $limit = 5;  // Maximum requests allowed
    $timeFrame = 60;  // Time frame in seconds (1 minute)

    if (!isset($_SESSION['requests'])) {
        $_SESSION['requests'] = [];
    }

    // Remove old requests from session
    $_SESSION['requests'] = array_filter($_SESSION['requests'], function ($timestamp) use ($timeFrame) {
        return ($timestamp > time() - $timeFrame);
    });

    // Check request count
    if (count($_SESSION['requests']) >= $limit) {
        die("Too many requests. Please try again later.");
    }

    // Store current request
    $_SESSION['requests'][] = time();
}

// Apply rate limiting
rateLimit();

// Validate and sanitize input
$tracking_number = filter_input(INPUT_GET, 'tracking_number', FILTER_SANITIZE_STRING);

// Check if tracking number is provided
if ($tracking_number && !empty($tracking_number)) {
    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT tracking_number, status, location, estimated_delivery, last_updated 
                            FROM shipments WHERE tracking_number = ?");
    $stmt->bind_param("s", $tracking_number);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if shipment exists
    if ($result->num_rows > 0) {
        $shipment = $result->fetch_assoc();
        echo "<h2>Tracking Details</h2>";
        echo "<p>Tracking Number: " . htmlspecialchars($shipment['tracking_number']) . "</p>";
        echo "<p>Status: " . htmlspecialchars($shipment['status']) . "</p>";
        echo "<p>Current Location: " . htmlspecialchars($shipment['location']) . "</p>";
        echo "<p>Estimated Delivery: " . htmlspecialchars($shipment['estimated_delivery']) . "</p>";
        echo "<p>Last Updated: " . htmlspecialchars($shipment['last_updated']) . "</p>";
    } else {
        echo "<p>Invalid tracking number. Please check and try again.</p>";
    }

    $stmt->close();
} else {
    echo "<p>Please enter a valid tracking number.</p>";
}

$conn->close();
?>
