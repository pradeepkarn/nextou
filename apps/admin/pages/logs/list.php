<?php
$pl = $context->log_list;
$tp = $context->total_page;
$cp = $context->current_page;
$active = $context->is_active;
// myprint($pl)
?>
<style>
    .featured-post,
    .trending-post {
        font-size: 30px;
    }
</style>
<section class="section">
    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col my-3">
                            <h5 class="card-title">Logs</h5>

                        </div>
                        <div class="col my-3">
                            <form action="">
                                <div class="row">
                                    <div class="col-8">
                                        <input value="<?php echo isset($_GET['search']) ? $_GET['search'] : null; ?>" type="search" class="form-control" name="search" placeholder="Search...">
                                    </div>
                                    <div class="col-4">
                                        <button type="submit" class="btn btn-primary ">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col text-end my-3">
                            <a class="btn btn-dark" href="/<?php echo home . route('pageCreate'); ?>">Add New</a>
                        </div>
                    </div>
                    <form action="" id="delete-bulk-form">
                        <div id="deletebulkres"></div>
                        <div class="row my-2">
                            <div class="col-md-4">
                                <select name="action" class="form-select" id="">
                                    <option value="">Action</option>
                                    <option value="delete_selected_items">Delete selected (Parmanently)</option>
                                </select>
                            </div>
                            <div class="col-md-4">

                                <button type="submit" id="delete-bulk-btn" class="btn btn-danger">Done</button>

                            </div>
                        </div>
                    </form>
                    <?php
                    ajaxActive("#upload-info");
                    pkAjax_form("#delete-bulk-btn", "#delete-bulk-form", "#deletebulkres");
                    ?>
                    <!-- Table with stripped rows -->
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">
                                    <input type="checkbox" id="selct_all_ids"> Select
                                </th>
                                <th scope="col">Log ID</th>
                                <th scope="col">Message</th>
                                <th scope="col">Device Info</th>
                                <th scope="col">Device ID</th>
                                <th scope="col">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pl as $key => $pv) :
                                $pv = obj($pv);
                            ?>

                                <tr>

                                    <th>
                                        <input type="checkbox" name="selected_obj_id" value="<?php echo $pv->id; ?>">
                                    </th>
                                    <th scope="row"><?php echo $pv->id; ?></th>
                                    <td><?php echo $pv->message; ?></td>
                                    <td><?php echo $pv->device_info; ?></td>
                                    <td><?php echo $pv->device_id ?? "NA"; ?></td>
                                    <td><?php echo $pv->created_at; ?></td>
                                </tr>

                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <!-- End Table with stripped rows -->
                    <!-- Pagination -->
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">

                            <?php
                            $tp = $tp;
                            $current_page = $cp; // Assuming first page is the current page
                            $link =  route('logList');
                            // Show first two pages
                            for ($i = 1; $i <= $tp; $i++) {
                            ?>
                                <li class="page-item"><a class="page-link" href="/<?php echo home . $link . "?page=$i"; ?>"><?php echo $i; ?></a></li>
                            <?php
                            } ?>

                        </ul>
                    </nav>

                    <!-- Pagination -->
                </div>

            </div>

        </div>
    </div>
</section>


<script>
    const selectAllCheckbox = document.getElementById('selct_all_ids');
    const individualCheckboxes = document.querySelectorAll('input[name="selected_obj_id"]');
    const deleteBulkForm = document.getElementById('delete-bulk-form');

    selectAllCheckbox.addEventListener('change', function() {
        individualCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
            updateFormInputs(checkbox);
        });
    });

    individualCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateFormInputs(checkbox);
            selectAllCheckbox.checked = Array.from(individualCheckboxes).every(checkbox => checkbox.checked);
        });
    });

    function updateFormInputs(checkbox) {
        if (checkbox.checked) {
            appendInput(deleteBulkForm, 'selected_ids[]', checkbox.value);
        } else {
            removeInput(deleteBulkForm, 'selected_ids[]', checkbox.value);
        }
    }

    function appendInput(form, name, value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    }

    function removeInput(form, name, value) {
        const inputToRemove = form.querySelector(`input[name="${name}"][value="${value}"]`);
        if (inputToRemove) {
            form.removeChild(inputToRemove);
        }
    }

    deleteBulkForm.addEventListener('submit', function(event) {
        individualCheckboxes.forEach(checkbox => {
            if (!checkbox.checked) {
                removeInput(deleteBulkForm, 'selected_ids[]', checkbox.value);
            }
        });
    });
</script>