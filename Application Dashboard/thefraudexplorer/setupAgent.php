<?php

/*
 * The Fraud Explorer
 * https://www.thefraudexplorer.com/
 *
 * Copyright (c) 2017 The Fraud Explorer
 * email: customer@thefraudexplorer.com
 * Licensed under GNU GPL v3
 * https://www.thefraudexplorer.com/License
 *
 * Date: 2017-06
 * Revision: v1.0.1-beta
 *
 * Description: Code for setup agent
 */

include "lbs/login/session.php";
include "lbs/security.php";

if(!$session->logged_in)
{
    header ("Location: index");
    exit;
}

include "lbs/global-vars.php";
include "lbs/open-db-connection.php";

$agent_enc=filter($_GET['agent']);
$agent_dec=base64_decode(base64_decode($agent_enc));

?>

<style>

    .title
    {
        font-family: 'FFont', sans-serif; font-size:12px;
    }

    .input-value-text
    {
        width:100%; 
        height: 30px; 
        padding: 5px; 
        border: solid 1px #c9c9c9; 
        outline: none;
        font-family: 'FFont', sans-serif; font-size:12px;
    }

    .window-footer
    {
        padding: 0px 0px 0px 0px;
    }

    .div-container
    {
        margin: 20px;
    }

    .select-ruleset-styled, .select-gender-styled
    {
        position: relative;
        border: 1px solid #ccc;
        width: 100%;
        height: 30px;
        overflow: scroll;
        background-color: #fff;
        outline: 0 !important;
    }

    .select-ruleset-styled:before, .select-gender-styled:before
    {
        content: '';
        position: absolute;
        right: 5px;
        top: 7px;
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 7px 5px 0 5px;
        border-color: #000000 transparent transparent transparent;
        z-index: 5;
        pointer-events: none;
    }

    .select-ruleset-styled select, .select-gender-styled select
    {
        padding: 5px 8px;
        width: 130%;
        border: none;
        box-shadow: none;
        background-color: transparent;
        background-image: none;
        appearance: none;
    }

</style>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title window-title" id="myModalLabel">Endpoint setup</h4>
</div>

<?php

/* SQL Queries */

$queryName = "SELECT name FROM (SELECT SUBSTRING_INDEX(agent, '_', 1) AS agent, name, heartbeat FROM t_agents GROUP BY agent ORDER BY heartbeat DESC) AS agents WHERE agent='%s' GROUP BY agent";
$queryRule = "SELECT ruleset FROM (SELECT SUBSTRING_INDEX(agent, '_', 1) AS agent, ruleset, heartbeat FROM t_agents GROUP BY agent ORDER BY heartbeat DESC) AS agents WHERE agent='%s' GROUP BY agent";
$queryGender = "SELECT gender FROM (SELECT SUBSTRING_INDEX(agent, '_', 1) AS agent, gender, heartbeat FROM t_agents GROUP BY agent ORDER BY heartbeat DESC) AS agents WHERE agent='%s' GROUP BY agent";

?>

<div class="div-container">
    <form id="formSetup" name="formSetup" method="post" action="<?php echo 'setupAgentParameters?agent='.$agent_enc; ?>">
        <p class="title">Agent alias</p><br>
        <input type="text" name="alias" id="alias" autocomplete="off" placeholder=":alias here <?php $aliasquery = mysql_query(sprintf($queryName,$agent_dec)); $alias = mysql_fetch_array($aliasquery); if ($alias[0] == NULL) echo '(current value: Not alias yet)'; else echo '(current value: '.$alias[0].')'; ?>" class="input-value-text">
        <br><br><p class="title">Ruleset or Dictionary</p><br>

        <select class="select-ruleset-styled" name="ruleset" id="ruleset">
            <option selected="selected">Choose the ruleset <?php $rulesetquery = mysql_query(sprintf($queryRule, $agent_dec)); $ruleset = mysql_fetch_array($rulesetquery); if ($ruleset[0] == NULL) echo '(current dictionary: BASELINE)'; else echo '(current dictionary: '.$ruleset[0].')'; ?></option>

            <?php

            $configFile = parse_ini_file("config.ini");
            $jsonFT = json_decode(file_get_contents($configFile['fta_text_rule_spanish']), true);
            $GLOBALS['listRuleset'] = null;

            foreach ($jsonFT['dictionary'] as $ruleset => $value)
            {
                echo '<option value="'.$ruleset.'">'.$ruleset.'</option>';
            }

            ?>
        </select> 

        <br><br><p class="title">Endpoint gender</p><br>

        <select class="select-gender-styled" name="gender" id="gender">
            <option selected="selected">Choose the gender <?php $genderquery = mysql_query(sprintf($queryGender, $agent_dec)); $gender = mysql_fetch_array($genderquery); if ($gender[0] == NULL) echo '(current value: Not gender yet)'; else echo '(current value: '.$gender[0].')'; ?></option>
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select>

        <br><br>
        <div class="modal-footer window-footer">
            <br><button type="button" class="btn btn-default" data-dismiss="modal" style="outline: 0 !important;">Cancel</button>
            <input type="submit" class="btn btn-danger setup" value="Set values" style="outline: 0 !important;">
        </div>
    </form>
</div>

<?php include "lbs/close-db-connection.php"; ?>