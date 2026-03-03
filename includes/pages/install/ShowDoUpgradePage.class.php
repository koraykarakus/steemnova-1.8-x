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

class ShowDoUpgradePage extends AbstractInstallPage
{
    public static $requireModule = 0;

    public function __construct()
    {
        parent::__construct();
        $this->initTemplate();
    }

    public function show(): void
    {
        // TODO:Need a rewrite!
        require 'includes/config.php';

        // Create a Backup
        $sql_table_raw = Database::get()->nativeQuery("SHOW TABLE STATUS FROM `" . DB_NAME . "`;");
        $prefix_counts = strlen(DB_PREFIX);
        $db_tables = [];
        foreach ($sql_table_raw as $table)
        {
            if (DB_PREFIX == substr($table['Name'], 0, $prefix_counts))
            {
                $db_tables[] = $table['Name'];
            }
        }

        if (empty($db_tables))
        {
            throw new Exception('No tables found for dump.');
        }

        @set_time_limit(600);

        $file_name = '2MoonsBackup_' . date('Y_m_d_H_i_s', TIMESTAMP) . '.sql';
        $file_path = 'includes/backups/' . $file_name;
        require 'includes/classes/SQLDumper.class.php';
        $dump = new SQLDumper();
        $dump->dumpTablesToFile($db_tables, $file_path);

        try
        {
            $sql = "SELECT dbVersion FROM %%SYSTEM%%;";
            $db_version = Database::get()->selectSingle($sql, [], 'dbVersion');
        }
        catch (Exception $e)
        {
            $db_version = 0;
        }

        $http_root = PROTOCOL . HTTP_HOST . str_replace(
            ['\\', '//'],
            '/',
            dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/'
        );

        $revision = $db_version;
        $file_list = [];
        $directory_iterator = new DirectoryIterator(ROOT_PATH . 'install/migrations/');
        /** @var DirectoryIterator $file_info */
        foreach ($directory_iterator as $file_info)
        {
            if (!$file_info->isFile())
            {
                continue;
            }
            $file_revision = substr($file_info->getFilename(), 10, -4);
            if ($file_revision > $revision
                && $file_revision <= DB_VERSION_REQUIRED)
            {
                $file_extension = pathinfo($file_path, PATHINFO_EXTENSION);
                $key = $file_revision . ((int)$file_extension === 'php');
                $file_list[$key] = [
                    'fileName'       => $file_info->getFilename(),
                    'fileRevision'   => $file_revision,
                    'file_extension' => $file_extension,
                ];
            }
        }
        ksort($file_list);
        foreach ($file_list as $file_info)
        {
            switch ($file_info['file_extension'])
            {
                case 'php':
                    copy(
                        ROOT_PATH.'install/migrations/' . $file_info['fileName'],
                        ROOT_PATH.$file_info['fileName']
                    );
                    $ch = curl_init($http_root . $file_info['fileName']);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_NOBODY, true);
                    curl_setopt($ch, CURLOPT_MUTE, true);
                    curl_exec($ch);
                    if (curl_errno($ch))
                    {
                        $error_msg = 'CURL-Error on update ' . basename($file_info['filePath']) . ':' . curl_error($ch);
                        try
                        {
                            $dump->restoreDatabase($file_path);
                            $message = 'Update error.<br><br>' . $error_msg . '<br><br><b><i>Backup restored.</i></b>';
                        }
                        catch (Exception $e)
                        {
                            $message = 'Update error.<br><br>' . $error_msg . '<br><br><b><i>Can not restore backup. Your game is maybe broken right now.</i></b><br><br>Restore error:<br>' . $e->getMessage();
                        }
                        throw new Exception($message);
                    }
                    curl_close($ch);
                    unlink($file_info['fileName']);
                    break;
                case 'sql':
                    $data = file_get_contents(ROOT_PATH . 'install/migrations/' . $file_info['fileName']);
                    try
                    {
                        $queries = explode(";\n", str_replace('%PREFIX%', DB_PREFIX, $data));
                        $queries = array_filter($queries);
                        foreach ($queries as $query)
                        {
                            try
                            {
                                // alter table IF NOT EXISTS
                                Database::get()->nativeQuery(trim($query));
                            }
                            catch (Exception $e)
                            {
                                error_log('Query: [' . $query . '] failed. Error: ' . $e->getMessage() . '. Skipped');
                            }
                        }
                    }
                    catch (Exception $e)
                    {
                        $error_msg = $e->getMessage();
                        try
                        {
                            $dump->restoreDatabase($file_path);
                            $message = 'Update error.<br><br>' . $error_msg . '<br><br><b><i>Backup restored.</i></b>';
                        }
                        catch (Exception $e)
                        {
                            $message = 'Update error.<br><br>' . $error_msg . '<br><br><b><i>Can not restore backup. Your game is maybe broken right now.</i></b><br><br>Restore error:<br>' . $e->getMessage();
                        }
                        throw new Exception($message);
                    }
                    break;
            }
        }
        $revision = end($file_list);
        $revision = $revision['fileRevision'];

        Database::get()->update("UPDATE %%SYSTEM%% SET dbVersion = " . DB_VERSION_REQUIRED . ";");

        ClearCache();

        $this->assign([
            'update'   => !empty($file_list),
            'revision' => $revision,
            'header'   => $LNG['menu_upgrade'],
        ]);

        $this->display('ins_doupdate.tpl');
        unlink($path_install_file);
    }
}
