  <!-- Main Sidebar Container -->
  <?php
  // Function to check 
  function listTicket($rules_id, $conn)
  {
    $query = "SELECT list_ticket_status FROM tbl_users_rules WHERE rules_id = $rules_id";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
      $row = $result->fetch_assoc();
      return $row['list_ticket_status'] == 1; // Check if add_status is set to 1 (allowed)
    }
    return false; // Default to false if no permission found
  }

  // Function to check if 
  function listStation($rules_id, $conn)
  {
    $query = "SELECT list_station FROM tbl_users_rules WHERE rules_id = $rules_id";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
      $row = $result->fetch_assoc();
      return $row['list_station'] == 1; // Check if edit_status is set to 1 (allowed)
    }
    return false; // Default to false if no permission found
  }

  // Function to check if 
  function listUsers($rules_id, $conn)
  {
    $query = "SELECT list_user_status FROM tbl_users_rules WHERE rules_id = $rules_id";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
      $row = $result->fetch_assoc();
      return $row['list_user_status'] == 1; // Check if delete_status is set to 1 (allowed)
    }
    return false; // Default to false if no permission found
  }

  // Function to check if 
  function listUsersRules($rules_id, $conn)
  {
    $query = "SELECT list_user_rules FROM tbl_users_rules WHERE rules_id = $rules_id";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
      $row = $result->fetch_assoc();
      return $row['list_user_rules'] == 1; // Check if delete_status is set to 1 (allowed)
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
    $listTicket = listTicket($rules_id, $conn);
    $listStation = listStation($rules_id, $conn);
    $listUsers = listUsers($rules_id, $conn);
    $listUsersRules = listUsersRules($rules_id, $conn);
  } else {
    // Handle error if user not found or permission check fails
    $_SESSION['error_message'] = "User not found or permission check failed.";
    // header("Location: users_rules.php"); // Redirect to appropriate page
    // exit;

  }
  // Define the current page URL
  $current_page = basename($_SERVER['PHP_SELF']);
  ?>
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <div class=" text-center">
      <img src="dist/img/ptt.png" alt="ptt cambodia" class="w-100 p-3" class="brand-image img-circle elevation-5" style="opacity: .8">
    </div>

    <!-- Sidebar -->
    <div class="sidebar">

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

          <li class="nav-item menu-open">
            <a href="index.php" <?php if ($current_page === 'index.php') echo 'class="nav-link active"';
                                else echo 'class="nav-link"'; ?>>

              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          <!-- menu ticket -->
          <?php
          if ($listTicket) { ?>
            <li class="nav-item fa-lg">
              <?php ?>
              <a href="ticket.php" <?php if ($current_page === 'ticket.php') echo 'class="nav-link active"';
                                    else echo 'class="nav-link"'; ?>>
                <i class="nav-icon fas fa-ticket-alt"></i>
                <p>
                  Ticket
                </p>
              </a>
            </li>
          <?php
          }
          ?>

          <!-- menu station -->
          <?php
          if ($listStation) { ?>
            <li class="nav-item fa-lg d-inline">
              <a href="station.php" <?php if ($current_page === 'station.php') echo 'class="nav-link active"';
                                    else echo 'class="nav-link"'; ?>>
                <i class='nav-icon bx bxs-gas-pump '></i>
                <p>
                  Station
                </p>
              </a>
            </li>
          <?php
          }
          ?>
          <!-- menu users -->
          <?php
          if ($listUsers) { ?>
            <li class="nav-item fa-lg d-inline">

              <a href="users.php" <?php if ($current_page === 'users.php') echo 'class="nav-link active"';
                                  else echo 'class="nav-link"'; ?>>
                <i class="nav-icon fas fa-user"></i>
                <p>
                  Users

                </p>
              </a>
            </li>
          <?php
          }
          ?>
          <!-- menu user rules -->
          <?php
          if ($listUsersRules) { ?>
            <li class="nav-item fa-lg d-inline">

              <a href="users_rules.php" <?php if ($current_page === 'users_rules.php') echo 'class="nav-link active"';
                                        else echo 'class="nav-link"'; ?>>
                <i class="nav-icon fas fa-users-cog"></i>
                <p>
                  Users Rules

                </p>
              </a>
            </li>
          <?php
          }
          ?>




          <li class="nav-item fa-lg">
            <a href="report.php" <?php if ($current_page === 'report.php') echo 'class="nav-link active"';
                                  else echo 'class="nav-link"'; ?>>
              <i class="nav-icon fa fa-file"></i>
              <p>
                Report
              </p>
            </a>
          </li>

          <li class="nav-item fa-lg d-inline">
            <a href="../logout-user.php" class="nav-link ">
              <i class='nav-icon bx bx-log-out'></i>
              <p>
                logout
              </p>
            </a>
          </li>

          <!-- <li class="nav-item fa-lg d-inline">
            <a href="#" class="nav-link">
            <i class="nav-icon fas fa-user-alt"></i>
              <p>
                User
              </p>
            </a>
          </li>

          <li class="nav-item fa-lg d-inline">
            <a href="#" class="nav-link">
            <i class='bx bxs-user-check'></i>
              <p>
                User Roles
              </p>
            </a>
          </li> -->

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>