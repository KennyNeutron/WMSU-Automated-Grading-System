$(document).ready(function(){
  dataTable = $("#home_table").DataTable({
    pageLength: 5,
    lengthChange: false, // Disable length change
    'columnDefs': [ {
      'targets': [1,2,3,4,6,7,8,9], /* column index */
      'orderable': false, /* true or false */
    }]
  });
});