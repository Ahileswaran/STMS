
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


// Function to initialize the Google Translate element
function googleTranslateElementInit() {
    console.log("Initializing Google Translate");
    new google.translate.TranslateElement({
        pageLanguage: 'en',
        includedLanguages: 'en,ta,si', // Add other languages as needed
        layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
        autoDisplay: false
    }, 'google_translate_element');
}


// Load the Google Translate script asynchronously
(function () {
    var gtScript = document.createElement('script');
    gtScript.type = 'text/javascript';
    gtScript.async = true;
    gtScript.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
    document.getElementsByTagName('head')[0].appendChild(gtScript);
})();        


// Function to change the language based on the dropdown selection
function changeLanguage(selectElement) {
    var selectedValue = selectElement.value;
    var languageCode;

    // Map selected values to Google Translate language codes
    switch (selectedValue) {
        case 'ta':
            languageCode = 'ta'; // தமிழ்
            break;
        case 'si':
            languageCode = 'si'; // Sinhala
            break;
        default:
            languageCode = 'en'; // සිංහල
            break;
    }

    // Set the Google Translate language
    google.translate.translatePage(languageCode);
}


//function for username suggestions
$(document).ready(function() {
    $('#username').on('input', function() {
        var username = $(this).val();
        $.ajax({
            url: 'check_username.php',
            type: 'post',
            data: {username: username},
            success: function(response) {
                $('#username-suggestions').html(response);
            }
        });
    });
});

