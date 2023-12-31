<?php

class Product_api
{
    public $db;
    private $headers;
    function __construct()
    {
        $this->db = (new DB_ctrl)->db;
        $this->headers = getallheaders();
    }
    function list($req = null)
    {
        $req = obj($req);
        header('Content-Type: application/json');
        $headers = $this->headers;
        $token = isset($headers['user_token']) ? $headers['user_token'] : null;
        $userapi = new Users_api;
        $user = $userapi->get_user_by_token($token);
        $user_id = null;
        if ($user) {
            $user_id = $user['id'];
        }
        $products = $this->get_all_products($liked_by = $user_id);
        if ($products) {
            msg_set('Products fetched successfully');
            $api['success'] = true;
            $api['data'] = $products;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        } else {
            msg_set('Product not found');
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
    function search_listing($req = null)
    {
        $req = isset($_GET['keyword']) ? $_GET : null;
        $req = obj($req);
        header('Content-Type: application/json');
        $headers = $this->headers;
        $token = isset($headers['user_token']) ? $headers['user_token'] : null;
        $userapi = new Users_api;
        $user = $userapi->get_user_by_token($token);
        $user_id = null;
        if ($user) {
            $user_id = $user['id'];
        }
        if (!isset($req->keyword)) {
            msg_set('Keyword is required');
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $products = $this->search_products($req->keyword, $liked_by = $user_id);
        if ($products) {
            msg_set('Products fetched successfully');
            $api['success'] = true;
            $api['data'] = $products;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        } else {
            msg_set('Product not found');
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
    function list_my_products($req = null)
    {
        $req = obj($req);
        header('Content-Type: application/json');
        $headers = $this->headers;
        $token = isset($headers['user_token']) ? $headers['user_token'] : null;
        $userapi = new Users_api;
        $user = $userapi->get_user_by_token($token);
        if (!$user) {
            msg_set('Invalid token');
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $user_id = $user['id'];
        $products = $this->get_my_products($user_id);
        if ($products) {
            msg_set('Products fetched successfully');
            $api['success'] = true;
            $api['data'] = $products;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        } else {
            msg_set('Product not found');
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
    function list_favs($req = null)
    {
        $req = obj($req);
        header('Content-Type: application/json');
        $headers = $this->headers;
        $token = isset($headers['user_token']) ? $headers['user_token'] : null;
        $userapi = new Users_api;
        $user = $userapi->get_user_by_token($token);
        $user_id = null;
        if ($user) {
            $user_id = $user['id'];
        }
        $products = $this->get_all_fav_products($user_id);
        if ($products) {
            msg_set('Products fetched successfully');
            $api['success'] = true;
            $api['data'] = $products;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        } else {
            msg_set('Product not found');
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
    function list_categories($req = null)
    {
        $req = obj($req);
        header('Content-Type: application/json');
        $products = $this->get_all_categories();
        if ($products) {
            msg_set('Categories fetched successfully');
            $api['success'] = true;
            $api['data'] = $products;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        } else {
            msg_set('Category not found');
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
    function mark_as_favunfav($req = null)
    {
        header('Content-Type: application/json');
        $request = null;
        $data = null;
        $data = json_decode(file_get_contents('php://input'));
        $rules = [
            'token' => 'required|string',
            'id' => 'required|numeric'
        ];
        $pass = validateData(data: arr($data), rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $request = $data;
        $content = obj(getData(table: 'content', id: $request->id));
        $req = $request;
        if ($content == false) {
            $_SESSION['msg'][] = "Object not found";
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $userCtrl = new Users_api;
        $user = $userCtrl->get_user_by_token($request->token);
        if (!$user) {
            msg_set('Invalid token');
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $u = obj($user);
        $created_at = date('Y-m-d H:i:s');

        try {
            $db = $this->db;
            $db->tableName = "bookmarks";
            $is_fav = $db->findOne(['content_id' => $req->id, 'content_group' => 'fav', 'user_id' => $user['id']]);
            if ($is_fav) {
                $db->delete();
                if ($u->firebase_device_token != '') {
                    Push_ctrl::push($u->firebase_device_token, array('title' => 'Favourite', 'body' => "Removed from favourite list"));
                }
                $api['success'] = true;
                $api['data'] =  false;
                msg_set('Favourite removed');
            } else {
                $db->insertData = ['content_id' => $req->id, 'content_group' => 'fav', 'user_id' => $user['id'], 'created_at' => $created_at];
                $db->create();
                if ($u->firebase_device_token != '') {
                    Push_ctrl::push($u->firebase_device_token, array('title' => 'Favourite', 'body' => "Marked as favourite"));
                }
                $api['success'] = true;
                $api['data'] =  true;
                msg_set('Favourite saved');
            }
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        } catch (PDOException $e) {
            msg_set('Product not saved/removed, check if any missing data');
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
    function details($req = null)
    {
        header('Content-Type: application/json');
        $rules = [
            'id' => 'required|string'
        ];
        $pass = validateData(data: $req, rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $req = obj($req);
        $headers = $this->headers;
        $token = isset($headers['user_token']) ? $headers['user_token'] : null;
        $userapi = new Users_api;
        $user = $userapi->get_user_by_token($token);
        $user_id = null;
        if ($user) {
            $user_id = $user['id'];
        }
        $products = $this->product_details($req->id, $user_id);
        if ($products) {
            msg_set('Products fetched successfully');
            $api['success'] = true;
            $api['data'] = $products;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        } else {
            msg_set('Product not found');
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
    // Save by ajax call
    public function create($req = null)
    {
        header('Content-Type: application/json');
        $request = null;
        $data = null;
        $data = $_POST;
        $data['banner'] = $_FILES['banner'] ?? null;
        $rules = [
            'token' => 'required|string',
            'title' => 'required|string',
            'price' => 'required|numeric',
            'content' => 'required|string',
            'banner' => 'required|file',
            'category_id' => 'required|numeric'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $request = obj($data);
        $userCtrl = new Users_api;
        $user = $userCtrl->get_user_by_token($request->token);
        if (!$user) {
            msg_set('Invalid token');
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $user = obj($user);
        $json_arr = array();
        if (isset($request->meta_tags)) {
            $json_arr['meta']['tags'] = $request->meta_tags;
        }
        if (isset($request->meta_description)) {
            $json_arr['meta']['description'] = $request->meta_description;
        }
        if (isset($request->title)) {
            $arr = null;
            $arr['json_obj'] = json_encode($json_arr);
            $arr['content_group'] = "product";
            $arr['title'] = $request->title;
            $arr['price'] = $request->price;
            $arr['slug'] = getUrlSafeString(generate_slug(trim($request->title)));
            $arr['content'] = $request->content;
            $arr['parent_id'] = $request->category_id;
            $arr['created_at'] = date('Y-m-d H:i:s');
            $arr['created_by'] = $user->id;

            // more images
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
                $arr['imgs'] = json_encode($moreimg);
            }

            $postid = (new Model('content'))->store($arr);
            if (intval($postid)) {
                if ($user->firebase_device_token != '') {
                    Push_ctrl::push($user->firebase_device_token, array('title' => 'Product created', 'body' => "Congratulations product {$request->title}, (id : {$postid}) created!"));
                }
                _note(message: "Product: {$postid} created", created_by: $user->id, cg: 1, via: 2);
                $ext = pathinfo($request->banner['name'], PATHINFO_EXTENSION);
                $imgname = str_replace(" ", "_", getUrlSafeString($request->title)) . uniqid("_") . "." . $ext;
                $dir = MEDIA_ROOT . "images/pages/" . $imgname;
                $upload = move_uploaded_file($request->banner['tmp_name'], $dir);
                if ($upload) {
                    (new Model('content'))->update($postid, array('banner' => $imgname));
                }
                msg_set('Product created');
                $api['success'] = true;
                $api['data'] =  [];
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            } else {
                if ($user->firebase_device_token != '') {
                    Push_ctrl::push($user->firebase_device_token, array('title' => 'Product not created', 'body' => "Product {$request->title} not created."));
                }
                msg_set('Product not created');
                $api['success'] = false;
                $api['data'] =  null;
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            }
        }
    }
    public function update($req = null)
    {
        header('Content-Type: application/json');
        $request = null;
        $data = null;
        $data = $_POST;
        $data['banner'] = $_FILES['banner'] ?? null;
        $rules = [
            'token' => 'required|string',
            'id' => 'required|integer',
            'title' => 'required|string',
            'price' => 'required|numeric',
            'content' => 'required|string',
            'category_id' => 'required|numeric'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $request = obj($data);
        $content = obj(getData(table: 'content', id: $request->id));
        if ($content == false) {
            $_SESSION['msg'][] = "Object not found";
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }

        $json_arr = array();
        $userCtrl = new Users_api;
        $user = $userCtrl->get_user_by_token($request->token);
        if (!$user) {
            msg_set('Invalid token');
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        if (isset($request->meta_tags)) {
            $json_arr['meta']['tags'] = $request->meta_tags;
        }
        if (isset($request->meta_description)) {
            $json_arr['meta']['description'] = $request->meta_description;
        }
        if (isset($request->title)) {
            $arr = null;
            $arr['json_obj'] = json_encode($json_arr);
            $arr['content_group'] = "product";
            $arr['title'] = $request->title;
            $arr['price'] = $request->price;
            // if ($content->slug != $request->slug) {
            //     $arr['slug'] = generate_slug(trim($request->slug));
            // }
            $arr['content'] = $request->content;
            $arr['parent_id'] = $request->category_id;
            $arr['updated_at'] = date('Y-m-d H:i:s');

            // update more images
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
            if (isset($_FILES['banner'])) {
                if ($request->banner['name'] != "" && $request->banner['error'] == 0) {
                    $ext = pathinfo($request->banner['name'], PATHINFO_EXTENSION);
                    $imgname = str_replace(" ", "_", getUrlSafeString($request->title)) . uniqid("_") . "." . $ext;
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
                _note(message: "Product: {$request->id} updated", created_by: $user['id'], cg: 1, via: 2);
                $u = obj($user);
                if ($u->firebase_device_token != '') {
                    Push_ctrl::push($u->firebase_device_token, array('title' => 'Product updated', 'body' => "Product {$request->title}, (id : {$request->id}) updated!"));
                }
                msg_set('Product updated');
                $api['success'] = true;
                $api['data'] =  [];
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            } catch (PDOException $e) {
                msg_set('Product not updated, check if any missing data');
                $api['success'] = false;
                $api['data'] =  null;
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            }
        }
        msg_set('Product not updated, title must not be empty');
        $api['success'] = false;
        $api['data'] =  null;
        $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
        echo json_encode($api);
        exit;
    }
    function get_all_products($liked_by = null)
    {
        $this->db->tableName = 'content';
        $arr['is_active'] = 1;
        $arr['content_group'] = 'product';
        $product_array = $this->db->filter($arr);
        $products = null;
        if ($product_array) {
            foreach ($product_array as $key => $p) {
                $p = obj($p);
                $products[] = $this->format_product($p, $liked_by);
            }
        }
        return  $products;
    }
    function search_products($keyword, $liked_by = null)
    {
        $this->db->tableName = 'content';
        $arr['is_active'] = 1;
        $arr['content_group'] = 'product';
        $sarr['title'] = $keyword;
        $sarr['content'] = $keyword;
        $product_array = $this->db->search(assoc_arr: $sarr, whr_arr: $arr);
        $products = null;
        if ($product_array) {
            foreach ($product_array as $key => $p) {
                $p = obj($p);
                $products[] = $this->format_product($p, $liked_by);
            }
        }
        return  $products;
    }
    function get_my_products($created_by = null)
    {
        if (!$created_by) {
            return null;
        }
        $this->db->tableName = 'content';
        $arr['is_active'] = 1;
        $arr['content_group'] = 'product';
        $arr['created_by'] = $created_by;
        $product_array = $this->db->filter($arr);
        $products = null;
        if ($product_array) {
            foreach ($product_array as $key => $p) {
                $p = obj($p);
                $products[] = $this->format_product($p, $created_by);
            }
        }
        return  $products;
    }
    function get_all_fav_products($user_id = null)
    {
        $sql = "select bookmarks.content_id from bookmarks where bookmarks.user_id = '$user_id' and content_group='fav'";
        $content_ids = $this->db->show($sql);
        if (!$content_ids) {
            return null;
        }
        $ids = array_column($content_ids, 'content_id');
        $idsString = implode(',', $ids);
        $sql = "select content.* from content where content_group = 'product' and content.is_active = 1 and content.id IN ($idsString)";
        $product_array = $this->db->show($sql);
        $products = null;
        if ($product_array) {
            foreach ($product_array as $key => $p) {
                $p = obj($p);
                $products[] = $this->format_product($p, $user_id);
            }
        }
        return  $products;
    }
    function get_all_categories()
    {
        $cats = $this->db->show("select id, title from content where content_group='product_category' and is_active=1;");
        if ($cats) {
            return $cats;
        }
        return  null;
    }

    function product_details($id, $user_id = null)
    {
        $this->db->tableName = 'content';
        $arr['is_active'] = 1;
        $arr['id'] = $id;
        $arr['content_group'] = 'product';
        $p = $this->db->findOne($arr);
        $prod = null;
        if ($p) {
            $p = obj($p);
            $prod = $this->format_product($p, $user_id);
        }
        return  $prod;
    }
    function format_product(object $p, $user_id = null)
    {
        $seller = $this->db->showOne("select id,first_name,last_name,address,isd_code,mobile from pk_user where pk_user.id = '$p->created_by'");
        $reviews = $this->db->show("select id,rating,name,email,message from review where item_id = '$p->id' and item_group = 'product' order by updated_at desc limit 5");
        $imgs = json_decode($p->imgs ?? '[]');
        $images = array_map(function ($img) {
            return img_or_null($img);
        }, $imgs);
        $is_fav = null;
        if ($user_id) {
            $is_fav = $this->is_fav_content($this->db, $user_id, $p->id);
        }
        return array(
            'id' => $p->id,
            'title' => $p->title,
            'content' => $p->content,
            'price' => $p->price,
            'is_fav' => $is_fav,
            'banner' => img_or_null($p->banner),
            'images' => $images,
            'seller' => $seller,
            'reviews' => $reviews,
        );
    }
    function is_fav_content($db, $user_id, $content_id)
    {
        $db->tableName = "bookmarks";
        $fav = $db->findOne(['user_id' => $user_id, 'content_id' => $content_id, 'content_group' => 'fav']);
        if ($fav) {
            return true;
        } else {
            return false;
        }
    }
    function upload_files($postid, $request, $user = null)
    {
        if (intval($postid)) {
            $old = $user ? obj($user) : null;
            if (isset($request->image) && $request->image['banner'] != "" && $request->image['error'] == 0) {
                $ext = pathinfo($request->image['name'], PATHINFO_EXTENSION);
                $imgname = str_replace(" ", "_", getUrlSafeString($request->username)) . uniqid("_") . "." . $ext;
                $dir = MEDIA_ROOT . "images/profiles/" . $imgname;
                $upload = move_uploaded_file($request->image['tmp_name'], $dir);
                if ($upload) {
                    $arr['image'] = $imgname;
                    if ($old) {
                        if ($old->image != "") {
                            $olddir = MEDIA_ROOT . "images/profiles/" . $old->image;
                            if (file_exists($olddir)) {
                                unlink($olddir);
                            }
                        }
                    }
                    $filearr['image'] = $imgname;
                }
            }
            if (isset($request->nid_doc) && $request->nid_doc['name'] != "" && $request->nid_doc['error'] == 0) {
                $ext = pathinfo($request->nid_doc['name'], PATHINFO_EXTENSION);
                $docname = str_replace(" ", "_", getUrlSafeString($request->username)) . uniqid("_") . "." . $ext;
                $dir = MEDIA_ROOT . "docs/" . $docname;
                $upload = move_uploaded_file($request->nid_doc['tmp_name'], $dir);
                if ($upload) {
                    $arr['nid_doc'] = $docname;
                    if ($old) {
                        if ($old->image != "") {
                            $olddir = MEDIA_ROOT . "docs/" . $old->nid_doc;
                            if (file_exists($olddir)) {
                                unlink($olddir);
                            }
                        }
                    }
                    $filearr['nid_doc'] = $docname;
                }
            }
            return $filearr;
        } else {
            return false;
        }
    }
    function chat_save_api($req = null)
    {
        header('Content-Type: application/json');
        $req = json_decode(file_get_contents('php://input'));
        $rules = [
            'token' => 'required|string',
            'receiver_id' => 'required|integer',
            'message' => 'required|string',
            'created_at' => 'required|datetime',
        ];
        $pass = validateData(data: arr($req), rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $userCtrl = new Users_api;
        $user = $userCtrl->get_user_by_token($req->token);
        $receiver = $userCtrl->get_user_by_id($req->receiver_id);
        if (!$receiver) {
            msg_set('Seller not found');
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        if (!$user) {
            msg_set('Invalid token');
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        if ($user['id'] == $receiver['id']) {
            msg_set('You can not chat to yourself');
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $user = obj($user);
        $receiver = obj($receiver);
        $was_saved = $this->save_chat_in_db($this->db, (object)array(
            "sender_id" => $user->id,
            "receiver_id" => $receiver->id,
            "message" => $req->message,
            "created_at" =>  $req->created_at
        ));
        if ($was_saved) {
            msg_set('Data saved');
            $api['success'] = true;
            $api['data'] =  [];
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        } else {
            msg_set('Data not saved');
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
    function save_chat_in_db($db, $data)
    {
        try {
            $db->tableName = 'chat_history';
            $arr['users'] = json_encode([$data->sender_id, $data->receiver_id]);
            $arr['sender_id'] = $data->sender_id;
            $arr['message'] = $data->message;
            $arr['created_at'] = $data->created_at;
            $arr['jsn'] = json_encode($data);
            $db->insertData = $arr;
            return $db->create();
        } catch (PDOException $th) {
            return null;
        }
    }
    function chat_hist_api($req = null)
    {
        header('Content-Type: application/json');
        $req = json_decode(file_get_contents('php://input'));
        $rules = [
            'token' => 'required|string',
            'seller_id' => 'required|integer',
        ];
        $pass = validateData(data: arr($req), rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $userCtrl = new Users_api;
        $user = $userCtrl->get_user_by_token($req->token);
        if (!$user) {
            msg_set('Invalid token');
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        if ($user['id'] == $req->seller_id) {
            msg_set('Please check seller id');
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $user = obj($user);
        $chat = $this->chat_history($this->db, $myid = $user->id, $sellerid = $req->seller_id);
        if ($chat) {
            msg_set('Chat history found');
            $api['success'] = true;
            $api['data'] =  $chat;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        } else {
            msg_set('No Chat history found');
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
    function chat_user_list_api($req = null)
    {
        header('Content-Type: application/json');
        $req = json_decode(file_get_contents('php://input'));
        $rules = [
            'token' => 'required|string'
        ];
        $pass = validateData(data: arr($req), rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $userCtrl = new Users_api;
        $user = $userCtrl->get_user_by_token($req->token);
        if (!$user) {
            msg_set('Invalid token');
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $user = obj($user);
        $chat = $this->chat_user_list($this->db, $myid = $user->id);
        if ($chat) {
            msg_set('Chat history found');
            $api['success'] = true;
            $api['data'] =  $chat;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        } else {
            msg_set('No user history found');
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }

    function chat_user_list($db = new Dbobjects, $myid)
    {
        try {
            $sql = "SELECT DISTINCT *
            FROM chat_history
            WHERE (JSON_UNQUOTE(JSON_EXTRACT(jsn, '$.sender_id')) = '$myid'
                OR JSON_UNQUOTE(JSON_EXTRACT(jsn, '$.receiver_id')) = '$myid')
            ORDER BY id DESC;
            ";
            $hist = $db->show($sql);
            $returnarr = [];
            $uniqueContacts = [];

            foreach ($hist as $key => $h) {
                $msgarr = null;
                $h = json_decode($h['jsn'], true);
                $msgarr['message'] = $h['message'];

                if ($h['sender_id'] == $myid) {
                    $cont = (new Users_api)->get_user_by_id($h['receiver_id']);
                } else {
                    $cont = (new Users_api)->get_user_by_id($h['sender_id']);
                }
                $msgarr['receiver_id'] = $cont['id'];
                $msgarr['first_name'] = $cont['first_name'];
                $msgarr['last_name'] = $cont['last_name'];
                $msgarr['image'] = dp_or_null($cont['image']);
                if (isset($h['created_at'])) {
                    $msgarr['created_at'] = strtotime($h['created_at']);
                }
                $returnarr[] = $msgarr;
            }

            // Iterate over the array and check for uniqueness based on the "contact" information
            foreach ($returnarr as $arr) {
                $contactId = $arr['receiver_id'];

                // Check if the contact ID is not already present in the $uniqueContacts array
                if (!isset($uniqueContacts[$contactId])) {
                    $uniqueContacts[$contactId] = $arr;
                }
            }

            // Reset array keys to maintain a sequential array
            $uniqueArr = array_values($uniqueContacts);

            return $uniqueArr;
        } catch (\PDOException $th) {
            return null;
        }
        return null;
    }
    function chat_history($db = new Dbobjects, $myid, $sellerid)
    {
        try {
            $sql = "SELECT *
            FROM chat_history
            WHERE (JSON_UNQUOTE(JSON_EXTRACT(jsn, '$.sender_id')) = '$myid'
               AND JSON_UNQUOTE(JSON_EXTRACT(jsn, '$.receiver_id')) = '$sellerid') OR
               (JSON_UNQUOTE(JSON_EXTRACT(jsn, '$.sender_id')) = '$sellerid'
               AND JSON_UNQUOTE(JSON_EXTRACT(jsn, '$.receiver_id')) = '$myid')
            ORDER BY created_at;
            ";
            $hist = $db->show($sql);
            if (!$hist) {
                return null;
            }
            return array_map(function ($h) {
                $h['chat_obj'] = json_decode($h['jsn'], true);
                if (isset($h['chat_obj']['created_at'])) {
                    $h['chat_obj']['created_at'] = strtotime($h['chat_obj']['created_at']);
                }
                unset($h['jsn']);
                unset($h['users']);
                unset($h['message']);
                return $h['chat_obj'];
            }, $hist);
        } catch (\PDOException $th) {
            return null;
        }
        return null;
    }
}
