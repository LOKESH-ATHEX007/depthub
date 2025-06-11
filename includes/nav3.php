<div class="lokesh">
    <div class="logo"></div>
    <ul class="menu">
        <li class="active">
            <a href="dept_admin_dashboard.php">
                <i class="fa-solid fa-gauge"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="#">
                <i class="fa-solid fa-user"></i>
                <span>Students</span>
                <ul class="dropdown">
                    <li><a href="st_crud_A.php">Manage Students</a></li>
                </ul>
            </a>
        </li>
        <li>
            <a href="#">
                <i class="fa-solid fa-users"></i>
                <span>Teachers</span>
                <ul class="dropdown">
                    <li><a href="teacher_crud_A.php">Manage Teachers</a></li>
                    <li><a href="classTeacher.php">Allocate classes</a></li>
                </ul>
            </a>
        </li>
        <li>
            <a href="#">
                <i class="fa-solid fa-book"></i>
                <span>POST</span>
                <ul class="dropdown">
                    <li><a href="announce_A.php">Announcement</a></li>
                    <li><a href="internshipentry_A.php">Internship</a></li>
                </ul>
            </a>
        </li>
        <li>
            <a href="admin_scholarship_requests.php">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>Scholarship Requests</span>
            </a>
        </li>
        <li>
            <a href="complaints_admin.php">
                <i class="fa-solid fa-marker"></i>
                <span>Complaints</span>
            </a>
        </li>
        <li class="logout">
            <a href="php/logout1.php">
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
            // Prevent default only for # links
            if (link.getAttribute('href') === '#') {
                e.preventDefault();
            }
            
            // Get the parent li of clicked link
            const menuItem = link.parentElement;
            
            // Remove 'active' class from all menu items
            document.querySelectorAll('.menu > li').forEach((item) => {
                item.classList.remove('active');
            });

            // Add 'active' class to the parent li
            menuItem.classList.add('active');
        });
    });
</script>