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
});
