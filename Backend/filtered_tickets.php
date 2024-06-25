<?php
include "config.php"; // Include your database connection file
session_start(); // Initialize session for messages

// Initialize variables for filter criteria
$station_id = isset($_GET['station_id']) ? $_GET['station_id'] : '';
$issue_type = isset($_GET['issue_type']) ? $_GET['issue_type'] : '';
$priority = isset($_GET['priority']) ? $_GET['priority'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$assign = isset($_GET['assign']) ? $_GET['assign'] : '';
$ticket_open_from = isset($_GET['ticket_open_from']) ? $_GET['ticket_open_from'] : '';
$ticket_open_to = isset($_GET['ticket_open_to']) ? $_GET['ticket_open_to'] : '';
$ticket_close_from = isset($_GET['ticket_close_from']) ? $_GET['ticket_close_from'] : '';
$ticket_close_to = isset($_GET['ticket_close_to']) ? $_GET['ticket_close_to'] : '';

// Construct the SQL query with filters
$query = "SELECT * FROM tbl_ticket WHERE 1=1";

if (!empty($station_id)) {
    $query .= " AND station_id = '" . $conn->real_escape_string($station_id) . "'";
}
if (!empty($issue_type)) {
    $query .= " AND issue_type = '" . $conn->real_escape_string($issue_type) . "'";
}
if (!empty($priority)) {
    $query .= " AND priority = '" . $conn->real_escape_string($priority) . "'";
}
if (!empty($status)) {
    $query .= " AND status = '" . $conn->real_escape_string($status) . "'";
}
if (!empty($assign)) {
    $query .= " AND assign = '" . $conn->real_escape_string($assign) . "'";
}
if (!empty($ticket_open_from)) {
    $query .= " AND ticket_open >= '" . $conn->real_escape_string($ticket_open_from) . "'";
}
if (!empty($ticket_open_to)) {
    $query .= " AND ticket_open <= '" . $conn->real_escape_string($ticket_open_to) . "'";
}
if (!empty($ticket_close_from)) {
    $query .= " AND ticket_close >= '" . $conn->real_escape_string($ticket_close_from) . "'";
}
if (!empty($ticket_close_to)) {
    $query .= " AND ticket_close <= '" . $conn->real_escape_string($ticket_close_to) . "'";
}

// Execute the query
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "<table class='table table-bordered'>";
    echo "<thead><tr><th>Ticket ID</th><th>Station ID</th><th>Station Name</th><th>Issue Type</th><th>Priority</th><th>Status</th><th>Assign</th><th>Ticket Open</th><th>Ticket Close</th></tr></thead>";
    echo "<tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['ticket_id'] . "</td>";
        echo "<td>" . $row['station_id'] . "</td>";
        echo "<td>" . $row['station_name'] . "</td>";
        echo "<td>" . $row['issue_type'] . "</td>";
        echo "<td>" . $row['priority'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "<td>" . $row['users_id'] . "</td>";
        echo "<td>" . $row['ticket_open'] . "</td>";
        echo "<td>" . $row['ticket_close'] . "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
} else {
    echo "<p>No tickets found based on the filter criteria.</p>";
}
