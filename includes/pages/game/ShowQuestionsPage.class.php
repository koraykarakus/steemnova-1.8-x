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

class ShowQuestionsPage extends AbstractGamePage
{
    public static $requireModule = 0;

    public function __construct()
    {
        parent::__construct();
    }

    public function show()
    {
        global $LNG;

        $LNG->includeData(['FAQ']);

        $this->display('page.questions.default.tpl');
    }

    public function single()
    {
        global $LNG;

        $LNG->includeData(['FAQ']);

        $categoryID = HTTP::_GP('categoryID', 0);
        $questionID = HTTP::_GP('questionID', 0);

        if (!isset($LNG['questions'][$categoryID][$questionID]))
        {
            HTTP::redirectTo('game.php?page=questions');
        }

        $this->assign([
            'questionRow' => $LNG['questions'][$categoryID][$questionID],
        ]);
        $this->display('page.questions.single.tpl');
    }
}
