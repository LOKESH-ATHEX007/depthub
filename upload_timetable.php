<?php
session_start();
require_once "php/conn.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Timetable</title>
    <link rel="stylesheet" href="styles/nav3.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
                * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: rgb(255, 255, 255);
            height: 100vh;
            max-height: 100vh;
        }
        
        .timetable-container {
            display: flex;
            align-items: flex-start;
            width: calc(100% - 120px);
            margin-left: 120px;
            gap: 20px;
        }
        
        /* Form Styling */
        .upload-form {
            width: 320px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            gap: 10px;
            height: 100%;
            max-height: 100%;
            background-color: aliceblue;
        }
        
        .upload-form h3 {
            text-align: center;
            margin-bottom: 15px;
            color: #333;
        }
        
        .upload-form label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }
        
        .upload-form select,
        .upload-form input,
        .upload-form button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        
        .upload-form button {
            background: #007bff;
            color: white;
            cursor: pointer;
            font-weight: bold;
            margin-top: 15px;
            border: none;
        }
        
        .upload-form button:hover {
            background: #0056b3;
        }
        
        /* Table Styling */
        .timetable-list {
            flex-grow: 1;
            max-width: 75%;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }
        
        .timetable-list h2 {
            margin-bottom: 10px;
            text-align: center;
        }
        
        .table-container {
            max-height: 400px;
            overflow-y: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background: #007bff;
            color: white;
            text-align: center;
        }
        
        tr:hover {
            background: #f9f9f9;
        }
        
        .action-btn {
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 5px;
            border: none;
            margin: 0 5px;
        }
        
        .view-btn {
            background: #28a745;
            color: white;
        }
        
        .view-btn:hover {
            background: #218838;
        }
        
        .delete-btn {
            background: #dc3545;
            color: white;
        }
        
        .delete-btn:hover {
            background: #c82333;
        }
        
        /* Modal styling */
        .modal-dialog {
            max-width: 80%;
        }
        
        .modal-body img {
            max-height: 70vh;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <?php require "includes/nav3.php" ?>
    
    <div class="timetable-container">
        <form id="uploadForm" class="upload-form">
            <h3>Upload Timetable</h3>
            
            <label for="currentYear">Current Year:</label>
            <select name="currentYear" id="currentYear" class="form-select" required>
                <option value="">Select Year</option>
            </select>

            <label for="class">Class:</label>
            <select name="class" id="class" class="form-select" required>
                <option value="">Select Class</option>
            </select>

            <label for="timetableImage">Timetable Image:</label>
            <input type="file" name="timetableImage" id="timetableImage" class="form-control" required>

            <button type="submit" class="btn btn-primary">Upload</button>
        </form>

        <div class="timetable-list">
            <h2>Uploaded Timetables</h2>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Class</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="timetableList">
                        <!-- Records loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for Viewing Image -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Timetable Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid" alt="Timetable Image">
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            loadTimetables();
            fetchClassOptions();

            function fetchClassOptions() {
                $.ajax({
                    url: "php/timetable/timetable_upload.php",
                    type: "GET",
                    data: { fetch_classes: true },
                    dataType: "json",
                    success: function(data) {
                        var yearDropdown = $("#currentYear");
                        var classDropdown = $("#class");
                        yearDropdown.empty().append('<option value="">Select Year</option>');
                        classDropdown.empty().append('<option value="">Select Class</option>');

                        let uniqueYears = new Set();
                        let uniqueClasses = new Set();

                        $.each(data, function(index, item) {
                            uniqueYears.add(item.currentYear);
                            uniqueClasses.add(item.class);
                        });

                        uniqueYears.forEach(year => {
                            yearDropdown.append(`<option value="${year}">Year ${year}</option>`);
                        });

                        uniqueClasses.forEach(cls => {
                            classDropdown.append(`<option value="${cls}">Class ${cls}</option>`);
                        });
                    }
                });
            }

            $("#uploadForm").on("submit", function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: "php/timetable/timetable_upload.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.trim() === "success") {
                            Swal.fire("Success", "Timetable uploaded successfully!", "success");
                            $("#uploadForm")[0].reset();
                            loadTimetables();
                        } else {
                            Swal.fire("Upload Failed", response, "error");
                        }
                    }
                });
            });

            function loadTimetables() {
                $.ajax({
                    url: "php/timetable/fetch_timetables.php",
                    type: "GET",
                    success: function(data) {
                        $("#timetableList").html(data);
                    }
                });
            }

            $(document).on("click", ".view-btn", function() {
                var imgSrc = $(this).data("img");
                $("#modalImage").attr("src", imgSrc);
                $("#imageModal").modal("show");
            });

            $(document).on("click", ".delete-btn", function() {
                var id = $(this).data("id");

                Swal.fire({
                    title: "Are you sure?",
                    text: "This timetable will be deleted!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    confirmButtonText: "Delete"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post("php/timetable/delete_timetable.php", { id: id }, function(response) {
                            if (response.trim() === "success") {
                                Swal.fire("Deleted!", "Timetable has been deleted.", "success");
                                loadTimetables();
                            } else {
                                Swal.fire("Error", "Failed to delete timetable.", "error");
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>