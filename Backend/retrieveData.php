<?php
// Include your database connection file (e.g., config.php)
include "config.php";
session_start();
$email = $_SESSION['email'];
$password = $_SESSION['password'];
if ($email != false && $password != false) {
    $sql = "SELECT * FROM tbl_users WHERE email = '$email'";
    $run_Sql = mysqli_query($conn, $sql);
    if ($run_Sql) {
        $fetch_info = mysqli_fetch_assoc($run_Sql);
    }
}
include "include/rules_ticket.php";
// Initialize variables for filter criteria
$station_id = isset($_POST['station_id']) ? $_POST['station_id'] : '';
$issue_type = isset($_POST['issue_type']) ? $_POST['issue_type'] : '';
$priority = isset($_POST['priority']) ? $_POST['priority'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : '';
$users_id = isset($_POST['users_id']) ? $_POST['users_id'] : '';
$ticket_open_from = isset($_POST['ticket_open_from']) ? $_POST['ticket_open_from'] : '';
$ticket_open_to = isset($_POST['ticket_open_to']) ? $_POST['ticket_open_to'] : '';
$ticket_close_from = isset($_POST['ticket_close_from']) ? $_POST['ticket_close_from'] : '';
$ticket_close_to = isset($_POST['ticket_close_to']) ? $_POST['ticket_close_to'] : '';

// Construct the SQL query with filters
$query = "SELECT t.*, u.users_name
          FROM tbl_ticket t
          LEFT JOIN tbl_users u ON FIND_IN_SET(u.users_id, t.users_id)
          WHERE 1=1";

if (!empty($station_id)) {
    $query .= " AND t.station_id = '" . $conn->real_escape_string($station_id) . "'";
}
if (!empty($issue_type)) {
    $query .= " AND t.issue_type LIKE '%" . $conn->real_escape_string($issue_type) . "%'";
}
if (!empty($priority)) {
    $query .= " AND t.priority = '" . $conn->real_escape_string($priority) . "'";
}
if (!empty($status)) {
    $query .= " AND t.status = '" . $conn->real_escape_string($status) . "'";
}
if (!empty($users_id)) {
    $query .= " AND FIND_IN_SET('" . $conn->real_escape_string($users_id) . "', t.users_id)";
}
if (!empty($ticket_open_from)) {
    $query .= " AND t.ticket_open >= '" . $conn->real_escape_string($ticket_open_from) . "'";
}
if (!empty($ticket_open_to)) {
    $query .= " AND t.ticket_open <= '" . $conn->real_escape_string($ticket_open_to) . "'";
}
if (!empty($ticket_close_from)) {
    $query .= " AND t.ticket_close >= '" . $conn->real_escape_string($ticket_close_from) . "'";
}
if (!empty($ticket_close_to)) {
    $query .= " AND t.ticket_close <= '" . $conn->real_escape_string($ticket_close_to) . "'";
}

$query .= " ORDER BY t.ticket_id DESC"; // Example order by ticket_id, adjust as per your requirement

// Execute the query

$result = $conn->query($query);
// Prepare the HTML response
$i =  1;
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
        if ($issue_type == null) {
            echo "<td>" . $row['issue_type'] . "</td>";
        } else {
            echo "<td>" . $issue_type . "</td>";
        }
        echo "<td>" . $row['priority'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "<td>" . $row['users_name'] . "</td>";
        echo "<td>" . $row['ticket_open'] . "</td>";
        echo "<td>" . $row['ticket_close'] . "</td>";
        echo "<td>" . $row['comment'] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='15' class='text-center'>No tickets found</td></tr>";
}

// Close database connection
$conn->close();
