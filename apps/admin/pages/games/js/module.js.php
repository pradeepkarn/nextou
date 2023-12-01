<script>
    window.onload = () => {

        const imageInputgame = document.getElementById('image-input');
        const imagegame = document.getElementById('banner');

        imageInputgame.addEventListener('change', (event) => {
            const file = event.target.files[0];
            const fileReader = new FileReader();

            fileReader.onload = () => {
                imagegame.src = fileReader.result;
            };

            fileReader.readAsDataURL(file);
        });

        // for slug
        const titleInput = document.querySelector('input[name="slug"]');
        const slugInput = document.querySelector('input[name="slug"]');
        if (titleInput && slugInput) {
            titleInput.addEventListener('keyup', () => {
                const title = titleInput.value.trim();
                generateSlug(title, slugInput);
            });
        }
    }



    $(document).ready(function() {
        // Attach a click event handler to the cat-items
        $('.delete-more-img').on('click', function() {
            var imgSrc = $(this).data('img-src');
            var contentId = $(this).data('content-id');
            // Make an AJAX request to the server
            $.ajax({
                url: '/<?php echo home . route('gameDeleteMoreImgAjax'); ?>', // Replace with your server URL
                type: 'POST', // You can change this to 'GET' if needed
                data: {
                    content_id: contentId,
                    img_src: imgSrc
                }, // Send the cat_id to the server
                success: function(res) {
                    if (res.success === true) {
                        alert(res.msg);
                        location.reload();
                    } else if (res.success === false) {
                        alert(res.msg);
                    } else {
                        alert("Something went wrong");
                    }
                },
                error: function(error) {
                    console.error('AJAX error:', error);
                }
            });
        });
    });


    $(document).ready(function() {
        $('#add-image').on('click', function() {
            // Create a new image input field
            var newInput = '<input accept="image/*" type="file" name="moreimgs[]" class="form-control my-3">';
            $('#image-container').append(newInput);
        });
    });
</script>