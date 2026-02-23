<?php

/**
 *  2Moons
 *   by Jan-Otto Kröpke 2009-2016
 *
 * For the full copyright and license information, please view the LICENSE
 *
 * @package 2Moons
 * @author Jan-Otto Kröpke <slaver7@gmail.com>
 * @copyright 2009 Lucky
 * @copyright 2016 Jan-Otto Kröpke <slaver7@gmail.com>
 * @licence MIT
 * @version 1.8.x Koray Karakuş <koraykarakus@yahoo.com>
 * @link https://github.com/jkroepke/2Moons
 */

/**
 *
 */
class ShowCronjobPage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    // TODO: set return type
    public function getCronjobTimes($row, $max)
    {
        $arr = explode(',', $row);
        if (count($arr) > 1)
        {
            return $arr;
        }

        if (substr($arr[0], 0, (2 - strlen($arr[0]))) == '*/')
        {
            return range(0, $max, (int) substr($arr[0], (2 - strlen($arr[0]))));
        }
        else
        {
            return $arr[0];
        }
    }

    // TODO : set return type
    public function checkPostData($column, $max)
    {
        $all = HTTP::_GP($column.'_all', 0);
        if ($all)
        {
            return '*';
        }

        $post = HTTP::_GP($column, []);
        $post = array_filter($post, 'is_numeric');
        if (empty($post))
        {
            return false;
        }

        $check = [2, 3, 5, 6, 7, 10, 14, 15, 20, 30];
        $result = [];
        foreach ($check as $i)
        {
            if ($i <= $max && range(0, $max, $i) == $post)
            {
                $result[] = $i;
            }
        }
        if (!empty($result))
        {
            return '*/'.max($result);
        }
        else
        {
            return implode(',', $post);
        }

    }

    public function show(): void
    {
        global $LNG;
        $db = Database::get();

        $sql = "SELECT * FROM %%CRONJOBS%%;";

        $data = $db->select($sql);

        if (!$data)
        {
            $this->printMessage($LNG['cronjob_no_data']);
        }

        $CronjobArray = [];
        foreach ($data as $CronjobRow)
        {
            $CronjobArray[] = [
                'id'       => $CronjobRow['cronjobID'],
                'isActive' => $CronjobRow['isActive'],
                'name'     => $CronjobRow['name'],
                'min'      => $CronjobRow['min'],
                'hours'    => $CronjobRow['hours'],
                'dom'      => $CronjobRow['dom'],
                'month'    => $this->getCronjobTimes($CronjobRow['month'], 12),
                'dow'      => $this->getCronjobTimes($CronjobRow['dow'], 6),
                'class'    => $CronjobRow['class'],
                'nextTime' => $CronjobRow['nextTime'],
                'lock'     => !empty($CronjobRow['lock']),
            ];
        }

        $this->assign([
            'CronjobArray' => $CronjobArray,
        ]);

        $this->display("page.cronjob.overview.tpl");

    }

    public function showCronjobDetail(): void
    {

        $db = Database::get();

        $cronjobID = HTTP::_GP('id', 0);

        $avalibleCrons = [];

        $dir = new DirectoryIterator('includes/classes/cronjob/');

        foreach ($dir as $fileinfo)
        {
            if ($fileinfo->isFile() && $fileinfo->getBasename('.class.php') != $fileinfo->getFilename())
            {
                $avalibleCrons[] = $fileinfo->getBasename('.class.php');
            }
        }

        $sql = "SELECT * FROM %%CRONJOBS%% WHERE cronjobID = :cronjobID";

        $CronjobRow = $db->selectSingle($sql, [
            ':cronjobID' => $cronjobID,
        ]);

        if (!$CronjobRow)
        {
            $this->printMessage('Cronjob could not be found !');
        }

        $this->assign([
            'avalibleCrons' => $avalibleCrons,
            'cronjobID'     => $cronjobID,
            'name'          => isset($_POST['name']) ? HTTP::_GP('name', '') : $CronjobRow['name'],
            'min'           => isset($_POST['min_all']) ? [0 => '*'] : (isset($_POST['min']) ? HTTP::_GP('min', []) : $this->getCronjobTimes($CronjobRow['min'], 59)),
            'hours'         => isset($_POST['hours_all']) ? [0 => '*'] : (isset($_POST['hours']) ? HTTP::_GP('hours', []) : $this->getCronjobTimes($CronjobRow['hours'], 23)),
            'dom'           => isset($_POST['dom_all']) ? [0 => '*'] : (isset($_POST['dom']) ? HTTP::_GP('dom', []) : $this->getCronjobTimes($CronjobRow['dom'], 31)),
            'month'         => isset($_POST['month_all']) ? [0 => '*'] : (isset($_POST['month']) ? HTTP::_GP('month', []) : $this->getCronjobTimes($CronjobRow['month'], 12)),
            'dow'           => isset($_POST['dow_all']) ? [0 => '*'] : (isset($_POST['dow']) ? HTTP::_GP('dow', []) : $this->getCronjobTimes($CronjobRow['dow'], 6)),
            'class'         => isset($_POST['class']) ? HTTP::_GP('class', '') : $CronjobRow['class'],
            'error_msg'     => null,
        ]);

        $this->display("page.cronjob.detail.tpl");

    }

    public function showCronjobCreate(): void
    {

        $avalibleCrons = [];

        $dir = new DirectoryIterator('includes/classes/cronjob/');

        foreach ($dir as $fileinfo)
        {
            if ($fileinfo->isFile() && $fileinfo->getBasename('.class.php') != $fileinfo->getFilename())
            {
                $avalibleCrons[] = $fileinfo->getBasename('.class.php');
            }
        }

        $this->assign([
            'avalibleCrons' => $avalibleCrons,
            'cronjobID'     => 'add',
            'name'          => HTTP::_GP('name', ''),
            'min'           => isset($_POST['min_all']) ? [0 => '*'] : HTTP::_GP('min', [0 => 0]),
            'hours'         => isset($_POST['hours_all']) ? [0 => '*'] : HTTP::_GP('hours', [0 => 0]),
            'dom'           => isset($_POST['dom_all']) ? [0 => '*'] : HTTP::_GP('dom', [0 => 0]),
            'month'         => isset($_POST['month_all']) ? [0 => '*'] : HTTP::_GP('month', [0 => 0]),
            'dow'           => isset($_POST['dow_all']) ? [0 => '*'] : HTTP::_GP('dow', [0 => 0]),
            'class'         => HTTP::_GP('class', ''),
            'error_msg'     => 'NOT FOUND',
        ]);

        $this->display("page.cronjob.create.tpl");

    }

    public function lock(): void
    {

        $cronjobId = HTTP::_GP('id', 0);

        $sql = "UPDATE %%CRONJOBS%% SET `lock` = MD5(UNIX_TIMESTAMP()) WHERE cronjobID = :cronjobId;";

        Database::get()->update($sql, [
            ':cronjobId' => $cronjobId,
        ]);

        HTTP::redirectTo('admin.php?page=cronjob');
    }

    public function unlock(): void
    {

        $cronjobId = HTTP::_GP('id', 0);

        $sql = "UPDATE %%CRONJOBS%% SET `lock` = NULL WHERE cronjobID = :cronjobId;";

        Database::get()->update($sql, [
            ':cronjobId' => $cronjobId,
        ]);

        HTTP::redirectTo('admin.php?page=cronjob');
    }

    public function enable(): void
    {

        $cronjobId = HTTP::_GP('id', 0);

        $sql = "UPDATE %%CRONJOBS%% SET `isActive` = :isActive WHERE cronjobID = :cronjobId;";

        Database::get()->update($sql, [
            ':isActive'  => HTTP::_GP('enable', 0),
            ':cronjobId' => $cronjobId,
        ]);

        HTTP::redirectTo('admin.php?page=cronjob');
    }

    public function delete(): void
    {

        $cronjobId = HTTP::_GP('id', 0);

        $sql = "DELETE FROM %%CRONJOBS%% WHERE cronjobID = :cronjobId;";

        Database::get()->delete($sql, [
            ':cronjobId' => $cronjobId,
        ]);

        $sql = "DELETE FROM %%CRONJOBS_LOG%% WHERE cronjobId = :cronjobId;";

        Database::get()->delete($sql, [
            ':cronjobId' => $cronjobId,
        ]);

        HTTP::redirectTo('admin.php?page=cronjob');
    }

    public function edit(): void
    {
        global $LNG;
        $post_id = HTTP::_GP('id', 0);

        $post_name = HTTP::_GP('name', '');
        $post_min = $this->checkPostData('min', 59);
        $post_hours = $this->checkPostData('hours', 23);
        $post_month = $this->checkPostData('month', 12);
        $post_dow = $this->checkPostData('dow', 6);
        $post_dom = $this->checkPostData('dom', 31);
        $post_class = HTTP::_GP('class', '');
        $error = [];

        if ($post_name == '')
        {
            $error[] = $LNG['cronjob_error_name'];
        }
        if ($post_min === false)
        {
            $error[] = $LNG['cronjob_error_min'];
        }
        if ($post_hours === false)
        {
            $error[] = $LNG['cronjob_error_hours'];
        }
        if ($post_month === false)
        {
            $error[] = $LNG['cronjob_error_month'];
        }
        if ($post_dow === false)
        {
            $error[] = $LNG['cronjob_error_dow'];
        }
        if ($post_dom === false)
        {
            $error[] = $LNG['cronjob_error_dom'];
        }
        if ($post_class == '')
        {
            $error[] = $LNG['cronjob_error_class'];
        }
        elseif (!file_exists('includes/classes/cronjob/'.$post_class.'.class.php'))
        {
            $error[] = $LNG['cronjob_error_filenotfound'].'includes/classes/cronjobs/'.$post_class.'.class.php';
        }

        if (!empty($error_msg))
        {
            $this->printMessage($error_msg);
        }

        if ($post_id != 0)
        {

            $sql = "UPDATE %%CRONJOBS%% SET name = :post_name, min = :post_min, hours = :post_hours, month = :post_month, dow = :post_dow, dom = :post_dom, class = :post_class WHERE cronjobID = :post_id;";

            Database::get()->update($sql, [
                ':post_name'  => $post_name,
                ':post_min'   => $post_min,
                ':post_hours' => $post_hours,
                ':post_month' => $post_month,
                ':post_dow'   => $post_dow,
                ':post_dom'   => $post_dom,
                ':post_class' => $post_class,
                ':post_id'    => $post_id,
            ]);

        }
        else
        {

            $sql = "INSERT INTO %%CRONJOBS%% SET name = :post_name, min = :post_min, hours = :post_hours, month = :post_month, dow = :post_dow, dom = :post_dom, class = :post_class;";

            Database::get()->insert($sql, [
                ':post_name'  => $post_name,
                ':post_min'   => $post_min,
                ':post_hours' => $post_hours,
                ':post_month' => $post_month,
                ':post_dow'   => $post_dow,
                ':post_dom'   => $post_dom,
                ':post_class' => $post_class,
            ]);

        }

        $redirectButton = [];
        $redirectButton[] = [
            'url'   => 'admin.php?page=cronjob&mode=show',
            'label' => $LNG['uvs_back'],
        ];

        $this->printMessage($LNG['settings_successful'], $redirectButton);

    }

    public function create(): void
    {
        global $LNG;

        $post_name = HTTP::_GP('name', '');
        $post_min = $this->checkPostData('min', 59);
        $post_hours = $this->checkPostData('hours', 23);
        $post_month = $this->checkPostData('month', 12);
        $post_dow = $this->checkPostData('dow', 6);
        $post_dom = $this->checkPostData('dom', 31);
        $post_class = HTTP::_GP('class', '');
        $error = [];

        if ($post_name == '')
        {
            $error[] = $LNG['cronjob_error_name'];
        }
        if ($post_min === false)
        {
            $error[] = $LNG['cronjob_error_min'];
        }
        if ($post_hours === false)
        {
            $error[] = $LNG['cronjob_error_hours'];
        }
        if ($post_month === false)
        {
            $error[] = $LNG['cronjob_error_month'];
        }
        if ($post_dow === false)
        {
            $error[] = $LNG['cronjob_error_dow'];
        }
        if ($post_dom === false)
        {
            $error[] = $LNG['cronjob_error_dom'];
        }
        if ($post_class == '')
        {
            $error[] = $LNG['cronjob_error_class'];
        }
        elseif (!file_exists('includes/classes/cronjob/'.$post_class.'.class.php'))
        {
            $error[] = $LNG['cronjob_error_filenotfound'].'includes/classes/cronjobs/'.$post_class.'.class.php';
        }

        if (!empty($error_msg))
        {
            $this->printMessage($error_msg);
        }

        $sql = "INSERT INTO %%CRONJOBS%% SET name = :post_name, min = :post_min, hours = :post_hours, month = :post_month, dow = :post_dow, dom = :post_dom, class = :post_class;";

        Database::get()->insert($sql, [
            ':post_name'  => $post_name,
            ':post_min'   => $post_min,
            ':post_hours' => $post_hours,
            ':post_month' => $post_month,
            ':post_dow'   => $post_dow,
            ':post_dom'   => $post_dom,
            ':post_class' => $post_class,
        ]);

        $redirectButton = [];
        $redirectButton[] = [
            'url'   => 'admin.php?page=cronjob&mode=show',
            'label' => $LNG['uvs_back'],
        ];

        $this->printMessage($LNG['settings_successful'], $redirectButton);
    }

}

function ShowCronjob()
{
    $cronId = HTTP::_GP('id', 0);
    switch (HTTP::_GP('action', 'overview'))
    {
        case 'edit':
            ShowCronjobEdit($cronId);
            break;
        case 'delete':
            ShowCronjobDelete($cronId);
            break;
        case 'lock':
            ShowCronjobLock($cronId);
            break;
        case 'unlock':
            ShowCronjobUnlock($cronId);
            break;
        case 'detail':
            ShowCronjobDetail($cronId);
            break;
        case 'enable':
            ShowCronjobEnable($cronId);
            break;
        case 'overview':
            ShowCronjobOverview($cronId);
            // no break
        default:
            ShowCronjobOverview($cronId);
            break;
    }
}

function ShowCronjobEdit($post_id)
{

}

function ShowCronjobDelete($cronjobId)
{

    $sql = "DELETE FROM %%CRONJOBS%% WHERE cronjobID = :cronjobId;";

    Database::get()->delete($sql, [
        ':cronjobId' => $cronjobId,
    ]);

    $sql = "DELETE FROM %%CRONJOBS_LOG%% WHERE cronjobId = :cronjobId;";

    Database::get()->delete($sql, [
        ':cronjobId' => $cronjobId,
    ]);

    HTTP::redirectTo('admin.php?page=cronjob');
}

function ShowCronjobLock($cronjobId)
{

    $sql = "UPDATE %%CRONJOBS%% SET `lock` = MD5(UNIX_TIMESTAMP()) WHERE cronjobID = :cronjobId;";

    Database::get()->update($sql, [
        ':cronjobId' => $cronjobId,
    ]);

    HTTP::redirectTo('admin.php?page=cronjob');
}

function ShowCronjobUnlock($cronjobId)
{

    $sql = "UPDATE %%CRONJOBS%% SET `lock` = NULL WHERE cronjobID = :cronjobId;";

    Database::get()->update($sql, [
        ':cronjobId' => $cronjobId,
    ]);

    HTTP::redirectTo('admin.php?page=cronjob');
}

function ShowCronjobEnable($cronjobId)
{

    $sql = "UPDATE %%CRONJOBS%% SET `isActive` = :isActive WHERE cronjobID = :cronjobId;";

    Database::get()->update($sql, [
        ':isActive'  => HTTP::_GP('enable', 0),
        ':cronjobId' => $cronjobId,
    ]);

    HTTP::redirectTo('admin.php?page=cronjob');
}

function ShowCronjobOverview()
{
    $db = Database::get();

    $sql = "SELECT * FROM %%CRONJOBS%%;";

    $data = $db->select($sql);

    if (!$data)
    {
        $template = new template();
        $template->message($LNG['cronjob_no_data']);
    }

    $CronjobArray = [];
    foreach ($data as $CronjobRow)
    {
        $CronjobArray[] = [
            'id'       => $CronjobRow['cronjobID'],
            'isActive' => $CronjobRow['isActive'],
            'name'     => $CronjobRow['name'],
            'min'      => $CronjobRow['min'],
            'hours'    => $CronjobRow['hours'],
            'dom'      => $CronjobRow['dom'],
            'month'    => getCronjobTimes($CronjobRow['month'], 12),
            'dow'      => getCronjobTimes($CronjobRow['dow'], 6),
            'class'    => $CronjobRow['class'],
            'nextTime' => $CronjobRow['nextTime'],
            'lock'     => !empty($CronjobRow['lock']),
        ];
    }

    $template = new template();

    $template->assign_vars([
        'CronjobArray' => $CronjobArray,
    ]);

    $template->show("CronjobOverview.tpl");
}

function ShowCronjobDetail($detail, $error_msg = null)
{
    $template = new template();

    $db = Database::get();

    $avalibleCrons = [];

    $dir = new DirectoryIterator('includes/classes/cronjob/');
    foreach ($dir as $fileinfo)
    {
        if ($fileinfo->isFile() && $fileinfo->getBasename('.class.php') != $fileinfo->getFilename())
        {
            $avalibleCrons[] = $fileinfo->getBasename('.class.php');
        }
    }

    $template->assign_vars([
        'avalibleCrons' => $avalibleCrons,
    ]);

    if ($detail != 0)
    {
        $sql = "SELECT * FROM %%CRONJOBS%% WHERE cronjobID = :detail";
        $CronjobRow = $db->selectSingle($sql, [
            ':detail' => $detail,
        ]);
        $template->assign_vars([
            'id'        => $CronjobRow['cronjobID'],
            'name'      => isset($_POST['name']) ? HTTP::_GP('name', '') : $CronjobRow['name'],
            'min'       => isset($_POST['min_all']) ? [0 => '*'] : (isset($_POST['min']) ? HTTP::_GP('min', []) : getCronjobTimes($CronjobRow['min'], 59)),
            'hours'     => isset($_POST['hours_all']) ? [0 => '*'] : (isset($_POST['hours']) ? HTTP::_GP('hours', []) : getCronjobTimes($CronjobRow['hours'], 23)),
            'dom'       => isset($_POST['dom_all']) ? [0 => '*'] : (isset($_POST['dom']) ? HTTP::_GP('dom', []) : getCronjobTimes($CronjobRow['dom'], 31)),
            'month'     => isset($_POST['month_all']) ? [0 => '*'] : (isset($_POST['month']) ? HTTP::_GP('month', []) : getCronjobTimes($CronjobRow['month'], 12)),
            'dow'       => isset($_POST['dow_all']) ? [0 => '*'] : (isset($_POST['dow']) ? HTTP::_GP('dow', []) : getCronjobTimes($CronjobRow['dow'], 6)),
            'class'     => isset($_POST['class']) ? HTTP::_GP('class', '') : $CronjobRow['class'],
            'error_msg' => $error_msg,
        ]);
    }
    else
    {
        $template->assign_vars([
            'id'        => 'add',
            'name'      => HTTP::_GP('name', ''),
            'min'       => isset($_POST['min_all']) ? [0 => '*'] : HTTP::_GP('min', []),
            'hours'     => isset($_POST['hours_all']) ? [0 => '*'] : HTTP::_GP('hours', []),
            'dom'       => isset($_POST['dom_all']) ? [0 => '*'] : HTTP::_GP('dom', []),
            'month'     => isset($_POST['month_all']) ? [0 => '*'] : HTTP::_GP('month', []),
            'dow'       => isset($_POST['dow_all']) ? [0 => '*'] : HTTP::_GP('dow', []),
            'class'     => HTTP::_GP('class', ''),
            'error_msg' => $error_msg,
        ]);
    }
    $template->show("CronjobDetail.tpl");
}
