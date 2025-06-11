<?php
$servername = "sql202.infinityfree.com";
$username = "if0_39191720"; 
$password = "JqlHPQjc3rGSk";  // Replace with your actual InfinityFree MySQL password
$database = "if0_39191720_depthub";
session_start();

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle fetching a single announcement for JSON response
if (isset($_GET['A_id'])) {
    $A_id = intval($_GET['A_id']); // Sanitize input

    try {
        $query = "SELECT * FROM announcements WHERE A_id = $A_id";
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
$categories_query = "SELECT DISTINCT category FROM announcements";
$categories_result = mysqli_query($conn, $categories_query);
if (!$categories_result) {
    die("Error fetching categories: " . mysqli_error($conn));
}


// Fetch announcements for the sidebar
$regno=$_SESSION['regno'];
$query = "SELECT currentYear, class FROM st1 WHERE regno =$regno";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Error fetching student details: " . mysqli_error($conn));
}
$student = mysqli_fetch_assoc($result);

$currentYear = null;
$class = null;
if ($student) {
    $currentYear = $student['currentYear'];
    $class = $student['class'];
}

$announcements_query = "
    SELECT A_id, title, createdAt, category,is_read 
    FROM announcements 
    WHERE  (sentTo = 'everyone' 
        OR sentTo = '$currentYear' 
        OR sentTo = '$currentYear$class') 
        AND (expiryDate IS NULL OR expiryDate >= CURDATE())
    ORDER BY createdAt DESC";

$announcements = mysqli_query($conn, $announcements_query);
if (!$announcements) {
    die("Error fetching announcements: " . mysqli_error($conn));
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>

    <link rel="stylesheet" href="styles/nav.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&display=swap">
    <link rel="stylesheet" href="styles/a7.css">
    <script src="https://kit.fontawesome.com/fa8acceed4.js" crossorigin="anonymous"></script>
    <style>
        
    </style>
</head>

<body>
    <?php include "includes/nav.php" ?>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Announcements</h2>
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
            <ul id="announcements-list">
                <?php while ($row = mysqli_fetch_assoc($announcements)) { ?>
                    <li class="announcement-item" data-id="<?php echo $row['A_id']; ?>" data-category="<?php echo htmlspecialchars($row['category']); ?>" data-createdat="<?php echo htmlspecialchars($row['createdAt']); ?>">
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p><?php echo htmlspecialchars($row['createdAt']); ?></p>
                        <?php if ($row['is_read'] == 0) { ?>
                <span class="unread-badge">New</span>
            <?php } ?>
                    </li>
                <?php } ?>
                <?php if (!mysqli_num_rows($announcements)) { ?> <li class="announcement-item"><h3>No announcements were posted to you</h3></li> <?php } ?>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="internship invisible">
           
            </div>
            <div class="vectorText">
                <h1>Select an announcement to view details</h1>
                 <img src="pic/announcements/3.png" alt="">
            </div>
        </div>
    </div>

    <script>
        const announcements = document.querySelectorAll('.announcement-item');
        const detailsWrapper = document.querySelector('.internship');
        const vectorText =document.querySelector('.vectorText');

        announcements.forEach(item => {
            item.addEventListener('click', () => {
                const A_id = item.getAttribute('data-id');
                fetch('?A_id=' + A_id)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('HTTP error! status: ' + response.status);
                        }
                        return response.text(); // Parse the response as text
                    })
                    .then(text => {
                        try {
                            const data = JSON.parse(text); // Attempt to parse the text as JSON

                            if (data.error) {
                                detailsWrapper.innerHTML = '<p>' + data.error + '</p>';
                            } else {
                                vectorText.classList.add("invisible")
                                detailsWrapper.classList.remove("invisible")
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
                            console.log('Response Text:', text); // Log the response text for debugging
                            detailsWrapper.innerHTML = '<p>Failed to load announcement details.</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        detailsWrapper.innerHTML = '<p>Failed to load announcement details.</p>';
                    });
            });
        });

        const categoryFilter = document.getElementById('category-filter');
        const startDateInput = document.getElementById('start-date');
        const endDateInput = document.getElementById('end-date');
        const dateFilterBtn = document.getElementById('date-filter-btn');

        categoryFilter.addEventListener('change', function() {
            const selectedCategory = this.value;
            filterAnnouncements();
        });

        dateFilterBtn.addEventListener('click', function() {
            filterAnnouncements();
        });

        function filterAnnouncements() {
            const selectedCategory = categoryFilter.value;
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;

            announcements.forEach(item => {
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
