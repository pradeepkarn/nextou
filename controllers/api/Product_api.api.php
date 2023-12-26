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
        $products = $this->get_all_products($user_id);
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


        $created_at = date('Y-m-d H:i:s');

        try {
            $db = $this->db;
            $db->tableName = "bookmarks";
            $is_fav = $db->findOne(['content_id' => $req->id, 'content_group' => 'fav', 'user_id' => $user['id']]);
            if ($is_fav) {
                $db->delete();
                $api['success'] = true;
                $api['data'] =  false;
                msg_set('Favourite removed');
            } else {
                $db->insertData = ['content_id' => $req->id, 'content_group' => 'fav', 'user_id' => $user['id'], 'created_at' => $created_at];
                $db->create();
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
    function get_all_products($user_id = null)
    {
        $this->db->tableName = 'content';
        $arr['is_active'] = 1;
        $arr['content_group'] = 'product';
        $product_array = $this->db->filter($arr);
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
        $seller = $this->db->showOne("select id,first_name,last_name,address from pk_user where pk_user.id = '$p->created_by'");
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
            'seller' => $seller
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
}
