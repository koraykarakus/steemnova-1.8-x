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

class Theme
{
    public static array $theme_array = [];
    private array $theme_settings = [];

    // default theme is nova.
    private string $theme = 'nova';

    private $custom_tpls;

    public function setUserTheme($inputTheme): void
    {
        if (empty($inputTheme))
        {
            return;
        }

        if (!file_exists(ROOT_PATH.'styles/theme/'.$inputTheme.'/style.cfg'))
        {
            return;
        }

        $this->theme = $inputTheme;
        $this->parseStyleCFG();
        $this->setStyleSettings();
    }

    public function getThemePath()
    {
        return './styles/theme/'.$this->theme.'/';
    }

    public function getTemplatePath()
    {
        return ROOT_PATH.'/styles/templates/'.$this->theme.'/';
    }

    public function isCustomTPL($tpl)
    {
        if (!isset($this->custom_tpls))
        {
            return false;
        }

        return in_array($tpl, $this->custom_tpls);
    }

    private function parseStyleCFG()
    {
        require(ROOT_PATH.'styles/theme/'.$this->theme.'/style.cfg');
        // get $Skin array from style.cfg of related theme
        $this->custom_tpls = (array) $Skin['templates'];
    }

    private function setStyleSettings()
    {
        if (file_exists(ROOT_PATH.'styles/theme/'.$this->theme.'/settings.cfg'))
        {
            require(ROOT_PATH.'styles/theme/'.$this->theme.'/settings.cfg');
        }

        /** @var Array $THEMESETTINGS */
        $this->theme_settings = $THEMESETTINGS;
    }

    public function getStyleSettings()
    {
        return $this->theme_settings;
    }

    public static function getAvalibleSkins(): array
    {
        if (empty(self::$theme_array))
        {
            if (file_exists(ROOT_PATH.'cache/cache.themes.php'))
            {
                self::$theme_array = unserialize(file_get_contents(ROOT_PATH.'cache/cache.themes.php'));
            }
            else
            {
                $Skins = array_diff(scandir(ROOT_PATH.'styles/theme/'), ['..', '.', '.svn', '.htaccess', 'index.htm']);
                $Themes = [];
                foreach ($Skins as $Theme)
                {
                    if (!file_exists(ROOT_PATH.'styles/theme/'.$Theme.'/style.cfg'))
                    {
                        continue;
                    }

                    require(ROOT_PATH.'styles/theme/'.$Theme.'/style.cfg');
                    $Themes[$Theme] = $Skin['name'];
                }
                file_put_contents(ROOT_PATH.'cache/cache.themes.php', serialize($Themes));
                self::$theme_array = $Themes;
            }
        }

        return self::$theme_array;
    }
}
