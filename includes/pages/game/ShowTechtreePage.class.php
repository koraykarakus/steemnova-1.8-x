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

class ShowTechtreePage extends AbstractGamePage
{
    public static $requireModule = MODULE_TECHTREE;

    public function __construct()
    {
        parent::__construct();
    }

    public function show()
    {
        global $resource, $requeriments, $reslist, $USER, $PLANET, $LNG;

        $elementIDs = array_merge(
            [0],
            $reslist['build'],
            [100],
            $reslist['tech'],
            [200],
            $reslist['fleet'],
            [400],
            $reslist['defense'],
            [500],
            $reslist['missile'],
            [600],
            $reslist['officier']
        );

        $techTreeList = [];
        foreach ($elementIDs as $elementId)
        {
            if (!isset($resource[$elementId]))
            {
                $techTreeList[$elementId] = $elementId;
            }
            else
            {
                $requirementsList = [];
                if (isset($requeriments[$elementId]))
                {
                    foreach ($requeriments[$elementId] as $requireID => $RedCount)
                    {
                        $requirementsList[$requireID] = [
                            'count' => $RedCount,
                            'own'   => isset($PLANET[$resource[$requireID]]) ? $PLANET[$resource[$requireID]] : $USER[$resource[$requireID]],
                        ];
                    }
                }

                $techTreeList[$elementId] = $requirementsList;
            }
        }

        $this->assign([
            'TechTreeList' => $techTreeList,
        ]);

        $this->display('page.techTree.default.tpl');
    }
}
