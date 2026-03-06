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
    public static $require_module = MODULE_TECHTREE;

    public function __construct()
    {
        parent::__construct();
    }

    public function show(): void
    {
        global $resource, $requeriments, $reslist, $USER, $PLANET, $LNG;

        $element_ids = array_merge(
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

        $tech_tree_list = [];
        foreach ($element_ids as $c_id)
        {
            if (!isset($resource[$c_id]))
            {
                $tech_tree_list[$c_id] = $c_id;
            }
            else
            {
                $requirements_list = [];
                if (isset($requeriments[$c_id]))
                {
                    foreach ($requeriments[$c_id] as $require_id => $red_count)
                    {
                        $requirements_list[$require_id] = [
                            'count' => $red_count,
                            'own'   => isset($PLANET[$resource[$require_id]]) ? $PLANET[$resource[$require_id]] : $USER[$resource[$require_id]],
                        ];
                    }
                }

                $tech_tree_list[$c_id] = $requirements_list;
            }
        }

        $this->assign([
            'TechTreeList' => $tech_tree_list,
        ]);

        $this->display('page.techTree.default.tpl');
    }
}
