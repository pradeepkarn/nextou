<?php
class Admin_fuel_ctrl extends DB_ctrl
{
    // Cretae page
    public function create($req = null)
    {
        $req = obj($req);
        if (isset($req->driver_id)) {
            $driver = $this->db->showOne("select * from pk_user where user_group='driver' and is_active=1 and id=$req->driver_id");
        }
        $context = (object) array(
            'page' => 'fuels/create.php',
            'data' => (object) array(
                'req' => obj($req),
                'cat_list' => $this->fuel_list(fuel_group: $req->fg, limit: 1000),
                'driver'=>$driver??null
            )
        );
        $this->render_main($context);
    }
    // List fuels
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
        $total_fuel = $this->fuel_list(fuel_group: $req->fg, ord: "DESC", limit: 10000, active: 1);
        $tu = count($total_fuel);
        if ($tu %  $data_limit == 0) {
            $tu = $tu / $data_limit;
        } else {
            $tu = floor($tu / $data_limit) + 1;
        }

        if (isset($req->search)) {
            $fuel_list = $this->fuel_search_list(fuel_group: $req->fg, keyword: $req->search, ord: "DESC", limit: $page_limit, active: 1);
        } 
        if (isset($req->driver_id)) {
            $fuel_list = $this->fuel_list(fuel_group: $req->fg, user_id:$req->driver_id, ord: "DESC", limit: $page_limit, active: 1);
        }
        else {
            $fuel_list = $this->fuel_list(fuel_group: $req->fg, ord: "DESC", limit: $page_limit, active: 1);
        }
        $context = (object) array(
            'page' => 'fuels/list.php',
            'data' => (object) array(
                'req' => obj($req),
                'fuel_list' => $fuel_list,
                'total_fuel' => $tu,
                'current_page' => $cp,
                'is_active' => true
            )
        );
        $this->render_main($context);
    }

    // fuel search list
    public function fuel_search_list($fuel_group = 'petrol', $keyword = '', $ord = "DESC", $limit = 5, $active = 1)
    {
        $cntobj = new Model('fuels',$this->db);
        $search_arr['username'] = $keyword;
        $search_arr['email'] = $keyword;
        $search_arr['first_name'] = $keyword;
        $search_arr['last_name'] = $keyword;
        $search_arr['mobile'] = $keyword;
        return $cntobj->search(
            assoc_arr: $search_arr,
            ord: $ord,
            limit: $limit,
            whr_arr: array('fuel_group' => $fuel_group, 'is_active' => $active)
        );
    }
    // Trashed fuel list
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
        $total_fuel = $this->fuel_list(fuel_group: $req->fg, ord: "DESC", limit: 10000, active: 0);
        $tu = count($total_fuel);
        if ($tu %  $data_limit == 0) {
            $tu = $tu / $data_limit;
        } else {
            $tp = floor($tu / $data_limit) + 1;
        }
        if (isset($req->search)) {
            $fuel_list = $this->fuel_search_list($fuel_group = $req->fg, $keyword = $req->search, $ord = "DESC", $limit = $page_limit, $active = 0);
        } else {
            $fuel_list = $this->fuel_list(fuel_group: $req->fg, ord: "DESC", limit: $page_limit, active: 0);
        }
        $context = (object) array(
            'page' => 'fuels/list.php',
            'data' => (object) array(
                'req' => obj($req),
                'fuel_list' => $fuel_list,
                'total_fuel' => $tu,
                'current_page' => $cp,
                'is_active' => false
            )
        );
        $this->render_main($context);
    }
    // Edit page
    public function edit($req = null)
    {
        $req = obj($req);
        if (isset($req->driver_id)) {
            $driver = $this->db->showOne("select * from pk_user where user_group='driver' and is_active=1 and id=$req->driver_id");
        }
        $context = (object) array(
            'page' => 'fuels/edit.php',
            'data' => (object) array(
                'req' => obj($req),
                'fuel_detail' => $this->fuel_detail(id: $req->id, fuel_group: $req->fg),
                'driver'=>$driver??null
            )
        );
        $this->render_main($context);
    }
    // Save fuel by ajax call
    public function save($req = null)
    {
        $req = obj($req);
        $request = null;
        $data = null;
        $data = $_POST;

        $rules = [
            'volume' => 'required|numeric',
            'user_id' => 'required|numeric',
            'email' => 'required|email',
            'username' => 'required|string',
            'first_name' => 'required|string',
        ];
        
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            exit;
        }

        $request = obj($data);
        
        if (isset($request->email)) {
            $arr = null;
            $arr['balance'] = $request->balance??0;
            $arr['user_id'] = $req->driver_id??$request->user_id;
            $arr['volume'] = abs($request->volume);
            $arr['unit'] = 'litre';
            $arr['fuel_group'] = $req->fg;
            $arr['email'] = $request->email;
            $arr['username'] = generate_clean_username($request->username);
            $arr['first_name'] = sanitize_remove_tags($request->first_name);
            $arr['last_name'] = sanitize_remove_tags($request->last_name);
            $arr['isd_code'] = intval($request->isd_code);
            $arr['mobile'] = intval($request->mobile);
            
            $arr['created_at'] = date('Y-m-d H:i:s');
            $postid = (new Model('fuels',$this->db))->store($arr);
            if (intval($postid)) {
                echo js_alert("$req->fg added");
                echo go_to(route('fuelListByDriver', ['fg' => $req->fg,'driver_id'=>$req->driver_id??$request->user_id]));
                return true;
            } else {
                echo js_alert("$req->fg not created");
                return false;
            }
        } else {
            echo js_alert("Missing required field, $req->fg not created");
            return false;
        }
    }
    // Save by ajax call
    public function update($req = null)
    {
        $req = obj($req);
        $request = null;
        $data = null;
        $data = $_POST;
        $data['fuel_id'] = $req->id??null;

        $rules = [
            'fuel_id' => 'required|numeric',
            'volume' => 'required|numeric',
            'user_id' => 'required|numeric',
            'email' => 'required|email',
            'username' => 'required|string',
            'first_name' => 'required|string',
        ];
        
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            exit;
        }

        $request = obj($data);
        // print_r($request);
        // return;
        if (isset($request->email)) {
            $arr = null;
            if ($request->balance) {
                $arr['balance'] = $request->balance;
            }
            $arr['user_id'] = $req->driver_id??$request->user_id;
            $arr['volume'] = $request->volume;
            $arr['unit'] = 'litre';
            $arr['fuel_group'] = $req->fg;
            $arr['email'] = $request->email;
            $arr['username'] = generate_clean_username($request->username);
            $arr['first_name'] = sanitize_remove_tags($request->first_name);
            $arr['last_name'] = sanitize_remove_tags($request->last_name);
            $arr['isd_code'] = intval($request->isd_code);
            $arr['mobile'] = intval($request->mobile);
           
            // $arr['created_at'] = date('Y-m-d H:i:s');
            $postid = (new Model('fuels',$this->db))->update($request->fuel_id,$arr);
            if ($postid!=false) {
                echo js_alert("$req->fg updated");
                echo go_to(route('fuelListByDriver', ['fg' => $req->fg, 'driver_id'=>$req->driver_id]));
                return true;
            } else {
                echo js_alert("$req->fg not updated");
                return false;
            }
        } else {
            echo js_alert("Missing required field, $req->fg not created");
            return false;
        }
    }
    public function move_to_trash($req = null)
    {
        $req = obj($req);
        $user_exists = (new Model('fuels',$this->db))->exists(['id' => $req->id, 'fuel_group' => $req->fg]);
        if ($user_exists == false) {
            $_SESSION['msg'][] = "Object not found";
            echo js_alert(msg_ssn("msg", true));
            echo go_to(route('fuelList', ['fg' => $req->fg]));
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
            echo go_to(route('fuelListByDriver', ['fg' => $req->fg,'driver_id'=>$req->driver_id]));
            exit;
        }
        try {
            (new Model('fuels',$this->db))->update($req->id, array('is_active' => 0));
            // echo js_alert('fuel moved to trash');
            echo go_to(route('fuelListByDriver', ['fg' => $req->fg,'driver_id'=>$req->driver_id]));
            exit;
        } catch (PDOException $e) {
            echo js_alert('Account not moved to trash');
            exit;
        }
    }
    public function restore($req = null)
    {
        $req = obj($req);
        // $user_exists = (new Model('fuels',$this->db))->exists(['id' => $req->id, 'fuel_group' => $req->fg]);
        // if ($user_exists == false) {
        //     $_SESSION['msg'][] = "Object not found";
        //     echo js_alert(msg_ssn("msg", true));
        //     echo go_to(route('fuelTrashListByDriver', ['fg' => $req->fg,'driver_id'=>$req->driver_id]));
        //     exit;
        // }
        // $user = obj(getData(table: 'pk_user', id: $req->id));
        $data = null;
        $data['id'] = $req->id;
        $rules = [
            'id' => 'required|integer'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            echo go_to(route('fuelTrashList', ['fg' => $req->fg]));
            exit;
        }
        try {
            (new Model('fuels',$this->db))->update($req->id, array('is_active' => 1));
            echo js_alert('fuel restored');
            echo go_to(route('fuelTrashListByDriver', ['fg' => $req->fg,'driver_id'=>$req->driver_id]));
            exit;
        } catch (PDOException $e) {
            echo js_alert('fuel can not be restored');
            exit;
        }
    }
    public function delete_trash($req = null)
    {
        $req = obj($req);
        // $user_exists = (new Model('fuels',$this->db))->exists(['id' => $req->id, 'fuel_group' => $req->fg]);
        // if ($user_exists == false) {
        //     $_SESSION['msg'][] = "Object not found";
        //     echo js_alert(msg_ssn("msg", true));
        //     echo go_to(route('fuelTrashListByDriver', ['fg' => $req->fg,'driver_id'=>$req->driver_id]));
        //     exit;
        // }
        // $user = obj(getData(table: 'pk_user', id: $req->id));
        $data = null;
        $data['id'] = $req->id;
        $rules = [
            'id' => 'required|integer'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            echo go_to(route('fuelTrashListByDriver', ['fg' => $req->fg,'driver_id'=>$req->driver_id]));
            exit;
        }
        try {
            $content_exists = (new Model('fuels',$this->db))->exists(['id' => $req->id, 'is_active' => 0, 'fuel_group' => $req->fg]);
            if ($content_exists) {
                $user = obj(getData('pk_user', $req->id));
                if ($user->username == 'admin') {
                    echo js_alert('Supreme account cannot be deleted');
                    echo go_to(route('fuelTrashListByDriver', ['fg' => $req->fg,'driver_id'=>$req->driver_id]));
                    exit;
                }
                if ((new Model('fuels',$this->db))->destroy($req->id)) {
                    echo js_alert('fuel deleted permanatly');
                    echo go_to(route('fuelTrashListByDriver', ['fg' => $req->fg,'driver_id'=>$req->driver_id]));
                    exit;
                }
            }
            echo js_alert('fuel does not exist');
            echo go_to(route('fuelTrashListByDriver', ['fg' => $req->fg,'driver_id'=>$req->driver_id]));
            exit;
        } catch (PDOException $e) {
            echo js_alert('fuel not deleted');
            exit;
        }
    }

    // User list
    public function fuel_list($fuel_group = "petrol", $user_id=null, $ord = "DESC", $limit = 5, $active = 1)
    {
        $cntobj = new Model('fuels',$this->db);
        if ($user_id!=null) {
            return $cntobj->filter_index(array('fuel_group' => $fuel_group, 'is_active' => $active, 'user_id'=>$user_id), $ord, $limit);
        }
        return $cntobj->filter_index(array('fuel_group' => $fuel_group, 'is_active' => $active), $ord, $limit);
    }
    // User detail
    public function fuel_detail($id, $fuel_group = 'petrol')
    {
        $cntobj = new Model('fuels',$this->db);
        $exists = $cntobj->exists(array('fuel_group' => $fuel_group, 'id' => $id));
        if ($exists) {
            return $cntobj->show($id);
        } else {
            return false;
        }
    }
    // render function
    public function render_main($context = null)
    {
        import("apps/admin/layouts/admin-main.php", $context);
    }
}
