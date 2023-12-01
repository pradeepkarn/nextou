<?php

class QR_ctrl
{
    public $db;
    function  __construct()
    {
        $this->db = (new DB_ctrl)->db;
    }
    public function list($req = null)
    {
        $req = obj($req);
        $current_page = 0;
        $data_limit = DB_ROW_LIMIT;
        $page_limit = "0,$data_limit";
        $cp = 0;
        if (isset($req->page) && intval($req->page)) {
            $cp = $req->page;
            $current_page = (abs($req->page) - 1) * $data_limit;
            $page_limit = "$current_page,$data_limit";
        }
        $total_data = $this->scanned_data_list(ord: "DESC", limit: 10000, active: 1);
        $tu = count($total_data);
        if ($tu %  $data_limit == 0) {
            $tu = $tu / $data_limit;
        } else {
            $tu = floor($tu / $data_limit) + 1;
        }

        $list = $this->scanned_data_list(ord: "DESC", limit: $page_limit, active: 1);
        $context = (object) array(
            'page' => 'qrdata/list.php',
            'data' => (object) array(
                'req' => obj($req),
                'list' => $list,
                'total_list' => $tu,
                'current_page' => $cp,
                'is_active' => true
            )
        );
        $this->render_main($context);
    }
    public function trash_list($req = null)
    {
        $req = obj($req);
        $current_page = 0;
        $data_limit = DB_ROW_LIMIT;
        $page_limit = "0,$data_limit";
        $cp = 0;
        if (isset($req->page) && intval($req->page)) {
            $cp = $req->page;
            $current_page = (abs($req->page) - 1) * $data_limit;
            $page_limit = "$current_page,$data_limit";
        }
        $total_data = $this->scanned_data_list(ord: "DESC", limit: 10000, active: 0);
        $tu = count($total_data);
        if ($tu %  $data_limit == 0) {
            $tu = $tu / $data_limit;
        } else {
            $tu = floor($tu / $data_limit) + 1;
        }

        $list = $this->scanned_data_list(ord: "DESC", limit: $page_limit, active: '0');
        $context = (object) array(
            'page' => 'qrdata/list.php',
            'data' => (object) array(
                'req' => obj($req),
                'list' => $list,
                'total_list' => $tu,
                'current_page' => $cp,
                'is_active' => false
            )
        );
        $this->render_main($context);
    }
    public function scanned_data_list($ord, $limit, $active = 1)
    {
        $sql = "SELECT qr_scan_data.id, 
        qr_scan_data.is_active, 
        qr_scan_data.scan_date, 
        qr_scan_data.scan_time, 
        qr_scan_data.created_at, 
        pk_user.email 
        FROM qr_scan_data 
        LEFT JOIN pk_user ON qr_scan_data.user_id = pk_user.id 
        WHERE qr_scan_data.is_active=$active
        ORDER BY qr_scan_data.id $ord LIMIT $limit;
        ";
        return $this->db->show($sql);
    }
    public function move_to_trash($req = null)
    {
        $req = obj($req);
        $user_exists = (new Model('qr_scan_data'))->exists(['id' => $req->id]);
        if ($user_exists == false) {
            $_SESSION['msg'][] = "Object not found";
            echo js_alert(msg_ssn("msg", true));
            echo go_to(route('qrdataList'));
            exit;
        }
        // $user = obj(getData(table: 'pk_user', id: $req->id));
        $data = null;
        $data['id'] = $req->id;
        $rules = [
            'id' => 'required|integer'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            echo go_to(route('qrdataList'));
            exit;
        }
        try {
            (new Model('qr_scan_data'))->update($req->id, array('is_active' => 0));
            // echo js_alert('qrdata moved to trash');
            echo go_to(route('qrdataList'));
            exit;
        } catch (PDOException $e) {
            echo js_alert('Account not moved to trash');
            exit;
        }
    }
    public function restore($req = null)
    {
        $req = obj($req);
        $user_exists = (new Model('qr_scan_data'))->exists(['id' => $req->id]);
        if ($user_exists == false) {
            $_SESSION['msg'][] = "Object not found";
            echo js_alert(msg_ssn("msg", true));
            echo go_to(route('qrdataTrashList'));
            exit;
        }
        // $user = obj(getData(table: 'pk_user', id: $req->id));
        $data = null;
        $data['id'] = $req->id;
        $rules = [
            'id' => 'required|integer'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            echo go_to(route('qrdataTrashList'));
            exit;
        }
        try {
            (new Model('qr_scan_data'))->update($req->id, array('is_active' => 1));
            echo js_alert(' Qrdata restored');
            echo go_to(route('qrdataTrashList'));
            exit;
        } catch (PDOException $e) {
            echo js_alert('qrdata can not be restored');
            exit;
        }
    }
    public function delete_trash($req = null)
    {
        $req = obj($req);
        $user_exists = (new Model('qr_scan_data'))->exists(['id' => $req->id]);
        if ($user_exists == false) {
            $_SESSION['msg'][] = "Object not found";
            echo js_alert(msg_ssn("msg", true));
            echo go_to(route('qrdataTrashList'));
            exit;
        }
        // $user = obj(getData(table: 'pk_user', id: $req->id));
        $data = null;
        $data['id'] = $req->id;
        $rules = [
            'id' => 'required|integer'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            echo go_to(route('qrdataTrashList'));
            exit;
        }
        try {
            $content_exists = (new Model('qr_scan_data'))->exists(['id' => $req->id, 'is_active' => 0]);
            if ($content_exists) {
                $user = obj(getData('pk_user', $req->id));
                if ($user->username == 'admin') {
                    echo js_alert('Supreme account cannot be deleted');
                    echo go_to(route('qrdataTrashList'));
                    exit;
                }
                if ((new Model('qr_scan_data'))->destroy($req->id)) {
                    echo js_alert('Qrdata deleted permanatly');
                    echo go_to(route('qrdataTrashList'));
                    exit;
                }
            }
            echo js_alert('qrdata does not exist');
            echo go_to(route('qrdataTrashList'));
            exit;
        } catch (PDOException $e) {
            echo js_alert('Qrdata not deleted');
            exit;
        }
    }
    public function render_main($context = null)
    {
        import("apps/admin/layouts/admin-main.php", $context);
    }
    function generate($req = null)
    {
        $req = obj($req);
        // Do not return anything to the browser
        if (!isset($req->id)) {
            exit;
        }
        $this->db->tableName = "pk_user";
        $user = $this->db->pk($req->id);
        if ($user) {
            $user = obj($user);
            // ob_start("callback");
            // Process the input string
            // $codeText = "ID: $user->id \n";
            // $codeText .= "NID: $user->nid_no \n";
            // $codeText .= "Email: $user->email \n";
            // $codeText .= "Name: $user->first_name $user->last_name\n";
            $codeText = array(
                'id'=>$user->id,
                'nid'=>$user->nid_no,
                'email'=>$user->email,
                'name'=>$user->first_name ." ". $user->last_name
            );
            $codeText = json_encode($codeText);
            // end of processing
            $output = MEDIA_ROOT . "images/qrcodes/" . $user->email . ".png";
            // $debugLog = ob_get_contents();
            // ob_end_clean();
            // outputs QR code as a PNG data
            QRcode::png(text: $codeText, outfile: $output, size: 5);
            QRcode::png(text: $codeText, size: 5);
        }
        exit;
    }
}
