<?php
require('include/header.php');
require('include/sidebar.php');
include "config.php";

$ticket_id = $_GET['id']; // Assuming you're passing the ticket ID through a GET parameter
$ticket_query = "SELECT * FROM tbl_ticket WHERE id = ?";
$stmt = $conn->prepare($ticket_query);
$stmt->bind_param("s", $ticket_id);
$stmt->execute();
$ticket_result = $stmt->get_result();
$ticket_row = $ticket_result->fetch_assoc();
$stmt->close();

if (!$ticket_row) {
    die("Ticket not found. Please check the ticket ID and try again.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $station_id = $_POST['station_id'];
    $issue_description = $_POST['issue_description'];
    $issue_type = implode(', ', $_POST['issue_type']);
    $priority = $_POST['priority'];
    $status = $_POST['status'];
    $comment = $_POST['comment'];
    $users_id = isset($_POST['users_id']) ? implode(',', array_map('trim', $_POST['users_id'])) : '';
    $ticket_close = NULL;

    // Check if the status is 'close' and set ticket_close
    if ($status == 'close') {
        $ticket_close = date('Y-m-d H:i:s');
    }

    // Process file upload if a new image is uploaded
    $issue_image_paths = $ticket_row['issue_image'];
    if (!empty($_FILES['issue_image']['name'][0])) {
        $uploaded_images = [];
        $target_dir = "uploads/";
        foreach ($_FILES['issue_image']['name'] as $key => $image) {
            $target_file = $target_dir . basename($image);
            if (move_uploaded_file($_FILES["issue_image"]["tmp_name"][$key], $target_file)) {
                $uploaded_images[] = $target_file;
            } else {
                $_SESSION['error_message'] = "Error uploading image: " . $image;
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit();
            }
        }
        $issue_image_paths = implode(',', $uploaded_images);
    }

    $update_query = "UPDATE tbl_ticket SET 
                    station_id = ?, 
                    issue_description = ?, 
                    issue_image = ?, 
                    issue_type = ?, 
                    priority = ?, 
                    status = ?, 
                    comment = ?, 
                    users_id = ?, 
                    ticket_close = ? 
                    WHERE id = ?";

    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssssssssss", $station_id, $issue_description, $issue_image_paths, $issue_type, $priority, $status, $comment, $users_id, $ticket_close, $ticket_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['success_message'] = "Ticket updated successfully";
        } else {
            $_SESSION['error_message'] = "No changes made.";
        }
    } else {
        $_SESSION['error_message'] = "Error: " . $stmt->error;
    }
    $stmt->close();

    // header('Location: ' . $_SERVER['REQUEST_URI']);
    // exit();
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h1 class="m-0">Update Ticket</h1>
                </div>
                <?php if (isset($_SESSION['success_message'])) : ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong><?php echo $_SESSION['success_message']; ?></strong>
                        <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert(this)"></button>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['error_message'])) : ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><?php echo $_SESSION['error_message']; ?></strong>
                        <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert(this)"></button>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- button back -->
    <div class="content-header">
        <div class="container-fluid ml-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <a href="users.php" class="btn btn-primary">BACK</a>
                </div>
            </div>
        </div>
    </div>
    <!-- dashboard -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Ticket ID: <?= htmlspecialchars($ticket_row['ticket_id']); ?></h3>
                        </div>
                        <form method="POST" id="quickForm" enctype="multipart/form-data">
                            <div class="card-body col">
                                <div class="row">
                                    <div class="form-group col-sm-4">
                                        <label for="station_input">Station ID <span class="text-danger">*</span></label>
                                        <input value="<?= htmlspecialchars($ticket_row['station_id']); ?>" class="form-control" type="text" name="station_id" id="station_id" autocomplete="off" onkeyup="showSuggestions(this.value)" readonly>
                                        <div id="suggestion_dropdown" class="dropdown-content"></div>
                                    </div>

                                    <div class="form-group col-sm-4">
                                        <label for="station_name">Station Name</label>
                                        <input value="<?= htmlspecialchars($ticket_row['station_name']); ?>" type="text" name="station_name" class="form-control" id="station_name" placeholder="Station Name" readonly>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label for="station_type">Station Type</label>
                                        <input value="<?= htmlspecialchars($ticket_row['station_type']); ?>" type="text" name="station_type" class="form-control" id="station_type" placeholder="Station Type" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-8">
                                        <label for="issue_description">Issue Description</label>
                                        <textarea id="issue_description" name="issue_description" class="form-control" rows="3" placeholder="Issue Description"><?= htmlspecialchars($ticket_row['issue_description']); ?></textarea>
                                    </div>

                                    <div class="form-group col-sm-4">
                                        <label for="issue_image">Issue Image</label>
                                        <div class="input-group col-12">
                                            <div class="custom-image">
                                                <input type="file" id="issue_image" name="issue_image[]" class="form-control" multiple>
                                                <?php if (!empty($ticket_row['issue_image'])) : ?>
                                                    <div class="mt-2">
                                                        <?php
                                                        $images = explode(',', $ticket_row['issue_image']);
                                                        foreach ($images as $image) {
                                                            echo '<img src="' . htmlspecialchars($image) . '" alt="Issue Image" style="max-width: 100px; margin-right: 10px;">';
                                                        }
                                                        ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-4">
                                        <label for="issue_type">Issue Type</label>
                                        <select name="issue_type[]" class="form-control" id="choices-multiple-remove-button" placeholder="Select up to 2 tags" multiple>
                                            <?php
                                            $issue_types = ['Hardware', 'Software', 'Network', 'Dispensor', 'Unassigned'];
                                            $selected_issue_types = explode(', ', $ticket_row['issue_type']);
                                            foreach ($issue_types as $issue_type) {
                                                $selected = in_array(trim($issue_type), $selected_issue_types) ? 'selected' : '';
                                                echo "<option value=\"$issue_type\" $selected>$issue_type</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label for="priority">Priority</label>
                                        <select name="priority" id="priority" class="form-control select2bs4" style="width: 100%;">
                                            <option value="CAT Hardware" <?= ($ticket_row['priority'] == 'CAT Hardware') ? 'selected' : ''; ?>>CAT Hardware</option>
                                            <option value="CAT 1*" <?= ($ticket_row['priority'] == 'CAT 1*') ? 'selected' : ''; ?>>CAT 1*</option>
                                            <option value="CAT 2*" <?= ($ticket_row['priority'] == 'CAT 2*') ? 'selected' : ''; ?>>CAT 2*</option>
                                            <option value="CAT 3*" <?= ($ticket_row['priority'] == 'CAT 3*') ? 'selected' : ''; ?>>CAT 3*</option>
                                            <option value="CAT 4*" <?= ($ticket_row['priority'] == 'CAT 4*') ? 'selected' : ''; ?>>CAT 4*</option>
                                            <option value="CAT 4 Report*" <?= ($ticket_row['priority'] == 'CAT 4 Report*') ? 'selected' : ''; ?>>CAT 4 Report*</option>
                                            <option value="CAT 5*" <?= ($ticket_row['priority'] == 'CAT 5*') ? 'selected' : ''; ?>>CAT 5*</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control select2bs4" style="width: 100%;">
                                            <option value="on_hold" <?= ($ticket_row['status'] == 'on_hold') ? 'selected' : ''; ?>>On Hold</option>
                                            <option value="in_progress" <?= ($ticket_row['status'] == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                            <option value="pending_vendor" <?= ($ticket_row['status'] == 'pending_vendor') ? 'selected' : ''; ?>>Pending Vendor</option>
                                            <option value="close" <?= ($ticket_row['status'] == 'close') ? 'selected' : ''; ?>>Closed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-4">
                                        <label for="users_id">Assign</label>
                                        <select name="users_id[]" class="form-control" id="choices-multiple-remove-button" placeholder="Select up to 2 tags" multiple>
                                            <?php
                                            $user_query = "SELECT users_id, users_name FROM tbl_users WHERE status = 1";
                                            $user_result = $conn->query($user_query);
                                            $assigned_users = explode(',', $ticket_row['users_id']);
                                            if ($user_result->num_rows > 0) {
                                                while ($user_row = $user_result->fetch_assoc()) {
                                                    $selected = in_array($user_row['users_id'], $assigned_users) ? 'selected' : '';
                                                    echo "<option value=\"" . htmlspecialchars($user_row['users_id']) . "\" $selected>" . htmlspecialchars($user_row['users_name']) . "</option>";
                                                }
                                            } else {
                                                echo "<option value=\"\">No active users found</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-8">
                                        <label for="comment">Comment</label>
                                        <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="Comment"><?= htmlspecialchars($ticket_row['comment']); ?></textarea>
                                    </div>
                                </div>
                                <div class="">
                                    <button type="submit" name="Submit" value="Submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- auto fill station -->
<script>
    $(document).ready(function() {
        $('#station_id').blur(function() {
            var station_id = $(this).val();
            fetchStationDetails(station_id);
        });

        $('#quickForm').on('submit', function(event) {
            if (!this.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            $(this).addClass('was-validated');
        });
    });

    // Function to fetch station details using AJAX
    function fetchStationDetails(station_id) {
        $.ajax({
            url: 'get_station_details.php',
            type: 'POST',
            data: {
                station_id: station_id
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#station_name').val(response.station_name);
                    $('#station_type').val(response.station_type);
                } else {
                    $('#station_name').val('');
                    $('#station_type').val('');
                }
            }
        });
    }

    // Function to show suggestions
    function showSuggestions(str) {
        if (str == "") {
            document.getElementById("suggestion_dropdown").innerHTML = "";
            return;
        } else {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("suggestion_dropdown").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "get_suggestions.php?q=" + str, true);
            xmlhttp.send();
        }
    }

    // Function to select a suggestion
    function selectSuggestion(station_id) {
        document.getElementById("station_id").value = station_id;
        document.getElementById("suggestion_dropdown").innerHTML = "";
        // Trigger blur event to fetch details
        $('#station_id').blur();
    }
</script>
<!-- ticket close -->
<script>
    $(document).ready(function() {
        $('#status').change(function() {
            var status = $(this).val();
            if (status === 'close') {
                var currentDateTime = new Date().toISOString().slice(0, 19).replace('T', ' ');
                $('#ticket_close').val(currentDateTime);
                $('#ticket_close_group').show();
            } else {
                $('#ticket_close').val('');
                $('#ticket_close_group').hide();
            }
        });

        // Trigger change event to set the correct visibility on page load
        $('#status').trigger('change');
    });
</script>

<script>
    $(document).ready(function() {

        var multipleCancelButton = new Choices('#choices-multiple-remove-button', {
            removeItemButton: true,
            maxItemCount: 5,
            searchResultLimit: 3,
            renderChoiceLimit: 3
        });
    });
</script>
<?php
require('include/footer.php');
?>

<script>
    function closeAlert(button) {
        var alert = button.closest('.alert');
        alert.style.display = 'none';
    }
</script>