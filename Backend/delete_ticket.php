<?php
include "config.php";

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare the SQL statement to retrieve the issue_image path
    $query = "SELECT issue_image FROM tbl_ticket WHERE id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($issue_image_path);
        $stmt->fetch();
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
        exit();
    }

    // Prepare the SQL statement to delete the ticket
    $query = "DELETE FROM tbl_ticket WHERE id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            // If the deletion is successful, delete the image file
            if (!empty($issue_image_path) && file_exists($issue_image_path)) {
                unlink($issue_image_path);
            }
            // Redirect to the ticket page or the desired page
            header("Location: ticket.php");
            exit();
        } else {
            echo "Error deleting record: " . $conn->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    echo "No id parameter provided.";
}

// Close the database connection
$conn->close();
