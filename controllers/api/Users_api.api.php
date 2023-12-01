<?php

class Users_api
{
    public $get;
    public $post;
    public $files;
    public $db;
    function __construct()
    {
        $this->db = (new DB_ctrl)->db;
        $this->post = obj($_POST);
        $this->get = obj($_GET);
        $this->files = isset($_FILES) ? obj($_FILES) : null;
    }
    function login($req = null)
    {
        header('Content-Type: application/json');
        $ok = true;
        $req = obj($req);
        $data  = json_decode(file_get_contents('php://input'));
        if (isset($req->ug)) {
            if (!in_array($req->ug, USER_GROUP_LIST)) {
                $ok = false;
                msg_set("Invalid account group");
            }
        } else {
            $ok = false;
            msg_set("No user group provided");
        }
        if (!$ok) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $rules = [
            'credit' => 'required|string',
            'password' => 'required|string'
        ];

        $pass = validateData(data: arr($data), rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $user = false;
        $this->db->tableName = "pk_user";
        if (!$user) {
            $arr['username'] = $data->credit;
            $arr['password'] = md5($data->password);
            $user = $this->db->findOne($arr);
            $arr = null;
        }
        if (!$user) {
            $arr['email'] = $data->credit;
            $arr['password'] = md5($data->password);
            $user = $this->db->findOne($arr);
            $arr = null;
        }

        if (!$user) {
            $arr['mobile'] = $data->credit;
            $arr['password'] = md5($data->password);
            $user = $this->db->findOne($arr);
            $arr = null;
        }

        if ($user) {
            if ($user['user_group'] != $req->ug) {
                $ok = false;
                msg_set("Invalid login portal");
                $api['success'] = false;
                $api['data'] = null;
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            }
            $after_second = 10 * 60;
            $app_login_time = strtotime($user['app_login_time'] ?? date('Y-m-d H:i:s'));
            $time_out = $after_second + $app_login_time;
            $current_time = strtotime(date('Y-m-d H:i:s'));
            if ($current_time > $time_out) {
                $token = uniqid() . bin2hex(random_bytes(8)) . "u" . $user['id'];
                $datetime = date('Y-m-d H:i:s');
                $this->db->tableName = 'pk_user';
                $this->db->insertData = array('app_login_token' => $token, 'app_login_time' => $datetime);
                $this->db->pk($user['id']);
                $this->db->update();
                $user = $this->get_user_by_id($id = $user['id']);
                msg_set("User found, token refreshed");
                $api['success'] = true;
                $api['data'] = $user;
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            } else {
                $user = $this->get_user_by_id($id = $user['id']);
                msg_set("User found");
                $api['success'] = true;
                $api['data'] = $user;
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            }
        } else {
            msg_set("User not found");
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
    function update_account($req = null)
    {
        header('Content-Type: application/json');
        $ok = true;
        $req = obj($req);
        $data  = $_POST;
        $data['image'] = $_FILES['image'] ?? null;


        if (isset($req->ug)) {
            if (!in_array($req->ug, USER_GROUP_LIST)) {
                $ok = false;
                msg_set("Invalid account group");
            }
        } else {
            $ok = false;
            msg_set("No user group provided");
        }
        if (!$ok) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $rules = [
            'token' => 'required|string'
        ];

        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }

        $request = obj($data);
        $user = $this->get_user_by_token($request->token);
        if (!$user) {
            msg_set("Invalid token");
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $user = obj($user);
        // $request->username = $user->username;
        $this->db = $this->db;
        $pdo = $this->db->conn;
        $pdo->beginTransaction();
        $this->db->tableName = 'pk_user';

        if (!$ok) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        if (isset($user)) {
            $arr = null;
            $arr['first_name'] = $request->first_name ?? $user->first_name;
            $arr['last_name'] = $request->last_name ?? $user->last_name;
            if (isset($request->password)) {
                $arr['password'] = md5($request->password);
            }

            if (isset($request->bio)) {
                $arr['bio'] = $request->bio;
            }
            $arr['created_at'] = date('Y-m-d H:i:s');
            $this->db->tableName = 'pk_user';
            $this->db->insertData = $arr;
            try {
                $this->db->pk($user->id);
                $this->db->update();
                $request->username = $user->username;
                if (isset($_FILES['image'])) {
                    $filearr = $this->upload_files($user->id, $request);
                    if ($filearr) {
                        $this->db->pk($user->id);
                        $this->db->insertData = $filearr;
                        $this->db->update();
                    }
                }

                msg_set('Account updated');
                $ok = true;
                $pdo->commit();
            } catch (PDOException $th) {
                $pdo->rollBack();
                msg_set('Account not updated');
                $ok = false;
            }
        } else {
            $pdo->rollBack();
            msg_set('Missing required field, uaser not updated');
            $ok = false;
        }
        if (!$ok) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        } else {
            $api['success'] = true;
            $api['data'] = $this->get_user_by_token($request->token);
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
    function login_via_token($req = null)
    {
        header('Content-Type: application/json');
        $ok = true;
        $req = obj($req);
        $data  = json_decode(file_get_contents('php://input'));
        if (isset($req->ug)) {
            if (!in_array($req->ug, USER_GROUP_LIST)) {
                $ok = false;
                msg_set("Invalid account group");
            }
        } else {
            $ok = false;
            msg_set("No user group provided");
        }
        if (!$ok) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $rules = [
            'token' => 'required|string'
        ];

        $pass = validateData(data: arr($data), rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $user = false;
        $user = $this->get_user_by_token($data->token);

        if ($user) {
            if ($user['user_group'] != $req->ug) {
                $ok = false;
                msg_set("Invalid login portal");
                $api['success'] = false;
                $api['data'] = null;
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            }
            msg_set("User found");
            $api['success'] = true;
            $api['data'] = $user;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }else{
            msg_set("User not found, invalid token");
            $api['success'] = true;
            $api['data'] = $user;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
    function create_account($req = null)
    {
        header('Content-Type: application/json');
        $ok = true;
        $req = obj($req);
        $data  = $_POST;
        $data['image'] = $_FILES['image'] ?? null;
        $data['dl_doc'] = $_FILES['dl_doc'] ?? null;
        $data['nid_doc'] = $_FILES['nid_doc'] ?? null;

        if (isset($req->ug)) {
            if (!in_array($req->ug, USER_GROUP_LIST)) {
                $ok = false;
                msg_set("Invalid account group");
            }
        } else {
            $ok = false;
            msg_set("No user group provided");
        }
        if (!$ok) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $rules = [
            'email' => 'required|email',
            'image' => 'required|file',
            'first_name' => 'required|string',
            'password' => 'required|string'
        ];
        if ($req->ug == 'caterer') {
            $rules_caterer = [
                'nid_doc' => 'required|file',
                'nid_no' => 'required|string',
            ];
            $rules = array_merge($rules, $rules_caterer);
        }
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }

        $request = obj($data);
        $request->username = $request->username??uniqid();
        $this->db = $this->db;
        $pdo = $this->db->conn;
        $pdo->beginTransaction();
        $this->db->tableName = 'pk_user';
        $username = generate_clean_username($request->username );
        $username_exists = $this->db->get(['username' => $username]);
        $email_exists = $this->db->get(['email' => $request->email]);
        if ($username_exists) {
            $_SESSION['msg'][] = 'Usernam not availble please try with another username';
            $ok = false;
        }
        if ($email_exists) {
            $_SESSION['msg'][] = 'Email is already exists';
            $ok = false;
        }
        if (!$ok) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        if (isset($request->email)) {
            $arr = null;
            $arr['user_group'] = $req->ug;
            $arr['email'] = $request->email;
            $arr['username'] = $username;
            $arr['first_name'] = $request->first_name;
            $arr['last_name'] = $request->last_name ?? null;
            $arr['isd_code'] = intval($request?->isd_code) ?? null;
            $arr['mobile'] = intval($request?->mobile) ?? null;
            $arr['password'] = md5($request->password);
            $arr['nid_no'] = sanitize_remove_tags($request->nid_no ?? null);
           
            if (isset($request->bio)) {
                $arr['bio'] = $request->bio;
            }
            $arr['created_at'] = date('Y-m-d H:i:s');
            $this->db->tableName = 'pk_user';
            $this->db->insertData = $arr;
            try {
                $userid = $this->db->create();
                $filearr = $this->upload_files($userid, $request);
                if ($filearr) {
                    $this->db->pk($userid);
                    $this->db->insertData = $filearr;
                    $this->db->update();
                }
                msg_set('Account created');
                $ok = true;
                $pdo->commit();
            } catch (PDOException $th) {
                $pdo->rollBack();
                msg_set('Account not created');
                $ok = false;
            }
        } else {
            $pdo->rollBack();
            msg_set('Missing required field, uaser not created');
            $ok = false;
        }
        if (!$ok) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        } else {
            $api['success'] = true;
            $api['data'] = [];
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }

    function upload_files($postid, $request, $user = null)
    {
        if (intval($postid)) {
            $old = $user ? obj($user) : null;
            if (isset($request->image) && $request->image['name'] != "" && $request->image['error'] == 0) {
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

   
    function get_user_by_id($id = null)
    {
        if ($id) {
            $u = $this->db->showOne("select * from pk_user where id = $id");
            if ($u) {
                $u = obj($u);
                return array(
                    'id' => strval($u->id),
                    'user_group' => $u->user_group,
                    'username' => strval($u->username),
                    'first_name' => $u->first_name,
                    'last_name' => $u->last_name,
                    'image' => dp_or_null($u->image),
                    'email' => $u->email,
                    'isd_code' => $u->isd_code,
                    'mobile' => $u->mobile,
                    'food_category' => $u->food_category,
                    'token' => $u->app_login_token,
                );
            }
        }
        return false;
    }
    function get_user_by_token($token = null)
    {
        if ($token) {
            $u = $this->db->showOne("select * from pk_user where app_login_token = '$token'");
            if ($u) {
                $u = obj($u);
                return array(
                    'id' => strval($u->id),
                    'user_group' => $u->user_group,
                    'username' => strval($u->username),
                    'first_name' => $u->first_name,
                    'last_name' => $u->last_name,
                    'image' => dp_or_null($u->image),
                    'email' => $u->email,
                    'isd_code' => $u->isd_code,
                    'mobile' => $u->mobile,
                    'food_category' => $u->food_category,
                    'token' => $u->app_login_token,
                );
            }
        }
        return false;
    }
}
