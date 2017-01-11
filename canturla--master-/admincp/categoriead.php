<?PHP
require_once("template/headerad.php");
require_once("template/asidead.php");
require_once("template/bridcrumsad.php");
$action = $_GET["action"];
$do = $_GET["do"];
if (!$action) {
    $action = $_POST["action"];
}
if (!$do) {
    $do = $_POST["do"];
}

if ($action == "ajouter") {
    if ($do == "ajouter") {
        $erreur = "";
        $name = $_POST["nom"];
        if (!$name) {
            $erreur .= "manque nom...";
        }
        $visible = $_POST["visible"];
        if ($visible != "oui" && $visible != "non") {
            $erreur .= "visible doit etre oui ou non...";
        }
        $sort_index = $_POST["ordre"];
        if (!$sort_index) {
            $erreur .= "manque l'ordre....";
        }
        $subcast = $_POST["scat"];
        if ($subcast != "oui" && $subcast != "non") {
            $erreur .= "sous-catégorie doit etre oui ou non...";
        }
        $image = $_POST["image"];
        $imageName = $_FILES["name"];
        $prent_cast = $_POST["catparent"];
        $sub_sort = $_POST["ordre sc"];
        if ($erreur == "") {
            SQL_Query_exec("INSERT INTO categories (`name`, `visible`, `sort_index`, `subcast`, `prent_cast`, `sub_sort`) VALUES ('$name', '$visible', '$sort_index', '$subcast', '$prent_cast', '$sub_sort')");
            echo "Vous avez bien ajouté:<br />";
            echo "Nom: " . $name . " Visible: " . $visible . " Ordre: " . $sort_index . " Sous catégorie: " . $subcast . " Image: " . $image . " Catégorie parente: " . $prent_cast . " Ordre sous-Catégorie: " . $sub_sort;
            echo "<br /><br />
            <a href='categoriead.php'>Retour aux categories</a>";
        } else {
            echo $erreur;
        }
    } else {
        echo '<div class="page-header">
	    <h1>
			Catégories
			<small>
			    <i class="ace-icon fa fa-angle-double-right"></i>
				ajout catégorie
			</small>
		</h1>
	</div><!-- /.page-header -->
    <div class="row">
        <div class="col-xs-12">
            <form class="form-horizontal" role="form" method="post" action="categoriead.php">
                <input type="hidden" name="action" value="ajouter">
                <input type="hidden" name="do" value="ajouter">
                <div class="form-group">
				    <label class="col-sm-3 control-label no-padding-right" for="form-field-nom">nom</label>
                    <div class="col-sm-9">
					    <input type="text" name="nom" id="form-field-nom" placeholder="nom" class="col-xs-10 col-sm-5" />
					</div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-field-visible">visible</label>
                    <div class="col-xs-1">
					    <select class="form-control" id="form-field-visible" name="visible">
                            <option value="oui">oui</option>
                            <option value="non">non</option>
                        </select>
				    </div>
				</div>
				<div class="form-group">
				    <label class="col-sm-3 control-label no-padding-right" for="form-field-ordre">ordre</label>
				    <div class="col-sm-9">
					    <input type="text" name="ordre" id="form-field-ordre" placeholder="ordre" class="col-xs-2 col-sm-2" />
					    					<span class="help-inline col-xs-3 ">
						<span class="middle">ordre de la catégorie</span>
					</span>
					</div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-field-sc">sous-catégorie</label>
                    <div class="col-xs-1">
					    <select class="form-control" id="form-field-sc" name="scat">
					            <option value="non" selected="selected">non</option>
					            <option value="oui">oui</option>
                        </select>
				    </div>
				</div>
				<div class="form-group">
				    <label class="col-sm-3 control-label no-padding-right" for="form-field-img">image</label>
                    <div class="col-sm-9">
					    <input type="file" name="image" id="form-field-img" placeholder="image" class="col-xs-10 col-sm-5" />
					</div>
                </div>
				<div class="form-group">
				    <label class="col-sm-3 control-label no-padding-right" for="form-field-cp">categorie parent</label>
				    <div class="col-sm-9">
					    <input type="text" name="catparent" id="form-field-cp" placeholder="catparent" class="col-xs-2 col-sm-2" />
					    					<span class="help-inline col-xs-3 ">
						<span class="middle">catégorie parent</span>
					</span>
					</div>
                </div>
                <div class="form-group">
				    <label class="col-sm-3 control-label no-padding-right" for="form-field-ordresc">ordre-sous-categorie</label>
				    <div class="col-sm-9">
					    <input type="text" name="ordre sc" id="form-field-ordresc" placeholder="ordre-sous-categorie" class="col-xs-2 col-sm-2" />
					    					<span class="help-inline col-xs-3 ">
						<span class="middle">ordre sous categorie</span>
					</span>
					</div>
                </div>
                <div class="clearfix form-actions">
					<div class="col-md-offset-3 col-md-9">
						<button class="btn btn-info" type="submit">
								<i class="ace-icon fa fa-check bigger-110"></i>
								Submit
						</button>

						&nbsp; &nbsp; &nbsp;
						<button class="btn" type="reset">
								<i class="ace-icon fa fa-undo bigger-110"></i>
								Reset
						</button>
					</div>
				</div>

            </form>
        </div>
    </div>';
    }
} elseif ($action == "modifier") {
    if ($do == "modifier") {
        $id = $_POST["id"];
        $erreur = "";
        $name = $_POST["nom"];
        if (!$name) {
            $erreur .= "manque nom...";
        }
        $visible = $_POST["visible"];
        if ($visible != "oui" && $visible != "non") {
            $erreur .= "visible doit etre oui ou non...";
        }
        $sort_index = $_POST["ordre"];
        if (!$sort_index) {
            $erreur .= "manque l'ordre....";
        }
        $subcast = $_POST["scat"];
        if ($subcast != "oui" && $subcast != "non") {
            $erreur .= "sous-catégorie doit etre oui ou non...";
        }
        $image = $_POST["image"];
        $imageName = $_FILES["name"];
        $prent_cast = $_POST["catparent"];
        $sub_sort = $_POST["ordre sc"];
        if ($erreur == "") {
            SQL_Query_exec("UPDATE categories SET `name`='$name', `visible`='$visible', `sort_index`='$sort_index', `subcast`='$subcast', `prent_cast`='$prent_cast', `sub_sort`='$sub_sort' WHERE id=$id");
            echo "Vous avez bien modifié:<br />";
            echo "Nom: " . $name . " Visible: " . $visible . " Ordre: " . $sort_index . " Sous catégorie: " . $subcast . " Image: " . $image . " Catégorie parente: " . $prent_cast . " Ordre sous-Catégorie: " . $sub_sort;
            echo "<br /><br />
            <a href='categoriead.php'>Retour aux categories</a>";
        } else {
            echo $erreur;
        }
    } else {
        $id = $_GET["id"];
        $catsquery = SQL_Query_exec("SELECT * FROM categories WHERE id=$id");
        $catsrow = mysqli_fetch_assoc($catsquery);
        echo '<div class="page-header">
	    <h1>
			Catégories
			<small>
			    <i class="ace-icon fa fa-angle-double-right"></i>
				modifier catégorie
			</small>
		</h1>
	</div><!-- /.page-header -->
    <div class="row">
        <div class="col-xs-12">
            <form class="form-horizontal" role="form" method="post" action="categoriead.php">
                <input type="hidden" name="action" value="modifier">
                <input type="hidden" name="do" value="modifier">
                <input type="hidden" name="id" value="' . $id . '">
                <div class="form-group">
				    <label class="col-sm-3 control-label no-padding-right" for="form-field-nom">nom</label>
                    <div class="col-sm-9">
					    <input type="text" name="nom" value="' . $catsrow["name"] . '" id="form-field-nom"  class="col-xs-10 col-sm-5" />
					</div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-field-visible">visible</label>
                    <div class="col-xs-1">
					    <select class="form-control" id="form-field-visible" name="visible" value="' . $catsrow["visible"] . '">
                            <option value="oui">oui</option>
                            <option value="non">non</option>
                        </select>
				    </div>
				</div>
				<div class="form-group">
				    <label class="col-sm-3 control-label no-padding-right" for="form-field-ordre">ordre</label>
				    <div class="col-sm-9">
					    <input type="text" name="ordre" value="' . $catsrow["sort_index"] . '" id="form-field-ordre"  class="col-xs-2 col-sm-2" />
					    					<span class="help-inline col-xs-3 ">
						<span class="middle">ordre de la catégorie</span>
					</span>
					</div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-field-sc">sous-catégorie</label>
                    <div class="col-xs-1">
					    <select class="form-control" id="form-field-sc" value="' . $catsrow["subcast"] . '" name="scat">
					            <option value="non" selected="selected">non</option>
					            <option value="oui">oui</option>
                        </select>
				    </div>
				</div>
				<div class="form-group">
				    <label class="col-sm-3 control-label no-padding-right" for="form-field-img">image</label>
                    <div class="col-sm-9">
					    <input type="file" name="image" id="form-field-img" placeholder="image" class="col-xs-10 col-sm-5" />
					</div>
                </div>
				<div class="form-group">
				    <label class="col-sm-3 control-label no-padding-right" for="form-field-cp">categorie parent</label>
				    <div class="col-sm-9">
					    <input type="text" name="catparent" value="' . $catsrow["prent_cast"] . '" id="form-field-cp"  class="col-xs-2 col-sm-2" />
					    					<span class="help-inline col-xs-3 ">
						<span class="middle">catégorie parent</span>
					</span>
					</div>
                </div>
                <div class="form-group">
				    <label class="col-sm-3 control-label no-padding-right" for="form-field-ordresc">ordre-sous-categorie</label>
				    <div class="col-sm-9">
					    <input type="text" name="ordre sc" value="' . $catsrow["sub_sort"] . '" id="form-field-ordresc"  class="col-xs-2 col-sm-2" />
					    					<span class="help-inline col-xs-3 ">
						<span class="middle">ordre sous categorie</span>
					</span>
					</div>
                </div>
                <div class="clearfix form-actions">
					<div class="col-md-offset-3 col-md-9">
						<button class="btn btn-info" type="submit">
								<i class="ace-icon fa fa-check bigger-110"></i>
								Submit
						</button>

						&nbsp; &nbsp; &nbsp;
						<button class="btn" type="reset">
								<i class="ace-icon fa fa-undo bigger-110"></i>
								Reset
						</button>
					</div>
				</div>

            </form>
        </div>
    </div>';
    }
} elseif ($action == "supprimer") {
    $id = $_GET["id"];
    if (!is_numeric($id)) {
        die('je ne te conseil pas');
    }
    if ($do == "supprimer") {
        if ($id != "") {
            if (is_numeric($id)) {
                SQL_Query_exec("DELETE FROM categories WHERE id=$id");
                echo "votre produit a bien etait supprimé";
            } else {
                echo 'tu tente quoi la ??';
            }

        } else {
            echo 'Mauvais identifiant de categorie';
        }
    } else {
        echo 'Vous allez supprimer la categorie ' . $id;
        echo '<br /><br />';
        echo '<a class="red" href="categoriead.php?action=supprimer&do=supprimer&id=' . $id . '">Cliquez ici</a> si vous voulez effectuer cette action.';
    }
} else {
    ?>

    <div class="row">
        <div class="col-xs-12">
            <h3 class="header smaller lighter blue">Catégories
                <small>
                    <i class="ace-icon fa fa-angle-double-right"></i>
                    <a href="categoriead.php?action=ajouter">Ajouter une catégorie</a>
                </small>
            </h3>

            <div class="clearfix">
                <div class="pull-right tableTools-container"></div>
            </div>

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
                        <th>visible</th>
                        <th>ordre</th>
                        <th>sous-categorie</th>
                        <th>image</th>
                        <th>categorie-parent</th>
                        <th>ordre sc</th>
                        <th>options</th>
                    </tr>
                    </thead>

                    <tbody><?php
                    $catsquery = SQL_Query_exec("SELECT * FROM categories ORDER BY sort_index");
                    while ($catsrow = mysqli_fetch_assoc($catsquery)) {
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
                                <td>' . $catsrow["visible"] . '</td>
                                <td>' . $catsrow["sort_index"] . '</td>
                                <td>' . $catsrow["subcast"] . '</td>
                                <td >' . $catsrow["image"] . '</td>';
                        if (!$catsrow["prent_cast"]) {
                            echo '<td> --- </td>';
                        } else {
                            echo '<td>' . $catsrow["prent_cast"] . '</td>';
                        }

                        if (!$catsrow["sub_sort"]) {
                            echo '<td> --- </td>';
                        } else {
                            echo '<td>' . $catsrow["sub_sort"] . '</td>';
                        }

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
                    ?></tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="modal-table" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header no-padding">
                    <div class="table-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            <span class="white">&times;</span>
                        </button>
                        Results for "Latest Registered Domains
                    </div>
                </div>

                <div class="modal-body no-padding">
                    <table class="table table-striped table-bordered table-hover no-margin-bottom no-border-top">
                        <thead>
                        <tr>
                            <th>Domain</th>
                            <th>Price</th>
                            <th>Clicks</th>

                            <th>
                                <i class="ace-icon fa fa-clock-o bigger-110"></i>
                                Update
                            </th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr>
                            <td>
                                <a href="#">ace.com</a>
                            </td>
                            <td>$45</td>
                            <td>3,330</td>
                            <td>Feb 12</td>
                        </tr>

                        <tr>
                            <td>
                                <a href="#">base.com</a>
                            </td>
                            <td>$35</td>
                            <td>2,595</td>
                            <td>Feb 18</td>
                        </tr>

                        <tr>
                            <td>
                                <a href="#">max.com</a>
                            </td>
                            <td>$60</td>
                            <td>4,400</td>
                            <td>Mar 11</td>
                        </tr>

                        <tr>
                            <td>
                                <a href="#">best.com</a>
                            </td>
                            <td>$75</td>
                            <td>6,500</td>
                            <td>Apr 03</td>
                        </tr>

                        <tr>
                            <td>
                                <a href="#">pro.com</a>
                            </td>
                            <td>$55</td>
                            <td>4,250</td>
                            <td>Jan 21</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer no-margin-top">
                    <button class="btn btn-sm btn-danger pull-left" data-dismiss="modal">
                        <i class="ace-icon fa fa-times"></i>
                        Close
                    </button>

                    <ul class="pagination pull-right no-margin">
                        <li class="prev disabled">
                            <a href="#">
                                <i class="ace-icon fa fa-angle-double-left"></i>
                            </a>
                        </li>

                        <li class="active">
                            <a href="#">1</a>
                        </li>

                        <li>
                            <a href="#">2</a>
                        </li>

                        <li>
                            <a href="#">3</a>
                        </li>

                        <li class="next">
                            <a href="#">
                                <i class="ace-icon fa fa-angle-double-right"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <!-- PAGE CONTENT ENDS -->
    </div><!-- /.col -->
    </div><!-- /.row -->
    </div><!-- /.page-content -->
    </div>
    </div><!-- /.main-content -->
    <?php
}
require_once("template/footerad.php");
?>