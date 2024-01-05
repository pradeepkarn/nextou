<?php
$page = $context['prod'];
$pageid = uniqid('review');
?>
<section id="add-review">
    <div class="row">
        <div class="col-md-12">
            <b>
                <div class="text-danger" id="res<?php echo $pageid; ?>"></div>
            </b>
        </div>
    </div>
    <form id="add-review-form<?php echo $pageid; ?>" action="/<?php echo home . route('reviewAdminCreateAdmin'); ?>">
        <label for="">Name</label>
        <input type="text" name="name" class="form-control">
        <label for="">Rating Point</label>
        <select name="point" class="form-select">
            <option value="5">5</option>
            <option value="4">4</option>
            <option value="3">3</option>
            <option value="2">2</option>
            <option value="1">1</option>
        </select>
        <label for="">Review Message</label>
        <input type="hidden" name="product_id" value="<?php echo $page['id']; ?>">
        <textarea name="message" class="form-control"></textarea>
        <button id="add-review-btn<?php echo $pageid; ?>" class="btn btn-primary" type="button">Add review</button>
    </form>
    <?php pkAjax_form("#add-review-btn{$pageid}", "#add-review-form{$pageid}", "#res{$pageid}"); ?>
    <h3>Reviews by admin</h3>
    <table class="table table-hover" style="max-height: 200px; overflow-y:scroll;">
        <tr>
            <th>Action</th>
            <th>Rating Point</th>
            <th>Name</th>
            <th>Message</th>
        </tr>
        <tr style="background-color: dodgerblue; color:white;">
            <th colspan="10">
            </th>
        </tr>
        <?php
        // Dummy 

        $rvdb = new Dbobjects;
        $rvdb->tableName = "review";
        $arrv = null;
        $arrv['item_id'] = $page['id'];
        $arrv['item_group'] = 'product';
        $arrv['status'] = 1;
        $arrv['by_admin'] = 1;
        $rvdta = $rvdb->filter($arrv);

        foreach ($rvdta as $key => $dmrv) :
            $dmrv = obj($dmrv);
            $rtstar = showStars($rating = $dmrv->rating);
        ?>
            <tr>
                <td>
                    <input type="radio" class="remove-this-dm-review<?php echo $dmrv->id; ?>" name="review_id" value="<?php echo $dmrv->id; ?>">
                    <button id="<?php echo "remove-this-dm-review{$dmrv->id}"; ?>" type="button" class="btn btn-danger btn-sm">Delete</a>
                </td>
                <td>
                    <b><?php echo $dmrv->rating . " " . $rtstar; ?></b>
                </td>
                <td><?php echo $dmrv->name; ?></td>
                <td><?php echo $dmrv->message; ?></td>
                <td class="text-end">
                    <?php pkAjax("#remove-this-dm-review{$dmrv->id}", route('reviewAdminDeleteAdmin'), ".remove-this-dm-review{$dmrv->id}", "#res{$pageid}");
                    ?>
                </td>
            </tr>
        <?php endforeach;

        ?>
    </table>
</section>
<section id="reviews">
    <h3>Reviews by users</h3>
    <table class="table table-hover" style="max-height: 200px; overflow-y:scroll;">
        <tr>
            <th>Action</th>
            <th>Rating Point</th>
            <th>Name</th>
            <th>Message</th>
        </tr>
        <tr style="background-color: dodgerblue; color:white;">
            <th colspan="10">
            </th>
        </tr>
        <?php
        $bkmrks = new Model('review');
        $reviews = $bkmrks->filter_index(['item_group' => 'product', 'item_id' => $page['id'], 'by_admin' => '0']);
        foreach ($reviews as $key => $pv) :
            $bk = obj($pv);
            $star = showStars($rating = $bk->rating);
        ?>
            <tr>
                <td>
                    <input type="radio" class="remove-this-review<?php echo $bk->id; ?>" name="review_id" value="<?php echo $bk->id; ?>">
                    <button id="<?php echo "remove-this-review{$bk->id}"; ?>" type="button" class="btn btn-danger btn-sm">Delete</a>
                </td>
                <td>
                    <b><?php echo $bk->rating . " " . $star; ?></b>
                </td>
                <td><?php echo $bk->name; ?></td>
                <td><?php echo $bk->message; ?></td>
                <td><?php echo $bk->email; ?></td>
                <td class="text-end">
                    <?php pkAjax("#remove-this-review{$bk->id}", route('reviewAdminDeleteAdmin'), ".remove-this-review{$bk->id}", "#res{$pageid}");
                    ?>
                </td>
            </tr>
        <?php endforeach;  ?>
    </table>


</section>