              <!-- Table with stripped rows -->
              <table class="table datatable">
                        <thead>
                            <tr>
                                <th scope="col">Id</th>
                                <th scope="col">Active</th>
                                <th scope="col">Move to</th>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Post</th>
                                <th scope="col">Message</th>
                                <th scope="col">Date</th>
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
                            <?php foreach ($cl as $key => $pv) :
                                $pv = obj($pv);
                                $post = obj(getData('content', $pv->content_id));
                                if ($pv->is_active == true) {
                                    $move_to_text = "Trash";
                                    $move_to_link = route('commentTrash', ['id' => $pv->id, 'cg' => $req->cg]);
                                } else {
                                    $move_to_link = route('commentDelete', ['id' => $pv->id, 'cg' => $req->cg]);
                                    $move_to_text = "Delete";
                                    $restore_text = "Restore";
                                    $restore_link = route('commentRestore', ['id' => $pv->id, 'cg' => $req->cg]);
                                }
                            ?>

                                <tr>
                                    <th scope="row"><?php echo $pv->id; ?></th>
                                    <td>
                                        <button data-comment-id="<?php echo $pv->id; ?>" class="approve-btn btn btn-sm <?php echo $pv->is_approved ? 'btn-basic' : 'btn-primary'; ?>">
                                            <?php echo $pv->is_approved ? 'Approved' : 'Approve'; ?>
                                        </button>
                                    </td>
                                    <td>
                                        <button data-comment-id="<?php echo $pv->id; ?>" class="spam-btn btn btn-sm <?php echo $pv->comment_group=='spam' ? 'btn-primary' : 'btn-basic'; ?>">
                                            <?php echo $pv->comment_group=='spam' ? 'Inbox' : 'Spam'; ?>
                                        </button>
                                    </td>
                                    <td><?php echo $pv->name; ?></td>
                                    <td><?php echo $pv->email; ?></td>
                                    <td>
                                        <a target="_blank" href="<?php echo "/".home.route('readPost',['slug'=>$post->slug]); ?>"><?php echo $post->title; ?></a>
                                    </td>
                                    <td><?php echo $pv->message; ?></td>
                                    <td><?php echo $pv->created_at; ?></td>
                                    <?php
                                    if ($active == true) { ?>
                                        <td>
                                            <a class="btn-primary btn btn-sm" href="/<?php echo home . route('commentEdit', ['id' => $pv->id, 'cg' => $req->cg]); ?>">Edit</a>
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
                    <!-- End Table with stripped rows -->
                    <!-- Pagination -->
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">

                            <?php
                            $tc = $tc;
                            $current_page = $cp; // Assuming first page is the current page
                            if ($active == true) {
                                $link =  route('commentList', ['cg' => $req->cg]);
                            } else {
                                $link =  route('commentTrashList', ['cg' => $req->cg]);
                            }
                            // Show first two pages
                            for ($i = 1; $i <= $tc; $i++) {
                            ?>
                                <li class="page-item"><a class="page-link" href="/<?php echo home . $link . "?page=$i"; ?>"><?php echo $i; ?></a></li>
                            <?php
                            } ?>




                        </ul>
                    </nav>

                    <!-- Pagination -->