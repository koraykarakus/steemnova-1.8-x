{block name="content"}
    <form action="?page=reset&mode=send" class="bg-black w-75  p-3 my-3 mx-auto fs-12" method="post"
        onsubmit="return confirm('{$re_reset_universe_confirmation}');">

        <div class="form-group my-1 p-2">
            {$re_reset_all}: <input type="checkbox" name="resetall"
                onclick="$('input').attr('checked', this.checked ? 'checked' : false)">
        </div>

        <ul class="nav nav-tabs" id="" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab_1" type="button">
                    {$re_player_and_planets}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_2" type="button">
                    {$re_defenses_and_ships}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_3" type="button">
                    {$re_buldings}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_4" type="button">
                    {$re_inve_ofis}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_5" type="button">
                    {$re_resources}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_6" type="button">
                    {$re_general}
                </button>
            </li>
        </ul>

        <div class="tab-content mt-3">
            <div class="tab-pane fade show active" id="tab_1">
                <table>
                    <tr>
                        <td style="text-align:left">{$re_reset_player}</td>
                        <td style="text-align:right"><input type="checkbox" name="players"></td>
                    </tr>
                    <tr>
                        <td style="text-align:left">{$re_reset_planets}</td>
                        <td style="text-align:right"><input type="checkbox" name="planets"></td>
                    </tr>
                    <tr>
                        <td style="text-align:left">{$re_reset_moons}</td>
                        <td style="text-align:right"><input type="checkbox" name="moons"></td>
                    </tr>
                </table>
            </div>
            <div class="tab-pane fade" id="tab_2">
                <table>
                    <tr>
                        <td style="text-align:left">{$re_defenses}</td>
                        <td style="text-align:right"><input type="checkbox" name="defenses"></td>
                    </tr>
                    <tr>
                        <td style="text-align:left">{$re_ships}</td>
                        <td style="text-align:right"><input type="checkbox" name="ships"></td>
                    </tr>
                    <tr>
                        <td style="text-align:left">{$re_reset_hangar}</td>
                        <td style="text-align:right"><input type="checkbox" name="h_d"></td>
                    </tr>
                </table>
            </div>
            <div class="tab-pane fade" id="tab_3">
                <table>
                    <tr>
                        <td style="text-align:left">{$re_buildings_pl}</td>
                        <td style="text-align:right"><input type="checkbox" name="edif_p"></td>
                    </tr>
                    <tr>
                        <td style="text-align:left">{$re_buildings_lu}</td>
                        <td style="text-align:right"><input type="checkbox" name="edif_l"></td>
                    </tr>
                    <tr>
                        <td style="text-align:left">{$re_reset_buldings}</td>
                        <td style="text-align:right"><input type="checkbox" name="edif"></td>
                    </tr>
                </table>
            </div>
            <div class="tab-pane fade" id="tab_4">
                <table>
                    <tr>
                        <td style="text-align:left">{$re_ofici}</td>
                        <td style="text-align:right"><input type="checkbox" name="ofis"></td>
                    </tr>
                    <tr>
                        <td style="text-align:left">{$re_investigations}</td>
                        <td style="text-align:right"><input type="checkbox" name="inves"></td>
                    </tr>
                    <tr>
                        <td style="text-align:left">{$re_reset_invest}</td>
                        <td style="text-align:right"><input type="checkbox" name="inves_c"></td>
                    </tr>
                </table>
            </div>
            <div class="tab-pane fade" id="tab_5">
                <table>
                    <tr>
                        <td style="text-align:left">{$re_resources_dark}</td>
                        <td style="text-align:right"><input type="checkbox" name="dark"></td>
                    </tr>
                    <tr>
                        <td style="text-align:left">{$re_resources_met_cry}</td>
                        <td style="text-align:right"><input type="checkbox" name="resources"></td>
                    </tr>
                </table>
            </div>
            <div class="tab-pane fade" id="tab_6">
                <table>
                    <tr>
                        <td style="text-align:left">{$re_reset_notes}</td>
                        <td style="text-align:right"><input type="checkbox" name="notes"></td>
                    </tr>
                    <tr>
                        <td style="text-align:left">{$re_reset_rw}</td>
                        <td style="text-align:right"><input type="checkbox" name="rw"></td>
                    </tr>
                    <tr>
                        <td style="text-align:left">{$re_reset_buddies}</td>
                        <td style="text-align:right"><input type="checkbox" name="friends"></td>
                    </tr>
                    <tr>
                        <td style="text-align:left">{$re_reset_allys}</td>
                        <td style="text-align:right"><input type="checkbox" name="alliances"></td>
                    </tr>
                    <tr>
                        <td style="text-align:left">{$re_reset_fleets}</td>
                        <td style="text-align:right"><input type="checkbox" name="fleets"></td>
                    </tr>
                    <tr>
                        <td style="text-align:left">{$re_reset_errors}</td>
                        <td style="text-align:right"><input type="checkbox" name="errors"></td>
                    </tr>
                    <tr>
                        <td style="text-align:left">{$re_reset_banned}</td>
                        <td style="text-align:right"><input type="checkbox" name="banneds"></td>
                    </tr>
                    <tr>
                        <td style="text-align:left">{$re_reset_messages}</td>
                        <td style="text-align:right"><input type="checkbox" name="messages"></td>
                    </tr>
                    <tr>
                        <td style="text-align:left">{$re_reset_statpoints}</td>
                        <td style="text-align:right"><input type="checkbox" name="statpoints"></td>
                    </tr>
                </table>
            </div>
        </div>

        <input class="btn btn-primary " type="submit" value="{$button_submit}">

{/block}