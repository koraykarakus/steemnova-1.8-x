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
class ShowDumpPage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        $db = Database::get();

        $dump_data['perRequest'] = 100;

        $dump_data = [];

        $prefix_counts = strlen(DB_PREFIX);

        $dump_data['sqlTables'] = [];

        $sql = "SHOW TABLE STATUS FROM `" . DB_NAME ."`;";

        $sql_table_raw = $db->nativequery($sql);

        foreach ($sql_table_raw as $table)
        {
            if (DB_PREFIX == substr($table['Name'], 0, $prefix_counts))
            {
                $dump_data['sqlTables'][] = $table['Name'];
            }
        }

        $this->assign([
            'dumpData' => $dump_data,
        ]);

        $this->display('page.dump.default.tpl');

    }

    public function dump(): void
    {
        global $LNG;

        $db_tables = HTTP::_GP('dbtables', []);

        if (empty($db_tables))
        {
            $this->printMessage($LNG['du_not_tables_selected']);
        }

        $file_name = '2MoonsBackup_'.date('d_m_Y_H_i_s', TIMESTAMP).'.sql';
        $file_path = 'includes/backups/'.$file_name;

        require 'includes/classes/SQLDumper.class.php';

        $dump = new SQLDumper();
        $dump->dumpTablesToFile($db_tables, $file_path);

        $this->printMessage(sprintf($LNG['du_success'], 'includes/backups/'.$file_name));
    }

}
