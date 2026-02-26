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

if ($USER['authlevel'] == AUTH_USR)
{
    throw new Exception("Permission error!");
}

function ShowAutoCompletePage()
{
    $search_text = HTTP::_GP('term', '', UTF8_SUPPORT);
    $searchList = [];

    if (empty($search_text)
        || $search_text === '#')
    {
        echo json_encode([]);
        exit;
    }

    if (substr($search_text, 0, 1) === '#')
    {
        $where = 'id = '.((int) substr($search_text, 1));
        $orderBy = ' ORDER BY id ASC';
    }
    else
    {
        $where = "username LIKE '%". $search_text ."%'";
        $orderBy = " ORDER BY (IF(username = '". $search_text ."', 1, 0) + IF(username LIKE '" . $search_text ."%', 1, 0)) DESC, username";
    }

    $sql = "SELECT id, username 
    FROM %%USERS%% WHERE universe = :universe AND " . $where . $orderBy . " LIMIT 20";

    $users = Database::get()->select($sql, [
        ':universe' => Universe::getEmulated(),
    ]);

    foreach ($users as $c_user)
    {
        $searchList[] = [
            'label' => $c_user['username'].' (ID:'.$c_user['id'].')',
            'value' => $c_user['username'],
        ];
    }

    echo json_encode($searchList);
    exit;
}
