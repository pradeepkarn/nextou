<?php

class Event_api__
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
        $data  = json_decode(file_get_contents("php://input"), true);
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
        $req = obj($data);
        $user = (new Users_api)->get_user_by_token($req->token);
        if ($user) {
            $events = $this->get_all_events($user['id']);
        } else {
            $events = null;
        }
        if ($events) {
            msg_set('Events found successfully');
            $api['success'] = true;
            $api['data'] = $events;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        } else {
            msg_set('Event not found or it might be closed or you may not be assigned');
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
        }
    }
    function get_event_employees($req = null)
    {
        $req = obj($req);
        header('Content-Type: application/json');
        $data  = json_decode(file_get_contents("php://input"), true);
        $rules = [
            'token' => 'required|string',
            'event_id' => 'required|numeric'
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
        if ($user) {
            $event = $this->get_event_by_id($user['id'], $req->event_id);
        } else {
            $events = null;
        }
        if ($event) {
            msg_set('Employees found successfully');
            $api['success'] = true;
            $api['data'] = $event;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        } else {
            msg_set('Event not found or it might be closed or you may not be assigned');
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
        }
    }
    function get_event_by_id($myid, $event_id)
    {
        $this->db->tableName = 'content';
        $arr['is_active'] = 1;
        $arr['id'] = $event_id;
        $arr['content_group'] = 'event';
        $event = $this->db->get($arr);

        if ($event) {
            $event = obj($event);
            $managers = json_decode($event->managers ?? '[]');
            $employees = json_decode($event->employees ?? '[]');
            $employeesList = $this->get_employee_details($db=$this->db, $idList=$employees);
            $empls = null;
            $data = array_map(function($d) {
                $d['image'] = dp_or_null($d['image']);
                return $d;
            }, $employeesList);
            // $managersList = $this->get_employee_details($db=$this->db, $idList=$employees);
            // if (in_array($myid, $managers)) {
                // $unique_employees = array_unique(array_merge($managers, $employees));
                // $am_i_assigned = false;
                // $assigned_as = "NA";
                // if ($myid) {
                //     $am_i_assigned = in_array($myid, $unique_employees);
                //     if (in_array($myid, $managers) && in_array($myid, $employees)) {
                //         $assigned_as = "manager, employee";
                //     } else if (!in_array($myid, $managers) && in_array($myid, $employees)) {
                //         $assigned_as = "employee";
                //     } else if (in_array($myid, $managers) && !in_array($myid, $employees)) {
                //         $assigned_as = "manager";
                //     } else if (!in_array($myid, $managers) && !in_array($myid, $employees)) {
                //         $assigned_as = "NA";
                //         $am_i_assigned = false;
                //     }
                // }
                // $unique_employee_count_with_managers = count($unique_employees);

                // $myevent = array(
                    // 'id' => $event->id,
                    // 'title' => $event->title,
                    // 'logo' => img_or_null($event->banner),
                    // 'address' => $event->address,
                    // 'event_datetime' => strval(strtotime($event->event_date . " " . $event->event_time)),
                    // 'number_of_employees' => $unique_employee_count_with_managers,
                    // 'employees'=>$employeesList,
                    // 'am_i_assigned' => $am_i_assigned,
                    // 'assgined_as' => $assigned_as,
                // );
            // }
        }
        return  $data;
    }
    function get_employee_details($db, $idList)
    {
        $escapedIds = implode(',', array_map('intval', $idList));
        $sql = "SELECT id, CONCAT(first_name, ' ', last_name) AS name, email, image FROM pk_user WHERE id IN ($escapedIds)";
        return $db->show($sql);
    }
    function get_all_events($myid = null)
    {
        $this->db->tableName = 'content';
        $arr['is_active'] = 1;
        $arr['content_group'] = 'event';
        $events = $this->db->filter($arr);
        if ($events) {
            $myevents = null;
            foreach ($events as $key => $event) {
                $event = obj($event);
                $managers = json_decode($event->managers ?? '[]');
                $employees = json_decode($event->employees ?? '[]');
                if (in_array($myid, $managers)) {
                    $unique_employees = array_unique(array_merge($managers, $employees));
                    $am_i_assigned = false;
                    $assigned_as = "NA";
                    if ($myid) {
                        $am_i_assigned = in_array($myid, $unique_employees);
                        if (in_array($myid, $managers) && in_array($myid, $employees)) {
                            $assigned_as = "manager, employee";
                        } else if (!in_array($myid, $managers) && in_array($myid, $employees)) {
                            $assigned_as = "employee";
                        } else if (in_array($myid, $managers) && !in_array($myid, $employees)) {
                            $assigned_as = "manager";
                        } else if (!in_array($myid, $managers) && !in_array($myid, $employees)) {
                            $assigned_as = "NA";
                            $am_i_assigned = false;
                        }
                    }
                    $unique_employee_count_with_managers = count($unique_employees);

                    $myevents[] = array(
                        'id' => $event->id,
                        'title' => $event->title,
                        'logo' => img_or_null($event->banner),
                        'address' => $event->address,
                        'event_datetime' => strval(strtotime($event->event_date . " " . $event->event_time)),
                        'number_of_employees' => $unique_employee_count_with_managers,
                        'am_i_assigned' => $am_i_assigned,
                        'assgined_as' => $assigned_as,
                    );
                }
            }
            return  $myevents;
        }
        return null;
    }
}
