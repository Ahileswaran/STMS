// Function to handle the image slider
const items = document.querySelectorAll('.slider-item');
let currentItem = 0;

function showNextItem() {
    items[currentItem].classList.remove('active');
    currentItem = (currentItem + 1) % items.length;
    items[currentItem].classList.add('active');
}

setInterval(showNextItem, 3000); // Change image every 3 seconds

// Function to handle the drop down menu
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
        includedLanguages: 'en,ta,si',
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

    switch (selectedValue) {
        case 'ta':
            languageCode = 'ta'; // தமிழ்
            break;
        case 'si':
            languageCode = 'si'; // Sinhala
            break;
        default:
            languageCode = 'en'; // English
            break;
    }

    google.translate.translatePage(languageCode);
}

// Function for username suggestions
$(document).ready(function() {
    $('#username').on('input', function() {
        var username = $(this).val();
        $.ajax({
            url: 'check_username.php',
            type: 'post',
            data: { username: username },
            success: function(response) {
                $('#username-suggestions').html(response);
            }
        });
    });
});
