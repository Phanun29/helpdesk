<?php
include "include/header.php";
include "include/sidebar.php";
include "config.php"; // Assuming you have a config file to connect to the database

// Fetch existing data (assuming you have an ID or some identifier to fetch the record)
$rules_id = $_GET['id']; // or however you identify the record to edit
// Retrieve data from the database
$sql = "SELECT * FROM tbl_users_rules WHERE rules_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $rules_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $rules_name = $_POST['users_name'];
    $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];

    // Initialize permission statuses
    $add_user_status = in_array('add_user', $permissions) ? 1 : 0;
    $edit_user_status = in_array('edit_user', $permissions) ? 1 : 0;
    $delete_user_status = in_array('delete_user', $permissions) ? 1 : 0;
    $list_user_status = in_array('list_user', $permissions) ? 1 : 0;

    $add_ticket_status = in_array('add_ticket', $permissions) ? 1 : 0;
    $edit_ticket_status = in_array('edit_ticket', $permissions) ? 1 : 0;
    $delete_ticket_status = in_array('delete_ticket', $permissions) ? 1 : 0;
    $list_ticket_status = in_array('list_ticket', $permissions) ? 1 : 0;

    $add_station = in_array('add_station', $permissions) ? 1 : 0;
    $edit_station = in_array('edit_station', $permissions) ? 1 : 0;
    $delete_station = in_array('delete_station', $permissions) ? 1 : 0;
    $list_station = in_array('list_station', $permissions) ? 1 : 0;

    $add_user_rules = in_array('add_user_rules', $permissions) ? 1 : 0;
    $edit_user_rules = in_array('edit_user_rules', $permissions) ? 1 : 0;
    $delete_user_rules = in_array('delete_user_rules', $permissions) ? 1 : 0;
    $list_user_rules = in_array('list_user_rules', $permissions) ? 1 : 0;

    // Create a comma-separated list of permissions for menu_status


    // Sanitize inputs
    $rules_name = $conn->real_escape_string($rules_name);
  

    // Update in database
    $query = "UPDATE tbl_users_rules 
              SET rules_name = ?, add_user_status = ?, edit_user_status = ?, delete_user_status = ?, list_user_status = ?,
                  add_ticket_status = ?, edit_ticket_status = ?, delete_ticket_status = ?, list_ticket_status = ?,
                  add_station = ?, edit_station = ?, delete_station = ?, list_station = ?,
                  add_user_rules = ?, edit_user_rules = ?, delete_user_rules = ?, list_user_rules = ?
              WHERE rules_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "siiiiiiiiiiiiiiiss",
        $rules_name,
        $add_user_status,
        $edit_user_status,
        $delete_user_status,
        $list_user_status,
        $add_ticket_status,
        $edit_ticket_status,
        $delete_ticket_status,
        $list_ticket_status,
        $add_station,
        $edit_station,
        $delete_station,
        $list_station,
        $add_user_rules,
        $edit_user_rules,
        $delete_user_rules,
        $list_user_rules,
      
        $rules_id
    );

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Permission updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error: " . $stmt->error;
    }
    $stmt->close();

    // Redirect to the same page to avoid form resubmission on refresh
    // header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $rules_id);
    // exit();
}
?>

<div class="content-wrapper">
    <!-- Main content -->
    <div class="content-header">
        <div class="container-fluid ml-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Update Permission</h1>
                </div>
                  <!-- Display success/error messages -->
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
                    <a href="users_rules.php" class="btn btn-primary">BACK</a>
                </div>
            </div>
        </div>
    </div>
    <!-- /.Main content -->

    <!-- form update permission-->
    <section class="content">
        <div class="container-fluid">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user"></i> <b>Permission</b></h3>
                    </div>

                  

                    <form method="POST" action="">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="users_name">User Name <span class="text-danger">*</span></label>
                                        <input type="text" name="users_name" class="form-control" id="users_name" placeholder="Enter Name" value="<?php echo htmlspecialchars($row['rules_name']); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Permissions: Users -->
                            <div class="row">
                                <div class="col-sm-3 row">
                                    <div class="col-6">
                                        <label for="add_user">Add User</label>
                                    </div>
                                    <div class="col-6">
                                        <input type="checkbox" name="permissions[]" value="add_user" id="add_user" <?php echo $row['add_user_status'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                                <div class="col-sm-3 row">
                                    <div class="col-6">
                                        <label for="edit_user">Edit User</label>
                                    </div>
                                    <div class="col-6">
                                        <input type="checkbox" name="permissions[]" value="edit_user" id="edit_user" <?php echo $row['edit_user_status'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                                <div class="col-sm-3 row">
                                    <div class="col-6">
                                        <label for="delete_user">Delete User</label>
                                    </div>
                                    <div class="col-6">
                                        <input type="checkbox" name="permissions[]" value="delete_user" id="delete_user" <?php echo $row['delete_user_status'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                                <div class="col-sm-3 row">
                                    <div class="col-6">
                                        <label for="list_user">List User</label>
                                    </div>
                                    <div class="col-6">
                                        <input type="checkbox" name="permissions[]" value="list_user" id="list_user" <?php echo $row['list_user_status'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                            </div>

                            <!-- Permissions: Tickets -->
                            <div class="row">
                                <div class="col-sm-3 row">
                                    <div class="col-6">
                                        <label for="add_ticket">Add Ticket</label>
                                    </div>
                                    <div class="col-6">
                                        <input type="checkbox" name="permissions[]" value="add_ticket" id="add_ticket" <?php echo $row['add_ticket_status'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                                <div class="col-sm-3 row">
                                    <div class="col-6">
                                        <label for="edit_ticket">Edit Ticket</label>
                                    </div>
                                    <div class="col-6">
                                        <input type="checkbox" name="permissions[]" value="edit_ticket" id="edit_ticket" <?php echo $row['edit_ticket_status'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                                <div class="col-sm-3 row">
                                    <div class="col-6">
                                        <label for="delete_ticket">Delete Ticket</label>
                                    </div>
                                    <div class="col-6">
                                        <input type="checkbox" name="permissions[]" value="delete_ticket" id="delete_ticket" <?php echo $row['delete_ticket_status'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                                <div class="col-sm-3 row">
                                    <div class="col-6">
                                        <label for="list_ticket">List Ticket</label>
                                    </div>
                                    <div class="col-6">
                                        <input type="checkbox" name="permissions[]" value="list_ticket" id="list_ticket" <?php echo $row['list_ticket_status'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                            </div>

                            <!-- Permissions: Stations -->
                            <div class="row">
                                <div class="col-sm-3 row">
                                    <div class="col-6">
                                        <label for="add_station">Add Station</label>
                                    </div>
                                    <div class="col-6">
                                        <input type="checkbox" name="permissions[]" value="add_station" id="add_station" <?php echo $row['add_station'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                                <div class="col-sm-3 row">
                                    <div class="col-6">
                                        <label for="edit_station">Edit Station</label>
                                    </div>
                                    <div class="col-6">
                                        <input type="checkbox" name="permissions[]" value="edit_station" id="edit_station" <?php echo $row['edit_station'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                                <div class="col-sm-3 row">
                                    <div class="col-6">
                                        <label for="delete_station">Delete Station</label>
                                    </div>
                                    <div class="col-6">
                                        <input type="checkbox" name="permissions[]" value="delete_station" id="delete_station" <?php echo $row['delete_station'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                                <div class="col-sm-3 row">
                                    <div class="col-6">
                                        <label for="list_station">List Station</label>
                                    </div>
                                    <div class="col-6">
                                        <input type="checkbox" name="permissions[]" value="list_station" id="list_station" <?php echo $row['list_station'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                            </div>

                            <!-- Permissions: User Rules -->
                            <div class="row">
                                <div class="col-sm-3 row">
                                    <div class="col-6">
                                        <label for="add_user_rules">Add User Rules</label>
                                    </div>
                                    <div class="col-6">
                                        <input type="checkbox" name="permissions[]" value="add_user_rules" id="add_user_rules" <?php echo $row['add_user_rules'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                                <div class="col-sm-3 row">
                                    <div class="col-6">
                                        <label for="edit_user_rules">Edit User Rules</label>
                                    </div>
                                    <div class="col-6">
                                        <input type="checkbox" name="permissions[]" value="edit_user_rules" id="edit_user_rules" <?php echo $row['edit_user_rules'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                                <div class="col-sm-3 row">
                                    <div class="col-6">
                                        <label for="delete_user_rules">Delete User Rules</label>
                                    </div>
                                    <div class="col-6">
                                        <input type="checkbox" name="permissions[]" value="delete_user_rules" id="delete_user_rules" <?php echo $row['delete_user_rules'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                                <div class="col-sm-3 row">
                                    <div class="col-6">
                                        <label for="list_user_rules">List User Rules</label>
                                    </div>
                                    <div class="col-6">
                                        <input type="checkbox" name="permissions[]" value="list_user_rules" id="list_user_rules" <?php echo $row['list_user_rules'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" name="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- /.form update permission-->
</div>
<?php include "include/footer.php"; ?>