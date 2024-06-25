<?php require_once "../controllerUserData.php"; ?>

<?php
include "config.php";
$email = $_SESSION['email'];
$password = $_SESSION['password'];
if ($email != false && $password != false) {
  $sql = "SELECT * FROM tbl_users WHERE email = '$email'";
  $run_Sql = mysqli_query($con, $sql);
  if ($run_Sql) {
    $fetch_info = mysqli_fetch_assoc($run_Sql);
    $status = $fetch_info['status'];
    $code = $fetch_info['code'];
    if ($status == "1") {
      if ($code != 0) {
        header('Location: reset-code.php');
      }
    } else {
      header('Location: user-otp.php');
    }
  }
} else {
  header('Location: ../index.php');
}
?>
<?php
function asset($url)
{
  // $dir = 'http://localhost/helpdesk/backend/' . $url;
  // echo $dir;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Helpdesk</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
  <!-- icon -->
  <link rel="icon" href="../img/favicon.ico.png">
  <!-- JQUERY -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  <!-- DataTables CSS -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
  <!-- DataTables Buttons CSS -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.dataTables.min.css">

  <!-- Custom fonts for this template -->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">

  <!-- Custom styles for this page -->
  <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="style/style.css">
  <style>
    html,
    body {
      scroll-behavior: smooth;
    }

    /* Example media query for smaller screens */
    @media (max-width: 767px) {
      #title {
        display: none;
      }

      #card_tools {
        margin: 25px 50px 20px 20px;
      }

    }

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
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">


    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-primary navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item"> 
          <a class="nav-link" data-widget="pushmenu" href="#" role="button">
            <i class="fas fa-bars"></i>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-widget="" style="padding: 0;">
            <p style="color: white; font-size:26px; padding:0; margin:0;">Hi <?php echo $fetch_info['users_name'] ?>!</p>
          </a>
        </li>
      </ul>
      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">


        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="far fa-comments text-white"></i>
            <span class="badge badge-danger navbar-badge">3</span>
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="far fa-bell text-white"></i>
            <span class="badge badge-warning navbar-badge">15</span>
          </a>

        </li>
        <div id="title" class="col-sm">
          <h2 class="text-white">HELPDESK SYSTEM</h2>
        </div>
      </ul>
    </nav>
    <!-- /.navbar -->