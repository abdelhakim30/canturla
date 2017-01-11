<?php
require_once("../backend/functions.php");
dbconn(true);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta charset="utf-8"/>
    <title>Adimin Canturla</title>

    <meta name="description" content=""/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>

    <!-- bootstrap & fontawesome -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="assets/font-awesome/4.5.0/css/font-awesome.min.css"/>

    <!-- page specific plugin styles -->
    <link rel="stylesheet" href="assets/css/jquery-ui.custom.min.css"/>
    <link rel="stylesheet" href="assets/css/fullcalendar.min.css"/>
    <!-- page specific plugin styles -->
    <link rel="stylesheet" href="assets/css/jquery-ui.min.css"/>
    <link rel="stylesheet" href="assets/css/bootstrap-datepicker3.min.css"/>
    <link rel="stylesheet" href="assets/css/ui.jqgrid.min.css"/>
    <link rel="stylesheet" href="assets/css/chosen.min.css"/>
    <!-- text fonts -->
    <link rel="stylesheet" href="assets/css/fonts.googleapis.com.css"/>

    <!-- ace styles -->
    <link rel="stylesheet" href="assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style"/>

    <!--[if lte IE 9]>
    <link rel="stylesheet" href="assets/css/ace-part2.min.css" class="ace-main-stylesheet"/>
    <![endif]-->
    <link rel="stylesheet" href="assets/css/ace-skins.min.css"/>
    <link rel="stylesheet" href="assets/css/ace-rtl.min.css"/>

    <!--[if lte IE 9]>
    <link rel="stylesheet" href="assets/css/ace-ie.min.css"/>
    <![endif]-->

    <!-- inline styles related to this page -->

    <!-- ace settings handler -->
    <script src="assets/js/ace-extra.min.js"></script>

    <!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

    <!--[if lte IE 8]>
    <script src="assets/js/html5shiv.min.js"></script>
    <script src="assets/js/respond.min.js"></script>
    <![endif]-->
</head>
<div class=" pricing-box">
    <div class="widget-box widget-color-green">
        <div class="widget-header">
            <?php
            $catsquery = SQL_Query_exec("SELECT client FROM commande ");
            while ($catsrow = mysqli_fetch_assoc($catsquery)) {
                echo '      <h5 class="widget-title bigger lighter">client n° ' . $catsrow['client'] . '</h5>
        </div>';
            } ?>

            <div class="widget-body">
                <div class="widget-main">
                    <ul class="list-unstyled spaced2">
                        <li>
                            <i class="ace-icon fa fa-check green"></i>
                            50 GB Disk Space
                        </li>

                        <li>
                            <i class="ace-icon fa fa-check green"></i>
                            1 TB Bandwidth
                        </li>

                        <li>
                            <i class="ace-icon fa fa-check green"></i>
                            1000 Email Accounts
                        </li>

                        <li>
                            <i class="ace-icon fa fa-check green"></i>
                            100 MySQL Databases
                        </li>

                        <li>
                            <i class="ace-icon fa fa-check green"></i>
                            $25 Ad Credit
                        </li>

                        <li>
                            <i class="ace-icon fa fa-check green"></i>
                            Free Domain
                        </li>
                    </ul>

                </div>

            </div>
        </div>
    </div>


</html>