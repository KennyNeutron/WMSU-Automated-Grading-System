$(document).ready(function() {
    dataTable = $("#students").DataTable({
        dom: 'Brtp',
        responsive: true,
        fixedHeader: true,
        pageLength: 15,
        buttons: [{
            extend: 'pdf',
            split: ['excel', 'csv'],
        }],
        'columnDefs': [{
            'targets': [3],
            'orderable': false,
        }]
    });

    dataTable.buttons().container().appendTo($('#MyButtons'));

    var table = dataTable;
    var filter = createFilter(table, [1, 2]);

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
})