<?php
// Start session and include necessary files
session_start();
require_once "config.php";

$email = $_SESSION['email'];
$password = $_SESSION['password'];
if ($email != false && $password != false) {
        $sql = "SELECT * FROM tbl_users WHERE email = '$email'";
        $run_Sql = mysqli_query($conn, $sql);
        if ($run_Sql) {
                $fetch_info = mysqli_fetch_assoc($run_Sql);
                $status = $fetch_info['status'];
                $code = $fetch_info['code'];
                if ($status == "1") {
                        if ($code != 0) {
                                header('Location: reset-code.php');
                        }
                } else {
                        header('Location: user-otp.php');
                }
        }
} else {
        header('Location: ../index.php');
}

include "include/rules_ticket.php"; // Adjust the path as per your file structure

// Retrieve filter values (sanitize inputs to prevent SQL injection)
$station_id = isset($_POST['station_id']) ? trim($_POST['station_id']) : '';
$issue_type = isset($_POST['issue_type']) ? trim($_POST['issue_type']) : '';
$priority = isset($_POST['priority']) ? trim($_POST['priority']) : '';
$status = isset($_POST['status']) ? trim($_POST['status']) : '';
$users_id = isset($_POST['users_id']) ? trim($_POST['users_id']) : '';
$ticket_open_from = isset($_POST['ticket_open_from']) ? trim($_POST['ticket_open_from']) : '';
$ticket_open_to = isset($_POST['ticket_open_to']) ? trim($_POST['ticket_open_to']) : '';
$ticket_close_from = isset($_POST['ticket_close_from']) ? trim($_POST['ticket_close_from']) : '';
$ticket_close_to = isset($_POST['ticket_close_to']) ? trim($_POST['ticket_close_to']) : '';

// Initial base query
$sql = "
    SELECT 
        t.*, 
        REPLACE(GROUP_CONCAT(u.users_name SEPARATOR ', '), ', ', ',') as users_name
    FROM 
        tbl_ticket t
    LEFT JOIN 
        tbl_users u ON FIND_IN_SET(u.users_id, t.users_id)
";

// Define an array to hold filter conditions
$filterConditions = [];
$params = [];
$types = '';

// Check if each filter parameter is set and build conditions accordingly
if (!empty($station_id)) {
        $filterConditions[] = "t.station_id = ?";
        $params[] = $station_id;
        $types .= 's';
}

if (!empty($issue_type)) {
        $filterConditions[] = "t.issue_type = ?";
        $params[] = $issue_type;
        $types .= 's';
}

if (!empty($priority)) {
        $filterConditions[] = "t.priority = ?";
        $params[] = $priority;
        $types .= 's';
}

if (!empty($status)) {
        $filterConditions[] = "t.status = ?";
        $params[] = $status;
        $types .= 's';
}

if (!empty($users_id)) {
        $filterConditions[] = "FIND_IN_SET(?, t.users_id)"; // Assuming t.users_id is a comma-separated list
        $params[] = $users_id;
        $types .= 's';
}

if (!empty($ticket_open_from)) {
        $filterConditions[] = "t.ticket_open >= ?";
        $params[] = $ticket_open_from;
        $types .= 's';
}

if (!empty($ticket_open_to)) {
        $filterConditions[] = "t.ticket_open <= ?";
        $params[] = $ticket_open_to;
        $types .= 's';
}

if (!empty($ticket_close_from)) {
        $filterConditions[] = "t.ticket_close >= ?";
        $params[] = $ticket_close_from;
        $types .= 's';
}

if (!empty($ticket_close_to)) {
        $filterConditions[] = "t.ticket_close <= ?";
        $params[] = $ticket_close_to;
        $types .= 's';
}

// Combine filter conditions if there are any
if (!empty($filterConditions)) {
        $sql .= " WHERE " . implode(' AND ', $filterConditions);
}

// Add GROUP BY and ORDER BY clauses
$sql .= " GROUP BY t.ticket_id ORDER BY t.ticket_id DESC";

// Prepare and execute the statement with parameters
$stmt = $conn->prepare($sql);
if ($stmt === false) {
        die('Error preparing statement: ' . $conn->error);
}

// Bind parameters if there are any
if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
}

// Execute statement
$stmt->execute();
$result = $stmt->get_result();

// Use $result to fetch and display data
// Example: while ($row = $result->fetch_assoc()) { ... }


if (!$result) {
        die("Error in query: " . $conn->error);
}

// Fetch data and output HTML for the table rows
$i = 1;
if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $i++ . "</td>";
                //condition for button edit and delete
                if ($canEditStation == 0 &  $canDeleteStation == 0) {
                        echo " <td style='display:none;'></td>";
                } else {
                        echo "<td>";
                        if ($row['ticket_close'] === null) {
                                // Edit button if user has permission
                                if ($canEditStation) {
                                        echo "<a href='edit_ticket.php?id=" . $row['id'] . "' class='btn btn-primary'><i class='fa-solid fa-pen-to-square'></i></a> ";
                                }
                        }
                        // Delete button if user has permission
                        if ($canDeleteStation) {
                                echo "<a href='delete_ticket.php?id=" . $row['id'] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this item?\");'><i class='fa-solid fa-trash'></i></a>";
                        }
                        echo "</td>";
                }
                echo "<td>" . $row['ticket_id'] . "</td>";
                echo "<td>" . $row['station_id'] . "</td>";
                echo "<td>" . $row['station_name'] . "</td>";
                echo "<td>" . $row['station_type'] . "</td>";
                echo "<td>" . $row['issue_description'] . "</td>";
                echo "<td><button class='btn text-primary link-underline-success' onclick='showImage(\"" . $row['issue_image'] . "\")'>click</button></td>";
                echo "<td>" . $row['issue_type'] . "</td>";
                echo "<td>" . $row['priority'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "<td>" . $row['users_name'] . "</td>";
                echo "<td>" . $row['ticket_open'] . "</td>";
                echo "<td>" . $row['ticket_close'] . "</td>";
                echo "<td>" . $row['comment'] . "</td>";
                echo "</tr>";
        }
} else {
        echo "<tr><td colspan='15' class='text-center'>No tickets found nun</td></tr>";
}

$stmt->close();
$conn->close();
