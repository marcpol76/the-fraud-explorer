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
 * Description: Code for top menu
 */

include "lbs/login/session.php";
include "lbs/security.php";

if(!$session->logged_in)
{
    header ("Location: index");
    exit;
}

include "lbs/open-db-connection.php";
include "lbs/agent_methods.php";

/* SQL queries */

if ($session->domain == "all")
{
    if (samplerStatus($session->domain) == "enabled")
    {
        $queryCountTotalsSQL = "SELECT COUNT(*) AS total FROM (SELECT agent FROM (SELECT SUBSTRING_INDEX(agent, '_', 1) AS agent FROM t_agents) AS agents GROUP BY agent) AS totals";
        $queryCountActiveSQL = "SELECT COUNT(*) AS total FROM (SELECT agent, status FROM (SELECT SUBSTRING_INDEX(agent, '_', 1) AS agent, status FROM t_agents) AS agents GROUP BY agent) AS totals WHERE status='active'";
        $queryCountInactiveSQL = "SELECT COUNT(*) AS total FROM (SELECT agent, status FROM (SELECT SUBSTRING_INDEX(agent, '_', 1) AS agent, status FROM t_agents) AS agents GROUP BY agent) AS totals WHERE status='inactive'";
    }
    else
    {
        $queryCountTotalsSQL = "SELECT COUNT(*) AS total FROM (SELECT agent, domain FROM (SELECT SUBSTRING_INDEX(agent, '_', 1) AS agent, domain FROM t_agents) AS agents GROUP BY agent) AS totals WHERE domain NOT LIKE 'thefraudexplorer.com'";
        $queryCountActiveSQL = "SELECT COUNT(*) AS total FROM (SELECT agent, domain, status FROM (SELECT SUBSTRING_INDEX(agent, '_', 1) AS agent, domain, status FROM t_agents) AS agents GROUP BY agent) AS totals WHERE domain NOT LIKE 'thefraudexplorer.com' AND status='active'";
        $queryCountInactiveSQL = "SELECT COUNT(*) AS total FROM (SELECT agent, domain, status FROM (SELECT SUBSTRING_INDEX(agent, '_', 1) AS agent, domain, status FROM t_agents) AS agents GROUP BY agent) AS totals WHERE domain NOT LIKE 'thefraudexplorer.com' AND status='inactive'";
    }
}
else
{
    if (samplerStatus($session->domain) == "enabled")
    {
        $queryCountTotalsSQL = "SELECT COUNT(*) AS total FROM (SELECT agent, domain FROM (SELECT SUBSTRING_INDEX(agent, '_', 1) AS agent, domain FROM t_agents) AS agents GROUP BY agent) AS totals WHERE domain='".$session->domain."' OR domain='thefraudexplorer.com'";
        $queryCountActiveSQL = "SELECT COUNT(*) AS total FROM (SELECT agent, domain, status FROM (SELECT SUBSTRING_INDEX(agent, '_', 1) AS agent, domain, status FROM t_agents) AS agents GROUP BY agent) AS totals WHERE domain='".$session->domain."' OR domain='thefraudexplorer.com' AND status='active'";
        $queryCountInactiveSQL = "SELECT COUNT(*) AS total FROM (SELECT agent, domain, status FROM (SELECT SUBSTRING_INDEX(agent, '_', 1) AS agent, domain, status FROM t_agents) AS agents GROUP BY agent) AS totals WHERE domain='".$session->domain."' OR domain='thefraudexplorer.com' AND status='inactive'";
    }
    else
    {
        $queryCountTotalsSQL = "SELECT COUNT(*) AS total FROM (SELECT agent, domain FROM (SELECT SUBSTRING_INDEX(agent, '_', 1) AS agent, domain FROM t_agents) AS agents GROUP BY agent) AS totals WHERE domain='".$session->domain."' AND domain NOT LIKE 'thefraudexplorer.com'";
        $queryCountActiveSQL = "SELECT COUNT(*) AS total FROM (SELECT agent, domain, status FROM (SELECT SUBSTRING_INDEX(agent, '_', 1) AS agent, domain, status FROM t_agents) AS agents GROUP BY agent) AS totals WHERE domain='".$session->domain."' AND domain NOT LIKE 'thefraudexplorer.com' AND status='active'";
        $queryCountInactiveSQL = "SELECT COUNT(*) AS total FROM (SELECT agent, domain, status FROM (SELECT SUBSTRING_INDEX(agent, '_', 1) AS agent, domain, status FROM t_agents) AS agents GROUP BY agent) AS totals WHERE domain='".$session->domain."' AND domain NOT LIKE 'thefraudexplorer.com' AND status='inactive'";
    }
}

include "lbs/open-db-connection.php";
$count_all = mysql_fetch_assoc(mysql_query($queryCountTotalsSQL));
$count_online = mysql_fetch_assoc(mysql_query($queryCountActiveSQL));
$count_offline = mysql_fetch_assoc(mysql_query($queryCountInactiveSQL));
include "lbs/close-db-connection.php";

?>

<!-- Styles -->

<link rel="stylesheet" type="text/css" href="css/topMenu.css">

<ul class="ul" id="elm-topmenu">
    <li class="li">
        <p class="fixed-space">&nbsp;</p>
        &nbsp;&nbsp;<img src=images/nftop.svg class="main-logo">
    </li>
    <li class="li">
        <a href="dashBoard" id="elm-dashboard">Dashboard</a>
    </li>
    <li class="li">
        <a href="endPoints" id="elm-endpoints">Endpoints</a>
    </li>
    <li class="li">
        <a href="analyticsData" id="elm-analytics">Analytics</a>
    </li>
    <li class="li">
        <a href="setupRuleset" data-toggle="modal" data-target="#ruleset" href="#" id="elm-ruleset">Ruleset</a>
    </li>
    <li class="li">
        <a href="mainConfig" data-toggle="modal" data-target="#confirm-config" href="#" id="elm-configuration">Settings</a>
    </li>

    <?php
    
    if (isset($_GET['origin'])) $resourceOrigin=filter($_GET['origin']);
    else $resourceOrigin = "other";
    
    if ($session->domain == "all")
    {
        echo '<li class="li">';
        echo '<a href="rolesConfig" data-toggle="modal" data-target="#roles" href="#" id="elm-roles">Roles</a>';
        echo '</li>';
    }
        
    if ($resourceOrigin == "endpoints")
    {
        echo '<li class="li">';
        echo '<a href="eraseCommands" id="elm-queuereset">Reset</a>';
        echo '</li>';

        if ($session->domain == "all")
        {
            echo '<li class="li">';
            echo '<a id="elm-globalcommand" href="endPoints?agent='.base64_encode(base64_encode("all")).'&domain='.base64_encode(base64_encode("all")).'">Command</a>';
            echo '</li>';
        }
        else
        {
            echo '<li class="li">';
            echo '<a id="elm-globalcommand" href="endPoints?agent='.base64_encode(base64_encode("all")).'&domain='.base64_encode(base64_encode('.$session->domain.')).'">Command</a>';
            echo '</li>';
        }
    }
    
    ?>

    <li class="li">
        <a href="#" onclick="startTour()">Take tour</a>
    </li>
    <li style="float:right">
        <a class="active logout-button" href="logout">Logout</a>
    </li>
    <li class="search search-input" id="elm-search">
        <input type="search" name="search_text" autocomplete="off" id="search-box" class="search_text" data-column="any" placeholder="Search ..."/>
        <input class="input-search" type="button" name="search_button" id="search_button">
    </li>
    <li class="li counters">
        <button class="button-totals" id="totals-menu">Total<br><?php echo str_pad($count_all['total'], 4, "0", STR_PAD_LEFT); ?></button>
    </li>
    <li class="li counters" id="elm-counters">
        <button class="button-totals" id="totals-menu">Online<br><?php echo str_pad($count_online['total'], 4, "0", STR_PAD_LEFT); ?></button>
    </li>
    <li class="li counters">
        <button class="button-totals" id="totals-menu">Offline<br><?php echo str_pad($count_offline['total'], 4, "0", STR_PAD_LEFT); ?></button>
    </li>
</ul>
<br>

<!-- Modal for main Configuration -->

<div class="modal fade-scale" id="confirm-config" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-center">
            <div class="modal-content">
                <div class="modal-body">
                    <p class="debug-url window-debug"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Ruleset -->

<div class="modal fade-scale" id="ruleset" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-center">
            <div class="modal-content">
                <div class="modal-body">
                    <p class="debug-url window-debug"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Roles -->

<div class="modal fade-scale" id="roles" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-center">
            <div class="modal-content">
                <div class="modal-body">
                    <p class="debug-url window-debug"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

if (isset($_SESSION['instance']) && ($_SESSION['instance'] != "endPoints" && $_SESSION['instance'] != "alertData"))
{
    echo '<script>';
    echo 'document.getElementById("search-box").disabled = true;';
    echo 'document.getElementById("search-box").style.backgroundColor = "#e2e2e2";';
    echo 'document.getElementById("search-box").value = "Disabled search ...";';
    echo '</script>';
}

?>
