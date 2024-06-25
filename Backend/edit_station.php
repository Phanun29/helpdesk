<?php
require('include/header.php');
require('include/sidebar.php');
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $id = $_POST['id']; // The primary key of the station
    $new_station_id = $_POST['station_id'];
    $station_name = $_POST['station_name'];
    $station_type = $_POST['station_type'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Retrieve the current station_id
        $sql = "SELECT station_id FROM tbl_station WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            throw new Exception("Station not found.");
        }
        $station = $result->fetch_assoc();
        $old_station_id = $station['station_id'];
        $stmt->close();

        // Temporarily disable foreign key checks
        $conn->query("SET foreign_key_checks = 0");

        // Update the station_id in tbl_ticket
        $sql = "UPDATE tbl_ticket SET station_id = ? WHERE station_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_station_id, $old_station_id);

        if (!$stmt->execute()) {
            throw new Exception("Error updating tickets: " . $stmt->error);
        }
        $stmt->close();

        // Update the station in tbl_station
        $sql = "UPDATE tbl_station SET station_id = ?, station_name = ?, station_type = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $new_station_id, $station_name, $station_type, $id);

        if (!$stmt->execute()) {
            throw new Exception("Error updating station: " . $stmt->error);
        }

        // Re-enable foreign key checks
        $conn->query("SET foreign_key_checks = 1");

        // Commit the transaction
        $conn->commit();

        // Redirect back to the main page after successful update
        // header("Location: station.php");
        // exit();
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        echo "Failed: " . $e->getMessage();
    }

    $stmt->close();
    $conn->close();
} else {
    // Retrieve the station details for editing
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "SELECT * FROM tbl_station WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            echo "Station not found.";
            exit();
        }
        $station = $result->fetch_assoc();
        $stmt->close();
    } else {
        // Redirect back to the station list page if no station ID is provided
        // header("Location: station.php");
        // exit();
    }
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Station</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="" method="POST">
                            <input type="hidden" name="id" value="<?php echo isset($station['id']) ? $station['id'] : ''; ?>">
                            <div class="form-group">
                                <label for="station_id">Station ID</label>
                                <input type="text" class="form-control" id="station_id" name="station_id" value="<?php echo isset($station['station_id']) ? $station['station_id'] : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="station_name">Station Name</label>
                                <input type="text" class="form-control" id="station_name" name="station_name" value="<?php echo isset($station['station_name']) ? $station['station_name'] : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="station_type">Station Type</label>
                                <select name="station_type" class="form-control select2bs4" style="width: 100%;" required>
                                    <option value="CoCo" <?php echo (isset($station['station_type']) && $station['station_type'] == 'CoCo') ? 'selected' : ''; ?>>CoCo</option>
                                    <option value="DoDo" <?php echo (isset($station['station_type']) && $station['station_type'] == 'DoDo') ? 'selected' : ''; ?>>DoDo</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require('include/footer.php'); ?>