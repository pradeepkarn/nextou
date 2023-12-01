<?php
$createData = $context;
$catlist = $context->cat_list;
?>
<!-- <form action="<?php echo BASEURI.route('uploadGameViaCsvAjax'); ?>" id="uploadcsvform">
    <div class="card">
        <div class="card-body">
        <div class="row">
                <div class="col-12">
                <h4>Import bulk</h4>
                <div id="resupload" style="max-height: 100px; overflow-y:scroll;"></div>
                </div>
                <div class="col">
                    <input type="file" accept=".csv" name="csvfile" class="form-control">
                </div>
                <div class="col">
                    <select name="game_id" class="form-select">
                        <option value="">--Select game--</option>
                        <?php foreach ($catlist as  $cv) {
                            $cv = obj($cv);
                        ?>
                            <option value="<?php echo $cv->id; ?>"><?php echo $cv->title; ?></option>
                        <?php } ?>
                        <?php ?>
                    </select>
                </div>
                <div class="col">
                    <button id="uploadcsvbtn" class="btn btn-primary">Import</button>
                </div>
                <div class="col">
                    <h5 id="upload-info">Pleaase wait while uploading ...</h5>
                </div>
                <div class="col">
                    <a href="<?php echo BASEURI; ?>/data/csv/games.csv" download>Download Sample CSV</a>
                </div>
                <?php 
                ajaxActive("#upload-info");
                pkAjax_form("#uploadcsvbtn","#uploadcsvform","#resupload");
                ?>
            </div>
        </div>
    </div>
</form> -->
<form action="/<?php echo home . route('gameStoreAjax'); ?>" id="save-new-page-form">
    <div class="card">
        <div class="card-body">
           
            <div class="row">
                <!-- <div class="col">
                    <h5 class="card-title">Or add game manually</h5>
                </div> -->
                <div class="col text-end my-3">
                    <a class="btn btn-dark" href="/<?php echo home . route('gameListByGame',['game_id'=>$req->game_id]); ?>">Back</a>
                </div>
            </div>
            <div id="res"></div>

            <div class="row">
                <div class="col-md-8">
                    <!-- <h4>Title</h4> -->
                    <!-- <input type="text" name="title" class="form-control my-3" placeholder="Title"> -->
                    <!-- <h6>Slug</h6> -->
                    <!-- <input type="text" name="slug" class="form-control my-3" placeholder="slug"> -->

                    <!-- <textarea class="tinymce-editor" name="content" id="mce_0" aria-hidden="true"></textarea> -->
                    <!-- <h4>Tags</h4>
                    <textarea class="form-control" name="meta_tags" aria-hidden="true"></textarea>
                    <h4>Meta description</h4>
                    <textarea class="form-control" name="meta_description" aria-hidden="true"></textarea> -->
                </div>
                <div class="col-md-12">
                    <!-- <h4>Banner</h4>
                    <input accept="image/*" id="image-input" type="file" name="banner" class="form-control my-3">
                    <img style="width:100%; max-height:300px; object-fit:contain;" id="banner" src="" alt=""> -->
                    <!-- <div id="image-container"></div> -->
                    <!-- <button type="button" class="btn btn-secondary text-white mt-2" id="add-image">Images <i class="bi bi-plus"></i> </button> -->
                    <!-- <hr> -->
                    <h4>Choose Game</h4>
                    <select name="parent_id" class="form-select my-3">
                        <option value="0">--Select game--</option>
                        <?php foreach ($catlist as  $cv) {
                            $cv = obj($cv);
                        ?>
                            <option value="<?php echo $cv->id; ?>"><?php echo $cv->title; ?></option>
                        <?php } ?>
                        <?php ?>
                    </select>
                    <!-- <div class="row">
                        <div class="col">
                            <label for="">Game URL</label>
                            <input type="text" class="form-control my-2" name="link">
                        </div>
                    </div> -->
                    <!-- <h4>Price</h4>
                    <input type="number" scope="any" name="price" class="form-control my-3" placeholder="Price">
                    <h4>Live timing:</h4>
                    <label for="fromTime">Opens at</label>
                    <input type="time" class="form-control" name="opens_at" id="fromTime">
                    <label for="toTime">Closes at</label>
                    <input type="time" class="form-control" name="closes_at"  id="toTime"> -->
                    
                    <h4>Game Link</h4>
                    <input type="text" name="link" class="form-control my-3" placeholder="Game link">
                    <div class="d-grid">
                        <button id="save-page-btn" type="button" class="btn btn-primary my-3">Save</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</form>
<script>
    window.onload = () => {
        // const imageInputPage = document.getElementById('image-input');
        // const imagePage = document.getElementById('banner');

        // imageInputPage.addEventListener('change', (event) => {
        //     const file = event.target.files[0];
        //     const fileReader = new FileReader();

        //     fileReader.onload = () => {
        //         imagePage.src = fileReader.result;
        //     };

        //     fileReader.readAsDataURL(file);
        // });

        // for slug

        const titleInput = document.querySelector('input[name="title"]');
        const slugInput = document.querySelector('input[name="slug"]');
        if (titleInput && slugInput) {
            titleInput.addEventListener('keyup', () => {
                const title = titleInput.value.trim();
                generateSlug(title, slugInput);
            });
        }
    }



    $(document).ready(function() {
        $('#add-image').on('click', function() {
            // Create a new image input field
            var newInput = '<input accept="image/*" type="file" name="moreimgs[]" class="form-control my-3">';
            $('#image-container').append(newInput);
        });
    });
</script>
<?php pkAjax_form("#save-page-btn", "#save-new-page-form", "#res"); ?>