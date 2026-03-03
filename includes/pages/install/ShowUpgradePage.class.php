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

class ShowUpgradePage extends AbstractInstallPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $LNG;
        // Willkommen zum Update page. Anzeige, von und zu geupdatet wird.
        // Informationen, dass ein backup erstellt wird.
        try
        {
            $sql = "SELECT dbVersion FROM %%SYSTEM%%;";

            $db_version = Database::get()->selectSingle($sql, [], 'dbVersion');
        }
        catch (Exception $e)
        {
            $db_version = 0;
        }

        $updates = [];

        $file_revision = 0;

        $directory_iterator = new DirectoryIterator(ROOT_PATH . 'install/migrations/');
        /** @var DirectoryIterator $file_info */
        foreach ($directory_iterator as $file_info)
        {
            if (!$file_info->isFile()
                || !preg_match('/^migration_\d+/', $file_info->getFilename()))
            {
                continue;
            }

            $file_revision = substr($file_info->getFilename(), 10, -4);

            if ($file_revision <= $db_version
                || $file_revision > DB_VERSION_REQUIRED)
            {
                continue;
            }

            $updates[$file_info->getPathname()] = makebr(
                str_replace(
                    '%PREFIX%',
                    DB_PREFIX,
                    file_get_contents($file_info->getPathname())
                )
            );
        }

        $this->assign([
            'file_revision' => min(DB_VERSION_REQUIRED, $file_revision),
            'sql_revision'  => $db_version,
            'updates'       => $updates,
            'header'        => $LNG['menu_upgrade'],
        ]);

        $this->display('ins_update.tpl');
    }
}
