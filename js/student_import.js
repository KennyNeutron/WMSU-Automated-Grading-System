$(document).ready(function() {
    var dataTable = $('#data-table').DataTable();

    // Event listener for file input change
    $('#csv-file').on('change', function(event) {
        var file = event.target.files[0];
        var reader = new FileReader();

        reader.onload = function(e) {
            var csv = e.target.result;
            Papa.parse(csv, {
                header: true,
                complete: function(results) {
                    var data = results.data;

                    // Clear existing DataTable
                    dataTable.clear();

                    // Generate dropdowns for field selection
                    var headers = results.meta.fields;
                    var dropdownsHTML = '';
                    headers.forEach(function(header) {
                        dropdownsHTML += '<label for="' + header + '">' + header + ': </label>';
                        dropdownsHTML += '<select id="' + header + '" class="field-dropdown">';
                        dropdownsHTML += '<option value="">-- Select --</option>';
                        headers.forEach(function(column) {
                            dropdownsHTML += '<option value="' + column + '">' + column + '</option>';
                        });
                        dropdownsHTML += '</select><br>';
                    });
                    $('#field-dropdowns').html(dropdownsHTML);

                    // Event listener for dropdown change
                    $('.field-dropdown').on('change', function() {
                        populateDataTable(results.data);
                    });

                    // Populate DataTable with default column selections
                    populateDataTable(data);
                }
            });
        };

        // Read CSV file
        reader.readAsText(file);
    });

    // Function to populate DataTable based on field selections
    function populateDataTable(data) {
        var selectedFields = {};
        $('.field-dropdown').each(function() {
            var field = $(this).attr('id');
            var value = $(this).val();
            selectedFields[field] = value;
        });

        var dataTableData = data.map(function(row) {
            var rowData = [];
            Object.keys(selectedFields).forEach(function(field) {
                rowData.push(row[selectedFields[field]]);
            });
            return rowData;
        });

        // Clear existing DataTable
        dataTable.clear();

        // Add rows to the DataTable
        dataTableData.forEach(function(row) {
            dataTable.row.add(row);
        });

        // Draw DataTable
        dataTable.draw();
    }
});