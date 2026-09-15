document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("studentSearchInput");
    const filterBtns = document.querySelectorAll(".filter-btn");
    const studentRows = document.querySelectorAll(".student-row");
    const noResults = document.getElementById("noResults");

    let currentStatusFilter = "all";

    function filterStudents() {
        const query = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;

        studentRows.forEach(row => {
            const studentId = row.querySelector(".student-id") ? row.querySelector(".student-id").textContent.toLowerCase() : "";
            const studentName = row.querySelector(".student-name") ? row.querySelector(".student-name").textContent.toLowerCase() : "";
            const rowStatus = row.getAttribute("data-status");

            const matchesSearch = studentId.includes(query) || studentName.includes(query);
            const matchesStatus = (currentStatusFilter === "all") || (rowStatus === currentStatusFilter);

            if (matchesSearch && matchesStatus) {
                row.classList.remove("d-none");
                visibleCount++;
            } else {
                row.classList.add("d-none");
            }
        });

        if (visibleCount === 0) {
            noResults.classList.remove("d-none");
        } else {
            noResults.classList.add("d-none");
        }
    }

    // Real-time Instant Search Input Event
    if (searchInput) {
        searchInput.addEventListener("input", filterStudents);
    }

    // Status Filter Tabs Click Event
    filterBtns.forEach(btn => {
        btn.addEventListener("click", function () {
            filterBtns.forEach(b => b.classList.remove("active"));
            this.classList.add("active");

            currentStatusFilter = this.getAttribute("data-status");
            filterStudents();
        });
    });
});