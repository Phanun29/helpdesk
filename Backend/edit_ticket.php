<?php
include "config.php";
require('include/header.php');
require('include/sidebar.php');


$ticket_id = $_GET['id']; // Assuming you're passing the ticket ID through a GET parameter
// Function to check if the user has permission to add station

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Gather form data
    $station_id = $_POST['station_id'];
    $station_name = $_POST['station_name'];
    $station_type = $_POST['station_type'];
    $issue_description = $_POST['issue_description'];
    $issue_image = $_FILES['issue_image']['name']; // Assuming you handle file upload separately
    $issue_types = implode(', ', $_POST['issue_type']);
    $priority = $_POST['priority'];
    $status = $_POST['status'];
    $users_id = implode(',', $_POST['users_id']);
    $comment = $_POST['comment'];

    date_default_timezone_set('Asia/Bangkok');


    // if ($status == 'on_hold') {
    //     $ticket_on_hold = date('Y-m-d H:i:s');
    // } else {
    //     $ticket_on_hold = NULL;
    // }
    // if ($status == 'in_progress') {
    //     $ticket_in_progress = date('Y-m-d H:i:s');
    // } else {
    //     $ticket_in_progress = NULL;
    // }
    // if ($status == 'pending_vender') {
    //     $ticket_pending_vender = date('Y-m-d H:i:s');
    // } else {
    //     $ticket_pending_vender = Null;
    // }
    // Check if the status is 'close' and set ticket_close
    if ($status == 'close') {
        $ticket_close = date('Y-m-d H:i:s');
    } else {
        $ticket_close = NULL;
    }
    // Prepare the update query
    $update_query = "UPDATE tbl_ticket SET 
                        station_id = ?, 
                        station_name = ?, 
                        station_type = ?, 
                        issue_description = ?, 
                        issue_image = ?, 
                        issue_type = ?, 
                        priority = ?, 
                        status = ?, 
                        users_id = ?, 
                        comment = ?, 
                        ticket_on_hold=?,
                        ticket_in_progress=?,
                        ticket_pending_vender=?,
                        ticket_close = ?
                        WHERE id = ?";

    // Prepare the statement
    if ($stmt = $conn->prepare($update_query)) {
        // Bind the parameters
        $stmt->bind_param(
            'ssssssssssssssi',
            $station_id,
            $station_name,
            $station_type,
            $issue_description,
            $issue_image,
            $issue_types,
            $priority,
            $status,
            $users_id,
            $comment,
            $ticket_on_hold,
            $ticket_in_progress,
            $ticket_pending_vender,
            $ticket_close,
            $ticket_id
        );

        // Perform the update
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Ticket updated successfully";
        } else {
            $_SESSION['error_message'] = "Error updating ticket: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Error preparing statement: " . $conn->error;
    }

    // // Redirect to the page or do any additional handling after update
    // header("Location: edit_ticket.php?id=$ticket_id");
    // exit();
}

// Fetch ticket details
$ticket_query = "SELECT * FROM tbl_ticket WHERE id = ?";
if ($stmt = $conn->prepare($ticket_query)) {
    $stmt->bind_param('i', $ticket_id);
    $stmt->execute();
    $ticket_result = $stmt->get_result();
    $row = $ticket_result->fetch_assoc();
    $stmt->close();
} else {
    $_SESSION['error_message'] = "Error preparing statement: " . $conn->error;
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
                </div><!-- /.col -->
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
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- button back -->
    <div class="content-header">
        <div class="container-fluid ml-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <a href="ticket.php" class="btn btn-primary">BACK</a>
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
                            <h3 class="card-title">Ticket ID: <?= $row['ticket_id']; ?></h3>
                        </div>
                        <form method="POST" id="quickForm" novalidate="novalidate" enctype="multipart/form-data">
                            <div class="card-body col">
                                <div class="row">
                                    <div class="form-group col-sm-4 ">
                                        <label for="station_input">Station ID <span class="text-danger">*</span></label>
                                        <input value="<?php echo $row['station_id'] ?>" class="form-control" type="text" name="station_id" id="station_id" autocomplete="off" onkeyup="showSuggestions(this.value)" raedonly>
                                        <div id="suggestion_dropdown" class="dropdown-content"></div>
                                    </div>


                                    <div class="form-group col-sm-4">
                                        <label for="station_name">Station Name</label>
                                        <input value="<?php echo $row['station_name'] ?>" type="text" name="station_name" class="form-control" id="station_name" placeholder="Station Name" readonly>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label for="station_type">Station Type</label>
                                        <input value="<?php echo $row['station_type'] ?>" type="text" name="station_type" class="form-control" id="station_type" placeholder="Station Type" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-8">
                                        <label for="issue_description">Issue Description</label>
                                        <textarea id="issue_description" name="issue_description" class="form-control" rows="3" placeholder="Issue Description"><?php echo htmlspecialchars($row['issue_description']); ?></textarea>
                                    </div>

                                    <div class="form-group col-sm-4">
                                        <label for="issue_image">Issue Image</label>
                                        <div class="input-group col-12">
                                            <div class="custom-image">
                                                <input type="file" id="issue_image" name="issue_image" class="form-control">

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
                                            $selected_issue_types = explode(', ', $row['issue_type']);
                                            foreach ($issue_types as $issue_type) {
                                                $selected = in_array(trim($issue_type), $selected_issue_types) ? 'selected' : '';
                                                echo "<option value=\"$issue_type\" $selected>$issue_type</option>";
                                            }
                                            ?>
                                        </select>

                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label for="priority">SLA Category</label>
                                        <select name="priority" id="priority" class="form-control select2bs4" style="width: 100%;">
                                            <option value="CAT Hardware" <?php echo ($row['priority'] == 'CAT Hardware') ? 'selected' : ''; ?>>CAT Hardware</option>
                                            <option value="CAT 1*" <?php echo ($row['priority'] == 'CAT 1*') ? 'selected' : ''; ?>>CAT 1*</option>
                                            <option value="CAT 2*" <?php echo ($row['priority'] == 'CAT 2*') ? 'selected' : ''; ?>>CAT 2*</option>
                                            <option value="CAT 3*" <?php echo ($row['priority'] == 'CAT 3*') ? 'selected' : ''; ?>>CAT 3*</option>
                                            <option value="CAT 4*" <?php echo ($row['priority'] == 'CAT 4*') ? 'selected' : ''; ?>>CAT 4*</option>
                                            <option value="CAT 4 Report*" <?php echo ($row['priority'] == 'CAT 4 Report*') ? 'selected' : ''; ?>>CAT 4 Report*</option>
                                            <option value="CAT 5*" <?php echo ($row['priority'] == 'CAT 5*') ? 'selected' : ''; ?>>CAT 5*</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control select2bs4" style="width: 100%;">
                                            <!-- <option value="open" <?php echo ($row['status'] == 'open') ? 'selected' : ''; ?>>Open</option> -->
                                            <option value="On Hold" <?php echo ($row['status'] == 'On Hold') ? 'selected' : ''; ?>>On Hold</option>
                                            <option value="In Progress" <?php echo ($row['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                                            <option value="Pending Vendor" <?php echo ($row['status'] == 'Pending Vendor') ? 'selected' : ''; ?>>Pending Vendor</option>
                                            <option value="Close" <?php echo ($row['status'] == 'Close') ? 'selected' : ''; ?>>Close</option>
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
                                            $assigned_users = explode(',', $row['users_id']);
                                            if ($user_result->num_rows > 0) {
                                                while ($user_row = $user_result->fetch_assoc()) {
                                                    $selected = in_array($user_row['users_id'], $assigned_users) ? 'selected' : '';
                                                    echo "<option value=\"" . $user_row['users_id'] . "\" $selected>" . $user_row['users_name'] . "</option>";
                                                }
                                            } else {
                                                echo "<option value=\"\">No active users found</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-8">
                                        <label for="comment">Comment</label>
                                        <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="Comment"><?php echo htmlspecialchars($row['comment']); ?></textarea>
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

    $(document).ready(function() {
        // Fetch station details on blur (when input loses focus)
        $('#station_id').blur(function() {
            var station_id = $(this).val();
            fetchStationDetails(station_id);
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

<!-- <script>
    $(document).ready(function() {
        $('#station_id').blur(function() {
            var station_id = $(this).val();
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
                        //  alert("Station ID not found.");
                        $('#station_name').val('');
                        $('#station_type').val('');
                    }
                }
            });
        });
    });
</script> -->
<!-- selecte multiple -->
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>

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
<!-- suggestion -->
<!-- <script>
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
</script> -->


<?php
require('include/footer.php');
?>
<script>
    function closeAlert(button) {
        var alert = button.closest('.alert');
        alert.style.display = 'none';
    }
</script>