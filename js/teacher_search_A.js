$(document).ready(function() {
    // Load teachers with current filters
    function loadTeachers() {
        const search = $("#search_bar").val();
        const designation = $("#sortDesignation").val();
        
        $.ajax({
            url: "php/teacher_search_A.php",
            type: "GET",
            data: { 
                search: search,
                designation: designation
            },
            beforeSend: function() {
                $("#teacherTable").html('<tr><td colspan="6" class="text-center">Loading...</td></tr>');
            },
            success: function(response) {
                if(response.error) {
                    Swal.fire('Error', response.error, 'error');
                    return;
                }
                
                let html = '';
                if(response.data.length > 0) {
                    response.data.forEach(teacher => {
                        html += `<tr>
                            <td>${teacher.T_id}</td>
                            <td>${teacher.tName}</td>
                            <td>${teacher.phone}</td>
                            <td>${teacher.email}</td>
                            <td>${teacher.designation}</td>
                            <td class="action-btns">
                                <a href="teacher_view_A.php?t_id=${encodeURIComponent(teacher.T_id)}" class="btn btn-info btn-sm">View</a>
                                <a href="teacher_edit_A.php?t_id=${encodeURIComponent(teacher.T_id)}" class="btn btn-primary btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm delete-teacher-btn" data-tid="${teacher.T_id}">Delete</button>
                            </td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="6" class="text-center">No teachers found</td></tr>';
                }
                $("#teacherTable").html(html);
            },
            error: function(xhr) {
                Swal.fire('Error', 'Failed to load teachers', 'error');
            }
        });
    }

    // Initial load
    loadTeachers();

    // Event handlers
    $("#search_bar").on('keyup', function() {
        loadTeachers();
    });

    $("#sortDesignation").on('change', function() {
        loadTeachers();
    });
});