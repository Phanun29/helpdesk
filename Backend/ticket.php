<?php
require('include/header.php');
require('include/sidebar.php');
require "include/rules_ticket.php";
include "config.php";

// Default values for pagination
$records_per_page = isset($_GET['length']) ? intval($_GET['length']) : 10;
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($current_page - 1) * $records_per_page;
$ticket_query = "
    SELECT 
        t.*, 
        REPLACE(GROUP_CONCAT(u.users_name SEPARATOR ', '), ', ', ',') as users_name
    FROM 
        tbl_ticket t
    LEFT JOIN 
        tbl_users u ON FIND_IN_SET(u.users_id, t.users_id)
    GROUP BY 
        t.ticket_id DESC
    LIMIT $offset, $records_per_page 
";
$ticket_result = $conn->query($ticket_query);
$total_query = "SELECT COUNT(*) as total FROM tbl_ticket";
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
          <h1 class="m-0"><b>Ticket</b></h1>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- dashboard -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <div class="dt-buttons btn-group flex-wrap">
                <button class="btn btn-secondary buttons-csv buttons-html5" tabindex="0" aria-controls="tbl_ticket" onclick="exportToCSV()" type="button"><span>CSV</span></button>
                <button class="btn btn-secondary buttons-excel buttons-html5" tabindex="0" aria-controls="tbl_ticket" onclick="exportToExcel()" type="button"><span>Excel</span></button>
                <button class="btn btn-secondary buttons-pdf buttons-html5" tabindex="0" aria-controls="tbl_ticket" onclick="exportToPDF()" type="button"><span>PDF</span></button>
              </div>
              <?php if (isset($canAddStation) && $canAddStation) : ?>
                <a href="create_ticket.php" class="btn btn-primary ml-2">Add Ticket</a>
              <?php endif; ?>
              <div id="card_tools" class="card-tools">
                <div class="input-group input-group-sm" style="width: 250px;height: 42px">
                  <input type="text" id="table_search" style="height: 42px;" class="form-control float-right d-print-flex" placeholder="Search">
                  <div class="input-group-append">
                    <button type="button" class="btn btn-default">
                      <i class="fas fa-search"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <!-- filter -->
            <div class="card-header">
              <button id="toggleFilterBtn" class="btn btn-secondary mb-2">Show Filters</button>
              <div id="filterContainer" style="display: none;">
                <div class="row">
                  <!-- <form class="row">
                    <div class="form-group col-sm-3">
                      <label for="station_id">Station ID</label>
                      <input class="form-control" type="text" name="station_id" id="station_id" placeholder="Station ID" autocomplete="off" onkeyup="showSuggestions(this.value)">
                      <div id="suggestion_dropdown" class="dropdown-content"></div>
                    </div>
                    <div class="form-group col-sm-3">
                      <label for="issue_type">Issue Type</label>
                      <select class="form-control" name="issue_type" id="issue_type">
                        <option value="">Issue Type</option>
                        <option value="Hardware">Hardware</option>
                        <option value="Software">Software</option>
                        <option value="Network">Network</option>
                        <option value="Dispensor">Dispensor</option>
                        <option value="Unassigned">Unassigned</option>
                      </select>
                    </div>
                    <div class="form-group col-sm-3">
                      <label for="priority">Priority</label>
                      <select name="priority" id="priority" class="form-control select2bs4" style="width: 100%;">
                        <option>Priority</option>
                        <option value="CAT Hardware">CAT Hardware</option>
                        <option value="CAT 1*">CAT 1*</option>
                        <option value="CAT 2*">CAT 2*</option>
                        <option value="CAT 3*">CAT 3*</option>
                        <option value="CAT 4*">CAT 4*</option>
                        <option value="CAT 4 Report*">CAT 4 Report*</option>
                        <option value="CAT 5*">CAT 5*</option>
                      </select>
                    </div>
                    <div class="form-group col-sm-3">
                      <label for="status">Status</label>
                      <select name="status" id="status" class="form-control select2bs4" style="width: 100%;">
                        <option value="">Status</option>
                        <option value="open">Open</option>
                        <option value="on_hold">On Hold</option>
                        <option value="in_progress">In Progress</option>
                        <option value="pending_vendor">Pending Vendor</option>
                        <option value="close">Closed</option>
                      </select>
                    </div>
                    <div class="form-group col-sm-3">
                      <label for="users_id">Assign</label>
                      <select name="users_id" class="form-control">
                        <option value="">Assign</option>
                        <?php
                        $user_query1 = "SELECT users_id, users_name FROM tbl_users WHERE status = 1";
                        $user_result1 = $conn->query($user_query1);
                        if ($user_result1 && $user_result1->num_rows > 0) {
                          while ($row1 = $user_result1->fetch_assoc()) {
                            echo "<option value='" . $row1['users_id'] . "'>" . $row1['users_name'] . "</option>";
                          }
                        } else {
                          echo "<option value=''>No users found with status 1</option>";
                        }
                        ?>
                      </select>
                    </div>
                    <div class="form-group col-md-3">
                      <label for="ticket_open_from">Ticket Open From</label>
                      <input type="date" name="ticket_open_from" id="ticket_open_from" class="form-control">
                    </div>
                    <div class="form-group col-md-3">
                      <label for="ticket_open_to">Ticket Open To</label>
                      <input type="date" name="ticket_open_to" id="ticket_open_to" class="form-control">
                    </div>
                    <div class="form-group col-md-3">
                      <label for="ticket_close_from">Ticket Close From</label>
                      <input type="date" name="ticket_close_from" id="ticket_close_from" class="form-control">
                    </div>
                    <div class="form-group col-md-3">
                      <label for="ticket_close_to">Ticket Close To</label>
                      <input type="date" name="ticket_close_to" id="ticket_close_to" class="form-control">
                    </div>
                    <div class="form-group col-md-12">
                      <button type="button" id="applyFiltersBtn" class="btn btn-primary">Filter</button>
                      <button type="reset" class="btn btn-danger" id="filterResetBtn">Reset</button>
                    </div>
                  </form> -->
                  <form id="filterForm" class="row">
                    <div class="form-group col-sm-3">
                      <label for="station_id">Station ID</label>
                      <!-- <input class="form-control" type="text" name="station_id" id="station_id"> -->
                      <input class="form-control" type="text" name="station_id" id="station_id" autocomplete="off" onkeyup="showSuggestions(this.value)">
                      <div id="suggestion_dropdown" class="dropdown-content"></div>
                    </div>
                    <div class="form-group col-sm-3">
                      <label for="issue_type">Issue Type</label>
                      <select class="form-control" name="issue_type" id="issue_type">
                        <option value="">Issue Type</option>
                        <option value="Hardware">Hardware</option>
                        <option value="Software">Software</option>
                        <option value="Network">Network</option>
                        <option value="Dispensor">Dispensor</option>
                        <option value="Unassigned">Unassigned</option>
                      </select>
                    </div>
                    <div class="form-group col-sm-3">
                      <label for="priority">Priority</label>
                      <select name="priority" id="priority" class="form-control">
                        <option value="">Priority</option>
                        <option value="CAT Hardware">CAT Hardware</option>
                        <option value="CAT 1*">CAT 1*</option>
                        <option value="CAT 2*">CAT 2*</option>
                        <option value="CAT 3*">CAT 3*</option>
                        <option value="CAT 4*">CAT 4*</option>
                        <option value="CAT 4 Report*">CAT 4 Report*</option>
                        <option value="CAT 5*">CAT 5*</option>
                      </select>
                    </div>
                    <div class="form-group col-sm-3">
                      <label for="status">Status</label>
                      <select name="status" id="status" class="form-control" style="width: 100%;">
                        <option value="">Status</option>
                        <option value="Open">Open</option>
                        <option value="On Hold">On Hold</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Pending Vender">Pending Vendor</option>
                        <option value="Close">Closed</option>
                      </select>
                    </div>
                    <div class="form-group col-sm-3">
                      <label for="users_id">Assign</label>
                      <select name="users_id" id="users_id" class="form-control">
                        <option value="">Assign</option>
                        <?php
                        // Fetch users with status 1 from database
                        // $user_query = "SELECT users_id, users_name FROM tbl_users WHERE status = 1";
                        // $user_result = $conn->query($user_query);
                        // if ($user_result && $user_result->num_rows > 0) {
                        //   while ($row = $user_result->fetch_assoc()) {
                        //     echo "<option value='" . $row['users_id'] . "'>" . $row['users_name'] . "</option>";
                        //   }
                        // } else {
                        //   echo "<option value=''>No users found with status 1</option>";
                        // }

                        // Fetch users with status 1 from database
                        $user_query1 = "SELECT users_id, users_name FROM tbl_users WHERE status = 1";
                        $user_result1 = $conn->query($user_query1);

                        // Initialize an array to store users
                        $users = [];

                        if ($user_result1 && $user_result1->num_rows > 0) {
                          while ($row1 = $user_result1->fetch_assoc()) {
                            $users[] = $row1;
                          }
                        }

                        // Check if there are users to display
                        if (!empty($users)) {
                          // Iterate through users array using a for loop
                          for ($i = 0; $i < count($users); $i++) {
                            echo "<option value='" . $users[$i]['users_id'] . "'>" . $users[$i]['users_name'] . "</option>";
                          }

                          // Add the "end data" option
                          echo "<option value=''>End Data</option>";
                        } else {
                          echo "<option value=''>No users found with status 1</option>";
                        }
                        ?>


                      </select>
                    </div>
                    <div class="form-group col-md-3">
                      <label for="ticket_open_from">Ticket Open From</label>
                      <input type="date" name="ticket_open_from" id="ticket_open_from" class="form-control">
                    </div>
                    <div class="form-group col-md-3">
                      <label for="ticket_open_to">Ticket Open To</label>
                      <input type="date" name="ticket_open_to" id="ticket_open_to" class="form-control">
                    </div>
                    <div class="form-group col-md-3">
                      <label for="ticket_close_from">Ticket Close From</label>
                      <input type="date" name="ticket_close_from" id="ticket_close_from" class="form-control">
                    </div>
                    <div class="form-group col-md-3">
                      <label for="ticket_close_to">Ticket Close To</label>
                      <input type="date" name="ticket_close_to" id="ticket_close_to" class="form-control">
                    </div>
                    <div class="form-group col-md-12">
                      <button type="button" class="btn btn-primary" onclick="filterTickets()">Filter</button>
                      <button type="reset" class="btn btn-danger" id="filterResetBtn">Clear</button>
                    </div>
                  </form>

                </div>
              </div>
            </div>



            <!-- <script>
              $(document).ready(function() {
                $("#applyFiltersBtn").click(function() {

                  var station_id = $("#station_id").val();
                  var issue_type = $("#issue_type").val();
                  var priority = $("#priority").val();
                  var status = $("status").val();
                  var users_id = $("users_id").val();
                  var ticket_open_from = $("ticket_open_from").val();
                  var ticket_open_to = $("ticket_open_to").val();
                  var ticket_close_from = $("ticket_close_from").val();
                  var ticket_close_to = $("ticket_close_to").val();


                  // Check if either filterBreed1 or filterBreed2 is not empty
                  if (station_id !== '' || issue_type !== '' || priority !== '' || status !== '' || users_id !== '' || ticket_open_from !== '' || ticket_open_to !== '' || ticket_close_from !== '' || ticket_close_to !== '') {
                    $.ajax({
                      type: "POST",
                      url: "retrieveData.php",
                      data: {

                        station_id: station_id,
                        issue_type: issue_type,
                        priority: priority,
                        status: status,
                        users_id: users_id,
                        ticket_open_from: ticket_open_from,
                        ticket_open_to: ticket_open_to,
                        ticket_close_from: ticket_close_from,
                        ticket_close_to: ticket_close_to
                        // Pass version filter value to retrieveData.php

                      },
                      success: function(response) {
                        $("#myTable tbody").html(response); // Update tbody content
                      },
                      error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                      }
                    });
                  } else {
                    // Optionally, you can display a message to the user
                    // alert("Please enter a value in at least one filter field.");
                  }
                });
              });
            </script> -->
            <!-- /filter -->
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
            <div class="card-body table-responsive p-0">
              <table id="myTable" class="table table-hover text-nowrap">
                <thead>
                  <tr>
                    <th>#</th>
                    <?php if ($canEditStation || $canDeleteStation) : ?>
                      <th>Option</th>
                    <?php endif; ?>
                    <th>Ticket ID</th>
                    <th>Station ID</th>
                    <th>Station Name</th>
                    <th>Station Type</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Issue Type</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Assign</th>
                    <th>Ticket Open</th>
                    <th>Ticket Close</th>
                    <th>Comment</th>
                  </tr>
                </thead>
                <tbody id="ticketTableBody">
                  <?php
                  $i = $offset + 1;
                  if ($ticket_result->num_rows > 0) {
                    while ($row = $ticket_result->fetch_assoc()) {
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
                      echo "<td>" . $row['issue_type'] . "</td>";
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
                  ?>
              </table>
              <br>
              <div class="row ml-1">
                <div class="col-sm-12 col-md-5">
                  <div class="dataTables_info" id="dataTable_info" role="status" aria-live="polite">
                    Showing
                    <?= $offset + 1 ?> to
                    <?= min($offset + $records_per_page, $total_records) ?> of
                    <?= $total_records ?> entries
                  </div>
                </div>
                <div class="col-sm-12 col-md-7">
                  <div class="dataTables_paginate paging_simple_numbers" id="dataTable_paginate">
                    <ul class="pagination">
                      <li class="paginate_button page-item previous <?= ($current_page == 1) ? 'disabled' : '' ?>" id="dataTable_previous"><a href="?page=<?= $current_page - 1 ?>&length=<?= $records_per_page ?>" aria-controls="dataTable" data-dt-idx="0" tabindex="0" class="page-link">Previous</a></li>
                      <?php for ($page = 1; $page <= $total_pages; $page++) : ?>
                        <li class="paginate_button page-item <?= ($current_page == $page) ? 'active' : '' ?>">
                          <a href="?page=<?= $page ?>&length=<?= $records_per_page ?>" aria-controls="dataTable" data-dt-idx="<?= $page ?>" tabindex="0" class="page-link">
                            <?= $page ?>
                          </a>
                        </li>
                      <?php endfor; ?>
                      <li class="paginate_button page-item next <?= ($current_page == $total_pages) ? 'disabled' : '' ?>" id="dataTable_next"><a href="?page=<?= $current_page + 1 ?>&length=<?= $records_per_page ?>" aria-controls="dataTable" data-dt-idx="7" tabindex="0" class="page-link">Next</a></li>
                    </ul>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script>
  function filterTickets() {
    $.ajax({
      url: 'retrieveData.php',
      type: 'POST',
      data: $('#filterForm').serialize(),
      success: function(data) {
        $('#ticketTableBody').html(data);
      },
      error: function() {
        alert('An error occurred while filtering tickets.');
      }
    });
  }
</script>
<script>
  // Function to handle pagination link clicks
  // $(document).on("click", ".page-link", function(e) {
  //   e.preventDefault();
  //   var page = $(this).data("page-number");
  //   loadTickets(page);
  // });

  // Function to toggle filter visibility
  $("#toggleFilterBtn").on("click", function() {
    $("#filterContainer").toggle();
    var text = $(this).text().trim();
    $(this).text(text === "Show Filters" ? "Hide Filters" : "Show Filters");
  });

  // Initial load of tickets when the page loads
  $(document).ready(function() {
    loadTickets();
  });

  // Function to show image (assuming it's a modal or similar)
  function showImage(imagePath) {
    // Your implementation to show the image
  }
</script>
<?php
require('include/footer.php');
?>