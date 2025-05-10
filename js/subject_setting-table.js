$(document).ready(function(){
  dataTable = $("#subject_setting1").DataTable({
    lengthChange: false,
    searching: false, 
    paging: false, 
    info: false,
    'columnDefs': [ {
      'targets': [3], /* column index */
      'orderable': false, /* true or false */
    }]
  });

  dataTable = $("#subject_setting2").DataTable({
    lengthChange: false,
    searching: false, 
    paging: false, 
    info: false,
    'columnDefs': [ {
      'targets': [3], /* column index */
      'orderable': false, /* true or false */
    }]
  });
})