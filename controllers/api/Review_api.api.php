<?php
class Review_api
{
    public $db;
    function __construct()
    {
        $this->db = (new DB_ctrl)->db;
    }
    function create($req)
    {
        header('Content-Type: application/json');
        $method = $_SERVER['REQUEST_METHOD'];
        if (strtoupper($method) !== 'POST') {
            msg_set("Only post method is allowed");
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $req = obj($req);
        $data  = json_decode(file_get_contents("php://input"), true);
        $rules = [
            'token' => 'required|string',
            'product_id' => 'required|numeric',
            'message' => 'required|string',
            'point' => 'required|numeric'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $req = obj($data);
        $user = (new Users_api)->get_user_by_token($req->token);
        if (!$user) {
            msg_set("Invalid token");
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        if (!(0 < $req->point && $req->point <= 5)) {
            msg_set("The Rating point range is 1 to 5");
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $user = obj($user);
        $this->db->tableName = 'review';
        $arr['item_id'] = $req->product_id;
        $arr['email'] = $user->email;
        $already = $this->db->findOne($arr);
        $arr['name'] = "{$user->first_name} {$user->last_name}";
        $arr['message'] = $req->message;
        $arr['status'] = 1; //1: published 0: pending
        $arr['item_id'] = $req->product_id; //1: published 0: pending
        $arr['item_group'] = 'product';
        $arr['rating'] = $req->point;
        $this->db->insertData = $arr;
        try {
            if ($already) {
                $this->db->update();
                msg_set('Review updated successfully');
                $api['success'] = true;
                $api['data'] = [];
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            } else {
                $this->db->create();
                msg_set('Review created successfully');
                $api['success'] = true;
                $api['data'] = [];
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            }
        } catch (PDOException $th) {
            msg_set('Review not created');
            $api['success'] = false;
            $api['data'] = [];
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
    function list_by_product_id($req = null)
    {
        header('Content-Type: application/json');
        // $method = $_SERVER['REQUEST_METHOD'];
        // if (strtoupper($method)!=='POST') {
        //     msg_set("Only post method is allowed");
        //     $api['success'] = false;
        //     $api['data'] = null;
        //     $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
        //     echo json_encode($api);
        //     exit;
        // }
        $req = obj($req);
        // $data  = json_decode(file_get_contents("php://input"), true);
        // $rules = [
        //     // 'token' => 'required|string',
        //     'product_id' => 'required|numeric'
        // ];
        // $pass = validateData(data: $data, rules: $rules);
        // if (!$pass) {
        //     $api['success'] = false;
        //     $api['data'] = null;
        //     $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
        //     echo json_encode($api);
        //     exit;
        // }
        // $req = obj($data);
        // $user = (new Users_api)->get_user_by_token($req->token);
        // if (!$user) {
        //     msg_set("Invalid token");
        //     $api['success'] = false;
        //     $api['data'] = null;
        //     $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
        //     echo json_encode($api);
        //     exit;
        // }
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
