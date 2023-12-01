<?php

class QR_api
{
    public $db;
    function __construct()
    {
        $this->db = (new DB_ctrl)->db;
    }
    function scan_data($req = null)
    {
        $req = obj($req);
        header('Content-Type: application/json');
        $data  = json_decode(file_get_contents("php://input"), true);
        $rules = [
            'token' => 'required|string',
            'event_id' => 'required|numeric',
            'scan_group' => 'required|numeric'
        ];
        $req->rescan = $req->rescan ?? 0;
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $req = obj($data);
        $req->qrdata = obj($req->qrdata);
        if (!isset($req->qrdata->id) || !isset($req->qrdata->email) || !isset($req->qrdata->nid) || !isset($req->qrdata->name)) {
            msg_set('Invalid qr code');
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        if (!in_array($req->scan_group, array_flip(QR_SCAN_GROUP))) {
            msg_set('Invalid scan group');
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        // if (!in_array($req->food_category, array_flip(FOOD_CATEGORY))) {
        //     msg_set('Invalid food category');
        //     $api['success'] = false;
        //     $api['data'] = null;
        //     $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
        //     echo json_encode($api);
        //     exit;
        // }
        $user = (new Users_api)->get_user_by_token($req->token);
        $event = $this->get_event_by_id($req->event_id);
        if (!$event) {
            msg_set('Event not found');
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        if ($user) {
            $saccned_user_id = $req->qrdata->id;
            $employee = (new Users_api)->get_user_by_id($req->qrdata->id);
            if (!$employee) {
                msg_set('Invalid employee id');
                $api['success'] = false;
                $api['data'] = null;
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            }
            $user = obj($user);
            $employee  = obj($employee );
            if ($user->user_group!='manager') {
                msg_set('You are not authorised to scan, as you are not a manager.');
                $api['success'] = false;
                $api['data'] = null;
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            }
            if (!in_array($user->id, $event->managers)) {
                msg_set('You are not manager in this event');
                $api['success'] = false;
                $api['data'] = null;
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            }
            $this->db->tableName = 'qr_scan_data';
            $arr['user_id'] = $saccned_user_id;
            $arr['scan_date'] = date('Y-m-d');
            $arr['is_active'] = 1;
            $arr['scan_group'] = intval($req->scan_group);
            $already_today = $this->db->findOne($arr);
            $arr['scanned_by'] = $user->id;
            $arr['created_at'] = date('Y-m-d H:i:s');
            $arr['scan_time'] = date('H:i:s');
            $arr['event_id'] = $req->event_id ?? null;
            $arr['scan_data'] = json_encode($req->qrdata);
            $arr['food_category'] = $employee->food_category;
            $this->db->insertData = $arr;
            try {
                if ($already_today) {
                    if ($req->rescan != '1') {
                        msg_set('Please pass rescan permission to scan again');
                        $api['success'] = true;
                        $api['data'] = [
                            'already_scanned' => 1
                        ];
                        $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                        echo json_encode($api);
                        exit;
                    }
                }
                $qrid = $this->db->create();
                if ($qrid) {
                    msg_set('Scan success, saved in database');
                    $api['success'] = true;
                    $api['data'] = null;
                    $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                    echo json_encode($api);
                } else {
                    msg_set('Not not saved');
                    $api['success'] = false;
                    $api['data'] = null;
                    $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                    echo json_encode($api);
                }
                exit;
            } catch (PDOException $e) {
                msg_set('Not saved');
                $api['success'] = false;
                $api['data'] = null;
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            }
        } else {
            msg_set("Invalid user token");
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
    function get_scanned_data($req = null)
    {
        $req = obj($req);
        header('Content-Type: application/json');
        $data  = json_decode(file_get_contents("php://input"), true);
        $rules = [
            'token' => 'required|string',
            'event_id' => 'required|numeric',
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
        if (!isset($req->event_id)) {
            msg_set('Event ID is required');
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $emp_id = $req->employee_id ?? null;
        $event = $this->get_event_by_id($req->event_id);
        if (!$event) {
            msg_set('Event not found or it might be closed');
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        if ($user) {
            try {
                $user = obj($user);
                if (!in_array($user->id, $event->managers)) {
                    msg_set('You are not manager in this event');
                    $api['success'] = false;
                    $api['data'] = null;
                    $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                    echo json_encode($api);
                    exit;
                }
                $this->db->tableName = 'qr_scan_data';
                $arr['is_active'] = 1;
                $arr['event_id'] = $event->id;
                if ($emp_id) {
                    $arr['user_id'] = $emp_id;
                }
                $reports = $this->db->filter(assoc_arr:$arr,ord:"desc",change_order_by_col:"id");
                if ($reports) {
                    $dta = [];
                    foreach ($reports as $key => $rprts) {
                        $rprts['scan_data'] = json_decode($rprts['scan_data']);
                        $rprts['scan_datetime'] = strtotime($rprts['scan_date'] . " " . $rprts['scan_time']);
                        unset($rprts['created_at']);
                        unset($rprts['updated_at']);
                        unset($rprts['scan_date']);
                        unset($rprts['scan_time']);
                        $dta[] = $rprts;
                    }
                    msg_set('Reports fetched successfully');
                    $api['success'] = true;
                    $api['data'] =  $dta;
                    $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                    echo json_encode($api);
                } else {
                    msg_set('No report found');
                    $api['success'] = false;
                    $api['data'] = null;
                    $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                    echo json_encode($api);
                }
                exit;
            } catch (PDOException $e) {
                msg_set('Not found');
                $api['success'] = false;
                $api['data'] = null;
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            }
        } else {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
    function get_event_by_id($id)
    {
        $this->db->tableName = 'content';
        $arr['is_active'] = 1;
        $arr['content_group'] = 'event';
        $arr['id'] = $id;
        $event = $this->db->findOne($arr);
        if ($event) {
            $event = obj($event);
            return (object) array(
                'id' => $event->id,
                'title' => $event->title,
                'managers' => json_decode($event->managers ?? '[]'),
                'employees' => json_decode($event->employees ?? '[]'),
            );
        }
        return null;
    }
    function is_assigned($userid,$jsn) {
        $mngrs = json_decode($jsn??'[]');
        return in_array($userid,$mngrs);
    }
}
