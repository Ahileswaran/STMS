// Function to handle adding a profile picture
$(document).ready(function () {
    $('#add_pic').click(function () {
        $('#file_input').click(); 
    });

    $('#file_input').change(function () {
        var file = this.files[0]; 
        if (file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#upload_pic').attr('src', e.target.result); 
            };
            reader.readAsDataURL(file); 
        }
    });
});