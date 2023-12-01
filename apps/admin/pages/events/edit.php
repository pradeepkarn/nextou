<?php
$event_detail = $context->event_detail;
$pd = obj($event_detail);
$catlist = $context->cat_list;
$employee_list = $context->employee_list;
$manager_list = $context->manager_list;
$managers = json_decode($pd->managers ?? '[]', true) ?? [];
$employees = json_decode($pd->employees ?? '[]', true) ?? [];
$meta_tags = null;
$meta_desc = null;
if ($pd->json_obj != "") {
    $jsn = json_decode($pd->json_obj);
    if (isset($jsn->meta->tags)) {
        $meta_tags = $jsn->meta->tags;
    }
    if (isset($jsn->meta->description)) {
        $meta_desc = $jsn->meta->description;
    }
}
?>
<script>
    function commonCallbackHandler(res) {
        if (res.success === true) {
            swalert({
                title: 'Success',
                msg: res.msg,
                icon: 'success'
            });
            location.reload();
        } else if (res.success === false) {
            swalert({
                title: 'Failed',
                msg: res.msg,
                icon: 'error'
            });
        } else {
            swalert({
                title: 'Failed',
                msg: 'Something went wrong',
                icon: 'error'
            });
        }
    }
</script>
<form action="/<?php echo home . route('eventUpdateAjax', ['id' => $pd->id]); ?>" id="update-new-event-form">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h5 class="card-title">Edit event</h5>
                </div>
                <div class="col text-end my-3">
                    <a class="btn btn-dark" href="/<?php echo home . route('eventList'); ?>">Back</a>
                </div>
            </div>
            <div id="res"></div>
            <div class="row">
                <div class="col-md-8">
                    <?php 
                    // $evnt = new Events_ctrl;
                    // $data = $evnt->generate_excel($content_id=$pd->id);
                    // myprint($data);
                    ?>
                    <h4>Title</h4>
                    <input type="text" name="title" value="<?php echo $pd->title; ?>" class="form-control my-3" placeholder="Title">
                    <h6>Slug</h6>
                    <input type="text" name="slug" value="<?php echo $pd->slug; ?>" class="hide form-control my-3" placeholder="slug">


                    <textarea class="tinymce-editor" name="content" id="mce_0" aria-hidden="true"><?php echo $pd->content; ?></textarea>
                    <!-- <h4>Tags</h4>
                    <textarea class="form-control" name="meta_tags" aria-hidden="true"><?php //echo $meta_tags; 
                                                                                        ?></textarea>
                    <h4>Meta description</h4>
                    <textarea class="form-control" name="meta_description" aria-hidden="true"><?php // echo $meta_desc; 
                                                                                                ?></textarea> -->
                    <hr>
                    <h3>Staffs: </h3>
                    <hr>
                    <b>Managers:</b>
                    <ol>
                        <?php foreach ($manager_list as $key => $emp) :
                            $emp = obj($emp);
                        ?>
                            <?php echo in_array($emp->id, $managers) ? "<li>{$emp->id} - {$emp->first_name} {$emp->last_name}</li>"  : null; ?>
                        <?php endforeach; ?>
                    </ol>
                    <b>Employees:</b>
                    <ol>
                        <?php
                        $emp = null;
                        foreach ($employee_list as $key => $emp) :
                            $emp = obj($emp);
                        ?>
                            <?php
                            // if (!in_array($emp->id, $managers)) {
                                echo in_array($emp->id, $employees) ? "<li>{$emp->id} - {$emp->first_name} {$emp->last_name}</li>"  : null;
                            // }
                            ?>
                        <?php endforeach;
                        $emp = null;
                        ?>
                    </ol>
                </div>
                <div class="col-md-4">
                    <h4>Banner</h4>
                    <input accept="image/*" id="image-input" type="file" name="banner" class="form-control my-3">
                    <img style="width:100%; max-height:300px; object-fit:contain;" id="banner" src="/<?php echo MEDIA_URL; ?>/images/pages/<?php echo $pd->banner; ?>" alt="<?php echo $pd->banner; ?>">
                    <div id="image-container"></div>
                    <button type="button" class="btn btn-secondary text-white mt-2" id="add-image">Images <i class="bi bi-plus"></i> </button>
                    <hr>
                    <?php
                    $imgs = get_image_list($pd->imgs);
                    $moreimgcount = count($imgs);

                    // myprint($imgs);
                    ?>
                    <!-- <h4>Total more images count <?php //echo $moreimgcount; 
                                                        ?> </h4> -->
                    <div style="max-height: 200px; overflow-y:scroll; background-color: rgba(0,0,0,0.2);">
                        <?php
                        foreach ($imgs as $key => $img) { ?>
                            <button style="margin: 10px;" type="button" class="btn btn-danger delete-more-img" data-img-src="<?php echo $img; ?>" data-content-id="<?php echo $pd->id; ?>">Delete <i class="bi bi-arrow-down"></i></button>
                            <img style="width: 100%; padding: 10px;" src="/<?php echo MEDIA_URL; ?>/images/pages/<?php echo $img; ?>" alt="<?php echo $pd->title; ?>">
                            <hr>
                        <?php } ?>
                    </div>

                    <div class="dropdown my-3 d-grid">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="managerDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            Select Managers
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="managerDropdown">
                            <?php
                            $emp = null;
                            foreach ($manager_list as $key => $emp) :
                                $emp = obj($emp);
                                // if (!in_array($emp->id, $managers)) {
                            ?>
                                <li class="px-2 py-2">
                                    <div class="form-group">
                                        <label for="employee1">
                                            <input <?php echo in_array($emp->id, $managers) ? 'checked' : null; ?> name="managers[]" type="checkbox" value="<?php echo $emp->id; ?>" class="form-check-input" id="manager<?php echo $emp->id; ?>"> <?php echo "{$emp->first_name} {$emp->last_name}"; ?>
                                        </label>
                                    </div>
                                </li>
                            <?php
                            // } 
                            endforeach;
                            $emp = null;
                            ?>
                        </ul>
                    </div>
                    <div class="dropdown my-3 d-grid">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="employeeDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            Select Employees
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="employeeDropdown">
                            <?php
                            $emp = null;
                            foreach ($employee_list as $key => $emp) :
                                $emp = obj($emp);
                                // if (!in_array($emp->id, $employees)) {
                            ?>
                                <li class="px-2 py-2">
                                    <div class="form-group">
                                        <label for="employee1">
                                            <input <?php echo in_array($emp->id, $employees) ? 'checked' : null; ?> name="employees[]" type="checkbox" value="<?php echo $emp->id; ?>" class="form-check-input" id="employee<?php echo $emp->id; ?>"> <?php echo "{$emp->first_name} {$emp->last_name}"; ?>
                                        </label>
                                    </div>
                                </li>
                            <?php
                            // } 
                            endforeach;
                            $emp = null;
                            ?>
                        </ul>
                    </div>
                   
                    <h4>Event Date</h4>
                    <input type="date" name="event_date" value="<?php echo $pd->event_date; ?>" class="form-control my-3">
                    <h4>Start at</h4>
                    <input type="time" name="event_time" value="<?php echo $pd->event_time; ?>" class="form-control my-3">

                    <h4>Address</h4>
                    <textarea name="address" class="form-control my-3"><?php echo $pd->address; ?></textarea>


                    <h4>City</h4>
                    <input type="text" name="city" class="form-control my-3" value="<?php echo $pd->city; ?>" placeholder="City">

                    <div class="d-grid">
                        <button id="update-event-btn" type="button" class="btn btn-primary my-3">Update</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</form>
<script>
    window.onload = () => {
        const imageInputevent = document.getElementById('image-input');
        const imageevent = document.getElementById('banner');
        imageInputevent.addEventListener('change', (event) => {
            const file = event.target.files[0];
            const fileReader = new FileReader();

            fileReader.onload = () => {
                imageevent.src = fileReader.result;
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
</script>
<script>
    $(document).ready(function() {
        // Attach a click event handler to the cat-items
        $('.delete-more-img').on('click', function() {
            var imgSrc = $(this).data('img-src');
            var contentId = $(this).data('content-id');
            // Make an AJAX request to the server
            $.ajax({
                url: '/<?php echo home . route('eventDeleteMoreImgAjax'); ?>', // Replace with your server URL
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
</script>
<script>
    $(document).ready(function() {
        $('#add-image').on('click', function() {
            // Create a new image input field
            var newInput = '<input accept="image/*" type="file" name="moreimgs[]" class="form-control my-3">';
            $('#image-container').append(newInput);
        });
    });
</script>
<?php pkAjax_form("#update-event-btn", "#update-new-event-form", "#res"); ?>
<!-- for review -->

<?php send_to_server_wotf("#add-review-btn", ".review-data-send", "commonCallbackHandler",  route('addReviewAjax', ['rg' => 'event'])); ?>