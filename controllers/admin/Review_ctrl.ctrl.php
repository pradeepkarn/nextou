<?php
class Review_ctrl
{
    public $db;
    function __construct()
    {
        $this->db = (new DB_ctrl)->db;
    }
    function admin_create($req)
    {
        // header('Content-Type: application/json');
        $method = $_SERVER['REQUEST_METHOD'];
        if (strtoupper($method) !== 'POST') {
            msg_set("Only post method is allowed");
            echo msg_ssn(return: true, lnbrk: ", ");
            exit;
        }
        $req = obj($req);
        $data  = $_POST;
        $rules = [
            'product_id' => 'required|numeric',
            'point' => 'required|numeric',
            'name' => 'required|string',
            'message' => 'required|string',

        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo msg_ssn(return: true, lnbrk: ", ");
            exit;
        }
        $req = obj($data);
        // $user = (new Users_api)->get_user_by_token($req->token);
        if (!is_admin()) {
            msg_set("Your are not and admin, access denied");
            echo msg_ssn(return: true, lnbrk: ", ");
            exit;
        }
        if (!(0 < $req->point && $req->point <= 5)) {
            msg_set("The Rating point range is 1 to 5");
            echo msg_ssn(return: true, lnbrk: ", ");
            exit;
        }
        $this->db->tableName = 'review';
        $arr['item_id'] = $req->product_id;
        $arr['email'] = generate_dummy_email('usr');
        $already = $this->db->findOne($arr);
        $arr['name'] = $req->name;
        $arr['message'] = $req->message;
        $arr['status'] = 1; //1: published 0: pending
        $arr['item_id'] = $req->product_id; //1: published 0: pending
        $arr['item_group'] = 'product';
        $arr['rating'] = $req->point;
        $arr['by_admin'] = 1;
        $datetime = date("Y-m-d H:i:s");
        $arr['updated_at'] = $datetime;
        try {
            if ($already) {
                $this->db->insertData = $arr;
                $this->db->update();
                msg_set('Review updated successfully');
                echo msg_ssn(return: true, lnbrk: ", ");
                _note(message: "Review via admin: updated successfully", created_by: USER['id'], cg: 1, via: 1);
                echo RELOAD;
                exit;
            } else {
                $arr['created_at'] = $datetime;
                $this->db->insertData = $arr;
                $this->db->create();
                msg_set('Review created successfully');
                echo msg_ssn(return: true, lnbrk: ", ");
                _note(message: "Review via admin: created successfully", created_by: USER['id'], cg: 1, via: 1);
                echo RELOAD;
                exit;
            }
        } catch (PDOException $th) {
            msg_set('Review not created');
            echo msg_ssn(return: true, lnbrk: ", ");
            exit;
        }
    }
    function reviwe_delete($req = null)
    {
        header('Content-Type: application/json');
        $method = $_SERVER['REQUEST_METHOD'];
        if (strtoupper($method) !== 'POST') {
            msg_set("Only post method is allowed");
            echo msg_ssn(return: true, lnbrk: ", ");
            exit;
        }
        $req = obj($req);
        $data  = $_POST;
        $rules = [
            'review_id' => 'required|numeric'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo msg_ssn(return: true, lnbrk: ", ");
            exit;
        }
        $req = obj($data);
        if (!is_admin()) {
            msg_set("Your are not and admin, access denied");
            echo msg_ssn(return: true, lnbrk: ", ");
            exit;
        }
        $this->db->tableName = 'review';
        $this->db->pk($req->review_id);
        $already = $this->db->pk($req->review_id);
        try {
            if ($already) {
                $this->db->delete();
                echo msg_ssn(return: true, lnbrk: ", ");
                _note(message: "Review: {$req->review_id} deleted permanantly", created_by: USER['id'], cg: 1, via: 1);
                echo RELOAD;
                exit;
            } else {
                msg_set('Review not found');
                echo msg_ssn(return: true, lnbrk: ", ");
                exit;
            }
        } catch (PDOException $th) {
            _note(message: "Review: {$req->review_id} not deleted database error", created_by: USER['id'], cg: 1, via: 1);
            msg_set('Review not deleted database error');
            echo msg_ssn(return: true, lnbrk: ", ");
            exit;
        }
    }
    function list_by_product_id($req = null)
    {
        $req = obj($req);
        if (!isset($req->pid)) {
            msg_set("Please provide product id");
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $sql = "select id,rating,name,email,message from review where item_id = '$req->pid' and item_group = 'product'";
        $review = $this->db->show($sql);
        if ($review) {
            msg_set("Review found");
            $api['success'] = true;
            $api['data'] = $review;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        } else {
            msg_set("Review not found");
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
}
