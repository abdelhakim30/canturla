<?PHP
require_once("template/headerad.php");
require_once("template/asidead.php");
require_once("template/bridcrumsad.php");

$today = date("Y-m-d ");
?>
    <div class="row">
        <div class="col-xs-12">
            <h3 class="header smaller lighter blue">nouveautés</h3>

            <!-- div.table-responsive -->

            <!-- div.dataTables_borderWrap -->
            <div>
                <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th class="center">
                            <label class="pos-rel">
                                <input type="checkbox" class="ace"/>
                                <span class="lbl"></span>
                            </label>
                        </th>
                        <th>nom</th>
                        <th>Prix</th>
                        <th>Description</th>
                        <th>date d'ajout</th>
                        <th>vote</th>

                        <th></th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php
                    $catsquery = SQL_Query_exec("SELECT * FROM produits ORDER BY new = 'oui'");
                    while ($catsrow = mysqli_fetch_assoc($catsquery)) {
                        if($catsrow['new'] == 'oui') {


                            echo '<tr>
                                <td class="center">
                                    <label class="pos-rel">
                                    <input type="checkbox" class="ace" />
                                    <span class="lbl"></span>
                                    </label>
                                </td>

                                <td>
                                    <a href="#">' . $catsrow["name"] . '</a>
                                </td>
                                <td>' . $catsrow["prix"] . '</td>
                                <td>' . $catsrow["description"] . '</td>
                                <td>' . $catsrow["added"] . '</td>
                                <td >' . $catsrow["ratingnum"] . '</td>';

                            echo '<td>
                                    <div class="hidden-sm hidden-xs action-buttons">
                                        <a class="green" href="categoriead.php?action=modifier&id=' . $catsrow["id"] . '">
                                            <i class="ace-icon fa fa-pencil bigger-130"></i>
                                         </a>

                                         <a class="red" href="categoriead.php?action=supprimer&id=' . $catsrow["id"] . '">
                                            <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                        </a>
                                    </div>

                                </td>
                            </tr>';
                        }
                    }
                    ?></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PAGE CONTENT ENDS -->
    </div><!-- /.col -->
    </div><!-- /.row -->
    </div><!-- /.page-content -->
    </div>
    </div><!-- /.main-content -->
<?php
require_once("template/footerad.php");
?>