<?php
include "include/header.php";
include "include/sidebar.php";

// Function to check if the user has permission to add station
function AddUserRules($rules_id, $conn)
{
    $query = "SELECT add_user_rules FROM tbl_users_rules WHERE rules_id = $rules_id";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['add_user_rules'] == 1; // Check if add_status is set to 1 (allowed)
    }
    return false; // Default to false if no permission found
}

// Function to check if the user has permission to edit station
function EditUserRules($rules_id, $conn)
{
    $query = "SELECT edit_user_rules FROM tbl_users_rules WHERE rules_id = $rules_id";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['edit_user_rules'] == 1; // Check if edit_status is set to 1 (allowed)
    }
    return false; // Default to false if no permission found
}

// Function to check if the user has permission to delete station
function DeleteUserRules($rules_id, $conn)
{
    $query = "SELECT delete_user_rules FROM tbl_users_rules WHERE rules_id = $rules_id";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['delete_user_rules'] == 1; // Check if delete_status is set to 1 (allowed)
    }
    return false; // Default to false if no permission found
}

// Assume $user_id is fetched from session or database
$user_id = $fetch_info['users_id']; // Example user ID

// Fetch user details including rules_id
$query_user = "SELECT * FROM tbl_users WHERE users_id = $user_id";
$result_user = $conn->query($query_user);
if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
    $rules_id = $user['rules_id'];

    // Check if user has permission to add, edit, or delete stations
    $AddUserRules = AddUserRules($rules_id, $conn);
    $EditUserRules = EditUserRules($rules_id, $conn);
    $DeleteUserRules = DeleteUserRules($rules_id, $conn);
} else {
    // Handle error if user not found or permission check fails
    $_SESSION['error_message'] = "User not found or permission check failed.";
    // header("Location: users_rules.php"); // Redirect to appropriate page
    // exit;

}
// Default values for pagination
$records_per_page = isset($_GET['length']) ? intval($_GET['length']) : 10;
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($current_page - 1) * $records_per_page;
// Modify the query to join tbl_users with tbl_rules
$user_query = "SELECT* FROM tbl_users_rules order by  LIMIT  desc $offset,$records_per_page  ";
$user_query = "SELECT * FROM tbl_users_rules ORDER BY rules_id DESC LIMIT $offset, $records_per_page";



$user_result = $conn->query($user_query);
$total_query = "SELECT COUNT(*) as total FROM tbl_users_rules ";
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $records_per_page);
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><b>User Rules</b></h1>
                </div><!-- /.col -->
                <?php
                // session_start(); // Start the session at the beginning of your file

                if (isset($_SESSION['success_message'])) {
                    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
            <strong>{$_SESSION['success_message']}</strong>
            <button type='button' class='btn-close' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'></button>
          </div>";
                    unset($_SESSION['success_message']); // Clear the message after displaying
                }

                if (isset($_SESSION['error_message'])) {
                    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
            <strong>{$_SESSION['error_message']}</strong>
            <button type='button' class='btn-close' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'></button>
          </div>";
                    unset($_SESSION['error_message']); // Clear the message after displaying
                }
                ?>

            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- dashboard -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <!-- <div class="card-header">
                    
                </div> -->
                <div class="card">
                    <div class="card-header">
                        <?php if (isset($AddUserRules) && $DeleteUserRules) : ?>
                            <a href="add_permission.php" class="btn btn-primary m"><i class="fas fa-plus"></i> <b>Create New User Rules</b></a>
                        <?php endif; ?>

                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" id="table_search" style="height: 45px;" class="form-control float-right" placeholder="Search">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- row page -->
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <div class="dataTables_length" id="dataTable_length">
                                    show
                                    <label>
                                        <select id="entriesPerPage" aria-controls="dataTable" class="custom-select custom-select-sm form-control form-control-sm">
                                            <option value="10" <?= ($records_per_page == 10) ? 'selected' : '' ?>>10</option>
                                            <option value="25" <?= ($records_per_page == 25) ? 'selected' : '' ?>>25</option>
                                            <option value="50" <?= ($records_per_page == 50) ? 'selected' : '' ?>>50</option>
                                            <option value="100" <?= ($records_per_page == 100) ? 'selected' : '' ?>>100</option>
                                        </select>
                                    </label>
                                    entries
                                </div>
                            </div>

                        </div>

                    </div>
                    <!-- /row page -->
                    <div class="card-body table-responsive p-0">
                        <table id="myTable" class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Rules Name</th>
                                 


                                    <?php if ($EditUserRules || $DeleteUserRules) : ?>
                                        <th>Option</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $user_result = $conn->query($user_query);
                                $i = $offset + 1;
                                if ($user_result->num_rows > 0) {
                                    while ($row = $user_result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $i++ . "</td>";
                                        echo "<td>" . $row['rules_name'] . "</td>";
                                      
                                        if ($EditUserRules == 0 &  $DeleteUserRules == 0) {
                                            echo " <td style='display:none;'></td>";
                                        } else {
                                            echo "<td>";
                                            // Edit button if user has permission
                                            if ($EditUserRules) {
                                                echo "<a href='edit_permission.php?id=" . $row['rules_id'] . "' class='btn btn-primary'><i class='fa-solid fa-pen-to-square'></i></a> ";
                                            }
                                            // Delete button if user has permission
                                            if ($DeleteUserRules) {
                                                echo "<a href='delete_permission.php?id=" . $row['rules_id'] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this item?\");'><i class='fa-solid fa-trash'></i></a>";
                                            }
                                            echo "</td>";
                                        }
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td class='text-center' colspan='6'>No users found!</td></tr>";
                                }
                                ?>
                            </tbody>

                        </table>
                        <br>
                        <!-- pagination -->
                        <div class="row ml-1">
                            <div class="col-sm-12 col-md-5">
                                <div class="dataTables_info" id="dataTable_info" role="status" aria-live="polite">Showing <?= $offset + 1 ?> to <?= min($offset + $records_per_page, $total_records) ?> of <?= $total_records ?> entries</div>
                            </div>
                            <div class="col-sm-12 col-md-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="dataTable_paginate">
                                    <ul class="pagination">
                                        <li class="paginate_button page-item previous <?= ($current_page == 1) ? 'disabled' : '' ?>" id="dataTable_previous"><a href="?page=<?= $current_page - 1 ?>&length=<?= $records_per_page ?>" aria-controls="dataTable" data-dt-idx="0" tabindex="0" class="page-link">Previous</a></li>
                                        <?php for ($page = 1; $page <= $total_pages; $page++) : ?>
                                            <li class="paginate_button page-item <?= ($current_page == $page) ? 'active' : '' ?>"><a href="?page=<?= $page ?>&length=<?= $records_per_page ?>" aria-controls="dataTable" data-dt-idx="<?= $page ?>" tabindex="0" class="page-link"><?= $page ?></a></li>
                                        <?php endfor; ?>
                                        <li class="paginate_button page-item next <?= ($current_page == $total_pages) ? 'disabled' : '' ?>" id="dataTable_next"><a href="?page=<?= $current_page + 1 ?>&length=<?= $records_per_page ?>" aria-controls="dataTable" data-dt-idx="7" tabindex="0" class="page-link">Next</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- /pagination -->
                        <script>
                            document.getElementById('entriesPerPage').addEventListener('change', function() {
                                window.location.href = '?page=1&length=' + this.value;
                            });
                        </script>
                    </div>

                </div>



            </div>

    </section>
    <!-- /.content -->


</div>
<!-- /.content-wrapper -->
<!-- search -->
<script>
    $(document).ready(function() {
        $('#table_search').on('input', function() {
            var searchText = $(this).val().toLowerCase();
            $('table tbody tr').each(function() {
                var rowData = $(this).text().toLowerCase();
                if (rowData.indexOf(searchText) == -1) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        });
    });
</script>

<?php include "include/footer.php"; ?>