<?php  
$servername = "sql202.infinityfree.com";
$username = "if0_39191720"; 
$password = "JqlHPQjc3rGSk";  // Replace with your actual InfinityFree MySQL password
$database = "if0_39191720_depthub";

// Connect to database
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Student Complaints</title>
    
    <script src="https://kit.fontawesome.com/fa8acceed4.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="styles/nav3.css"/>
    <link rel="stylesheet" href="styles/complaints_admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />     

</head>
<?php include("includes/nav3.php") ?>

<body>

<h2 class="text-primary">STUDENTS COMPLAINTS</h2>

<div class="container">
    <div class="nav-container">
        <div class="tabs">
            <button class="tab active" onclick="showTab('pending')">Pending</button>
            <button class="tab" onclick="showTab('in_progress')">In Progress</button>
            <button class="tab" onclick="showTab('resolved')">Resolved</button>
        </div>
        <div class="sort-container">
        <div class="sort-group">
        <select id="filter-complaint-type" onchange="filterComplaints()">
           <option value="all">All</option>
        </select>

        <label for="filter-complaint-type">Complaint Type</label>
    </div>
    <div class="sort-group">
        <input type="date" id="from-date" onchange="filterComplaints()" placeholder=" ">
        <label for="from-date">From</label>
    </div>
    
    <div class="sort-group">
        <input type="date" id="to-date" onchange="filterComplaints()" placeholder=" ">
        <label for="to-date">To</label>
    </div>



    <div class="sort-group">
        <select id="sort-date" onchange="sortComplaints()">
            <option value="latest">Latest First</option>
            <option value="oldest">Oldest First</option>
        </select>
        <label for="sort-date">Sort by</label>
    </div>
</div>

    </div>
    
    <div class="tab-container">
        <div id="pending" class="tab-content active"></div>
        <div id="in_progress" class="tab-content"></div>
        <div id="resolved" class="tab-content"></div>
    </div>
</div>

<div id="delete-resolved-container" style="display: none; text-align: center; margin-top: 15px;">
    <button class="delete-btn" onclick="deleteResolvedComplaints()">🗑️ Delete Resolved Complaints</button>
</div>


<script>

let lastSelectedSort = "latest"; // Default sort
let lastFromDate = "";
let lastToDate = "";
let lastSelectedType = "all";

function sortComplaints() {
    let selectedSort = document.getElementById("sort-date").value;
    lastSelectedSort = selectedSort; // Store selected sort option

    document.querySelectorAll('.tab-content').forEach(tab => {
        let complaints = [...tab.querySelectorAll('.complaint-box')];
        let container = tab;

        let sorted = complaints.sort((a, b) => {
            let dateA = new Date(a.querySelector('.hidden-date').getAttribute('data-date'));
            let dateB = new Date(b.querySelector('.hidden-date').getAttribute('data-date'));
            return selectedSort === "latest" ? dateB - dateA : dateA - dateB;
        });

        container.innerHTML = "";
        sorted.forEach(box => container.appendChild(box));
    });
}

function filterComplaints() {
    let fromDate = new Date(document.getElementById("from-date").value);
    let toDate = new Date(document.getElementById("to-date").value);
    let selectedType = document.getElementById("filter-complaint-type").value.toLowerCase();

    document.querySelectorAll('.tab-content.active').forEach(tab => {
        let complaintsVisible = false;
        let complaintBoxes = tab.querySelectorAll('.complaint-box');
        let noComplaintsDiv = tab.querySelector(".no-complaints");

        // ✅ If "No Complaints" div doesn't exist, create it
        if (!noComplaintsDiv) {
            noComplaintsDiv = document.createElement("div");
            noComplaintsDiv.className = "no-complaints";
            noComplaintsDiv.style.textAlign = "center";
            noComplaintsDiv.style.marginTop = "20px";
            noComplaintsDiv.innerHTML = `
                <img src="./pic/no-complaints.svg" alt="No Complaints" style="width: 400px; opacity: 1;">
                <p style="color: #777; font-size: 16px;">
                    <i class="fas fa-exclamation-circle"></i> No complaints found.
                </p>`;
            tab.appendChild(noComplaintsDiv);
        }

        complaintBoxes.forEach(box => {
            let complaintDate = new Date(box.querySelector('.hidden-date').getAttribute('data-date'));
            let complaintType = box.querySelector('.complaint-type').textContent.trim().toLowerCase();

            let dateMatch = (isNaN(fromDate) || isNaN(toDate)) || 
                            (complaintDate >= fromDate && complaintDate <= toDate);
            let typeMatch = (selectedType === "all" || complaintType === selectedType);

            if (dateMatch && typeMatch) {
                box.style.display = 'flex';
                complaintsVisible = true;
            } else {
                box.style.display = 'none';
            }
        });

        // ✅ Show "No Complaints Found" message only if all complaints are hidden
        noComplaintsDiv.style.display = complaintsVisible ? "none" : "block";
    });
}

function resetFilters() {
    document.getElementById("sort-date").value = "latest";
    document.getElementById("from-date").value = "";
    document.getElementById("to-date").value = "";
    document.getElementById("filter-complaint-type").value = "all";

    lastSelectedSort = "latest";
    lastFromDate = "";
    lastToDate = "";
    lastSelectedType = "all";
}

function showTab(tab) {
    $('.tab-content').removeClass('active');
    $('#' + tab).addClass('active');
    $('.tab').removeClass('active');
    event.target.classList.add('active');

    if (tab === "resolved") {
        $("#delete-resolved-container").show();
    } else {
        $("#delete-resolved-container").hide();
    }

    // ✅ Reset filters and sorting when switching tabs
    resetFilters();
    loadComplaints();
}

function loadComplaints() {
    $.ajax({
        url: 'php/complaints/fetch_complaints.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            ["pending", "in_progress", "resolved"].forEach(status => {
                let container = $("#" + status);
                container.html("");

                if (data[status].length > 0) {
                    data[status].forEach(row => {
                        container.append(`
                            <div class="complaint-box">
                                <div class="complaint-type">${row.complaint_type}</div>
                                <div class="complaint-details">
                                    <p><strong>Name:</strong> ${row.stName}</p>
                                    <p><strong>Reg No:</strong> ${row.regno}</p>
                                    <p><i class="fas fa-clock"></i> ${row.complaint_date}</p>
                                    <span class="hidden-date" data-date="${row.complaint_date}"></span>
                                </div>
                                <a href="./view_complaint.php?id=${row.id}" class="view-btn">
                                    <i class="fas fa-eye"></i> View More
                                </a>
                            </div>
                        `);
                    });
                } else {
                    container.append(`
                        <div class="no-complaints" style="text-align: center; margin-top: 20px;">
                            <img src="./pic/no-complaints.svg" alt="No Complaints" style="width: 400px; opacity: 1;">
                            <p style="color: #777; font-size: 16px;">
                                <i class="fas fa-exclamation-circle"></i> No complaints found.
                            </p>
                        </div>
                    `);
                }
            });

            // ✅ Apply sorting and filtering after loading new data
            sortComplaints();
            filterComplaints();
        },
        error: function(xhr, status, error) {
            console.error("Error fetching complaints:", error);
        }
    });
}




function deleteResolvedComplaints() {
    Swal.fire({
        title: "Are you sure?",
        text: "This will delete all resolved complaints!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete all!",
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("php/complaints/delete_resolved.php", { method: "POST" })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        Swal.fire("Deleted!", data.message, "success");
                        loadComplaints(); // Refresh the complaints list
                    } else if (data.status === "info") {
                        Swal.fire("No Complaints", data.message, "info");
                    } else {
                        Swal.fire("Error", data.message, "error");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    Swal.fire("Error", "Something went wrong!", "error");
                });
        }
    });
}



    $(document).ready(function() {
        loadComplaints();
        setInterval(loadComplaints, 5000); // Refresh every 5 seconds
    });

    function loadComplaintTypes() {
    $.ajax({
        url: "php/complaints/fetch_complaints.php",
        method: "GET",
        data: { fetchTypes: true },
        dataType: "json",
        success: function(data) {
            let dropdown = $("#filter-complaint-type");
            dropdown.html('<option value="all">All</option>'); // Reset and add "All"

            data.forEach(type => {
                dropdown.append(`<option value="${type}">${type}</option>`);
            });
        },
        error: function(xhr, status, error) {
            console.error("Error fetching complaint types:", error);
        }
    });
}



// Load complaint types on page load
$(document).ready(function() {
    loadComplaintTypes();
});

</script>

</body>
</html>
