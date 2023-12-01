<?php
$fuel_detail = $context->fuel_detail;
$fd = obj($fuel_detail);
$fg =  explode("/", REQUEST_URI);
$fg = $fg[3];
$req = $context->req;
$driver = isset($context->driver) ? obj($context->driver) : null;
?>

<form action="/<?php echo home . route('fuelUpdateAjaxByDriver', ['id' => $fd->id, 'fg' => $req->fg,'driver_id'=>$req->driver_id]); ?>" id="update-new-fuel-form">
    <div class="card">
        <div class="card-body">

            <div id="res"></div>
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title">Update <?php echo $req->fg; ?></h5>
                        </div>
                        <!-- <div class="position-relative">
                                <div>
                                    <input id="userSearchInput" type="seach" name="searchuser" class="form-control" placeholder="search user...">
                                </div>
                                <div>
                                    <ul class="position-absolute w-100 bg-white border-dark" id="suggestionList"></ul>
                                </div>
                            </div>
                         -->
                        <div class="col text-end my-3">
                            <a class="btn btn-dark" href="/<?php echo home . route('fuelListByDriver', ['fg' => $req->fg, 'driver_id' => $req->driver_id]); ?>">Back</a>
                        </div>
                    </div>
                    <div class="row hide">
                        <div class="col-md-8">
                            <h4>Email</h4>
                            <input readonly type="email" value="<?php echo $fd->email; ?>" name="email" class="form-control my-3" placeholder="Eemail">
                        </div>
                        <div class="col-md-4">
                            <h4>Username</h4>
                            <input readonly type="text" name="username" value="<?php echo $fd->username; ?>" class="form-control my-3" placeholder="username">
                        </div>
                        <div class="col-md-3">
                            <h4>ISD Code</h4>
                            <input id="isd_code" type="number" name="isd_code" value="<?php echo $fd->isd_code; ?>" class="form-control my-3" placeholder="Ex: 96">
                        </div>
                        <div class="col-md-9">
                            <h4>Mobile</h4>
                            <input id="mobile" type="number" name="mobile" value="<?php echo $fd->mobile; ?>" class="form-control my-3" placeholder="mobile">
                        </div>
                        <div class="col-md-6">
                            <h4>First name</h4>
                            <input type="text" name="first_name" value="<?php echo $fd->first_name; ?>" class="form-control my-3" placeholder="First name">
                        </div>
                        <div class="col-md-6">
                            <h4>Lats name</h4>
                            <input type="text" name="last_name" value="<?php echo $fd->last_name; ?>" class="form-control my-3" placeholder="Last name">
                        </div>

                    </div>

                    <h4 class="text-capitalize"><?php echo $req->fg; ?></h4>
                    <div class="d-flex gap-2 align-items-center">
                        <div>
                            <select class="form-control" name="balance">
                                <option <?php echo $fd->balance == 1 ? 'selected' : null; ?> value="1">Add</option>
                                <option <?php echo $fd->balance == 0 ? 'selected' : null; ?> value="0">Deduct</option>
                            </select>
                        </div>
                        <div><input type="number" name="volume" value="<?php echo $fd->volume; ?>" class="form-control my-3" placeholder="<?php echo ucfirst($req->fg); ?> volume"></div>
                        <div><span>Litre</span></div>
                    </div>

                    <div class="d-grid">
                        <input type="hidden" name="user_id" id="user_id" value="<?php echo $fd->user_id; ?>">
                        <input type="hidden" name="fg" value="<?php echo $req->fg; ?>">
                        <button id="update-fuel-btn" type="button" class="btn btn-primary my-3">Update</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</form>

<?php pkAjax_form("#update-fuel-btn", "#update-new-fuel-form", "#res"); ?>
<?php //import("apps/admin/helpers/js/user-search.js.php"); 
?>