<?php
$conn = new mysqli("sql100.infinityfree.com", "if0_40015319", "******", "if0_40015319_voter_voting");

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
} else {
    echo "✅ Connected successfully!";
}

// Test query
$result = $conn->query("SHOW TABLES;");
if ($result) {
    while ($row = $result->fetch_array()) {
        echo "<br>Table: " . $row[0];
    }
} else {
    echo "<br>❌ Query failed: " . $conn->error;
}
?>
