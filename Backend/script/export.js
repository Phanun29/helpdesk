
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
    doc.text("Ticket", 10, 10);

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
