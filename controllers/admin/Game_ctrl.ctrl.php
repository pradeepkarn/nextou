<?php

use League\Csv\Reader;

class Game_ctrl
{
    // Cretae page
    public function create($req = null)
    {
        $context = (object) array(
            'page' => 'games/create.php',
            'data' => (object) array(
                'req' => obj($req),
                'cat_list' => $this->cat_list(limit: 100)
            )
        );
        $this->render_main($context);
    }
    // List page
    public function list($req = null)
    {
        $req = obj($req);
        $game_id = $req->game_id??null;
        // myprint($req);
        $current_page = 0;
        $data_limit = DB_ROW_LIMIT;
        $page_limit = "0,$data_limit";
        $cp = 0;
        if (isset($req->page) && intval($req->page)) {
            $cp = $req->page;
            $current_page = (abs($req->page) - 1) * $data_limit;
            $page_limit = "$current_page,$data_limit";
        }
        $total_page = $this->game_list(ord: "DESC", limit: 10000, active: 1, game_id:$game_id);
        $tp = count($total_page);
        if ($tp %  $data_limit == 0) {
            $tp = $tp / $data_limit;
        } else {
            $tp = floor($tp / $data_limit) + 1;
        }
        if (isset($req->search)) {
            $game_list = $this->game_search_list($keyword = $req->search, $ord = "DESC", $limit = $page_limit, $active = 1, $game_id = $game_id);
        } else {
            $game_list = $this->game_list(ord: "DESC", limit: $page_limit, active: 1,game_id:$game_id);
        }
        $context = (object) array(
            'page' => 'games/list.php',
            'data' => (object) array(
                'req' => obj($req),
                'game_list' => $game_list,
                'total_page' => $tp,
                'current_page' => $cp,
                'is_active' => true
            )
        );
        $this->render_main($context);
    }
    // Trashed post list
    public function trash_list($req = null)
    {
        $req = obj($req);
        $game_id = $req->game_id??null;
        $current_page = 0;
        $data_limit = DB_ROW_LIMIT;
        $page_limit = "0,$data_limit";
        $cp = 0;
        if (isset($req->page) && intval($req->page)) {
            $cp = $req->page;
            $current_page = (abs($req->page) - 1) * $data_limit;
            $page_limit = "$current_page,$data_limit";
        }
        $total_page = $this->game_list(ord: "DESC", limit: 10000, active: 0,game_id:$game_id);
        $tp = count($total_page);
        if ($tp %  $data_limit == 0) {
            $tp = $tp / $data_limit;
        } else {
            $tp = floor($tp / $data_limit) + 1;
        }
        if (isset($req->search)) {
            $game_list = $this->game_search_list($keyword = $req->search, $ord = "DESC", $limit = $page_limit, $active = 0, $game_id=$game_id);
        } else {
            $game_list = $this->game_list(ord: "DESC", limit: $page_limit, active: 0, game_id:$game_id);
        }
        $context = (object) array(
            'page' => 'games/list.php',
            'data' => (object) array(
                'req' => obj($req),
                'game_list' => $game_list,
                'total_page' => $tp,
                'current_page' => $cp,
                'is_active' => false
            )
        );
        $this->render_main($context);
    }
    // Edit page
    public function edit($req = null)
    {
        $req = obj($req);
        $rvdb = new Dbobjects;
        $rvdb->tableName = "review";
        $arrv = null;
        $arrv['item_id'] = $req->id;
        $arrv['item_group'] = 'game';
        $arrv['status'] = "published";
        $reviewdata = $rvdb->filter($arrv);
        $context = (object) array(
            'page' => 'games/edit.php',
            'data' => (object) array(
                'req' => obj($req),
                'game_detail' => $this->game_detail($req->id),
                'cat_list' => $this->cat_list(limit: 1000),
                'reviewdata' => $reviewdata
            )
        );
        $this->render_main($context);
    }
    // Save by ajax call
    public function save($req = null)
    {
        $request = null;
        $data = null;
        $data = $_POST;
        // $data['banner'] = $_FILES['banner']??null;

        $rules = [
            // 'title' => 'required|string',
            // 'slug' => 'required|string',
            // 'content' => 'required|string',
            // 'banner' => 'required|file',
            'parent_id' => 'required|integer',
            // 'price' => 'required|numeric',
            'link' => 'required|string',
            // 'opens_at' => 'required|time',
            // 'closes_at' => 'required|time',
        ];

        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            exit;
        }
        $request = obj($data);
        $json_arr = array();
        if (isset($request->meta_tags)) {
            $json_arr['meta']['tags'] = $request->meta_tags;
        }
        if (isset($request->meta_description)) {
            $json_arr['meta']['description'] = $request->meta_description;
        }
        $arr = null;
        $arr['json_obj'] = json_encode($json_arr);
        $arr['content_group'] = "game";
        $arr['title'] = "gameurl";
        $arr['slug'] = generate_slug(uniqid('slug'));
        // $arr['price'] = $request->price;
        // $arr['content'] = $request->content;
        $arr['parent_id'] = $request->parent_id;
        // $arr['created_at'] = date('Y-m-d H:i:s');
        $arr['link'] = $request->link;
        // $arr['opens_at'] = $request->opens_at;
        // $arr['closes_at'] = $request->closes_at;

        $moreimg = [];
        // if (isset($_FILES['moreimgs'])) {
        //     $fl = $_FILES['moreimgs'];
        //     for ($i = 0; $i < count($fl['name']); $i++) {
        //         if ($fl['name'][$i] != '' && $fl['error'][$i] === UPLOAD_ERR_OK) {
        //             $ext = pathinfo($fl['name'][$i], PATHINFO_EXTENSION);
        //             $imgstr = getUrlSafeString($fl['name'][$i]);
        //             $moreimgname = str_replace(" ", "_", $imgstr) . uniqid("_moreimg_") . "." . $ext;
        //             $dir = MEDIA_ROOT . "images/pages/" . $moreimgname;
        //             $upload = move_uploaded_file($fl['tmp_name'][$i], $dir);
        //             if ($upload) {
        //                 $moreimg[] = $moreimgname;
        //             }
        //         }
        //     }
        //     $arr['imgs'] = json_encode($moreimg);
        // }
        $postid = (new Model('content'))->store($arr);
        if (intval($postid)) {
            $upload = false;
            if (isset($request->banner)) {
                $ext = pathinfo($request->banner['name'], PATHINFO_EXTENSION);
                $imgstr = getUrlSafeString($request->title);
                $imgname = str_replace(" ", "_", $imgstr) . uniqid("_") . "." . $ext;
                $dir = MEDIA_ROOT . "images/pages/" . $imgname;
                $upload = move_uploaded_file($request->banner['tmp_name'], $dir);
            }
            if ($upload) {
                (new Model('content'))->update($postid, array('banner' => $imgname));
            }
            echo js_alert('game created');
            echo go_to(route('gameList'));
        } else {
            echo js_alert('game not created');
            return false;
        }
    }
    function delete_bulk_game()
    {
        $action = $_POST['action'] ?? null;
        $ids = $_POST['selected_ids'] ?? null;
        if ($action != null && $action == "delete_selected_items" && $ids != null) {
            $num = count($ids);
            if ($num == 0) {
                echo js_alert('Object not seleted');
                exit;
            };
            $idsString = implode(',', $ids);
            $db = new Dbobjects;
            $pdo = $db->conn;
            $pdo->beginTransaction();
            $sql = "DELETE FROM content WHERE id IN ($idsString) and content_group='game'";
            try {
                $db->show($sql);
                $pdo->commit();
                echo js_alert("$num Selected item deleted");
                echo RELOAD;
                return true;
            } catch (PDOException $pd) {
                $pdo->rollBack();
                echo js_alert('Database quer error');
                return false;
            }
        } else {
            echo js_alert('Action not or items not selected');
            exit;
        }
    }
    function upload_bulk_game($req = null)
    {
        $req = obj($req);
        $data = null;
        $data = $_POST;
        $data['csvfile'] = $_FILES['csvfile'] ?? null;

        $rules = [
            'csvfile' => 'required|file',
            'game_id' => 'required|numeric',
        ];

        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            exit;
        }
        $req = obj($data);
        if ($req->csvfile['name'] != "" && $req->csvfile['error'] == 0) {
            $ext = pathinfo($req->csvfile['name'], PATHINFO_EXTENSION);
            if ($ext != 'csv') {
                msg_set('Invalid file format, please provide .csv');
                echo js_alert(msg_ssn("msg", true));
                exit;
            } else {
                $csvFilePath = $req->csvfile['tmp_name'];
                $csv = Reader::createFromPath($csvFilePath, 'r');
                $csv->setHeaderOffset(0); // Assumes the first row contains headers
                $db = new Dbobjects;

                $total = iterator_count($csv->getRecords());
                foreach ($csv->getRecords() as $key => $record) {
                    set_time_limit(60);
                    $rc = obj($record);
                    $exists = $db->showOne("select link from content where content_group='game' and link='{$rc->url}'");
                    $db->tableName = 'content';
                    $db->insertData['title'] = $rc->url_title;
                    $db->insertData['content'] = $rc->details;
                    $db->insertData['price'] = $rc->price;
                    $db->insertData['link'] = $rc->url;
                    $db->insertData['parent_id'] = $req->game_id;
                    $db->insertData['content_group'] = 'game';
                    $db->insertData['slug'] = generate_slug(uniqid($rc->url_title));
                    $db->insertData['opens_at'] = $rc->opens_at;
                    $db->insertData['closes_at'] = $rc->closes_at;
                    $db->insertData['created_by'] = USER['id'];
                    try {
                        if (!$exists) {
                            $db->create();
                        }
                    } catch (PDOException $th) {
                        throw $th;
                    };
                    echo server_progress($key, $total) . "<br>";
                }
            }
        }
    }
    public function toggle_trending($req = null)
    {

        $request = json_decode(file_get_contents('php://input'));
        if (isset($request->content_id) && isset($request->action) && ($request->action == 'is_trending' || $request->action == 'is_featured')) {
            $id = $request->content_id;
            $tobj = new Model('content');
            $arr['id'] = $id;
            $arr[$request->action] = 1;
            $trending_post = $tobj->filter_index($arr);
            if (count($trending_post) > 0) {
                $tobj->update($id, [$request->action => 0]);
                $res['msg'] = 'success';
                $res['data'] = "Page removed from $request->action";
            } else {
                $tobj->update($id, [$request->action => 1]);
                $res['msg'] = 'success';
                $res['data'] = "game marked as $request->action";
            }
            echo json_encode($res);
            exit;
        } else {
            $res['msg'] = 'Something went wrong';
            $res['data'] = null;
            echo json_encode($res);
            exit;
        }
    }
    // Save by ajax call
    public function update($req = null)
    {
        $req = obj($req);
        $content = obj(getData(table: 'content', id: $req->id));
        if ($content == false) {
            $_SESSION['msg'][] = "Object not found";
            echo js_alert(msg_ssn("msg", true));
            exit;
        }
        $request = null;
        $data = null;
        $data = $_POST;
        $data['id'] = $req->id;
        $data['banner'] = $_FILES['banner'] ?? null;
        $rules = [
            'id' => 'required|integer',
            // 'title' => 'required|string',
            // 'content' => 'required|string',
            'parent_id' => 'required|integer',
            // 'price' => 'required|numeric',
            'link' => 'required|string',
            // 'opens_at' => 'required|time',
            // 'closes_at' => 'required|time',
        ];

        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            exit;
        }
        $request = obj($data);
        $json_arr = array();
        if (isset($request->meta_tags)) {
            $json_arr['meta']['tags'] = $request->meta_tags;
        }
        if (isset($request->meta_description)) {
            $json_arr['meta']['description'] = $request->meta_description;
        }
        if (isset($request->link)) {
            $arr = null;
            $arr['json_obj'] = json_encode($json_arr);
            $arr['content_group'] = "game";
            // $arr['title'] = $request->title;
            // if ($content->slug != $request->slug) {
            //     $arr['slug'] = generate_slug(trim($request->slug));
            // }
            // $arr['content'] = $request->content;
            // // $arr['days'] = $request->days;
            // $arr['price'] = $request->price;
            // $arr['city'] = $request->city;
            $arr['parent_id'] = $request->parent_id;
            $arr['updated_at'] = date('Y-m-d H:i:s');
            $arr['link'] = $request->link;
            // $arr['opens_at'] = $request->opens_at;
            // $arr['closes_at'] = $request->closes_at;
            $arr['is_sold'] = isset($request->is_sold) ? 1 : 0;
            $imsgjsn = json_decode($content->imgs ?? '[]', true);
            $moreimg = [];
            if (isset($_FILES['moreimgs'])) {
                $fl = $_FILES['moreimgs'];
                for ($i = 0; $i < count($fl['name']); $i++) {
                    if ($fl['name'][$i] != '' && $fl['error'][$i] === UPLOAD_ERR_OK) {
                        $ext = pathinfo($fl['name'][$i], PATHINFO_EXTENSION);
                        $imgstr = getUrlSafeString($fl['name'][$i]);
                        $moreimgname = str_replace(" ", "_", $imgstr) . uniqid("_moreimg_") . "." . $ext;
                        $dir = MEDIA_ROOT . "images/pages/" . $moreimgname;
                        $upload = move_uploaded_file($fl['tmp_name'][$i], $dir);
                        if ($upload) {
                            $moreimg[] = $moreimgname;
                        }
                    }
                }
                $newimgs = array_merge($imsgjsn, $moreimg);
                $arr['imgs'] = json_encode($newimgs);
            }
            if (isset($request->banner)) {
                if ($request->banner['name'] != "" && $request->banner['error'] == 0) {
                    $ext = pathinfo($request->banner['name'], PATHINFO_EXTENSION);
                    $imgstr = getUrlSafeString($request->title);
                    $imgname = str_replace(" ", "_", $imgstr) . uniqid("_") . "." . $ext;
                    $dir = MEDIA_ROOT . "images/pages/" . $imgname;
                    $upload = move_uploaded_file($request->banner['tmp_name'], $dir);
                    if ($upload) {
                        $arr['banner'] = $imgname;
                        $old = obj($content);
                        if ($old) {
                            if ($old->banner != "") {
                                $olddir = MEDIA_ROOT . "images/pages/" . $old->banner;
                                if (file_exists($olddir)) {
                                    unlink($olddir);
                                }
                            }
                        }
                    }
                }
            }
            try {
                (new Model('content'))->update($request->id, $arr);
                echo js_alert('game updated');
                // echo js_alert($request->closes_at);
                echo go_to(route('gameEdit', ['id' => $request->id]));
                exit;
            } catch (PDOException $e) {
                echo js_alert('game not updated, check slug or content data');
                exit;
            }
        }
    }
    function delete_more_img($req = null)
    {
        header('Content-Type: application/json');
        $datavald = $_POST;
        $req = obj($_POST);
        $rules = [
            'content_id' => 'required|integer',
            'img_src' => 'required|string',
        ];
        $pass = validateData(data: $datavald, rules: $rules);
        $data = null;
        if (!$pass) {
            $data['msg'] = msg_ssn(return: true, lnbrk: "<br>");
            $data['success'] = false;
            $data['data'] = null;
            echo json_encode($data);
            exit;
        }
        $db = new Dbobjects;
        $pdo = $db->conn;
        $pdo->beginTransaction();
        $db->tableName = "content";
        $content = $db->pk($req->content_id);
        if ($content) {
            $content = obj($content);
            $imgsjson = modifyJsonArray(jsonString: $content->imgs, valueToDelete: $req->img_src);
            if ($imgsjson !== false) {
                $imgpath = RPATH . "/media/images/pages/" . $req->img_src;
                if ($req->img_src != null && file_exists($imgpath)) {
                    unlink($imgpath);
                }
                try {
                    $db->insertData['imgs'] = $imgsjson;
                    $db->pk($req->content_id);
                    $db->update();
                    $pdo->commit();
                    msg_set("Image deleted");
                    $data['msg'] = msg_ssn(return: true, lnbrk: "\n");
                    $data['success'] = true;
                    $data['data'] = null;
                    echo json_encode($data);
                    exit;
                } catch (PDOException $th) {
                    $pdo->rollback();
                    msg_set("Image not deleted");
                    $data['msg'] = msg_ssn(return: true, lnbrk: "\n");
                    $data['success'] = false;
                    $data['data'] = null;
                    echo json_encode($data);
                    exit;
                }
            }
        } else {
            msg_set("content not found");
            $data['msg'] = msg_ssn(return: true, lnbrk: "\n");
            $data['success'] = false;
            $data['data'] = null;
            echo json_encode($data);
            exit;
        }
    }
    public function move_to_trash($req = null)
    {
        $req = obj($req);
        $content = obj(getData(table: 'content', id: $req->id));
        if ($content == false) {
            $_SESSION['msg'][] = "Object not found";
            echo js_alert(msg_ssn("msg", true));
            exit;
        }
        $data = null;
        $data['id'] = $req->id;
        $rules = [
            'id' => 'required|integer'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            exit;
        }
        try {
            (new Model('content'))->update($req->id, array('is_active' => 0));
            // echo js_alert('Content moved to trash');
            echo go_to(route('gameList'));
            exit;
        } catch (PDOException $e) {
            echo js_alert('Content not moved to trash');
            exit;
        }
    }
    public function restore($req = null)
    {
        $req = obj($req);
        $content = obj(getData(table: 'content', id: $req->id));
        if ($content == false) {
            $_SESSION['msg'][] = "Object not found";
            echo js_alert(msg_ssn("msg", true));
            exit;
        }
        $data = null;
        $data['id'] = $req->id;
        $rules = [
            'id' => 'required|integer'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            exit;
        }
        try {
            (new Model('content'))->update($req->id, array('is_active' => 1));
            // echo js_alert('Content moved to active list');
            echo go_to(route('gameTrashList'));
            exit;
        } catch (PDOException $e) {
            echo js_alert('Content not moved to active list');
            exit;
        }
    }
    public function delete_trash($req = null)
    {
        $req = obj($req);
        $content = obj(getData(table: 'content', id: $req->id));
        if ($content == false) {
            $_SESSION['msg'][] = "Object not found";
            echo js_alert(msg_ssn("msg", true));
            exit;
        }
        $data = null;
        $data['id'] = $req->id;
        $rules = [
            'id' => 'required|integer'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            exit;
        }
        try {
            $content_exists = (new Model('content'))->exists(['id' => $req->id, 'is_active' => 0]);
            if ($content_exists) {
                if ((new Model('content'))->destroy($req->id)) {
                    // echo js_alert('Content deleted permanatly');
                    echo go_to(route('gameTrashList'));
                    exit;
                }
            }
            echo js_alert('Content does not exist');
            echo go_to(route('gameTrashList'));
            exit;
        } catch (PDOException $e) {
            echo js_alert('Content not deleted');
            exit;
        }
    }
    // render function
    public function render_main($context = null)
    {
        import("apps/admin/layouts/admin-main.php", $context);
    }
    // Post list
    public function game_list($ord = "DESC", $limit = 5, $active = 1, $sort_by = 'id',$game_id=null)
    {
        $cntobj = new Model('content');
        if ($game_id!=null) {
            return $cntobj->filter_index(array('parent_id'=>$game_id,'content_group' => 'game', 'is_active' => $active), $ord, $limit, $change_order_by_col = $sort_by);
        }
        return $cntobj->filter_index(array('content_group' => 'game', 'is_active' => $active), $ord, $limit, $change_order_by_col = $sort_by);
    }
    public function game_search_list($keyword, $ord = "DESC", $limit = 5, $active = 1,$game_id=null)
    {
        $cntobj = new Model('content');
        $search_arr['id'] = $keyword;
        $search_arr['title'] = $keyword;
        // $search_arr['content'] = $keyword;
        $search_arr['author'] = $keyword;
        // $search_arr['created_at'] = $keyword;
        // $search_arr['updated_at'] = $keyword;
        if ($game_id!=null) {
            return $cntobj->search(
                assoc_arr: $search_arr,
                ord: $ord,
                limit: $limit,
                whr_arr: array('parent_id'=>$game_id,'content_group' => 'game', 'is_active' => $active)
            );
        }
        return $cntobj->search(
            assoc_arr: $search_arr,
            ord: $ord,
            limit: $limit,
            whr_arr: array('content_group' => 'game', 'is_active' => $active)
        );
    }
    public function game_detail($id)
    {
        $cntobj = new Model('content');
        return $cntobj->show($id);
    }
    // category list
    public function cat_list($ord = "DESC", $limit = 5, $active = 1)
    {
        $cntobj = new Model('content');
        return $cntobj->filter_index(array('content_group' => 'product_category', 'is_active' => $active), $ord, $limit);
    }
}
