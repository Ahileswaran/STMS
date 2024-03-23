
//Function to handle the image slider

$(document).ready(function () {
    const slider = $(".slider");
    const navButtons = $(".nav-btn");

    let currentIndex = 0;

    function updateSlider() {
        const translateValue = -currentIndex * 100;
        slider.css("transform", `translateX(${translateValue}%)`);

        navButtons.removeClass("active");
        navButtons.eq(currentIndex).addClass("active");
    }

    function nextSlide() {
        currentIndex = (currentIndex + 1) % 3;
        updateSlider();
    }

    function selectSlide(index) {
        currentIndex = index;
        updateSlider();
    }

    // Auto slide every 3 seconds
    setInterval(nextSlide, 3000);

    // Handle manual navigation
    navButtons.click(function () {
        const index = $(this).data("index");
        selectSlide(index);
    });

    //current year
    let year = new Date().getFullYear();
    document.getElementById("current-year").innerHTML= year;
});



//Function to handle the drop down menu

function redirect(selectElement) {
    var selectedValue = selectElement.value;
    if (selectedValue === "teachers_guide") {
        window.location.href = "pages/download_pages/teachers_guide.html";
    } else if (selectedValue === "syllabi") {
        window.location.href = "pages/download_pages/Syllabi_page.html";
    } else if (selectedValue === "resource_page") {
        window.location.href = "pages/download_pages/resource_page.html";
    }
}

