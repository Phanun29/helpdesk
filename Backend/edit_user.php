<?php
include "include/header.php";
include "include/sidebar.php";
include "config.php"; // Assuming you have a config file to connect to the database

// Check if form is submitted for update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_GET['id']; // Assuming you're passing the user's ID through a GET parameter

    // Retrieve form data
    $users_name = $_POST['users_name'];
    $email = $_POST['email'];
    $status = $_POST['status'];
    $rules_id = $_POST['rules_id'];
    $company = $_POST['company'];

    // Update user query
    $update_query = "UPDATE tbl_users SET users_name = '$users_name', email = '$email', status = '$status', rules_id = '$rules_id' ,company ='$company' WHERE users_id = $user_id";

    if ($conn->query($update_query) === TRUE) {
        $_SESSION['success_message'] = "User details updated successfully.";
    } else {
        $_SESSION['error_message'] = "Error updating user details: " . $conn->error;
    }

    // Redirect back to users.php
    // header("Location: users.php");
    // exit();
}

// Fetch user data based on user ID
$user_id = $_GET['id']; // Assuming you're passing the user's ID through a GET parameter
$user_query = "SELECT * FROM tbl_users WHERE users_id = $user_id";
$user_result = $conn->query($user_query);
$row = $user_result->fetch_assoc();
?>

<div class="content-wrapper">
    <!-- Main content -->
    <div class="content-header">
        <div class="container-fluid ml-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit User <?php //echo $row['users_name']; 
                                                ?></h1>
                </div>
            </div>
        </div>
    </div>
    <!-- button back -->
    <div class="content-header row">
        <div class="container-fluid ml-2 col-sm-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <a href="users.php" class="btn btn-primary">BACK</a>
                </div>
            </div>
        </div>
        <!-- Display success/error messages -->
        <?php if (isset($_SESSION['success_message'])) : ?>
            <div class="alert alert-success alert-dismissible fade show col-sm-4 mr-4" role="alert">
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

    <!-- /.Main content -->

    <!-- form update user-->
    <section class="content">
        <div class="container-fluid">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user"></i> <b>User</b></h3>
                    </div>



                    <form method="POST" action="">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="users_name">User Name</label>
                                        <input type="text" name="users_name" class="form-control" id="users_name" placeholder="Enter Name" value="<?php echo $row['users_name'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="email">Email address</label>
                                        <input type="email" name="email" class="form-control" id="email" placeholder="Enter email" value="<?php echo $row['email'] ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" class="form-control select2bs4" style="width: 100%;" required>
                                            <option value="1" <?php echo ($row['status'] == '1') ? 'selected' : ''; ?>>Active</option>
                                            <option value="0" <?php echo ($row['status'] == '0') ? 'selected' : ''; ?>>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="rules_id">Permission</label>
                                        <select id="rules_id" name="rules_id" class="form-control">
                                            <?php
                                            $rules_query = "SELECT rules_id, rules_name FROM tbl_users_rules";
                                            $rules_result = $conn->query($rules_query);
                                            if ($rules_result->num_rows > 0) {
                                                while ($rule_row = $rules_result->fetch_assoc()) {
                                                    // Check if this option should be selected
                                                    $selected = ($rule_row['rules_id'] == $row['rules_id']) ? 'selected' : '';
                                                    echo "<option value='" . $rule_row['rules_id'] . "' $selected>" . $rule_row['rules_name'] . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>

                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="company">Company</label>

                                        <select class="form-control" name="company" id="company">

                                            <option value="PTTCL" <?php echo $row['company'] == 'PTTCL' ? 'selected' : ''; ?>>PTTCL</option>
                                            <option value="PTTDigital" <?php echo $row['company']  == 'PTTDigital' ? 'selected' : ''; ?>>PTTDigital</option>

                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- /.form update user-->
</div>

<?php include "include/footer.php"; ?>