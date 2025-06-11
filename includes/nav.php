
<nav class="re_nav">
        <ul class="re_sidebar">
            <li class="re_xmark_icon"><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="48px" fill="#5f6368"><path d="m249-207-42-42 231-231-231-231 42-42 231 231 231-231 42 42-231 231 231 231-42 42-231-231-231 231Z"/></svg></li>
            <li><a id="first_a" href="stHomepage1.php">Home</a></li>
            <li><a href="a7.php">Announcements</a></li>
            <li><a href="i7.php">Internships</a></li>
            <li><a href="contact.html">Contact</a></li>
            <li><a href="complaint.php">Complaint</a></li>
        </ul>
        <ul class="re_sidebarone">
            <li><a id="brandname" href="">DEPT HUB</a></li>
            <li class="re_removable"><a href="stHomepage1.php">Home</a></li>
            <li class="re_removable"><a href="a7.php">Announcements</a></li>
            <li class="re_removable"><a href="i7.php">Internships</a></li>
            <li class="re_removable"><a href="contact.html">Contact</a></li>
            <li class="re_removable"><a href="complaint.php">Complaint</a></li>
            <li class="re_menu_icon"><i class="fa-solid fa-bars fa-2xl" style="color: #ffffff;"></i></li>
        </ul>
    </nav>

    <script>
      
let re_menu_icon=document.querySelector(".re_menu_icon")
let re_xmark_icon=document.querySelector(".re_xmark_icon")
let re_sidebar=document.querySelector(".re_sidebar")

re_menu_icon.addEventListener("click",()=>{
   
  re_sidebar.classList.add("re_show_sidebar")
})

re_xmark_icon.addEventListener("click",()=>{
   
    // re_sidebar.style.transform="translateX(20rem)"
    re_sidebar.classList.remove("re_show_sidebar")
})

document.addEventListener("click", (e) => {
    if (!re_sidebar.contains(e.target) && !re_menu_icon.contains(e.target)) {
        re_sidebar.classList.remove("re_show_sidebar");
    }
});

    </script>