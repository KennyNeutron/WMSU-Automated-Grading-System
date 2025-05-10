$(document).ready(function(){
  dataTable = $("#Student_grade").DataTable({
    dom: 'Brtp',
    responsive: true,
    fixedHeader: true,
    pageLength: 10,
    buttons: [
      {
        extend: 'excel',
        split: [ 'pdf', 'csv'],
      }
    ],
    'columnDefs': [ {
      'targets': [1,3,6,7], /* column index */
      'orderable': false, /* true or false */
    }]
  });

  dataTable.buttons().container().appendTo($('#MyButtons'));

  var table = dataTable;
  var filter = createFilter(table, [1,2,3]);

  function createFilter(table, columns) {
    var input = $('input#keyword').on("keyup", function() {
      table.draw();
    });
    
    $.fn.dataTable.ext.search.push(function(
      settings,
      searchData,
      index,
      rowData,
      counter
    ) {
      var val = input.val().toLowerCase();
  
      for (var i = 0, ien = columns.length; i < ien; i++) {
        if (searchData[columns[i]].toLowerCase().indexOf(val) !== -1) {
        return true;
        }
      }
  
      return false;
    });
    
    return input;
  }

  $('select#student-point_eqv').on('change', function(e){
    var status = $(this).val();
    dataTable.columns([5]).search(status).draw();
  });

  $('select#student-remark').on('change', function(e){
    var status = $(this).val();
    dataTable.columns([6]).search(status).draw();
  });
})

