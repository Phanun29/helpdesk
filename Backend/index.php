<?php
include "config.php"; // Include your database connection configuration

// Fetch the counts of tickets by status
$query_status = "
    SELECT status, COUNT(*) as count
    FROM tbl_ticket
    GROUP BY status
";

$result_status = $conn->query($query_status);

// Initialize counts
$status_counts = [
  'Open' => 0,
  'On Hold' => 0,
  'In Progress' => 0,
  'Pending Vender' => 0,
  'Close' => 0
];

// Populate counts from the database result
while ($row = $result_status->fetch_assoc()) {
  $status = $row['status'];
  $count = $row['count'];
  $status_counts[$status] = $count;
}

// Fetch the counts of tickets by priority
$query_priority = "
    SELECT priority, COUNT(*) as count
    FROM tbl_ticket
    GROUP BY priority
";

$result_priority = $conn->query($query_priority);

// Initialize priority counts
$priority_counts = [
  'CAT Hardware' => 0,
  'CAT 1*' => 0,
  'CAT 2*' => 0,
  'CAT 3*' => 0,
  'CAT 4*' => 0,
  'CAT 4 Report*' => 0,
  'CAT 5*' => 0
];

$total_tickets = 0; // Initialize total tickets count

// Populate counts from the database result
while ($row = $result_priority->fetch_assoc()) {
  $priority = $row['priority'];
  $count = $row['count'];
  $priority_counts[$priority] = $count;
  $total_tickets += $count; // Sum up total tickets
}

// Calculate percentages
$priority_percentages = [
  'CAT Hardware' => $total_tickets > 0 ? round(($priority_counts['CAT Hardware'] / $total_tickets) * 100) : 0,
  'CAT 1*' => $total_tickets > 0 ? round(($priority_counts['CAT 1*'] / $total_tickets) * 100) : 0,
  'CAT 2*' => $total_tickets > 0 ? round(($priority_counts['CAT 2*'] / $total_tickets) * 100) : 0,
  'CAT 3*' => $total_tickets > 0 ? round(($priority_counts['CAT 3*'] / $total_tickets) * 100) : 0,
  'CAT 4*' => $total_tickets > 0 ? round(($priority_counts['CAT 4*'] / $total_tickets) * 100) : 0,
  'CAT 4 Report*' => $total_tickets > 0 ? round(($priority_counts['CAT 4 Report*'] / $total_tickets) * 100) : 0,
  'CAT 5*' => $total_tickets > 0 ? round(($priority_counts['CAT 5*'] / $total_tickets) * 100) : 0,
];

/// Fetch data from the database
$query_issue = "SELECT issue_type FROM tbl_ticket";
$result_issue = $conn->query($query_issue);

// Initialize arrays to store data
$issue_counts = [
  'Hardware' => 0,
  'Software' => 0,
  'Network' => 0,
  'Dispensor' => 0,
  'Unassigned' => 0
];

// Process the fetched data
while ($row = $result_issue->fetch_assoc()) {
  $issue_types = explode(', ', $row['issue_type']);
  foreach ($issue_types as $issue_type) {
    $type = trim($issue_type);
    if (isset($issue_counts[$type])) {
      $issue_counts[$type]++;
    } else {
      $issue_counts['Unassigned']++; // Handle any unassigned issue types
    }
  }
}

// Prepare labels and data for Chart.js
$labels = array_keys($issue_counts);
$data = array_values($issue_counts);

$conn->close();
?>

<?php require('include/header.php'); ?>
<!-- top header -->

<div class="content-wrapper">

  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Dashboard</h1>
        </div>
      </div>
    </div>
  </section>

  <!-- / top header -->

  <!-- Dashboard -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">

        <!-- section start Summary -->

        <div class="row col-12 ">
          <div class="card col-sm-6">
            <div class="card-header">
              <h3 class="card-title"> Status</h3>
            </div>
            <div class="row">
              <div class="col-lg-4 col-4">
                <div class="small-box bg-info">
                  <div class="inner">
                    <h3><?php echo $status_counts['Open']; ?></h3>
                    <p>Open</p>
                  </div>
                  <div class="icon">

                  </div>
                  <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>

              <div class="col-lg-4 col-4">
                <div class="small-box bg-danger">
                  <div class="inner">
                    <h3><?php echo $status_counts['On Hold']; ?></h3>
                    <p>On Hold</p>
                  </div>
                  <div class="icon">

                  </div>
                  <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>

              <div class="col-lg-4 col-4">
                <div class="small-box bg-warning">
                  <div class="inner">
                    <h3><?php echo $status_counts['In Progress']; ?></h3>
                    <p>In Progress</p>
                  </div>
                  <div class="icon">

                  </div>
                  <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-8 col-6">
                <div class="small-box bg-primary">
                  <div class="inner">
                    <h3><?php echo $status_counts['Pending Vender']; ?></h3>
                    <p>Pending Vendor</p>
                  </div>
                  <div class="icon">

                  </div>
                  <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>

              <div class="col-lg-4 col-6">
                <div class="small-box bg-success">
                  <div class="inner">
                    <h3><?php echo $status_counts['Close']; ?></h3>
                    <p>Close</p>
                  </div>
                  <div class="icon">

                  </div>
                  <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
            </div>
          </div>
          <div class="card col-sm-6">
            <div class="chartMenu">
            </div>
            <div class="chartCard">
              <div class="chartBox">
                <canvas id="myChart"></canvas>
              </div>
            </div>
          </div>
          <!-- /.section start Summary -->

          <!-- Section Start Priority -->
          <div class="card col-12">
            <div class="card-header">
              <h3 class="card-title"><b>Priority %</b></h3>
            </div>
            <div class="col-12">
              <div class="card-body">
                <div class="row">
                  <div class="col-6 col-md-3 text-center">
                    <div style="display:inline;width:90px;height:90px;">
                      <input type="text" class="knob" value="<?php echo $priority_percentages['CAT Hardware']; ?>" data-width="90" data-height="90" data-fgcolor="#3c8dbc" data-readOnly="true">
                    </div>
                    <div class="knob-label">CAT Hardware</div>
                  </div>

                  <div class="col-6 col-md-3 text-center">
                    <div style="display:inline;width:90px;height:90px;">
                      <input type="text" class="knob" value="<?php echo $priority_percentages['CAT 1*']; ?>" data-width="90" data-height="90" data-fgcolor="#f56954" data-readOnly="true">
                    </div>
                    <div class="knob-label">CAT 1*</div>
                  </div>

                  <div class="col-6 col-md-3 text-center">
                    <div style="display:inline;width:90px;height:90px;">
                      <input type="text" class="knob" value="<?php echo $priority_percentages['CAT 2*']; ?>" data-width="90" data-height="90" data-fgcolor="#932ab6" data-readOnly="true">
                    </div>
                    <div class="knob-label">CAT 2*</div>
                  </div>
                  <div class="col-6 col-md-3 text-center">
                    <div style="display:inline;width:90px;height:90px;">
                      <input type="text" class="knob" value="<?php echo $priority_percentages['CAT 3*']; ?>" data-width="90" data-height="90" data-fgcolor="#932ab6" data-readOnly="true">
                    </div>
                    <div class="knob-label">CAT 3*</div>
                  </div>
                  <div class="col-6 col-md-3 text-center">
                    <div style="display:inline;width:90px;height:90px;">
                      <input type="text" class="knob" value="<?php echo $priority_percentages['CAT 4*']; ?>" data-width="90" data-height="90" data-fgcolor="#932ab6" data-readOnly="true">
                    </div>
                    <div class="knob-label">CAT 4*</div>
                  </div>
                  <div class="col-6 col-md-3 text-center">
                    <div style="display:inline;width:90px;height:90px;">
                      <input type="text" class="knob" value="<?php echo $priority_percentages['CAT 4 Report*']; ?>" data-width="90" data-height="90" data-fgcolor="#932ab6" data-readOnly="true">
                    </div>
                    <div class="knob-label">CAT 4 Report*</div>
                  </div>
                  <div class="col-6 col-md-3 text-center">
                    <div style="display:inline;width:90px;height:90px;">
                      <input type="text" class="knob" value="<?php echo $priority_percentages['CAT 5*']; ?>" data-width="90" data-height="90" data-fgcolor="#932ab6" data-readOnly="true">
                    </div>
                    <div class="knob-label">CAT 5*</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- /.Section Start Priority -->

        </div>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Ticket</h3>
            <div class="card-tools">
              <div class="input-group input-group-sm" style="width: 150px;">
                <input type="text" id="table_search" name="table_search" class="form-control float-right" placeholder="Search">
                <div class="input-group-append">
                  <button type="submit" class="btn btn-default">
                    <i class="fas fa-search"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body table-responsive p-0" >
            <table id="myTable" class="table table-head-fixed text-nowrap">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Ticket ID</th>
                  <th>Station ID</th>
                  <th>Station Name</th>
                  <th>Station Type</th>
                  <th>Description</th>
                  <th>Type</th>
                  <th>Priority</th>
                  <th>Status</th>
                  <th>Ticket Open</th>
                  <th>Ticket Close</th>
                  <th>Comment</th>
                </tr>
              </thead>
              <?php
              $user_query = "SELECT * FROM tbl_ticket ORDER BY ticket_id DESC LIMIT 20";

              $user_result = $conn->query($user_query);
              $i = 1;
              if ($user_result->num_rows > 0) {
                while ($row = $user_result->fetch_assoc()) {
                  echo " <tbody>";
                  echo "<tr>";
                  echo " <td>" . $i++ . "</td>";
                  echo "<td>" . $row['ticket_id'] . "</td>";
                  echo "<td>" . $row['station_id'] . "</td>";
                  echo "<td>" . $row['station_name'] . "</td>";
                  echo "<td>" . $row['station_type'] . "</td>";
                  echo "<td>" . $row['issue_description'] . "</td>";

                  echo "<td>" . $row['issue_type'] . "</td>";
                  echo "<td>" . $row['priority'] . "</td>";
                  echo "<td>" . $row['status'] . "</td>";
                  // echo "<td>" . $row['users_id'] . "</td>";
                  echo "<td>" . $row['ticket_open'] . "</td>";
                  echo "<td>" . $row['ticket_close'] . "</td>";
                  echo "<td>" . $row['comment'] . "</td>";
                  echo "</tbody>";
                }
              }
              ?>

            </table>
          </div>

        </div>


      </div>

    </div>

</div>

</div>


<!-- /.Dashboard -->
<?php require('include/footer.php'); ?>
<?php require('include/sidebar.php'); ?>
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
<script>
  $(function() {
    $(".knob").knob({
      'format': function(value) {
        return value + '%';
      }
    });
  });
</script>
<script>
  // Data from PHP
  const labels = <?php echo json_encode($labels); ?>;
  const data = <?php echo json_encode($data); ?>;

  // Chart.js setup
  const config = {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Issue Type',
        data: data,
        backgroundColor: [
          'rgba(0, 166, 158, 1)', // Hardware
          'rgba(255, 184, 34, 1)', // Software
          'rgba(255, 182, 94, 1)', // Network
          'rgba(141, 68, 173, 1)', // Dispenser
          'rgba(107, 210, 190, 1)' // Unassigned
        ],
        borderColor: [
          'rgba(0, 166, 158, 1)',
          'rgba(255, 184, 34, 1)',
          'rgba(255, 182, 94, 1)',
          'rgba(141, 68, 173, 1)',
          'rgba(107, 210, 190, 1)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      indexAxis: 'y',
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  };

  // Render the chart
  const myChart = new Chart(document.getElementById('myChart'), config);
</script>