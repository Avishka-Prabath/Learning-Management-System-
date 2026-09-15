document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("courseSearchInput");
    const categorySelect = document.getElementById("categorySelect");
    const courseItems = document.querySelectorAll(".course-item");
    const noResults = document.getElementById("noResults");

    function filterCourses() {
        const query = searchInput.value.toLowerCase().trim();
        const selectedCategory = categorySelect.value;
        let visibleCount = 0;

        courseItems.forEach(item => {
            const text = item.innerText.toLowerCase();
            const itemCategory = item.getAttribute("data-category");

            const matchesSearch = text.includes(query);
            const matchesCategory = (selectedCategory === "all" || itemCategory === selectedCategory);

            if (matchesSearch && matchesCategory) {
                // Use '' (default) instead of block so the Bootstrap grid doesn't break
                item.style.display = ""; 
                visibleCount++;
            } else {
                item.style.display = "none";
            }
        });

        if (noResults) {
            if (visibleCount === 0) {
                noResults.classList.remove("d-none");
            } else {
                noResults.classList.add("d-none");
            }
        }
    }

    if (searchInput) searchInput.addEventListener("input", filterCourses);
    if (categorySelect) categorySelect.addEventListener("change", filterCourses);
});