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
        global $RESOURCE, $REQUIREMENTS, $RESLIST, $USER, $PLANET, $LNG;

        $element_ids = array_merge(
            [0],
            $RESLIST['build'],
            [100],
            $RESLIST['tech'],
            [200],
            $RESLIST['fleet'],
            [400],
            $RESLIST['defense'],
            [500],
            $RESLIST['missile'],
            [600],
            $RESLIST['officers']
        );

        $tech_tree_list = [];
        foreach ($element_ids as $c_id)
        {
            if (!isset($RESOURCE[$c_id]))
            {
                $tech_tree_list[$c_id] = $c_id;
            }
            else
            {
                $requirements_list = [];
                if (isset($REQUIREMENTS[$c_id]))
                {
                    foreach ($REQUIREMENTS[$c_id] as $require_id => $red_count)
                    {
                        $requirements_list[$require_id] = [
                            'count' => $red_count,
                            'own'   => isset($PLANET[$RESOURCE[$require_id]]) ? $PLANET[$RESOURCE[$require_id]] : $USER[$RESOURCE[$require_id]],
                        ];
                    }
                }

                $tech_tree_list[$c_id] = $requirements_list;
            }
        }

        $this->assign([
            'tech_tree_list' => $tech_tree_list,
        ]);

        $this->display('page.techTree.default.tpl');
    }
}
