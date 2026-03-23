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

// VARS DB -> SCRIPT WRAPPER

$cache = Cache::get();
$cache->add('vars', 'VarsBuildCache');
extract($cache->getData('vars'));

$RESOURCE[901] = 'metal';
$RESOURCE[902] = 'crystal';
$RESOURCE[903] = 'deuterium';
$RESOURCE[911] = 'energy';
$RESOURCE[921] = 'darkmatter';

$RESLIST['ressources'] = [901, 902, 903, 911, 921];
$RESLIST['resstype'][1] = [901, 902, 903];
$RESLIST['resstype'][2] = [911];
$RESLIST['resstype'][3] = [921];
