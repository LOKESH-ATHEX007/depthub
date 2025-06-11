<?php
$servername = "sql202.infinityfree.com";
$username = "if0_39191720"; 
$password = "JqlHPQjc3rGSk";
$database = "if0_39191720_depthub";

// Database connection
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$events = [];
$result = $conn->query("SELECT date, dayorder, events FROM day");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[$row['date']] = [
            'dayorder' => $row['dayorder'],
            'event' => $row['events']
        ];
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Day Order Calendar</title>
  <link rel="stylesheet" href="styles/nav.css">
  <link rel="stylesheet" href="styles/calendar.css">

  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
  <script src="https://kit.fontawesome.com/fa8acceed4.js" crossorigin="anonymous"></script>
  <style>
    /* Include your full CSS here as in your previous code */
    body {
      background: url("pic/day-order-bg.jpg") no-repeat center center/cover;
      min-height: 100vh;
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      overflow: hidden;
    }

    /* Add the rest of your CSS here... */
    /* chatbot-container, modal, calendar, etc. */
  </style>
</head>
<body>
<?php include "components/nav.php"; ?>

<div class="chatbot-container">
  <div class="chatbot-header">
    <form id="calendarForm" method="GET">
      <label for="year">Year:</label>
      <select id="year" name="year" onchange="updateCalendar()">
        <?php
        $currentYear = date('Y');
        for ($y = $currentYear - 5; $y <= $currentYear + 5; $y++) {
            $selected = ($_GET['year'] ?? date('Y')) == $y ? 'selected' : '';
            echo "<option value='$y' $selected>$y</option>";
        }
        ?>
      </select>

      <label for="month">Month:</label>
      <select id="month" name="month" onchange="updateCalendar()">
        <?php
        for ($m = 1; $m <= 12; $m++) {
            $monthName = date('F', mktime(0, 0, 0, $m, 1));
            $selected = ($_GET['month'] ?? date('m')) == $m ? 'selected' : '';
            echo "<option value='$m' $selected>$monthName</option>";
        }
        ?>
      </select>
    </form>
  </div>

  <div class="chatbot-body">
    <div class="weekdays">
      <div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div><div>Sun</div>
    </div>
    <div class="calendar">
      <?php
      $selectedYear = $_GET['year'] ?? date('Y');
      $selectedMonth = $_GET['month'] ?? date('m');
      $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear);
      $firstDay = date('N', strtotime("$selectedYear-$selectedMonth-01"));

      for ($blank = 1; $blank < $firstDay; $blank++) {
          echo "<div class='day'></div>";
      }

      for ($day = 1; $day <= $daysInMonth; $day++) {
          $date = "$selectedYear-" . str_pad($selectedMonth, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
          $isHighlighted = isset($events[$date]);
          $dayorder = $isHighlighted ? $events[$date]['dayorder'] : 'N/A';
          $event = $isHighlighted ? $events[$date]['event'] : 'No Events';
          $class = $isHighlighted ? "day highlight" : "day";
          $clickFnAttribute = $isHighlighted ? "onclick='showDetails(this)' data-dayOrder='$dayorder' data-day='$day' data-events='$event'" : "";
          echo "<div class='$class' $clickFnAttribute><strong>$day</strong></div>";
      }
      ?>
    </div>
  </div>

  <div class="chatbot-footer" style="padding: 1rem; background-color: aliceblue; color: black;">
    <div style="display:flex; align-items:center;">
      <p>Clickable</p>
      <div style="width:1rem; height:1rem; background-color:rgb(127,127,255); margin-left:0.5rem; border-radius:3px;"></div>
    </div>
    <div style="display:flex; align-items:center;">
      <p>Not clickable</p>
      <div style="width:1rem; height:1rem; background-color:black; margin-left:0.5rem; border-radius:3px;"></div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="overlay" id="overlay"></div>
<div class="modal" id="modal">
  <div class="modal-header">
    <h2 id="modal-header">Date Details</h2>
    <button class="close-btn" id="closeModalBtn">&times;</button>
  </div>
  <div class="modal-body">
    <p id="modal-day-order"></p>
    <p id="modal-events"></p>
  </div>
</div>

<script>
  function updateCalendar() {
    const year = document.getElementById('year').value;
    const month = document.getElementById('month').value;
    window.location.href = `?year=${year}&month=${month}`;
  }

  function showDetails(el) {
    const day = el.getAttribute("data-day");
    const dayOrder = el.getAttribute("data-dayOrder");
    const events = el.getAttribute("data-events");

    document.getElementById("modal-header").textContent = `Date: ${day}`;
    document.getElementById("modal-day-order").textContent = `Day Order: ${dayOrder}`;
    document.getElementById("modal-events").textContent = `Events: ${events}`;

    document.getElementById("modal").style.display = "block";
    document.getElementById("overlay").style.display = "block";
  }

  document.getElementById("closeModalBtn").onclick = () => {
    document.getElementById("modal").style.display = "none";
    document.getElementById("overlay").style.display = "none";
  };

  document.getElementById("overlay").onclick = () => {
    document.getElementById("modal").style.display = "none";
    document.getElementById("overlay").style.display = "none";
  };
</script>
</body>
</html>
