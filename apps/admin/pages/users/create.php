<?php
$createData = $context;
$ug =  explode("/", REQUEST_URI);
$ug = $ug[3];
// $req = new stdClass;
$req = $context->req;

// $indexedPositions = [
//     0 => "HELPER", 1 => "MASON", 2 => "SCAFFOLDER", 3 => "ELECTRICIAN", 4 => "LAYDOWN SECURITY",
//     5 => "TEA BOY", 6 => "CIVIL FOREMAN", 7 => "FLAG MAN", 8 => "CARPENTER", 9 => "CARPENTER",
//     10 => "PLUMBER", 11 => "SCAFFOLDING FOREMAN", 12 => "STEEL FIXER", 13 => "CONCRETE",
//     14 => "ROLLER OPERATOR", 15 => "GRADER OPERATOR", 16 => "LIGHT DRIVER", 17 => "TANKER DRIVER",
//     18 => "HEAVY DRIVER", 19 => "EXCAVATOR OPERATOR", 20 => "EXCAVATION OPERATOR", 21 => "LOADER OPERATOR",
//     22 => "SHOVEL OPERATOR", 23 => "COASTER DRIVER", 24 => "SURVEYOR", 25 => "HSE ENGINEER",
//     26 => "HSE MANAGER", 27 => "PLANNING ENGINEER", 28 => "SURVEYOR", 29 => "EQUIPMENT INSPECTOR",
//     30 => "HSE OFFICER"
// ];

// // Remove duplicates
// $uniqueIndexedPositions = array_unique($indexedPositions);

// // Reassign numeric keys
// $finalIndexedPositions = array_values($uniqueIndexedPositions);

// // Output the result
// print_r($finalIndexedPositions);

?>

<form action="/<?php echo home . route('userStoreAjax', ['ug' => $req->ug]); ?>" id="register-new-user-form">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h5 class="card-title">Add <?php echo $req->ug; ?></h5>
                </div>
                <div class="col text-end my-3">
                    <a class="btn btn-dark" href="/<?php echo home . route('userList', ['ug' => $req->ug]); ?>">Back</a>
                </div>
            </div>
            <div id="res"></div>
            <div class="row">
                <div class="col-md-8">
                    <?php
                    $auth = new Auth();
                    // $permsns = $auth->permissions('employee');
                    // myprint($permsns);
                    ?>
                    <div class="row">
                        <div class="col-md-8">
                            <h4>Email</h4>
                            <input type="email" name="email" class="form-control my-3" placeholder="Email">
                        </div>
                        <div class="col-md-4">
                            <h4>Username</h4>
                            <input type="text" name="username" class="form-control my-3" placeholder="username">
                        </div>
                        <div class="col-md-6">
                            <h4>First name</h4>
                            <input type="text" name="first_name" class="form-control my-3" placeholder="First name">
                        </div>
                        <div class="col-md-6">
                            <h4>Lats name</h4>
                            <input type="text" name="last_name" class="form-control my-3" placeholder="Last name">
                        </div>
                        <div class="col-md-2 my-2">
                            <label for="isdCode">ISD Code</label>
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
                                    <option value="<?php echo $cd->dial_code; ?>"><?php echo $cd->dial_code; ?> (<?php echo $cd->name; ?>)</option>
                                <?php } ?>
                            </select>
                            <script>
                                $(document).ready(function() {
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
                            <input id="mobileInput" type="text" name="mobile" class="form-control">
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
                                    <option value="<?php echo $cd->name; ?>"><?php echo $cd->name; ?></option>
                                <?php } ?>
                            </select>
                            <script>
                                $(document).ready(function() {

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
                            <textarea name="address" class="form-control"></textarea>
                        </div>
                        <div class="col-md-4 my-2">
                            <label for="">State</label>
                            <input type="text" name="state" class="form-control">
                        </div>
                        <div class="col-md-4 my-2">
                            <label for="">City</label>
                            <input type="text" name="city" class="form-control">
                        </div>
                        <div class="col-md-4 my-2">
                            <label for="">zip</label>
                            <input type="text" name="zipcode" class="form-control">
                        </div>

                        <div class="col-md-12">
                            <h4>Bio</h4>
                            <textarea class="form-control" name="bio" aria-hidden="true"></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <h4>Profile Image</h4>
                    <input accept="image/*" id="image-input" type="file" name="image" class="form-control my-3">
                    <img style="width:100%; max-height:300px; object-fit:contain;" id="banner" src="" alt="">
                    <?php if ($req->ug == 'seller') : ?>


                        <label for="">IQMA NUMBER.</label>
                        <input type="text" name="nid_no" class="form-control">

                        <label for="">IQMA ID DOC (PDF)</label>
                        <input accept="application/pdf" type="file" name="nid_doc" class="form-control">

                        <h4>Account Type</h4>
                        <select name="ac_type" class="form-select my-3">
                            <option value="1">Individual</option>
                            <option value="2">Agency</option>
                        </select>

                        <label for="">Company Name</label>
                        <input type="text" name="company" class="form-control">


                    <?php endif; ?>
                    <h4>Password</h4>
                    <input type="text" name="password" class="form-control my-3" placeholder="Password">

                    <div class="d-grid">
                        <button id="register-user-btn" type="button" class="btn btn-primary my-3">Save</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</form>
<script>
    const imageInputPost = document.getElementById('image-input');
    const imagePost = document.getElementById('banner');

    imageInputPost.addEventListener('change', (event) => {
        const file = event.target.files[0];
        const fileReader = new FileReader();

        fileReader.onload = () => {
            imagePost.src = fileReader.result;
        };

        fileReader.readAsDataURL(file);
    });
</script>



<?php pkAjax_form("#register-user-btn", "#register-new-user-form", "#res"); ?>