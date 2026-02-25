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

class ShowRaportPage extends AbstractGamePage
{
    public static $requireModule = 0;

    protected $disableEcoSystem = true;

    public function __construct()
    {
        parent::__construct();
    }

    private function BCWrapperPreRev2321($combat_report): mixed
    {
        if (isset($combat_report['moon']['desfail']))
        {
            $combat_report['moon'] = [
                'moonName'            => $combat_report['moon']['name'],
                'moonChance'          => $combat_report['moon']['chance'],
                'moonDestroySuccess'  => !$combat_report['moon']['desfail'],
                'fleetDestroyChance'  => $combat_report['moon']['chance2'],
                'fleetDestroySuccess' => !$combat_report['moon']['fleetfail'],
            ];
        }
        elseif (isset($combat_report['moon'][0]))
        {
            $combat_report['moon'] = [
                'moonName'            => $combat_report['moon'][1],
                'moonChance'          => $combat_report['moon'][0],
                'moonDestroySuccess'  => !$combat_report['moon'][2],
                'fleetDestroyChance'  => $combat_report['moon'][3],
                'fleetDestroySuccess' => !$combat_report['moon'][4],
            ];
        }

        if (isset($combat_report['simu']))
        {
            $combat_report['additionalInfo'] = $combat_report['simu'];
        }

        if (isset($combat_report['debris'][0]))
        {
            $combat_report['debris'] = [
                901 => $combat_report['debris'][0],
                902 => $combat_report['debris'][1],
            ];
        }

        if (!empty($combat_report['steal']['metal']))
        {
            $combat_report['steal'] = [
                901 => $combat_report['steal']['metal'],
                902 => $combat_report['steal']['crystal'],
                903 => $combat_report['steal']['deuterium'],
            ];
        }

        return $combat_report;
    }

    public function battlehall(): void
    {
        global $LNG, $USER;

        $LNG->includeData(['FLEET']);
        $this->setWindow('popup');

        $db = Database::get();

        $rid = HTTP::_GP('raport', '');

        $sql = "SELECT
			raport, time,
			(
				SELECT
				GROUP_CONCAT(username SEPARATOR ' & ') as attacker
				FROM %%USERS%%
				WHERE id IN (SELECT uid FROM %%TOPKB_USERS%% 
                WHERE %%TOPKB_USERS%%.rid = %%RW%%.rid AND role = 1)
			) as attacker,
			(
				SELECT
				GROUP_CONCAT(username SEPARATOR ' & ') as defender
				FROM %%USERS%%
				WHERE id IN (SELECT uid FROM %%TOPKB_USERS%% 
                WHERE %%TOPKB_USERS%%.rid = %%RW%%.rid AND role = 2)
			) as defender
			FROM %%RW%%
			WHERE rid = :reportID;";

        $report_data = $db->selectSingle($sql, [
            ':reportID' => $rid,
        ]);

        if (!$report_data)
        {
            $this->printMessage($LNG['sys_raport_not_found']);
        }

        $info = [$report_data["attacker"], $report_data["defender"]];

        $combat_report = unserialize($report_data['raport']);
        $combat_report['time'] = _date($LNG['php_tdformat'], $combat_report['time'], $USER['timezone']);
        $combat_report = $this->BCWrapperPreRev2321($combat_report);

        $this->assign([
            'Raport'    => $combat_report,
            'Info'      => $info,
            'pageTitle' => $LNG['lm_topkb'],
        ]);

        $this->display('shared.mission.raport.tpl');
    }

    public function show(): void
    {
        global $LNG, $USER;

        $LNG->includeData(['FLEET']);
        $this->setWindow('popup');

        $db = Database::get();

        $rid = HTTP::_GP('raport', '');

        $sql = "SELECT raport,attacker,defender FROM %%RW%% WHERE rid = :reportID;";
        $report_data = $db->selectSingle($sql, [
            ':reportID' => $rid,
        ]);

        if (empty($report_data))
        {
            $this->printMessage($LNG['sys_raport_not_found']);
        }

        // empty is BC for pre r2484
        $isAttacker = empty($report_data['attacker']) || in_array($USER['id'], explode(",", $report_data['attacker']));
        $isDefender = empty($report_data['defender']) || in_array($USER['id'], explode(",", $report_data['defender']));

        if (empty($report_data))
        {
            $this->printMessage($LNG['sys_raport_not_found']);
        }

        $combat_report = unserialize($report_data['raport']);
        if ($isAttacker
            && !$isDefender
            && $combat_report['result'] == 'r'
            && count($combat_report['rounds']) <= 2)
        {
            $this->printMessage($LNG['sys_raport_lost_contact']);
        }

        $combat_report['time'] = _date($LNG['php_tdformat'], $combat_report['time'], $USER['timezone']);
        $combat_report = $this->BCWrapperPreRev2321($combat_report);

        $this->assign([
            'Raport'    => $combat_report,
            'pageTitle' => $LNG['sys_mess_attack_report'],
        ]);

        $this->display('shared.mission.raport.tpl');
    }
}
