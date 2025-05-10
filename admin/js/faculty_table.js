$(document).ready(function () {
  // Initialize DataTable for Regular Lecturers
  var dataTableRegular = $("#main_faculty_regular").DataTable({
    dom: "Brtp",
    scrollX: true,
    pageLength: 10,
    buttons: [
      {
        remove: "true",
      },
    ],
    columnDefs: [
      {
        targets: [1, 3, 4, 5, 6, 8, 9], // Columns to disable sorting
        orderable: false,
      },
    ],
  });

  // Initialize DataTable for Visiting Lecturers
  var dataTableVisiting = $("#main_faculty_visiting").DataTable({
    dom: "Brtp",
    pageLength: 10,
    buttons: [
      {
        remove: "true",
      },
    ],
    columnDefs: [
      {
        targets: [1, 3, 4, 5, 6, 8, 9], // Columns to disable sorting
        orderable: false,
      },
    ],
  });

  function createFilter(table, inputSelector, columns) {
    var input = $(inputSelector).on("keyup", function () {
      table.draw();
    });

    $.fn.dataTable.ext.search.push(function (settings, searchData) {
      var val = $(inputSelector).val().toLowerCase();
      if (!val) return true; 

      for (var i = 0; i < columns.length; i++) {
        if (searchData[columns[i]].toLowerCase().includes(val)) {
          return true;
        }
      }
      return false;
    });

    return input;
  }

  createFilter(dataTableRegular, "#keyword", [1, 2]); 
  createFilter(dataTableVisiting, "#keyword1", [1, 2]);

  // Function to adjust columns when switching tabs
  $('a[data-bs-toggle="tab"]').on("shown.bs.tab", function () {
    // Adjust columns for all visible DataTables
    $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
  });

  // Filters for Regular Lecturers
  $("select#school_year").on("change", function () {
    var status = $(this).val();
    dataTableRegular.columns([7]).search(status).draw();
  });

  $("select#semester").on("change", function () {
    var status = $(this).val();
    dataTableRegular.columns([8]).search(status).draw();
  });

  // Filters for Visiting Lecturers
  $("select#school_year").on("change", function () {
    var status = $(this).val();
    dataTableVisiting.columns([7]).search(status).draw();
  });

  $("select#semester").on("change", function () {
    var status = $(this).val();
    dataTableVisiting.columns([8]).search(status).draw();
  });
});
