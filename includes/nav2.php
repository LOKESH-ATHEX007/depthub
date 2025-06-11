<div class="lokesh">
    <div class="logo"></div>
    <ul class="menu">
        <li class="active">
            <a href="trHomePage.php">
                <i class="fa-solid fa-gauge"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="#">
                <i class="fa-solid fa-user"></i>
                <span>Students</span>
                <ul class="dropdown">
                    <li><a href="st_crud_T.php">Manage Students</a></li>
                </ul>
            </a>
        </li>
        <li>
            <a href="#">
                <i class="fa-solid fa-book"></i>
                <span>POST</span>
                <ul class="dropdown">
                    <li><a href="announce.php">Announcement</a></li>
                    <li><a href="internshipentry.php">Internship</a></li>
                </ul>
            </a>
        </li>
        <li>
            <a href="view_scholarship_request.php">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>Scholarship Requests</span>
            </a>
        </li>
        <li>
            <a href="#">
                <i class="fa-solid fa-marker"></i>
                <span>Mark</span>
            </a>
        </li>
        <li>
            <a href="msg_board_all.php">
                <i class="fa-solid fa-users"></i>
                <span>Connect with</span>
                <ul class="dropdown">
                    <li><a href="tmsg_board1.php">Class Students</a></li>
                    <li><a href="msg_board_all.php">All Students</a></li>
                </ul>
            </a>
        </li>
        <li>
            <a href="#">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </li>
        <li class="logout">
            <a href="php/logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</div>

<script>
    // Select all anchor tags inside menu items
    const menuLinks = document.querySelectorAll('.menu > li > a');

    // Loop through each menu link
    menuLinks.forEach((link) => {
        link.addEventListener('click', (e) => {
            // Get the parent li of clicked link
            const menuItem = link.parentElement;
            
            // Remove 'active' class from all menu items
            document.querySelectorAll('.menu > li').forEach((item) => {
                item.classList.remove('active');
            });

            // Add 'active' class to the parent li
            menuItem.classList.add('active');
            
            // Prevent default only if it's a dropdown toggle (#) and not a real link
            if (link.getAttribute('href') === '#') {
                e.preventDefault();
            }
        });
    });
</script>