 <?php

/**
 *  2Moons
 *  Copyright (C) 2016 Jan-Otto Kröpke
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
class ShowVertifyPage extends AbstractAdminPage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        $file = HTTP::_GP("file", "");
        $this->tplObj->loadscript('vertify.js');

        $this->display("page.vertify.default.tpl");

    }

    public function check(): void
    {

        $REV = explode(".", Config::get("VERSION"));
        $REV = $REV[2];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_URL, 'https://raw.githubusercontent.com/jkroepke/2Moons/master/'.$file);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "2Moons Update API");
        curl_setopt($ch, CURLOPT_CRLF, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $FILE = curl_exec($ch);
        $SVNHASH = crc32(preg_replace(["/(\r\n)|(\r)/", '/(\\/\\*[\\d\\D]*?\\*\\/)/', '/\$I'.'d[^\$]+\$/'], ["\n", '', ''], $FILE));

        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 404)
        {
            echo 4;
            exit;
        }

        if (curl_errno($ch))
        {
            echo 3;
            exit;
        }

        curl_close($ch);
        $FILE2 = file_get_contents(ROOT_PATH.$file);
        $LOCALHASH = crc32(preg_replace(["/(\r\n)|(\r)/", '/(\\/\\*[\\d\\D]*?\\*\\/)/', '/\$I'.'d[^\$]+\$/'], ["\n", '', ''], $FILE2));
        if ($SVNHASH == $LOCALHASH)
        {
            echo 1;
            exit;
        }
        else
        {
            echo 2;
            exit;
        }

    }

    public function getFileList(): void
    {
        $EXT = explode("|", HTTP::_GP("ext", ""));

        $this->sendJSON(array_merge(
            $this->dir_tree('./', $EXT, false),
            $this->dir_tree('chat/', $EXT),
            $this->dir_tree('includes/', $EXT),
            $this->dir_tree('includes/', $EXT),
            $this->dir_tree('language/', $EXT),
            $this->dir_tree('scripts/', $EXT),
            $this->dir_tree('styles/', $EXT)
        ));

    }

    public function dir_tree($dir, $EXT, $subDir = true): array
    {
        $path = [];
        $stack[] = $dir;
        while ($stack)
        {
            $thisdir = array_pop($stack);
            if ($dircont = scandir($thisdir))
            {
                $i = 0;
                while (isset($dircont[$i]))
                {
                    if (!in_array($dircont[$i], ['.', '..', '.svn', '.info']))
                    {
                        $current_file = $thisdir.$dircont[$i];
                        if (is_file($current_file))
                        {
                            foreach ($EXT as $FILEXT)
                            {
                                if (preg_match("/\.".preg_quote($FILEXT)."$/i", $current_file))
                                {
                                    $path[] = str_replace(ROOT_PATH, '', str_replace('\\', '/', $current_file));
                                    break;
                                }
                            }
                        }
                        elseif ($subDir && is_dir($current_file))
                        {
                            $stack[] = $current_file."/";
                        }
                    }
                    $i++;
                }
            }
        }
        return $path;
    }

}
