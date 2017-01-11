<?PHP
require_once("template/headerad.php");
require_once("template/asidead.php");
require_once("template/bridcrumsad.php");

$today = date("Y-m-d");
?>
    <div class="page-header">
        <h1>
            Commande
        </h1>
    </div><!-- /.page-header -->

    <div class="row">
    <div class="col-xs-12">
    <!-- PAGE CONTENT BEGINS -->
    <div class="row">
        <div class="col-xs-6 col-sm-6 pricing-box">
            <div class="widget-box widget-color-dark">
                <div class="widget-header">
                    <h5 class="widget-title bigger lighter">Matin</h5>
                </div>
                <div class="widget-body">
                    <div class="widget-main">
                        <?php
                        $catsquery = SQL_Query_exec("SELECT * FROM commande ORDER BY dates");

                        while ($catsrow = mysqli_fetch_assoc($catsquery)) {
                            if ($catsrow['dates'] == $today) {
                                if ($catsrow['momment'] == "matin") {
                                    echo '
                                <tr>                 
                                    <td>
                                        <a href="#null" onclick="javascript:open_infos();">client n°' . $catsrow["client"] . '</a>
                                    </td>
                                    <td>commande pour le ' . $catsrow["dates"] . '</td>
                                </tr>
                                <br/>';
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xs-6 col-sm-6 pricing-box">
            <div class="widget-box widget-color-orange">
                <div class="widget-header">
                    <h5 class="widget-title bigger lighter">Apres-midi</h5>
                </div>

                <div class="widget-body">
                    <div class="widget-main">
                        <?php
                        $catsquery = SQL_Query_exec("SELECT * FROM commande ORDER BY dates");
                        while ($catsrow = mysqli_fetch_assoc($catsquery)) {
                            if ($catsrow['dates'] == $today) {
                                if ($catsrow['momment'] == "aprem") {
                                    echo '
                                <tr>                 
                                    <td>
                                        <a href="#null" onclick="javascript:open_infos();">client n°' . $catsrow["client"] . '</a>
                                    </td>
                                    <td>commande pour le ' . $catsrow["dates"] . '</td>
                                </tr>
                                <br/>';
                                }
                            }
                        }
                        ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        <!--
        function open_infos() {
            window.open('popup.php', 'nom_de_ma_popup', 'menubar=no, scrollbars=no, top=100, left=400, width=1000, height=600');
        }
        -->
    </script>
<?php
require_once("template/footerad.php");
?>