<?php
class Logs_ctrl
{
    public function list($req = null)
    {
        $req = obj($req);
        if (isset($_POST['action'])) {
            if ($_POST['action'] == 'delete_selected_items') {
                if (is_superuser()) {
                    $this->delete_bulk();
                }
                return;
            }
            return;
        }
        $current_page = 0;
        $data_limit = DB_ROW_LIMIT;
        $page_limit = "0,$data_limit";
        $cp = 0;
        if (isset($req->page) && intval($req->page)) {
            $cp = $req->page;
            $current_page = (abs($req->page) - 1) * $data_limit;
            $page_limit = "$current_page,$data_limit";
        }
        $total_page = $this->log_list(ord: "DESC", limit: 10000);
        $tp = count($total_page);
        if ($tp %  $data_limit == 0) {
            $tp = $tp / $data_limit;
        } else {
            $tp = floor($tp / $data_limit) + 1;
        }
        if (isset($req->search)) {
            $log_list = $this->log_search_list($keyword = $req->search, $ord = "DESC", $limit = $page_limit);
        } else {
            $log_list = $this->log_list(ord: "DESC", limit: $page_limit);
        }
        $context = (object) array(
            'page' => 'logs/list.php',
            'data' => (object) array(
                'req' => obj($req),
                'log_list' => $log_list,
                'total_page' => $tp,
                'current_page' => $cp,
                'is_active' => true
            )
        );
        $this->render_main($context);
    }
    function delete_bulk()
    {
        $action = $_POST['action'] ?? null;
        $ids = $_POST['selected_ids'] ?? null;
        if ($action != null && $action == "delete_selected_items" && $ids != null) {
            $num = count($ids);
            if ($num == 0) {
                echo js_alert('Object not seleted');
                exit;
            };
            $idsString = implode(',', $ids);
            $db = new Dbobjects;
            $pdo = $db->conn;
            $pdo->beginTransaction();
            $sql = "DELETE FROM notifications WHERE id IN ($idsString)";
            try {
                $db->show($sql);
                $pdo->commit();
                echo js_alert("$num Selected item deleted");
                echo RELOAD;
                return true;
            } catch (PDOException $pd) {
                $pdo->rollBack();
                echo js_alert('Database quer error');
                return false;
            }
        } else {
            echo js_alert('Action or item not selected');
            exit;
        }
    }
    // Post list
    public function log_list($ord = "DESC", $limit = 5, $sort_by = 'id')
    {
        $cntobj = new Model('notifications');
        return $cntobj->index($ord, $limit, $change_order_by_col = $sort_by);
    }
    public function log_search_list($keyword, $ord = "DESC", $limit = 5)
    {
        $cntobj = new Model('notifications');
        $search_arr['deviece_info'] = $keyword;
        $search_arr['message'] = $keyword;
        $search_arr['user_id'] = $keyword;
        return $cntobj->search(
            assoc_arr: $search_arr,
            ord: $ord,
            limit: $limit
        );
    }
    // render function
    public function render_main($context = null)
    {
        import("apps/admin/layouts/admin-main.php", $context);
    }
}
