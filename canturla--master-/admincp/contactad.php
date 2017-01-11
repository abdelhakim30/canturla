<?PHP
require_once("template/headerad.php");
require_once("template/asidead.php");
require_once("template/bridcrumsad.php");


?>
<div class="col-xs-12 widget-container-col" id="widget-container-col-2">
    <div class="widget-box widget-color-blue" id="widget-box-2">
        <div class="widget-header">
            <h5 class="widget-title bigger lighter">
                <i class="ace-icon fa fa-users"></i>
                Contacts
            </h5>

            <div class="widget-toolbar widget-toolbar-light no-border">
                <select id="simple-colorpicker-1" class="hide">
                    <option selected="" data-class="blue" value="#307ECC">#307ECC</option>
                    <option data-class="blue2" value="#5090C1">#5090C1</option>
                    <option data-class="blue3" value="#6379AA">#6379AA</option>
                    <option data-class="green" value="#82AF6F">#82AF6F</option>
                    <option data-class="green2" value="#2E8965">#2E8965</option>
                    <option data-class="green3" value="#5FBC47">#5FBC47</option>
                    <option data-class="red" value="#E2755F">#E2755F</option>
                    <option data-class="red2" value="#E04141">#E04141</option>
                    <option data-class="red3" value="#D15B47">#D15B47</option>
                    <option data-class="orange" value="#FFC657">#FFC657</option>
                    <option data-class="purple" value="#7E6EB0">#7E6EB0</option>
                    <option data-class="pink" value="#CE6F9E">#CE6F9E</option>
                    <option data-class="dark" value="#404040">#404040</option>
                    <option data-class="grey" value="#848484">#848484</option>
                    <option data-class="default" value="#EEE">#EEE</option>
                </select>
            </div>
        </div>

        <div class="widget-body">
            <div class="widget-main no-padding">
                <table class="table table-striped table-bordered table-hover">
                    <thead class="thin-border-bottom">
                    <tr>
                        <th>
                            <i class="ace-icon fa fa-user"></i>
                            Utilisateur
                        </th>
                        <th>
                            nom

                        </th>
                        <th>
                            prenom
                        </th>

                        <th>
                            <i>@</i>
                            Email
                        </th>
                        <th>
                            tel
                        </th>
                        <th>
                            banir
                        </th>
                    </tr>
                    </thead>

                    <tbody>

                    <?php
                    $catsquery = SQL_Query_exec("SELECT * FROM clients ORDER BY username");
                    while ($catsrow = mysqli_fetch_assoc($catsquery)) {
                        echo '<tr>
               


                        <td>
                            <a href="#">' . $catsrow["username"] . '</a>
                        </td>
                        <td>' . $catsrow["nom"] . '</td>
                        <td>' . $catsrow["prenom"] . '</td>
                        <td>' . $catsrow["email"] . '</td>
                        <td>' . $catsrow["tel"] . '</td>
                        <td>' . $catsrow["banned"] . '</td>';
                    }
                    ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div><!-- /.span -->
</div><!-- /.row -->
<?php
require_once("template/footerad.php");
?>
