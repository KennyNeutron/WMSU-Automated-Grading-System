$(document).ready(function(){
  dataTable = $("#subject-info").DataTable({
    dom: 'Brtp',
    scrollX: true,
    pageLength: 10,
    buttons: [
      {
        extend: 'excel',
        split: [ 'pdf', 'csv'],
      }
    ],
    'columnDefs': [ {
        'targets': [1,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22], /* column index */
        'orderable': false, /* true or false */
    }]
});

  dataTable.buttons().container().appendTo($('#MyButtons'));

  var table = dataTable;
  var filter = createFilter(table, [1,2,3,23]);

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
    dataTable.columns([24]).search(status).draw();
  });
})

