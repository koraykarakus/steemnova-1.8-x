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

class ShowScreensPage extends AbstractLoginPage
{
    public static $require_module = 0;

    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        $screenshots = [];
        $directory_iterator = new DirectoryIterator('styles/resource/images/login/screens/');
        foreach ($directory_iterator as $c_file_info)
        {
            /** @var DirectoryIterator $c_file_info */
            if (!$c_file_info->isFile())
            {
                continue;
            }

            $thumbnail = 'styles/resource/images/login/screens/' .
            $c_file_info->getFilename();

            if (file_exists('styles/resource/images/login/screens/thumbnails/' . $c_file_info->getFilename()))
            {
                $thumbnail = 'styles/resource/images/login/screens/thumbnails/' .
                $c_file_info->getFilename();
            }

            $screenshots[] = [
                'path'      => 'styles/resource/images/login/screens/' . $c_file_info->getFilename(),
                'thumbnail' => $thumbnail,
            ];
        }

        $this->assign([
            'screenshots' => $screenshots,
        ]);

        $this->display('page.screens.default.tpl');
    }
}
