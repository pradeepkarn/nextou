<?php
class Logs_ctrl
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
        $products = $this->log_list($user_id = $user_id);
        if ($products) {
            msg_set('Log list fetched successfully');
            $api['success'] = true;
            $api['data'] = $products;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        } else {
            msg_set('Log not found');
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
    // Post list
    public function log_list($user_id, $ord = "DESC", $limit = 5, $sort_by = 'id')
    {
        $cntobj = new Model('notifications');
        return $cntobj->filter_index(['user_id' => $user_id], $ord, $limit, $change_order_by_col = $sort_by);
    }
    public function log_search_list($user_id, $keyword, $ord = "DESC", $limit = 5)
    {
        $cntobj = new Model('notifications');
        $search_arr['deviece_info'] = $keyword;
        $search_arr['message'] = $keyword;
        $search_arr['user_id'] = $keyword;
        return $cntobj->search(
            assoc_arr: $search_arr,
            ord: $ord,
            limit: $limit,
            whr_arr: ['user_id' => $user_id]
        );
    }
}
