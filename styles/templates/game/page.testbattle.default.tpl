{block name="title" prepend}
    Tittle test battle
{/block}

{block name="content"}
    <script>
        $(document).on('click', '#attacker_add_1000', function() {
            $('input[name^="atk_ship_"]').each(function() {
                let val = parseInt($(this).val()) || 0;
                $(this).val(val + 1000);
            });
        });
        $(document).on('click', '#defender_ships_add_1000', function() {
            $('input[name^="def_ship_"]').each(function() {
                let val = parseInt($(this).val()) || 0;
                $(this).val(val + 1000);
            });
        });
        $(document).on('click', '#defender_defs_add_1000', function() {
            $('input[name^="def_def_"]').each(function() {
                let val = parseInt($(this).val()) || 0;
                $(this).val(val + 1000);
            });
        });
    </script>


    <div class="ItemsWrapper">
        <form action="?page=testBattle&mode=send" method="post">
            <table class="table_game">
                <thead>
                    <th colspan="2">Planet Info</th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <table class="table_game">
                                <thead>
                                    <th colspan="2">Attacker Coordinates:</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Galaxy:</td>
                                        <td><input type="number" name="galaxy_attacker" value="0"></td>
                                    </tr>
                                    <tr>
                                        <td>System:</td>
                                        <td><input type="number" name="system_attacker" value="0"></td>
                                    </tr>
                                    <tr>
                                        <td>Planet:</td>
                                        <td><input type="number" name="planet_attacker" value="0"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td>
                            <table class="table_game">
                                <thead>
                                    <th colspan="2">Defender Coordinates:</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Galaxy:</td>
                                        <td><input type="number" name="galaxy_defender" value="0"></td>
                                    </tr>
                                    <tr>
                                        <td>System:</td>
                                        <td><input type="number" name="system_defender" value="0"></td>
                                    </tr>
                                    <tr>
                                        <td>Planet:</td>
                                        <td><input type="number" name="planet_defender" value="0"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="table_game">
                <tr>
                    <td>
                        <table>
                            <thead>
                                <th colspan="2">
                                    Attacker
                                    <button id="attacker_add_1000" type="button">+</button>
                                </th>
                            </thead>
                            {foreach $ships_list as $c_ship}
                                <tr>
                                    <td>{$c_ship.name}</td>
                                    <td>
                                        <input type="number" name="atk_ship_{$c_ship.id}" value="0">
                                    </td>
                                </tr>
                            {/foreach}
                        </table>
                    </td>
                    <td>
                        <table>
                            <thead>
                                <th colspan="2">
                                    Defender
                                    <button id="defender_ships_add_1000" type="button">+</button>
                                </th>
                            </thead>
                            {foreach $ships_list as $c_ship}
                                <tr>
                                    <td>{$c_ship.name}</td>
                                    <td>
                                        <input type="number" name="def_ship_{$c_ship.id}" value="0">
                                    </td>
                                </tr>
                            {/foreach}
                        </table>
                    </td>
                    <td>
                        <table>
                            <thead>
                                <th colspan="2">
                                    Defender
                                    <button id="defender_defs_add_1000" type="button">+</button>
                                </th>
                            </thead>
                            {foreach $defense_list as $c_defense}
                                <tr>
                                    <td>{$c_defense.name}</td>
                                    <td>
                                        <input type="number" name="def_def_{$c_defense.id}" value="0">
                                    </td>
                                </tr>
                            {/foreach}
                        </table>
                    </td>
                </tr>
            </table>
            <table class="table_game">
                <tr>
                    <td>
                        <button type="submit">Start fleets</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
{/block}