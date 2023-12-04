<?php
$user_detail = $context->user_detail;
$ud = obj($user_detail);
$ug =  explode("/", REQUEST_URI);
$ug = $ug[3];
$req = new stdClass;
$req->ug = $ug;
?>

<form action="/<?php echo home . route('userUpdateAjax', ['id' => $ud->id, 'ug' => $req->ug]); ?>" id="update-new-user-form">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h5 class="card-title">Add user</h5>
                </div>
                <div class="col text-end my-3">
                    <a class="btn btn-dark" href="/<?php echo home . route('userList', ['ug' => $req->ug]); ?>">Back</a>
                </div>
            </div>
            <div id="res"></div>
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>Email</h4>
                            <input <?php echo is_superuser()?null:"readonly"; ?> type="email" value="<?php echo $ud->email; ?>" name="email" class="form-control my-3" placeholder="Eemail">
                        </div>
                        <div class="col-md-4">
                            <h4>Username</h4>
                            <input type="text" name="username" value="<?php echo $ud->username; ?>" class="form-control my-3" placeholder="username">
                        </div>
                        <div class="col-md-6">
                            <h4>First name</h4>
                            <input type="text" name="first_name" value="<?php echo $ud->first_name; ?>" class="form-control my-3" placeholder="First name">
                        </div>
                        <div class="col-md-6">
                            <h4>Lats name</h4>
                            <input type="text" name="last_name" value="<?php echo $ud->last_name; ?>" class="form-control my-3" placeholder="Last name">
                        </div>
                        <div class="col-md-2 my-2">
                            <label for="isdCode">ISD Code</label>
                            <!-- Input for search -->
                            <style>
                                /* Apply the height to the select within #isdCodeSearchContainer */
                                .select2-selection--single,
                                #mobileInput {
                                    height: 40px !important;
                                    width: 100% !important;
                                }
                            </style>
                            <div id="isdcodecontainer"></div>
                            <select id="isdCodeSearch" class="form-select">
                                <?php
                                $isdjsn = jsonData("/dial-codes/std-code.json");
                                $isdcodes = json_decode($isdjsn);
                                foreach ($isdcodes as $key => $cd) {
                                    $isdcode = str_replace("+", "", $cd->dial_code);
                                ?>
                                    <option <?php echo "+{$ud->isd_code}" == $cd->dial_code ? "selected" : null; ?> value="<?php echo $cd->dial_code; ?>"><?php echo $cd->dial_code; ?> (<?php echo $cd->name; ?>)</option>
                                <?php } ?>
                            </select>
                            <script>
                                $(document).ready(function() {
                                    let prevIsdCode = "<?php echo "+{$ud->isd_code}"; ?>";
                                    if (prevIsdCode) {
                                        $('#isdCodeSearch').val(prevIsdCode);
                                    }
                                    // Initialize Select2 on the ISD code search input
                                    $('#isdCodeSearch').select2({
                                        placeholder: 'Search for ISD code',
                                        data: <?php echo $isdjsn; ?>
                                    });
                                    // Handle search functionality
                                    $('#isdCodeSearch').on('change', function() {
                                        let selectedCode = $(this).val();
                                        // Add the selected value to the form data
                                        $("#isdcodecontainer").html('<input type="hidden" name="isd_code" value="' + selectedCode + '">');
                                    });
                                });
                            </script>
                        </div>

                        <div class="col-md-4 my-2">
                            <label for="">Mobile</label>
                            <input id="mobileInput" type="text" name="mobile" value="<?php echo $ud->mobile; ?>" class="form-control">
                        </div>
                        <div class="col-md-6 my-2">
                            <label for="">Country</label>
                            <!-- Input for search -->
                            <style>
                                /* Apply the height to the select within #isdCodeSearchContainer */
                                .select2-selection--single,
                                #mobileInput {
                                    height: 40px !important;
                                    width: 100% !important;
                                }
                            </style>
                            <div id="countryCodeContainer"></div>
                            <select id="countryCodeSearch" class="form-select">
                                <?php
                                $isdjsn = jsonData("/dial-codes/std-code.json");
                                $isdcodes = json_decode($isdjsn);
                                foreach ($isdcodes as $key => $cd) {
                                    $isdcode = str_replace("+", "", $cd->dial_code);
                                ?>
                                    <option <?php echo "{$ud->country}" == "$cd->name" ? "selected" : null; ?> value="<?php echo $cd->name; ?>"><?php echo $cd->name; ?></option>
                                <?php } ?>
                            </select>
                            <script>
                                $(document).ready(function() {
                                    let prevIsdCode = "<?php echo "{$ud->country}"; ?>";
                                    if (prevIsdCode) {
                                        $('#countryCodeSearch').val(prevIsdCode);
                                    }
                                    // Initialize Select2 on the ISD code search input
                                    $('#countryCodeSearch').select2({
                                        placeholder: 'Search for ISD code',
                                        data: <?php echo $isdjsn; ?>
                                    });
                                    // Handle search functionality
                                    $('#countryCodeSearch').on('change', function() {
                                        let selectedCode = $(this).val();
                                        // Add the selected value to the form data
                                        $("#countryCodeContainer").html('<input type="hidden" name="country" value="' + selectedCode + '">');
                                    });
                                });
                            </script>
                        </div>

                        <div class="col-md-12 my-2">
                            <label for="">Address</label>
                            <textarea name="address" class="form-control"><?php echo $ud->address; ?></textarea>
                        </div>
                        <div class="col-md-4 my-2">
                            <label for="">State</label>
                            <input type="text" name="state" value="<?php echo $ud->state; ?>" class="form-control">
                        </div>
                        <div class="col-md-4 my-2">
                            <label for="">City</label>
                            <input type="text" name="city" value="<?php echo $ud->city; ?>" class="form-control">
                        </div>
                        <div class="col-md-4 my-2">
                            <label for="">zip</label>
                            <input type="text" name="zipcode" value="<?php echo $ud->zipcode; ?>" class="form-control">
                        </div>

                        <div class="col-md-12">
                            <h4>Bio</h4>
                            <textarea class="form-control" name="bio" aria-hidden="true"><?php echo $ud->bio; ?></textarea>
                            <div class=" my-2 text-center">
                                <a download href="/<?php echo MEDIA_URL . "/images/qrcodes/" . urlencode($ud->email); ?>.png">
                                    <img style="border: 2px dotted green; height: 200px; width:200px; object-fit:contain;" src="/<?php echo home . route('generateQRCode', ['id' => $ud->id]); ?>" alt="">
                                </a>
                                <p>Click QR Code to download</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <h4>Profile Image</h4>
                    <input accept="image/*" id="image-input" type="file" name="image" class="form-control my-3">
                    <div class="text-center">
                        <img style="width:200px; height:200px; object-fit:cover; border-radius:50%;" id="image" src="/<?php echo MEDIA_URL; ?>/images/profiles/<?php echo $ud->image; ?>" alt="<?php echo $ud->image; ?>">
                    </div>
                    <?php if ($req->ug == 'seller') : ?>


                        <label for="">IQMA NUMBER.</label>
                        <input type="text" name="nid_no" value="<?php echo $ud->nid_no; ?>" class="form-control">

                        <label for="">IQMA ID DOC (PDF)</label>
                        <input accept="application/pdf" type="file" name="nid_doc" class="form-control">

                        <h4>Account Type</h4>
                        <select name="ac_type" class="form-select my-3">
                            <option <?php echo $ud->ac_type==1?'selected':null; ?> value="1">Individual</option>
                            <option <?php echo $ud->ac_type==2?'selected':null; ?> value="2">Agency</option>
                        </select>

                        <label for="">Company Name</label>
                        <input type="text" name="company" value="<?php echo $ud->company; ?>" class="form-control">


                    <?php endif; ?>
                    <h4>Password</h4>
                    <input type="text" name="password" class="form-control my-3" placeholder="Password">

                    <div class="d-grid">
                        <button id="update-user-btn" type="button" class="btn btn-primary my-3">Update</button>
                    </div>
                    <hr>
                    <!-- <div class="d-flex justify-content-between">

                        <a class="btn btn-sm btn-success" target="_blank" href="/<?php // echo MEDIA_URL . "/docs/" . urlencode($ud->nid_doc); 
                                                                                    ?>"> <i class="bi bi-eye"></i> National ID</a>


                    </div> -->


                </div>
            </div>

        </div>
    </div>

</form>
<script>
    const imageInputPost = document.getElementById('image-input');
    const imagePost = document.getElementById('image');

    imageInputPost.addEventListener('change', (event) => {
        const file = event.target.files[0];
        const fileReader = new FileReader();

        fileReader.onload = () => {
            imagePost.src = fileReader.result;
        };

        fileReader.readAsDataURL(file);
    });
</script>
<?php pkAjax_form("#update-user-btn", "#update-new-user-form", "#res"); ?>