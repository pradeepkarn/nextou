<?php

class Product_api
{
    public $db;
    function __construct()
    {
        $this->db = (new DB_ctrl)->db;
    }
    function list($req = null)
    {
        $req = obj($req);
        header('Content-Type: application/json');
        $products = $this->get_all_products();
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
        $products = $this->product_details($req->id);
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
    function create($req = null){
        header('Content-Type: application/json');
        $rules = [
            'category_id' => 'required|numeric',
            'title' => 'required|string',
            'content' => 'required|string',
            'banner' => 'required|file'
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
        $products = $this->product_details($req->id);
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
    function get_all_products()
    {
        $this->db->tableName = 'content';
        $arr['is_active'] = 1;
        $arr['content_group'] = 'product';
        $product_array = $this->db->filter($arr);
        $products = null;
        if ($product_array) {
            foreach ($product_array as $key => $p) {
                $p = obj($p);
                $products[] = $this->format_product($p);
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

    function product_details($id)
    {
        $this->db->tableName = 'content';
        $arr['is_active'] = 1;
        $arr['id'] = $id;
        $arr['content_group'] = 'product';
        $p = $this->db->findOne($arr);
        $prod = null;
        if ($p) {
            $p = obj($p);
            $prod = $this->format_product($p);
        }
        return  $prod;
    }
    function format_product(object $p)
    {
        $seller = $this->db->showOne("select id,first_name,last_name,address from pk_user where pk_user.id = '$p->created_by'");
        $imgs = json_decode($p->imgs ?? '[]');
        $images = array_map(function ($img) {
            return img_or_null($img);
        }, $imgs);
        return array(
            'id' => $p->id,
            'title' => $p->title,
            'price' => $p->price,
            'banner' => img_or_null($p->banner),
            'images' => $images,
            'seller' => $seller
        );
    }
}
