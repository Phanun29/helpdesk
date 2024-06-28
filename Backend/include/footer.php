  <footer class="main-footer">
    <strong>Copyright &copy; 2023-2024 <span>PTT CAMOBODIA LIMITED</span></strong>
  </footer>
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Select2 JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
  <!-- Initialization script -->
  <script>
    // $(document).ready(function() {
    //   $('.select2').select2();
    // });
  </script>
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
  </div>
  <!-- ./wrapper -->
  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap -->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- Ekko Lightbox -->
  <script src="plugins/ekko-lightbox/ekko-lightbox.min.js"></script>
  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="plugins/jquery-ui/jquery-ui.min.js"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge('uibutton', $.ui.button)
  </script>
  <!-- Bootstrap 4 -->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- ChartJS -->
  <script src="plugins/chart.js/Chart.min.js"></script>
  <!-- Sparkline -->
  <script src="plugins/sparklines/sparkline.js"></script>
  <!-- JQVMap -->
  <script src="plugins/jqvmap/jquery.vmap.min.js "></script>
  <script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
  <!-- jQuery Knob Chart -->
  <script src="plugins/jquery-knob/jquery.knob.min.js"></script>
  <!-- daterangepicker -->
  <script src="plugins/moment/moment.min.js"></script>
  <script src="plugins/daterangepicker/daterangepicker.js"></script>
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
  <!-- Summernote -->
  <script src="plugins/summernote/summernote-bs4.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- App -->
  <script src="dist/js/adminlte.js"></script>
  <!--  demo purposes -->
  <script src="dist/js/demo.js"></script>
  <!-- dashboard demo (This is only for demo purposes) -->
  <script src="dist/js/pages/dashboard.js"></script>
  <script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>

  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
  <script>
    function showImage(images) {
      if (typeof images === 'string') {
        images = images.split(',');
      }

      let carouselInner = document.getElementById('carouselInner');
      carouselInner.innerHTML = '';

      images.forEach(function(imagePath, index) {
        let carouselItem = document.createElement('div');
        carouselItem.className = 'carousel-item' + (index === 0 ? ' active' : '');
        let img = document.createElement('img');
        img.src = imagePath;
        img.alt = 'Issue Image';
        img.className = 'd-block w-100';
        carouselItem.appendChild(img);
        carouselInner.appendChild(carouselItem);
      });

      $('#imageModal').modal('show');
    }
  </script>



  <script>
    function exportToCSV() {
      // Get table element
      var table = document.getElementById("myTable");

      // Initialize empty string to hold CSV content
      var csvContent = "";

      // Loop through each row in the table
      var rows = table.getElementsByTagName("tr");
      for (var i = 0; i < rows.length; i++) {
        var cells = rows[i].getElementsByTagName("td");
        // Loop through each cell in the row
        for (var j = 0; j < cells.length; j++) {
          // Append cell value to CSV content
          csvContent += cells[j].textContent.trim() + ",";
        }
        // Add newline character after each row
        csvContent += "\n";
      }

      // Create a blob object from CSV content
      var blob = new Blob([csvContent], {
        type: "text/csv;charset=utf-8"
      });

      // Create a temporary anchor element to trigger download
      var link = document.createElement("a");
      var url = URL.createObjectURL(blob);
      link.href = url;
      link.download = "table.csv";

      // Append anchor to document body and trigger download
      document.body.appendChild(link);
      link.click();

      // Cleanup
      setTimeout(function() {
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
      }, 0);
    }

    function exportToExcel() {
      // Get table HTML content
      var table = document.getElementById("myTable");
      var html = table.outerHTML;

      // Create a blob object from HTML content
      var blob = new Blob([html], {
        type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8"
      });

      // Create a temporary anchor element to trigger download
      var link = document.createElement("a");
      var url = URL.createObjectURL(blob);
      link.href = url;
      link.download = "table.xls";

      // Append the anchor to the document body and trigger the download
      document.body.appendChild(link);
      link.click();

      // Cleanup
      setTimeout(function() {
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
      }, 0);
    }

    function exportToPDF() {
      // Create new jsPDF instance
      const {
        jsPDF
      } = window.jspdf;
      const doc = new jsPDF();

      // Add header
      //doc.text("Ticket", 10, 10);

      // Use autoTable to export HTML table to PDF
      doc.autoTable({
        html: '#myTable',
        startY: 20,
        headStyles: {
          fillColor: [22, 160, 133], // Custom header background color
          textColor: [255, 255, 255], // Custom header text color
          fontStyle: 'bold', // Bold header text
          fontSize: 5,
          halign: 'center' // Center align header text
        },
        bodyStyles: {
          fillColor: [238, 238, 238], // Custom body background color
          textColor: [0, 0, 0], // Custom body text color
          fontSize: 5,
          halign: 'center' // Center align body text
        },
        alternateRowStyles: {
          fillColor: [255, 255, 255] // Alternate row background color
        },
        margin: {
          top: 30
        }, // Custom top margin
        theme: 'grid' // Grid theme
      });

      // Save PDF
      doc.save("table.pdf");
    }
  </script>


  <!-- show image -->
  <script>
    function showImage(imagePath) {
      $('#imageModal').modal('show');
      $('#modalImage').attr('src', imagePath);
    }
  </script>
  <!-- search ticket -->
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
  <!-- /.content-wrapper -->
  <script>
    $(document).ready(function() {
      // Initialize DataTables
      $('#myTable').DataTable({
        "paging": true,
        "lengthMenu": [10, 25, 50, 100],
        "searching": true,
        "ordering": true,
        "info": true
      });

      // Search functionality
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



    // Show image modal
    function showImage(imagePath) {
      $('#imageModal').modal('show');
      $('#modalImage').attr('src', imagePath);
    }
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

    function selectSuggestion(station_id) {
      // document.getElementById("station_id").value = station_id;
      document.getElementById("station_id").value = station_id;
      document.getElementById("suggestion_dropdown").innerHTML = "";
    }
  </script> -->
  <!-- image -->
  <script>
    function showImage(images) {
      if (typeof images === 'string') {
        images = images.split(',');
      }

      let carouselInner = document.getElementById('carouselInner');
      carouselInner.innerHTML = '';

      images.forEach(function(imagePath, index) {
        let carouselItem = document.createElement('div');
        carouselItem.className = 'carousel-item' + (index === 0 ? ' active' : '');
        let img = document.createElement('img');
        img.src = imagePath;
        img.alt = 'Issue Image';
        img.className = 'd-block w-100';
        carouselItem.appendChild(img);
        carouselInner.appendChild(carouselItem);
      });

      $('#imageModal').modal('show');
    }
  </script>
  </body>

  </html>