<?php
$fl = $context->fuel_list;
$tp = $context->total_fuel;
$cp = $context->current_page;
$active = $context->is_active;

$ug =  explode("/", REQUEST_URI);
$ug = $ug[3];
// $req = new stdClass;
// $req->fg = $ug;
$req = $context->req;
?>

<section class="section">
    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col my-3">
                            <h5 class="card-title">All fuels</h5>
                            <nav class="nav">
                                <a class="nav-link <?php echo $active ? "btn btn-sm btn-primary text-white" : ""; ?>" href="/<?php echo home . route('fuelListByDriver', ['fg' => $req->fg,'driver_id'=>$req->driver_id]); ?>">Active List</a>
                                <a class="nav-link <?php echo $active ? "" : "btn btn-sm btn-danger text-white"; ?>" href="/<?php echo home . route('fuelTrashListByDriver', ['fg' => $req->fg,'driver_id'=>$req->driver_id]); ?>">Trash List</a>
                            </nav>

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
                            <div class="d-flex align-items-center gap-2 justify-content-between">
                                <div>
                                <a class="btn btn-dark" href="/<?php echo home . route('userList', ['ug' => 'driver']); ?>">Back</a>
                                </div>
                                <div>
                                <a class="btn btn-dark" href="/<?php echo home . route('fuelCreateByDriver', ['fg' => $req->fg,'driver_id'=>$req->driver_id]); ?>">Add/Deduct</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table with stripped rows -->
                    <div class="table-responsive">


                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th scope="col">Id</th>
                                    <th scope="col">Fuel Type</th>
                                    <th scope="col">Volume</th>
                                    <th scope="col">Unit</th>
                                    <th scope="col">Balance</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Username</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Topup Date</th>
                                    <?php
                                    if ($active == true) { ?>

                                        <th scope="col">Edit</th>

                                    <?php    }
                                    ?>
                                    <th scope="col">Action</th>
                                    <?php
                                    if ($active == false) { ?>
                                        <th scope="col">Restore</th>
                                    <?php    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($fl as $key => $pv) :
                                    $pv = obj($pv);
                                    if ($pv->is_active == true) {
                                        $move_to_text = "Trash";
                                        $move_to_link = route('fuelTrashByDriver', ['id' => $pv->id, 'fg' => $req->fg,'driver_id'=>$req->driver_id]);
                                    } else {
                                        $move_to_link = route('fuelDeleteByDriver', ['id' => $pv->id, 'fg' => $req->fg,'driver_id'=>$req->driver_id]);
                                        $move_to_text = "Delete";
                                        $restore_text = "Restore";
                                        $restore_link = route('fuelRestoreByDriver', ['id' => $pv->id, 'fg' => $req->fg,'driver_id'=>$req->driver_id]);
                                    }
                                ?>

                                    <tr>
                                        <th scope="row"><?php echo $pv->id; ?></th>
                                        <th><?php echo $pv->fuel_group; ?></th>
                                        <th><?php echo $pv->balance == 1 ? "+" : "-"; ?> <?php echo $pv->volume; ?></th>
                                        <th><?php echo $pv->unit; ?></th>
                                        <td>
                                            <span class="<?php echo $pv->balance == 1 ? "bg-success badge" : "badge bg-danger"; ?>">
                                                <?php echo $pv->balance == 1 ? "Added" : "Deducted"; ?>
                                            </span>

                                        </td>
                                        <td><?php echo $pv->first_name; ?></td>


                                        <td><?php echo $pv->username; ?></td>
                                        <td><?php echo $pv->email; ?></td>
                                        <td><?php echo $pv->created_at; ?></td>
                                        <?php
                                        if ($active == true) { ?>
                                            <td>
                                                <a class="btn-primary btn btn-sm" href="/<?php echo home . route('fuelEditByDriver', ['id' => $pv->id, 'fg' => $req->fg,'driver_id'=>$pv->user_id]); ?>">Edit</a>
                                            </td>
                                        <?php    }
                                        ?>

                                        <td>
                                            <a class="btn-danger btn btn-sm" href="/<?php echo home . $move_to_link; ?>"><?php echo $move_to_text; ?></a>
                                        </td>
                                        <?php
                                        if ($active == false) { ?>
                                            <td>
                                                <a class="btn-success btn btn-sm" href="/<?php echo home . $restore_link; ?>"><?php echo $restore_text; ?></a>
                                            </td>
                                        <?php    }
                                        ?>

                                    </tr>

                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="custom-pagination">
                        <?php
                        $pg = isset($_GET['page']) ? $_GET['page'] : 1;
                        $tu = $tp; // Total pages
                        $current_page = $cp; // Assuming first page is the current page
                        if ($active == true) {
                            $link =  route('fuelListByDriver', ['fg' => $req->fg,'driver_id'=>$req->driver_id]);
                        } else {
                            $link =  route('fuelTrashListByDriver', ['fg' => $req->fg,'driver_id'=>$req->driver_id]);
                        }
                        // Calculate start and end page numbers to display
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($start_page + 4, $tu);

                        // Show first page button if not on the first page
                        if ($current_page > 1) {
                            echo '<a class="first-button" href="/' . home . $link . '?page=1">&laquo;</a>';
                        }

                        // Show ellipsis if there are more pages before the start page
                        if ($start_page > 1) {
                            echo '<span>...</span>';
                        }

                        // Display page links within the range
                        for ($i = $start_page; $i <= $end_page; $i++) {
                            $active_class = ($pg == $i) ? "active" : null;
                            echo '<a class="' . $active_class . '" href="/' . home . $link . '?page=' . $i . '">' . $i . '</a>';
                        }

                        // Show ellipsis if there are more pages after the end page
                        if ($end_page < $tu) {
                            echo '<span>...</span>';
                        }

                        // Show last page button if not on the last page
                        if ($current_page < $tu) {
                            echo '<a class="last-button" href="/' . home . $link . '?page=' . $tu . '">&raquo;</a>';
                        }
                        ?>
                    </div>

                    <!-- End Table with stripped rows -->
                </div>

            </div>

        </div>
    </div>
</section>