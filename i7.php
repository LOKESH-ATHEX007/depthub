<?php
session_start();
$servername = "sql202.infinityfree.com";
$username = "if0_39191720"; 
$password = "JqlHPQjc3rGSk";  // Replace with your actual InfinityFree MySQL password
$database = "if0_39191720_depthub";



// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle fetching a single announcement for JSON response
if (isset($_GET['I_id'])) {
    $I_id = intval($_GET['I_id']); // Sanitize input

    try {
        $query = "SELECT * FROM internships WHERE I_id = $I_id";
        $result = mysqli_query($conn, $query);

        if ($result && $row = mysqli_fetch_assoc($result)) {
            // Output only the selected announcement details as JSON
            header('Content-Type: application/json');
            echo json_encode([
                'title' => htmlspecialchars($row['title']),
                'sentBy' => htmlspecialchars($row['sentBy']),
                'createdAt' => htmlspecialchars($row['createdAt']),
                'message' => nl2br(htmlspecialchars($row['message'])),
                'link' => htmlspecialchars($row['link']),
                'filePath' => htmlspecialchars($row['filePath']),
            ]);
        } else {
            throw new Exception("No announcement found.");
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit; // Stop further execution
}

// Fetch distinct categories
$categories_query = "SELECT DISTINCT category FROM internships";
$categories_result = mysqli_query($conn, $categories_query);
if (!$categories_result) {
    die("Error fetching categories: " . mysqli_error($conn));
}

// Get student's department and class/year info
$regno = $_SESSION['regno'];
$query = "SELECT s.currentYear, s.class, s.department 
          FROM st1 s
          WHERE s.regno = $regno";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Error fetching student details: " . mysqli_error($conn));
}
$student = mysqli_fetch_assoc($result);

$currentYear = null;
$class = null;
$department = null;
if ($student) {
    $currentYear = $student['currentYear'];
    $class = $student['class'];
    $department = $student['department'];
}

// Fetch internships for the sidebar with department filtering
// Fetch internships for the sidebar with strict department filtering
$internships_query = "
    SELECT I_id, title, createdAt, category, is_read 
    FROM internships 
    WHERE dept_id = '$department' 
      AND (sentTo = 'everyone' 
          OR sentTo = '$currentYear' 
          OR sentTo = '$currentYear$class') 
      AND (expiryDate IS NULL OR expiryDate >= CURDATE())
    ORDER BY createdAt DESC";

$internships = mysqli_query($conn, $internships_query);
if (!$internships) {
    die("Error fetching internships: " . mysqli_error($conn));
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>internships</title>
    <link rel="stylesheet" href="styles/i7.css">
    <link rel="stylesheet" href="styles/nav.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&display=swap">
    <script src="https://kit.fontawesome.com/fa8acceed4.js" crossorigin="anonymous"></script>
    <style>
        
    </style>
</head>

<body>
    <?php include "includes/nav.php" ?>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>internships</h2>
            <div class="sortingDiv">
                <label for="category-filter">Filter by Category:</label>
                <select id="category-filter">
                    <option value="all">All</option>
                    <?php while ($cat_row = mysqli_fetch_assoc($categories_result)) { ?>
                        <option value="<?php echo htmlspecialchars($cat_row['category']); ?>"><?php echo htmlspecialchars($cat_row['category']); ?></option>
                    <?php } ?>
                </select>
                <div class="date-inputs">
                    <div>
                        <label for="start-date">Start Date:</label>
                        <input type="date" id="start-date">
                    </div>
                    <div>
                        <label for="end-date">End Date:</label>
                        <input type="date" id="end-date">
                    </div>
                </div>
                <button id="date-filter-btn">Filter by Date</button>
            </div>
            <ul id="internships-list">
                <?php while ($row = mysqli_fetch_assoc($internships)) { ?>
                    <li class="announcement-item" data-id="<?php echo $row['I_id']; ?>" data-category="<?php echo htmlspecialchars($row['category']); ?>" data-createdat="<?php echo htmlspecialchars($row['createdAt']); ?>">
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p><?php echo htmlspecialchars($row['createdAt']); ?></p>
                        <?php if ($row['is_read'] == 0) { ?>
                          <span class="unread-badge">New</span>
                        <?php } ?>
                    </li>
                <?php } ?>
                <?php if (!mysqli_num_rows($internships)) { ?> <li class="announcement-item"><h3>No Internships were posted</h3></li> <?php } ?>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="internship invisible">
       
            </div>
            <div class="vectorText">
                <h1>Select an Internship to view details</h1>
                 <img src="pic/announcements/a.png" alt="">
            </div>
        </div>
    </div>

    <script>
        const internships = document.querySelectorAll('.announcement-item');
        const detailsWrapper = document.querySelector('.internship');
        const vectorText =document.querySelector('.vectorText');



        internships.forEach(item => {
    item.addEventListener('click', () => {
        const I_id = item.getAttribute('data-id');

        // Mark the internship as read
        fetch('php/fetch/mark_internship_as_read.php?I_id=' + I_id)
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP error! status: ' + response.status);
                }
                return response.text();
            })
            .then(text => {
                console.log("Response Text:", text);
                try {
                    const data = JSON.parse(text);
                    if (data.status === 'success') {
                        // Remove the "New" badge
                        const badge = item.querySelector('.unread-badge');
                        if (badge) {
                            badge.remove();
                        }

                        // Refresh the unread count
                        fetchUnreadInternshipCount();
                    }
                } catch (error) {
                    console.error('Error parsing JSON:', error);
                }
            })
            .catch(error => {
                console.error('Error marking internship as read:', error);
            });

        // Fetch and display the internship details
        fetch('?I_id=' + I_id)
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP error! status: ' + response.status);
                }
                return response.text();
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);

                    if (data.error) {
                        detailsWrapper.innerHTML = '<p>' + data.error + '</p>';
                    } else {
                        vectorText.classList.add("invisible");
                        detailsWrapper.classList.remove("invisible");
                        detailsWrapper.innerHTML = `
                            <h3 id="title">${data.title}</h3>
                            <p><strong>From:</strong> ${data.sentBy}</p>
                            <p><strong>Created At:</strong> ${data.createdAt}</p>
                            <div class="messageWrapper">${data.message}</div>
                            ${data.link ? `<a class="link button" href="${data.link}" target="_blank">Visit Link</a>` : ''}
                            ${data.filePath ? `<a class="file-download button" href="${data.filePath}" download>Download File</a> <a class="file-download button" href="${data.filePath}" target="_blank">View File</a>` : ''}
                        `;
                    }
                } catch (error) {
                    console.error('Error parsing JSON:', error);
                    console.log('Response Text:', text);
                    detailsWrapper.innerHTML = '<p>Failed to load internship details.</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                detailsWrapper.innerHTML = '<p>Failed to load internship details.</p>';
            });
    });
});
        const categoryFilter = document.getElementById('category-filter');
        const startDateInput = document.getElementById('start-date');
        const endDateInput = document.getElementById('end-date');
        const dateFilterBtn = document.getElementById('date-filter-btn');

        categoryFilter.addEventListener('change', function() {
            const selectedCategory = this.value;
            filterinternships();
        });

        dateFilterBtn.addEventListener('click', function() {
            filterinternships();
        });

        function filterinternships() {
            const selectedCategory = categoryFilter.value;
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;

            internships.forEach(item => {
                const itemCategory = item.getAttribute('data-category');
                const itemCreatedAt = item.getAttribute('data-createdat');

                let categoryMatch = (selectedCategory === 'all' || itemCategory === selectedCategory);
                let dateMatch = true;

                if (startDate && endDate) {
                    const itemDate = new Date(itemCreatedAt);
                    dateMatch = (itemDate >= new Date(startDate) && itemDate <= new Date(endDate));
                } else if (startDate) {
                    const itemDate = new Date(itemCreatedAt);
                    dateMatch = (itemDate >= new Date(startDate));
                } else if (endDate) {
                    const itemDate = new Date(itemCreatedAt);
                    dateMatch = (itemDate <= new Date(endDate));
                }

                if (categoryMatch && dateMatch) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }
            </script>

</body>

</html>
