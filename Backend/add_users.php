<?php
include "include/header.php";
include "include/sidebar.php";
include "config.php"; // Assuming you have a config file to connect to the database

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $users_name = $_POST['users_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $rules_id = $_POST['rules_id'];
    $company = $_POST['company'];
    $status = $_POST['status'];


    // Validate inputs
    if (empty($users_name) || empty($email) || empty($password)) {
        $_SESSION['error_message'] = "All fields are required.";
    } else {
        // Check if email already exists
        $check_email_query = "SELECT * FROM tbl_users WHERE email = ?";
        $stmt = $conn->prepare($check_email_query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['error_message'] = "Email already exists.";
        } else {
            // Insert new user into the database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_query = "INSERT INTO tbl_users (users_name, email, password, rules_id,company, status) VALUES (?, ?, ?, ?, ?,?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("sssiss", $users_name, $email, $hashed_password, $rules_id, $company, $status);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "User added successfully.";
            } else {
                $_SESSION['error_message'] = "Error adding user: " . $conn->error;
            }
        }
    }

    // Redirect to the same page to display messages
    // header('Location: ' . $_SERVER['REQUEST_URI']);
    // exit();
}
?>


<div class="content-wrapper">
    <!-- Main content -->
    <div class="content-header">
        <div class="container-fluid ml-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Add New Users</h1>
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
                    <a href="users.php" class="btn btn-primary">BACK</a>
                </div>

            </div>
        </div>
    </div>
    <!-- /.Main content -->

    <!-- form create user-->
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
                                        <label for="users_name">User Name <span class="text-danger">*</span></label>
                                        <input type="text" name="users_name" class="form-control" id="users_name" placeholder="Enter Name" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="email">Email address <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control" id="email" placeholder="Enter email" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="password">Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="company">Company <span class="text-danger">*</span></label>

                                        <select class="form-control" name="company" id="company" required>
                                            <option value="">select</option>
                                            <option value="PTTCL">PTTCL</option>
                                            <option value="PTTDigital">PTTDigital</option>

                                        </select>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-control select2bs4" style="width: 100%;" required>
                                            <option value="">select</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="rules_id">Permission <span class="text-danger">*</span></label>
                                        <select id="rules_id" name="rules_id" class="form-control" required>
                                            <?php
                                            $rules_query = "SELECT rules_id, rules_name FROM tbl_users_rules";
                                            $rules_result = $conn->query($rules_query);
                                            if ($rules_result->num_rows > 0) {
                                                while ($row = $rules_result->fetch_assoc()) {
                                                    echo "<option value='" . $row['rules_id'] . "'>" . $row['rules_name'] . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- /.form create user-->
</div>


<?php include "include/footer.php"; ?>