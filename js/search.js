const searchBar = document.querySelector("#search_bar");

searchBar.onkeyup = ()=>{
    let searchTerm = searchBar.value;
    
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/stSearch.php", true);
    xhr.onload = ()=>{
        if(xhr.readyState === XMLHttpRequest.DONE){
            if(xhr.status === 200){
                let data = xhr.response;
                document.querySelector("#tbody").innerHTML = data;
            }
        }
    }

    xhr.setRequestHeader("content-type", "application/x-www-form-urlencoded");
    xhr.send("searchTerm=" + searchTerm);
}





document.querySelector("#tbody").addEventListener("click", function (event) {
    if (event.target.classList.contains("delete-btn")) {
        let regno = event.target.dataset.regno;
        console.log("Delete button clicked! RegNo:", regno);  // Debugging

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to undo this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                console.log("Sending delete request for regno:", regno);  // Debugging
                fetch("php/delete.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "regno=" + regno,
                })
                .then(response => {
                    console.log("Response received:", response);  // Debugging
                    return response.json();
                })
                .then(data => {
                    console.log("Data received:", data);  // Debugging
                    if (data.status === "success") {
                        event.target.closest("tr").remove(); // Remove row from table
                        Swal.fire("Deleted!", "Student has been removed.", "success");
                    } else {
                        Swal.fire("Error!", data.message, "error");
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);  // Debugging
                    Swal.fire("Error!", "Failed to delete student.", "error");
                });
            }
        });
    }
});

