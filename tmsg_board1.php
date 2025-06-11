<?php
include "session_start.php";

// Ensure the session has user details and the user is a teacher
if (!isset($_SESSION["user_email"]) || $_SESSION["user_type"] !== "teacher") {
    header("Location: index.php");
    exit();
}

include "db.php";
$teacher_email = $_SESSION["user_email"];
$T_id = $_SESSION["T_id"];

// Get teacher's assigned class
$stmt = $conn->prepare("SELECT class, currentYear FROM class_teacher_allocation WHERE T_id = ?");
$stmt->bind_param("s", $T_id);
$stmt->execute();
$classResult = $stmt->get_result();
$classData = $classResult->fetch_assoc();

// Add this right after getting the class allocation
$dept_query = "SELECT department FROM teachers WHERE T_id = ?";
$dept_stmt = $conn->prepare($dept_query);
$dept_stmt->bind_param("s", $T_id);
$dept_stmt->execute();
$dept_result = $dept_stmt->get_result();
$teacher_dept = $dept_result->fetch_assoc()['department'];


// Check if the teacher is assigned to a class
$hasAssignedClass = $classData ? true : false;

if ($hasAssignedClass) {
    $class = $classData['class'];
    $currentYear = $classData['currentYear'];

    // Fetch students from the teacher's class with unread message count and last message time
// Change the query to include department filter
$query = "SELECT s.regno, s.email, s.stName, 
          (SELECT MAX(timestamp) FROM messages 
           WHERE (sender_email = s.email AND receiver_email = ?) 
              OR (sender_email = ? AND receiver_email = s.email)) AS last_message_time,
          (SELECT COUNT(*) FROM messages 
           WHERE sender_email = s.email AND receiver_email = ? AND is_read = 0) AS unread_count
          FROM st1 s
          WHERE s.class = ? 
            AND s.currentYear = ?
            AND s.department = '$teacher_dept'
          ORDER BY unread_count DESC, last_message_time DESC, regno ASC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $_SESSION["user_email"], $_SESSION["user_email"], $_SESSION["user_email"], $class, $currentYear);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Chat - Dept Hub</title>
    <link rel="stylesheet" href="styles/contact.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Students</h2>

            <div class="search-container">
                <input class="input" placeholder="Reg no, Name, Email " id="search" type="text" onkeyup="searchTeachers()">
                <svg viewBox="0 0 24 24" class="search__icon">
                    <g>
                        <path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"></path>
                    </g>
                </svg>
            </div>

            <ul class="student-list" id="student-list">
    <?php if ($hasAssignedClass) { ?>
        <?php if ($result->num_rows > 0) { ?>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <li class='student-item' 
                    data-regno='<?php echo $row["regno"]; ?>' 
                    data-email='<?php echo $row["email"]; ?>' 
                    onclick='selectStudent("<?php echo $row["email"]; ?>", "<?php echo addslashes($row["stName"]); ?>")'>
                    
                    <span class='student-name'><?php echo $row["stName"]; ?></span>
                    
                    <span class='unread-badge' id='unread-<?php echo $row["email"]; ?>'>
                        <?php echo ($row["unread_count"] > 0) ? $row["unread_count"] : ""; ?>
                    </span>
                </li>
            <?php } ?>
        <?php } else { ?>
            <!-- Message when no students are found -->
            <li class="no-students-message">
                <img src="pic/no-students.svg" alt="No students" class="no-students-icon">
                <p>No students found in your class.</p>
                <p>Please check with administration if this is unexpected.</p>
            </li>
        <?php } ?>
    <?php } else { ?>
        <!-- Existing message for teachers without an assigned class -->
        <li class="no-class-message">
            <p>You are not assigned to any class.</p>
            <br>
            <p>Please use Department connect page to communicate with students.</p>
        </li>
    <?php } ?>
</ul>

            <img id="arrow-down" src="pic/arrow.png" alt="arrow down icon">
            <a href="trHomepage.php" class="logout">Exit</a>
        </div>

        <div class="chat-box">
            <div class="chat-header">
                <span id="chat-with">Select Student</span>
            </div>
            <div class="chat-messages" id="chat-box">
                <?php if ($hasAssignedClass) { ?>
                    <img class="msg-box-vector floating-image" src="pic/select-user.svg" alt="">
                    <h1 class="chat-h1">Select a Student</h1>
                <?php } else { ?>
                    <img class="msg-box-vector floating-image" src="pic/no-class.svg" alt="">
                    <h1 class="chat-h1">No Class Assigned</h1>
                <?php } ?>
            </div>
            <div class="chat-input" id="chat-input-section" style="display: none;">
                <input type="hidden" id="receiver_email">
                <input type="text" id="message" placeholder="Type a message...">
                <input type="file" id="file">
                <button id="send-btn" onclick="sendMessage()">
                    <div class="svg-wrapper-1">
                        <div class="svg-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path fill="currentColor" d="M1.946 9.315c-.522-.174-.527-.455.01-.634l19.087-6.362c.529-.176.832.12.684.638l-5.454 19.086c-.15.529-.455.547-.679.045L12 14l6-8-8 6-8.054-2.685z"></path>
                            </svg>
                        </div>
                    </div>
                    <span>Send</span>
                </button>
            </div>
        </div>
    </div>


    <script>

    function selectStudent(email, name) {
    document.getElementById("chat-box").innerHTML = "";
    lastMessageId = 0;
    document.getElementById("receiver_email").value = email;
    document.getElementById("chat-with").innerText = "" + name;
    document.getElementById("chat-input-section").style.display = "flex";
    loadMessages();

    fetch(`mark_messages_read.php?sender=${encodeURIComponent(email)}`)
        .then(() => {
            document.getElementById(`unread-${email}`).style.display = "none"; // Hide unread badge
        })
        .catch(error => console.error("Error marking messages as read:", error));
}

document.addEventListener("keydown", function(event) {
    if (event.key === "Enter") {
        console.log("Enter key pressed!");
        // Perform some action
    }
});


let lastMessageId = 0;

function loadMessages() {
    let receiver = document.getElementById("receiver_email").value;
    if (!receiver) return;

    fetch("get_chats.php?receiver=" + encodeURIComponent(receiver) + "&lastMessageId=" + lastMessageId)
        .then(response => response.json())
        .then(data => {
            let chatBox = document.getElementById("chat-box");
            let hasNewMessages = false;
            
            // Get all current message IDs from the response
            let currentMessageIds = data.map(msg => parseInt(msg.id));
            
            // Remove messages that no longer exist (were deleted)
            document.querySelectorAll(".message").forEach(el => {
                let msgId = parseInt(el.getAttribute("data-id"));
                if (!currentMessageIds.includes(msgId)) {
                    el.remove();
                }
            });
            
           
            if (currentMessageIds.length === 0) {
                if (!document.querySelector(".msg-box-vector")) { 
        chatBox.innerHTML = `
            <img class="msg-box-vector floating-image" src="pic/teacher-chat-vector.svg" alt="No Messages">
            <p class="chat-p" id="teacher-chat-p">No messages yet. </p>
            <h1 class="chat-h1" id="teacher-chat-h1" >Start the conversation by sending a message to your student!</h1>

        `;
                }
        return; 
    }

            // Process new messages (those with ID > lastMessageId)
            data.forEach(msg => {
                if (parseInt(msg.id) > lastMessageId) {
                    lastMessageId = Math.max(lastMessageId, parseInt(msg.id));
                    hasNewMessages = true;

                    
                     let messageClass = (msg.sender_email === "<?php echo $teacher_email; ?>") ? "sent" : "received";

                    let fileDisplay = "";
                    if (msg.file_path) {
                        if (msg.file_path.match(/\.(jpg|jpeg|png|gif)$/i)) {
                            fileDisplay = `<br><img src="${msg.file_path}" alt="Image" style="max-width:200px; border-radius:5px;" onclick="window.open('${msg.file_path}', '_blank')">`;
                        } else {
                            fileDisplay = `<br><a href="${msg.file_path}" download>Download File</a>`;
                        }
                    }

                    let messageTime = new Date(msg.timestamp).toLocaleString();

                    if (!document.querySelector(`.message[data-id='${msg.id}']`)) {
    let messageElement = document.createElement("div");
    messageElement.className = `message ${messageClass}`;
    messageElement.setAttribute("data-id", msg.id);

    // Different class for sent and received messages
    let timeClass = messageClass === "sent" ? "message-time-sent" : "message-time-received";

    messageElement.innerHTML = `
        <div class="message-content">
            ${msg.message} ${fileDisplay}
            <div class="${timeClass}">${messageTime}</div> <!-- ✅ Different time class -->
        </div>
        ${messageClass === "sent" ? `
        <div class="dropdown">
            <button class="dropdown-btn" onclick="toggleDropdown(this)">⋮</button>
            <div class="dropdown-content">
                <button onclick="deleteMessage(${msg.id})">Delete</button>
                <button onclick="copyMessage('${msg.message}')">Copy</button>
            </div>
        </div>` : ''}
    `;

    chatBox.appendChild(messageElement);
}

                }
            });

            if (hasNewMessages) {
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        })
        .catch(error => console.error("Fetch error:", error));
}


document.addEventListener("keydown", function(event) {
    if (event.key === "Enter") {
        let activeElement = document.activeElement;
        
        // Prevent file dialog from reopening
        if (activeElement.tagName === "INPUT" && activeElement.type === "file") {
            event.preventDefault(); // Prevent file dialog re-trigger
        }

        // Call sendMessage only if not inside the file input OR if a file is selected
        let messageInput = document.getElementById("message").value.trim();
        let fileInput = document.getElementById("file").files.length > 0;

        if (messageInput || fileInput) {
            sendMessage();
        }
    }
});

function sendMessage() {
    let receiver = document.getElementById("receiver_email").value;
    let messageInput = document.getElementById("message");
    let fileInput = document.getElementById("file");

    if (!receiver) {
        alert("Please select a user to chat with.");
        return;
    }

    let message = messageInput.value.trim();
    let file = fileInput.files[0];

    if (!message && !file) {
        alert("Cannot send an empty message.");
        return;
    }

    let formData = new FormData();
    formData.append("receiver", receiver);
    formData.append("message", message);
    if (file) formData.append("file", file);

    fetch("send_chat.php", { method: "POST", body: formData })
        .then(response => response.json())
        .then(result => {
            if (result.status === "success") {
                messageInput.value = "";
                fileInput.value = "";
                document.querySelectorAll(".msg-box-vector, .chat-p, .chat-h1").forEach(el => el.remove());
                loadMessages();
            } else {
                alert("Error: " + result.message);
            }
        })
        .catch(error => console.error("Fetch error:", error));
}
function fetchUnreadMessages() {
    fetch("fetch_unread_messages.php")
        .then(response => response.json())
        .then(unreadCounts => {
            document.querySelectorAll(".teacher-item, .student-item").forEach(item => {
                let email = item.getAttribute("data-email");
                let unreadBadge = document.getElementById(`unread-${email}`);

                if (unreadCounts[email]) {
                    unreadBadge.innerText = unreadCounts[email];
                    unreadBadge.style.display = "inline-block";

                    // Move user to the top of the list if they have unread messages
                    item.parentNode.prepend(item);
                } else {
                    unreadBadge.style.display = "none";
                }
            });
        })
        .catch(error => console.error("Unread messages fetch error:", error));
}

// Fetch unread messages every 5 seconds
setInterval(fetchUnreadMessages, 1000);

// Function to delete a message
function deleteMessage(id) {
    if (confirm("Are you sure you want to delete this message?")) {
        fetch("delete_chat.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "id=" + encodeURIComponent(id)
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === "success") {
                document.querySelector(`.message[data-id='${id}']`).remove();
            } else {
                alert("Error: " + result.message);
            }
        })
        .catch(error => console.error("Fetch error:", error));
    }
}
document.addEventListener("DOMContentLoaded", function () {
    function addActiveClass(listSelector, itemClass, activeClass) {
        document.querySelectorAll(listSelector + " ." + itemClass).forEach(item => {
            item.addEventListener("click", function () {
                // Remove active class from all items within the same list
                document.querySelectorAll(listSelector + " ." + itemClass).forEach(el => el.classList.remove(activeClass));

                // Add active class to the clicked item
                this.classList.add(activeClass);
            });
        });
    }

    // Apply to both teacher and student lists
  
    addActiveClass(".student-list", "student-item", "active-item");
});

  

function searchStudents() {
    let input = document.getElementById("search").value.toLowerCase();
    let studentItems = document.querySelectorAll(".student-item");

    studentItems.forEach(item => {
        let studentName = item.textContent.toLowerCase();
        let studentEmail = item.getAttribute("data-email").toLowerCase();
        let studentRegno = item.getAttribute("data-regno").toLowerCase();

        if (studentName.includes(input) || studentEmail.includes(input) || studentRegno.includes(input)) {
            item.style.display = "";
        } else {
            item.style.display = "none";
        }
    });
}

// Function to copy a message
function copyMessage(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert("Message copied to clipboard");
    });
}

// Function to toggle dropdown menu
function toggleDropdown(button) {
    let dropdown = button.nextElementSibling;

    // Close all other dropdowns before opening the new one
    document.querySelectorAll(".dropdown-content").forEach(d => {
        if (d !== dropdown) d.style.display = "none";
    });

    // Toggle visibility
    let isVisible = dropdown.style.display === "block";
    dropdown.style.display = isVisible ? "none" : "block";

    // Change button icon based on dropdown state
    button.innerText = isVisible ? "⋮" : "✖";
}

// Close dropdown when clicking outside
document.addEventListener("click", function (event) {
    document.querySelectorAll(".dropdown-content").forEach(dropdown => {
        if (!dropdown.contains(event.target) && !dropdown.previousElementSibling.contains(event.target)) {
            dropdown.style.display = "none";
            dropdown.previousElementSibling.innerText = "⋮"; // Reset icon to default
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    let studentList = document.getElementById("student-list");
    let studentArrow = document.getElementById("arrow-down"); 
    function checkStudentOverflow() {
        if (studentList.scrollHeight > studentList.clientHeight) {
            studentArrow.style.display = "block"; 
        } else {
            studentArrow.style.display = "none"; 
        }
    }

    function checkStudentScrollPosition() {
        let isAtBottom = studentList.scrollTop + studentList.clientHeight >= studentList.scrollHeight - 1; 
        if (isAtBottom) {
            studentArrow.src = "pic/arrow-up.png"; 
        } else {
            studentArrow.src = "pic/arrow.png"; 
        }
    }

    // Initial check on page load
    checkStudentOverflow();

    // Listen for scrolling
    studentList.addEventListener("scroll", checkStudentScrollPosition);

    // Scroll functionality on arrow click
    studentArrow.addEventListener("click", function () {
        let isAtBottom = studentList.scrollTop + studentList.clientHeight >= studentList.scrollHeight - 1;
        if (!isAtBottom) {
            studentList.scrollBy({ top: 100, behavior: "smooth" });
        } else {
            studentList.scrollBy({ top: -100, behavior: "smooth" });
        }
    });
});



// Auto-refresh messages every 2 seconds
let isChatActive = true;
document.addEventListener("visibilitychange", function () {
    isChatActive = !document.hidden;
});

setInterval(() => {
    if (isChatActive) {
        loadMessages();
    }
}, 2000);
</script>

</body>
</html>
