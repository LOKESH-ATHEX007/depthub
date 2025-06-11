$(document).ready(function() {
    function loadStudents(search = '', year = '', classFilter = '') {
        $.ajax({
            url: "php/st_search_A.php",
            type: "GET",
            data: { 
                search: search,
                year: year, 
                class: classFilter
            },
            success: function(response) {
                $("#studentTable").html(response);
            },
            error: function(xhr) {
                Swal.fire('Error', 'Failed to load students', 'error');
            }
        });
    }

    // Initial load
    loadStudents();

    // Event handlers
    $("#search_bar").on('keyup', function() {
        loadStudents($(this).val(), $("#sortYear").val(), $("#sortClass").val());
    });

    $("#sortYear, #sortClass").on('change', function() {
        loadStudents($("#search_bar").val(), $("#sortYear").val(), $("#sortClass").val());
    });
});