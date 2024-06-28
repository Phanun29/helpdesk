<?php
require('include/header.php');
require('include/sidebar.php');
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $station_id = $_POST['station_id'];
  $issue_description = $_POST['issue_description'];
  $issue_type = implode(', ', $_POST['issue_type']); // Convert array to string without spaces
  $priority = $_POST['priority'];

  $users_id = isset($_POST['users_id']) ? implode(',', array_map('trim', $_POST['users_id'])) : ''; // Convert array to string without spaces

  date_default_timezone_set('Asia/Bangkok');
  $ticket_open = date('Y-m-d H:i:s');

  // Validate station_id
  $station_check_query = "SELECT station_name, station_type FROM tbl_station WHERE station_id = ?";
  $stmt = $conn->prepare($station_check_query);
  $stmt->bind_param("i", $station_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Error: Invalid Station ID.";
  } else {
    $row = $result->fetch_assoc();
    $station_name = $row['station_name'];
    $station_type = $row['station_type'];

    // Generate Ticket ID
    $current_year = date("y");
    $current_month = date("m");

    // Retrieve the last ticket ID from the database for the current month
    $last_ticket_query = "SELECT MAX(ticket_id) AS max_ticket_id FROM tbl_ticket WHERE ticket_id LIKE 'POS$current_year$current_month%'";
    $last_ticket_result = $conn->query($last_ticket_query);
    $row = $last_ticket_result->fetch_assoc();
    $last_ticket_id = $row['max_ticket_id'];

    // Extract the sequential number from the last ticket ID
    $last_seq_number = intval(substr($last_ticket_id, -6));

    // If the last ticket ID exists, increment the sequential number, otherwise set it to 1
    $new_seq_number = ($last_seq_number !== null) ? $last_seq_number + 1 : 1;

    // Pad the sequential number with leading zeros
    $padded_seq_number = str_pad($new_seq_number, 6, "0", STR_PAD_LEFT);

    // Construct the new ticket ID
    $ticket_id = "POS$current_year$current_month$padded_seq_number";

    // Process multiple file uploads
    $uploaded_images = [];
    if (!empty($_FILES['issue_image']['name'][0])) {
      $target_dir = "uploads/";
      foreach ($_FILES['issue_image']['name'] as $key => $image) {
        $target_file = $target_dir . basename($image);
        if (move_uploaded_file($_FILES["issue_image"]["tmp_name"][$key], $target_file)) {
          $uploaded_images[] = $target_file;
        } else {
          $_SESSION['error_message'] = "Error uploading image: " . $image;
          // header('Location: ' . $_SERVER['REQUEST_URI']);
          // exit();
        }
      }
    }

    // Convert the array of image paths to a comma-separated string
    $issue_image_paths = implode(',', $uploaded_images);

    $sql = "INSERT INTO tbl_ticket (ticket_id, station_id, station_name, station_type, issue_description, issue_image, issue_type, priority, users_id, ticket_open, ticket_close) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisssssssss", $ticket_id, $station_id, $station_name, $station_type, $issue_description, $issue_image_paths, $issue_type, $priority, $users_id, $ticket_open, $ticket_close);

    if ($stmt->execute()) {
      $_SESSION['success_message'] = "New ticket created successfully";
    } else {
      $_SESSION['error_message'] = "Error: " . $stmt->error;
    }
    $stmt->close();
  }
  // header('Location: ' . $_SERVER['REQUEST_URI']);
  // exit();
}

?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Main content -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Ticketing</h1>
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
  <div class="header">
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
              <h3 class="card-title">Create Ticket</h3>
            </div>
            <!-- <form method="POST" id="quickForm" novalidate="novalidate" enctype="multipart/form-data">
              <div class="card-body col">
                <div class="row">
                  <div class="form-group col-sm-4 ">
                    <label for="station_id">Station ID <span class="text-danger">*</span></label>
                    <input class="form-control" type="text" name="station_id" id="station_id" autocomplete="off" onkeyup="showSuggestions(this.value)" required>
                    <div id="suggestion_dropdown" class="dropdown-content"></div>
                  </div>

                  <div class="form-group col-sm-4">
                    <label for="station_name">Station Name</label>
                    <input type="text" name="station_name" class="form-control" id="station_name" placeholder="Station Name" readonly>
                  </div>
                  <div class="form-group col-sm-4">
                    <label for="station_type">Station Type</label>
                    <input type="text" name="station_type" class="form-control" id="station_type" placeholder="Station Type" readonly>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-sm-8">
                    <label for="issue_description">Issue Description</label>
                    <textarea id="issue_description" name="issue_description" class="form-control" rows="3" placeholder="Issue Description" required></textarea>
                  </div>
                  <div class="form-group col-sm-4">
                    <label for="issue_image">Issue Image</label>
                    <div class="input-group col-12">
                      <div class="custom-image">
                       
                        <input type="file" id="issue_image" name="issue_image[]" class="form-control" multiple>
                      </div>
                    </div>
                  </div>

                </div>
                <div class="row">
                  <div class="form-group col-sm-4">
                    <label for="issue_type">Issue Type</label>
                    <select name="issue_type[]" id="issue_type" class="form-control" id="choices-multiple-remove-button" placeholder="Select upto 2 tags" multiple required>
                      <option value="Hardware">Hardware</option>
                      <option value="Software">Software</option>
                      <option value="Network">Network</option>
                      <option value="Dispensor">Dispensor</option>
                      <option value="Unassigned">Unassigned</option>
                    </select>
                  </div>

                  <div class="form-group col-sm-4">
                    <label for="priority">Priority</label>
                    <select name="priority" id="priority" class="form-control select2bs4" style="width: 100%;" required>
                      <option value="CAT Hardware">CAT Hardware</option>
                      <option value="CAT 1*">CAT 1*</option>
                      <option value="CAT 2*">CAT 2*</option>
                      <option value="CAT 3*">CAT 3*</option>
                      <option value="CAT 4*">CAT 4*</option>
                      <option value="CAT 4 Report*">CAT 4 Report*</option>
                      <option value="CAT 5*">CAT 5*</option>
                    </select>
                  </div>
                  <div class="form-group col-sm-4">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control select2bs4" style="width: 100%;" required>
                      <option value="Open">Open</option>
                      <option value="On Hold">On Hold</option>
                      <option value="In Progress">In Progress</option>
                      <option value="Pending Vender">Pending Vendor</option>
                      <option value="Close">Close</option>
                    </select>
                  </div>
                  <div class="form-group col-sm-4">
                    <label for="users_id">Assign</label>
                    <select name="users_id[]" class="" id="users_id" placeholder="Select upto 2 tags" multiple required>
                      <?php
                      $user_query = "SELECT users_id, users_name FROM tbl_users WHERE status = 1";
                      $user_result = $conn->query($user_query);

                      if (!$user_result) {
                        // Query execution error
                        echo "Error: " . $conn->error;
                      } elseif ($user_result->num_rows > 0) {
                        while ($row = $user_result->fetch_assoc()) {
                          echo "<option value='" . $row['users_id'] . "'>" . $row['users_name'] . "</option>";
                        }
                      } else {
                        // No users with status 1 found
                        echo "No users found with status 1";
                      }

                      ?>
                    </select>
                  </div>
                  <div class="form-group col-sm-8">
                    <label for="comment">Comment</label>
                    <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="Comment"></textarea>
                  </div>
                </div>

                <div class="">
                  <button type="submit" name="Submit" value="Submit" class="btn btn-primary">Submit</button>
                </div>
              </div>
            </form> -->
            <form method="POST" id="quickForm" novalidate="novalidate" enctype="multipart/form-data">
              <div class="card-body col">
                <div class="row">
                  <div class="form-group col-sm-4">
                    <label for="station_id">Station ID <span class="text-danger">*</span></label>
                    <input class="form-control" type="text" name="station_id" id="station_id" autocomplete="off" onkeyup="showSuggestions(this.value)" required>
                    <div id="suggestion_dropdown" class="dropdown-content"></div>
                  </div>

                  <div class="form-group col-sm-4">
                    <label for="station_name">Station Name</label>
                    <input type="text" name="station_name" class="form-control" id="station_name" placeholder="Station Name" readonly>
                  </div>
                  <div class="form-group col-sm-4">
                    <label for="station_type">Station Type</label>
                    <input type="text" name="station_type" class="form-control" id="station_type" placeholder="Station Type" readonly>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-sm-8">
                    <label for="issue_description">Issue Description</label>
                    <textarea id="issue_description" name="issue_description" class="form-control" rows="3" placeholder="Issue Description" required></textarea>
                  </div>
                  <div class="form-group col-sm-4">
                    <label for="issue_image">Issue Image</label>
                    <div class="input-group col-12">
                      <div class="custom-image">
                        <input type="file" id="issue_image" name="issue_image[]" class="form-control" multiple>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-sm-4">
                    <label for="issue_type">Issue Type</label>
                    <select name="issue_type[]" id="issue_type" class="form-control" placeholder="Select" multiple required>
                      <option value="">Select</option>
                      <option value="Hardware">Hardware</option>
                      <option value="Software">Software</option>
                      <option value="Network">Network</option>
                      <option value="Dispensor">Dispensor</option>
                      <option value="Unassigned">Unassigned</option>
                    </select>
                  </div>
                  <div class="form-group col-sm-4">
                    <label for="priority">SLA Category</label>
                    <select name="priority" id="priority" class="form-control">
                      <option value="">Select</option>
                      <option value="CAT Hardware">CAT Hardware</option>
                      <option value="CAT 1*">CAT 1*</option>
                      <option value="CAT 2*">CAT 2*</option>
                      <option value="CAT 3*">CAT 3*</option>
                      <option value="CAT 4*">CAT 4*</option>
                      <option value="CAT 4 Report*">CAT 4 Report*</option>
                      <option value="CAT 5*">CAT 5*</option>
                    </select>
                  </div>

                  <input type="hidden" name="ope" value="open">

                  <div class="form-group col-sm-4">
                    <label for="users_id">Assign</label>
                    <select name="users_id[]" id="users_id" class="form-control" placeholder="Select" multiple>

                      <!-- PHP code to dynamically generate options -->
                      <?php
                      $user_query = "SELECT users_id, users_name FROM tbl_users WHERE status = 1";
                      $user_result = $conn->query($user_query);

                      if (!$user_result) {
                        echo "Error: " . $conn->error;
                      } elseif ($user_result->num_rows > 0) {
                        while ($row = $user_result->fetch_assoc()) {
                          echo "<option value='" . $row['users_id'] . "'>" . $row['users_name'] . "</option>";
                        }
                      } else {
                        echo "No users found with status 1";
                      }
                      ?>
                    </select>
                  </div>

                </div>

                <div class="">
                  <button type="submit" name="Submit" value="Submit" class="btn btn-primary">Submit</button>
                </div>
              </div>
            </form>

          </div>
        </div>
        <div class="col-md-6">
        </div>
      </div>
    </div>
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Initialization script -->

<!-- select multiple -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var issueTypeChoices = new Choices('#issue_type', {
      removeItemButton: true,
      maxItemCount: 5,
      searchResultLimit: 3,
      renderChoiceLimit: 3
    });

    var usersIdChoices = new Choices('#users_id', {
      removeItemButton: true,
      maxItemCount: 5,
      searchResultLimit: 3,
      renderChoiceLimit: 3
    });
  });
</script>



<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<!-- <script>
  $(document).ready(function() {
    $('#quickForm').validate({
      rules: {
        station_id: {
          required: true
        },
        issue_description: {
          required: true
        },
        'issue_type[]': {
          required: true
        },
        priority: {
          required: true
        },
        status: {
          required: true
        },
        'users_id[]': {
          required: true
        }
      },
      messages: {
        station_id: {
          required: "Please enter the Station ID"
        },
        issue_description: {
          required: "Please enter the Issue Description"
        },
        'issue_type[]': {
          required: "Please select at least one Issue Type"
        },
        priority: {
          required: "Please select a Priority"
        },
        status: {
          required: "Please select a Status"
        },
        'users_id[]': {
          required: "Please assign the issue to at least one user"
        }
      },
      errorElement: 'span',
      errorPlacement: function(error, element) {
        error.addClass('invalid-feedback');
        element.closest('.form-group').append(error);
      },
      highlight: function(element, errorClass, validClass) {
        $(element).addClass('is-invalid');
      },
      unhighlight: function(element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
      }
    });
  });
</script> -->

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

  // suggestion station id and station name 

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

  function selectSuggestion(station_id) {
    document.getElementById("station_id").value = station_id;
    // document.getElementById("station_id").value = station_id;
    document.getElementById("suggestion_dropdown").innerHTML = "";
  }
</script> -->
<style>
  .dropdown-content {
    position: absolute;
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
    z-index: 1;
  }

  .dropdown-content p {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    cursor: pointer;
  }

  .dropdown-content p:hover {
    background-color: #f1f1f1;
  }
</style>
<?php
require('include/footer.php');
?>
<script>
  function closeAlert(button) {
    var alert = button.closest('.alert');
    alert.style.display = 'none';
  }
</script>