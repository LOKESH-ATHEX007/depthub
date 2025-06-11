<?php
include "session_start.php";


if (!isset($_SESSION["user_email"]) || $_SESSION["user_type"] !== "student") {
    header("Location: index.php");
    exit();
}

include "db.php";
$student_email = $_SESSION["user_email"];
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Chat - Dept Hub</title>
    <link rel="stylesheet" href="styles/contact.css">


</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Teachers</h2>
            <!-- <input type="text" id="search" placeholder="Search teachers..." onkeyup="searchTeachers()"> -->
           
            <!-- From Uiverse.io by Smit-Prajapati --> 

<div class="search-container">
  <input class="input" placeholder="Reg no, Name, Email " id="search" type="text"  onkeyup="searchTeachers()">
  <svg viewBox="0 0 24 24" class="search__icon">
    <g>
      <path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z">
      </path>
    </g>
  </svg>
</div>




<ul class="teacher-list" id="teacher-list">
    <?php
    $student_email = $_SESSION["user_email"];
    $dept_query = "SELECT department FROM st1 WHERE email = ?";
    $dept_stmt = $conn->prepare($dept_query);
    $dept_stmt->bind_param("s", $student_email);
    $dept_stmt->execute();
    $dept_result = $dept_stmt->get_result();
    $student_dept = $dept_result->fetch_assoc()['department'];

$query = "SELECT t.T_id, t.email, t.tName, 
          (SELECT MAX(timestamp) FROM messages 
           WHERE (sender_email = t.email AND receiver_email = ?) 
              OR (sender_email = ? AND receiver_email = t.email)) AS last_message_time,
          (SELECT COUNT(*) FROM messages 
           WHERE sender_email = t.email AND receiver_email = ? AND is_read = 0) AS unread_count
          FROM teachers t
          WHERE t.department = '$student_dept' /* Directly inserted */
          ORDER BY unread_count DESC, last_message_time DESC, T_id ASC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $_SESSION["user_email"], $_SESSION["user_email"], $_SESSION["user_email"]);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo "<li class='teacher-item' 
                data-id='" . $row["T_id"] . "' 
                data-email='" . $row["email"] . "' 
                onclick='selectTeacher(\"" . $row["email"] . "\", \"" . addslashes($row["tName"]) . "\")'>
                <span class='teacher-name'>" . $row["tName"] . "</span>
                <span class='unread-badge' id='unread-" . $row["email"] . "'>" . 
                ($row["unread_count"] > 0 ? $row["unread_count"] : "") . 
                "</span>
            </li>";
    }
    ?>
</ul>



<img id="arrow-down" src="pic/arrow.png" alt="arrow down icon">
            <a href="logout.php" class="logout">Logout</a>
        </div>

        <div class="chat-box">
            <div class="chat-header">
                <span id="chat-with">Select Teacher </span>
            </div>
            <div class="chat-messages" id="chat-box">
                <img class="msg-box-vector floating-image" src="pic/select-user.svg" alt="">
                <h1 class="chat-h1">Select a Teacher </h1>
            </div>
            <div class="chat-input" id="chat-input-section" style="display: none;">
                <input type="hidden" id="receiver_email">
                <input type="text" id="message" placeholder="Type a message...">
                <input type="file" id="file">
               
<button id="send-btn" onclick="sendMessage()">
  <div class="svg-wrapper-1">
    <div class="svg-wrapper">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        width="24"
        height="24"
      >
        <path fill="none" d="M0 0h24v24H0z"></path>
        <path
          fill="currentColor"
          d="M1.946 9.315c-.522-.174-.527-.455.01-.634l19.087-6.362c.529-.176.832.12.684.638l-5.454 19.086c-.15.529-.455.547-.679.045L12 14l6-8-8 6-8.054-2.685z"
        ></path>
      </svg>
    </div>
  </div>
  <span>Send</span>
</button>

            </div>
        </div>
    </div>

    <script>

    function selectTeacher(email, name) {
        document.getElementById("chat-box").innerHTML = "";
        lastMessageId = 0;
        document.getElementById("receiver_email").value = email;
        document.getElementById("chat-with").innerText = " " + name;
        document.getElementById("chat-input-section").style.display = "flex";
        loadMessages();

        fetch(`mark_messages_read.php?sender=${encodeURIComponent(email)}`)
        .then(() => {
            document.getElementById(`unread-${email}`).style.display = "none"; // Hide unread badge
        })
        .catch(error => console.error("Error marking messages as read:", error));
    }



let lastMessageId = 0;

function loadMessages() {
  
    let receiver = document.getElementById("receiver_email").value;
    if (!receiver) return;

    fetch("	fetch_messages.php?receiver=" + encodeURIComponent(receiver) + "&lastMessageId=" + lastMessageId)
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
            <img class="msg-box-vector floating-image" src="pic/student-chat-vector.svg" alt="No Messages">
            <p class="chat-p"  > No messages yet.</p>
            <h1 class="chat-h1" > Feel free to ask your teacher any questions!</h1>
        `;
                }
        return; 
    }

            // Process new messages (those with ID > lastMessageId)
            data.forEach(msg => {
                if (parseInt(msg.id) > lastMessageId) {
                    lastMessageId = Math.max(lastMessageId, parseInt(msg.id));
                    hasNewMessages = true;

                    let messageClass = (msg.sender_email === "<?php echo $student_email; ?>") ? "sent" : "received";
                   

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

    fetch("	process_message.php", { method: "POST", body: formData })
        .then(response => response.json())
        .then(result => {
            if (result.status === "success") {
                fileInput.value = "";
                messageInput.value = "";
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
setInterval(fetchUnreadMessages, 5000);



// Function to delete a message
function deleteMessage(id) {
    if (confirm("Are you sure you want to delete this message?")) {
        fetch("remove_msg.php", {
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
    addActiveClass(".teacher-list", "teacher-item", "active-item");
  
});



function searchTeachers() {
    let input = document.getElementById("search").value.toLowerCase();
    let teacherItems = document.querySelectorAll(".teacher-item");

    teacherItems.forEach(item => {
        let teacherName = item.textContent.toLowerCase();
        let teacherEmail = item.getAttribute("data-email").toLowerCase();
        let teacherId = item.getAttribute("data-id").toLowerCase();

        if (teacherName.includes(input) || teacherEmail.includes(input) || teacherId.includes(input)) {
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
    let list = document.getElementById("teacher-list");
    let arrow = document.getElementById("arrow-down"); 

    function checkOverflow() {
        if (list.scrollHeight > list.clientHeight) {
            arrow.style.display = "block"; 
        } else {
            arrow.style.display = "none"; 
        }
    }

    function checkScrollPosition() {
        let isAtBottom = list.scrollTop + list.clientHeight >= list.scrollHeight - 1; 
        if (isAtBottom) {
            arrow.src = "pic/arrow-up.png"; 
        } else {
            arrow.src = "pic/arrow.png";
        }
    }

    // Initial check on page load
    checkOverflow();

    // Listen for scrolling
    list.addEventListener("scroll", checkScrollPosition);

    // Scroll functionality on arrow click
    arrow.addEventListener("click", function () {
        let isAtBottom = list.scrollTop + list.clientHeight >= list.scrollHeight - 1;
        if (!isAtBottom) {
            list.scrollBy({ top: 100, behavior: "smooth" });
        } else {
            list.scrollBy({ top: -100, behavior: "smooth" });
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