<?php
$game_detail = $context->game_detail;
$pd = obj($game_detail);
$catlist = $context->cat_list;
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
<form action="/<?php echo home . route('gameUpdateAjax', ['id' => $pd->id]); ?>" id="update-new-game-form">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h5 class="card-title">Edit game</h5>
                </div>
                <div class="col text-end my-3">
                    <a class="btn btn-dark" href="/<?php echo home . route('gameListByGame',['game_id'=>$pd->parent_id]); ?>">Back</a>
                </div>
            </div>
            <div id="res"></div>
            <div class="row">
                <div class="col-md-8">
                    <!-- <h4>Title</h4>
                    <input type="text" name="title" value="<?php echo $pd->title; ?>" class="form-control my-3" placeholder="Title">
                    <h6>Slug</h6>
                    <input type="text" name="slug" value="<?php echo $pd->slug; ?>" class="form-control my-3" placeholder="slug"> -->


                    <!-- <textarea class="tinymce-editor" name="content" id="mce_0" aria-hidden="true"><?php echo $pd->content; ?></textarea>
                    <h4>Tags</h4>
                    <textarea class="form-control" name="meta_tags" aria-hidden="true"><?php echo $meta_tags; ?></textarea>
                    <h4>Meta description</h4>
                    <textarea class="form-control" name="meta_description" aria-hidden="true"><?php echo $meta_desc; ?></textarea> -->

                    <!-- <section id="add-review">
                        <label for="">Name</label>
                        <input type="text" name="name_of_user" class="form-control review-data-send">
                        <label for="">Rating Point</label>
                        <select name="star_point" class="form-select review-data-send">
                            <option value="5">5</option>
                            <option value="4">4</option>
                            <option value="3">3</option>
                            <option value="2">2</option>
                            <option value="1">1</option>
                        </select>
                        <label for="">Review Message</label>
                        <input type="hidden" name="content_id" class="review-data-send" value="<?php echo $pd->id; ?>">
                        <textarea name="review_message" class="form-control review-data-send"></textarea>
                        <button id="add-review-btn" class="btn btn-primary" type="button">Add review</button>

                        <h3>Reviews by admin</h3>
                        <table class="table table-hover" style="max-height: 200px; overflow-y:scroll;">
                            <tr>
                                <th>Action</th>
                                <th>Rating Point</th>
                                <th>Message</th>
                                <th>Cust. Name</th>
                            </tr>
                            <tr style="background-color: dodgerblue; color:white;">
                                <th colspan="10">
                                </th>
                            </tr>
                            <?php

                            $rvdta = $context->reviewdata;

                            foreach ($rvdta as $key => $dmrv) :
                                $dmrv = obj($dmrv);
                                $rtstar = showStars($rating = $dmrv->rating);
                            ?>
                                <tr>
                                    <td>
                                        <input type="radio" class="remove-this-dm-review<?php echo $dmrv->id; ?>" name="dm_review_id" value="<?php echo $dmrv->id; ?>">
                                        <button id="<?php echo "remove-this-dm-review{$dmrv->id}"; ?>" type="button" class="btn btn-danger btn-sm">Delete</a>
                                    </td>
                                    <td>
                                        <b><?php echo $dmrv->rating . " " . $rtstar; ?></b>
                                    </td>
                                    <td><?php echo $dmrv->name; ?></td>
                                    <td><?php echo $dmrv->message; ?></td>
                                    <td class="text-end">
                                        <?php
                                        send_to_server_wotf("#remove-this-dm-review{$dmrv->id}", ".remove-this-dm-review{$dmrv->id}", "commonCallbackHandler", route('deleteReviewAjax', ['rg' => 'game']));
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach;

                            ?>


                        </table>
                    </section> -->


                </div>
                <div class="col-md-12">
                    <!-- <h4>Banner</h4>
                    <input accept="image/*" id="image-input" type="file" name="banner" class="form-control my-3">
                    <img style="width:100%; max-height:300px; object-fit:contain;" id="banner" src="/<?php //echo MEDIA_URL; ?>/images/pages/<?php //echo $pd->banner; ?>" alt="<?php //echo $pd->banner; ?>">
                    <div id="image-container"></div>
                    <button type="button" class="btn btn-secondary text-white mt-2" id="add-image">Images <i class="bi bi-plus"></i> </button> -->
                    <hr>
                    <?php
                    $imgs = get_image_list($pd->imgs);
                    $moreimgcount = count($imgs);
                    // myprint($imgs);
                    ?>
                    <!-- <h4>Total more images count <?php echo $moreimgcount; ?> </h4> -->
                    <!-- <div style="max-height: 200px; overflow-y:scroll; background-color: rgba(0,0,0,0.2);">
                        <?php
                        /*foreach ($imgs as $key => $img) { ?>
                            <button style="margin: 10px;" type="button" class="btn btn-danger delete-more-img" data-img-src="<?php echo $img; ?>" data-content-id="<?php echo $pd->id; ?>">Delete <i class="bi bi-arrow-down"></i></button>
                            <img style="width: 100%; padding: 10px;" src="/<?php echo MEDIA_URL; ?>/images/pages/<?php echo $img; ?>" alt="<?php echo $pd->title; ?>">
                            <hr>
                        <?php } */ ?>
                    </div> -->
                    <h4>Games</h4>
                    <select name="parent_id" class="form-select my-3">
                        <option <?php echo $pd->parent_id == 0 ? "selected" : null; ?> value="0">--select game--</option>
                        <?php foreach ($catlist as  $cv) {
                            $cv = obj($cv);
                        ?>
                            <option <?php echo $pd->parent_id == $cv->id ? "selected" : null; ?> value="<?php echo $cv->id; ?>"><?php echo $cv->title; ?></option>
                        <?php } ?>
                        <?php ?>
                    </select>
                    <!-- <h4>Live timing:</h4>
                    <label for="fromTime">Opens at</label>
                    <input type="datetime-local" class="form-control" name="opens_at" value="<?php echo $pd->opens_at??null; ?>" id="fromTime">
                    <label for="toTime">Closes at</label>
                    <input type="datetime-local" class="form-control" name="closes_at" value="<?php echo $pd->closes_at??null; ?>" id="toTime"> -->
                    <!-- <h4>Price/Unit</h4>
                    <input type="number" scope="any" name="price" value="<?php echo $pd->price; ?>" class="form-control my-3" placeholder="Price"> -->

                    <h4>Game Link</h4>
                    <input type="text" name="link" value="<?php echo $pd->link; ?>" class="form-control my-3" placeholder="Game link">
                    <h4>Is sold ? <input type="checkbox" <?php echo $pd->is_sold == 1 ? "checked" : null; ?> name="is_sold"></h4>

                    <div class="d-grid">
                        <button id="update-game-btn" type="button" class="btn btn-primary my-3">Update</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</form>


<?php import('apps/admin/pages/games/js/module.js.php'); ?>
<?php pkAjax_form("#update-game-btn", "#update-new-game-form", "#res"); ?>
<!-- for review -->

<?php send_to_server_wotf("#add-review-btn", ".review-data-send", "commonCallbackHandler",  route('addReviewAjax', ['rg' => 'game'])); ?>