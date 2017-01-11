<?PHP
require_once("template/headerad.php");
require_once("template/asidead.php");
require_once("template/bridcrumsad.php");

$today = date("Y-m-d H:i:s");
$value = $id;
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
        $name = $_POST["name"];
        if (!$name) {
            $erreur .= "manque nom...";
        }
        $description = $_POST["description"];
        if (!$description) {
            $erreur .= "manque la description";
        }
        $image = $_POST["image"];
        $imageName = $_FILES["name"];
        if (!$imageName) {
            $imageName = "Pas d\'images";
        }
        $image2 = $_POST["image2"];
        $imageName2 = $_FILES["name"];
        if (!$imageName2) {
            $imageName2 = "Pas d\'images";
        }
        $prix = $_POST["prix"];
        if (!$prix) {
            $erreur .= "manque le prix ....";
        }
        $categorie = $_POST["categorie"];
        if (!$categorie) {
            $erreur .= "manque la catégorie";
        }
        $added = $today;
        if (!$added) {
            $erreur .= "manque la date et l'heure d'ajout";
        }
        $newend = $today;
        if (!$newend) {
            $erreur .= "manque la date ";
        }
        $type = $_POST["type"];
        if (!$type) {
            $erreur .= "manque le type";
        }
        $visibles = $_POST["visibles"];
        if ($visibles != "oui" && $visibles != "non") {
            $erreur .= "visible doit etre oui ou non...";
        }
        $new = $_POST["new"];
        if ($new != "oui" && $new != "non"){
            $erreur .= "nouveautés doit etre oui ou non";
        }
        $owner = $_POST["owner"];
        if (!$owner) {
            $owner = 1;
        }
        if ($erreur == "") {
            SQL_Query_exec("INSERT INTO produits (`name`, `description`, `image`, `image2`, `prix`, `categorie`,`added`,`newend`,`type`,`visibles`,`new`,`owner`) VALUES ('$name', '$description', '$imageName', '$imageName2', '$prix', '$categorie','$added','$newend','$type','$visibles','$new','$owner')");
            echo "Vous avez bien ajouté:<br />";
            echo "Nom: " . $name . " description: " . $description . "image 1: " . $image . " image2: " . $image2 . " prix: " . $prix . " Catégorie: " . $categorie . " ajouter par: " . $added . "type :" . $type . "visible" . $visibles ."new" . $new. "ajouter par" . $owner;
            echo "<br /><br />
            <a href='produitad.php'>Retour aux Produits</a>";
        } else {
            echo $erreur;
        }
    } else {
        echo '<div class="page-header">
	    <h1>
			Produits
			<small>
			    <i class="ace-icon fa fa-angle-double-right"></i>
				ajout Produits
			</small>
		</h1>
	</div><!-- /.page-header -->
    <div class="row">
        <div class="col-xs-12">
            <form class="form-horizontal" role="form" method="post" action="produitad.php">
                <input type="hidden" name="action" value="ajouter">
                <input type="hidden" name="do" value="ajouter">
                <div class="form-group">
				    <label class="col-sm-3 control-label no-padding-right" for="form-field-nom">nom</label>
                    <div class="col-sm-9">
					    <input type="text" name="name" id="form-field-nom" placeholder="nom" class="col-xs-10 col-sm-5" />
					</div>
                </div>
                <div class="form-group" >
                    <label class="col-sm-3 control-label no-padding-right" for="form-field-description" >description</label>
                    <div class="col-sm-9" >
                       <textarea class="col-xs-10 col-sm-5" id="form-field-description" type="text" name="description"></textarea>
                    </div>
                </div>
				<div class="form-group">
				    <label class="col-sm-3 control-label no-padding-right" for="form-field-img">image</label>
                    <div class="col-sm-9">
					    <input type="file" name="image" id="form-field-img" placeholder="image" class="col-xs-10 col-sm-5" />
					</div>
                </div>
				<div class="form-group">
				    <label class="col-sm-3 control-label no-padding-right" for="form-field-img">image2</label>
                    <div class="col-sm-9">
					    <input type="file" name="image2" id="form-field-img" placeholder="image2" class="col-xs-10 col-sm-5" />
					</div>
                </div>
                <div class="form-group">
				    <label class="col-sm-3 control-label no-padding-right" for="form-field-prix">Prix</label>
                    <div class="col-sm-9">
					    <input type="text" name="prix" id="form-field-prix" placeholder="prix" class="col-xs-10 col-sm-5" />
					</div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-field-catégorie">categorie</label>
                    <div class="col-xs-1">
					    <select class="form-control" id="form-field-categorie" name="categorie">';
        $catsquery = SQL_Query_exec("SELECT * FROM categories ORDER BY sort_index");
        while ($castsrow = mysqli_fetch_assoc($catsquery)) {
            echo '<option value="' . $castsrow["id"] . '">' . $castsrow["name"] . '</option>';
        };

        echo '</select>
				    </div>
				</div>
                    <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-field-visible">visible</label>
                    <div class="col-xs-1">
					    <select class="form-control" id="form-field-visible" name="visibles">
                            <option value="oui">oui</option>
                            <option value="non">non</option>
                        </select>
				    </div>
				</div>
				<div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-field-visible">nouveautés</label>
                    <div class="col-xs-1">
					    <select class="form-control" id="form-field-visible" name="new">
                            <option value="oui">oui</option>
                            <option value="non">non</option>
                        </select>
				    </div>
				</div>
				<div class="form-group">
				    <label class="col-sm-3 control-label no-padding-right" for="form-field-type">type</label>
                    <div class="col-sm-9">
					    <input type="text" name="type" id="form-field-type" placeholder="type" class="col-xs-10 col-sm-5" />
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
        $name = $_POST["name"];
        if (!$name) {
            $erreur .= "manque nom...";
        }
        $description = $_POST["description"];
        if (!$description) {
            $erreur .= "manque la description";
        }
        $image = $_POST["image"];
        $imageName = $_FILES["name"];
        if (!$imageName) {
            $imageName = "Pas d\'images";
        }
        $image2 = $_POST["image2"];
        $imageName2 = $_FILES["name"];
        if (!$imageName2) {
            $imageName2 = "Pas d\'images";
        }
        $prix = $_POST["prix"];
        if (!$prix) {
            $erreur .= "manque le prix ....";
        }
        $categorie = $_POST["categorie"];
        if (!$categorie) {
            $erreur .= "manque la catégorie";
        }
        $added = $_POST["added"];
        if (!$added) {
            $erreur .= "manque la date et l'heure d'ajout";
        }
        $newend = $_POST["newend"];
        if (!$newend) {
            $erreur .= "manque la date";
        }
        $type = $_POST["type"];
        if (!$type) {
            $erreur .= "manque le type";
        }
        $visibles = $_POST["visibles"];
        if ($visibles != "oui" && $visibles != "non") {
            $erreur .= "visible doit etre oui ou non...";
        }
        $new = $_POST["new"];
        if ($new != "oui" && $new != "non"){
            $erreur .= "nouveautés doit etre oui ou non";
        }
        $owner = $_POST["owner"];
        if (!$owner) {
            $owner = 1;
        }
        if ($erreur == "") {
            SQL_Query_exec("UPDATE produits SET `name`='$name',`description`='$description',`image`='$image',`image2`='$image2',`prix`='$prix',`categorie`='$categorie',`added`='$added',`newend`='$newend',`type`='$type',`visibles`='$visibles',`new`='$new',`owner`='$owner' WHERE id=$id");
            echo "Vous avez bien ajouté:<br />";
            echo "Nom: " . $name . " description: " . $description . "image 1: " . $image . " image2: " . $image2 . " prix: " . $prix . " Catégorie: " . $categorie . " ajouter le: " . $added .  " fin le: " . $newend . "type :" . $type . "visible" . $visibles . "new" . $new. "ajouter par" . $owner;
            echo "<br /><br />
            <a href='produitad.php'>Retour aux Produits</a>";
        } else {
            echo $erreur;
        }
    } else {
        $id = $_GET["id"];
        $catsquery = SQL_Query_exec("SELECT * FROM produits WHERE id=$id");
        $catsrow = mysqli_fetch_assoc($catsquery);
        echo '<div class="page-header">
	    <h1>
			Produits
			<small>
			    <i class="ace-icon fa fa-angle-double-right"></i>
				modifier Produits
			</small>
		</h1>
	</div><!-- /.page-header -->
    <div class="row">
        <div class="col-xs-12">
            <form class="form-horizontal" role="form" method="post" action="produitad.php">
                <input type="hidden" name="action" value="modifier">
                <input type="hidden" name="do" value="modifier">
                <input type="hidden" name="id" value="' . $id . '">
                <div class="form-group">
				    <label class="col-sm-3 control-label no-padding-right" for="form-field-nom">nom</label>
                    <div class="col-sm-9">
					    <input type="text" name="name" id="form-field-nom" value="' . $catsrow["name"] . '" class="col-xs-10 col-sm-5" />
					</div>
                </div>
                <div class="form-group" >
                    <label class="col-sm-3 control-label no-padding-right" for="form-field-description" >description</label>
                    <div class="col-sm-9" >
                       <textarea class="col-xs-10 col-sm-5" id="form-field-description" type="text" name="description" value="' . $catsrow["description"] . '"></textarea>
                    </div>
                </div>
				<div class="form-group">
				    <label class="col-sm-3 control-label no-padding-right" for="form-field-img">image</label>
                    <div class="col-sm-9">
					    <input type="file" name="image" id="form-field-img" placeholder="image" class="col-xs-10 col-sm-5" />
					</div>
                </div>
				<div class="form-group">
				    <label class="col-sm-3 control-label no-padding-right" for="form-field-img">image2</label>
                    <div class="col-sm-9">
					    <input type="file" name="image2" id="form-field-img" placeholder="image2" class="col-xs-10 col-sm-5" />
					</div>
                </div>
                <div class="form-group">
				    <label class="col-sm-3 control-label no-padding-right" for="form-field-prix">Prix</label>
                    <div class="col-sm-9">
					    <input type="text" name="prix" id="form-field-prix" value="' . $catsrow["prix"] . '" class="col-xs-10 col-sm-5" />
					</div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-field-catégorie">categorie</label>
                    <div class="col-xs-1">
					    <select class="form-control" id="form-field-categorie" name="categorie" value="' . $catsrow["categorie"] . '>';
        $catsquery = SQL_Query_exec("SELECT * FROM categories ORDER BY sort_index");
        while ($castsrow = mysqli_fetch_assoc($catsquery)) {
            echo '<option value="' . $castsrow["id"] . '">' . $castsrow["name"] . '</option>';
        };

        echo '</select>
				    </div>
				</div>
                    <div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-field-visible">visible</label>
                    <div class="col-xs-1">
					    <select class="form-control" id="form-field-visible" name="visibles" value="' . $catsrow["visibles"] . '">
                            <option value="oui">oui</option>
                            <option value="non">non</option>
                        </select>
				    </div>
				</div>
				<div class="form-group">
                    <label class="col-sm-3 control-label no-padding-right" for="form-field-new">nouveautés</label>
                    <div class="col-xs-1">
					    <select class="form-control" id="form-field-new" name="new" value="' . $catsrow["new"] .'">
                            <option value="oui">oui</option>
                            <option value="non">non</option>
                        </select>
				    </div>
				</div>
				<div class="form-group">
				    <label class="col-sm-3 control-label no-padding-right" for="form-field-type">type</label>
                    <div class="col-sm-9">
					    <input type="text" name="type" id="form-field-type" value="' . $catsrow["type"] . '" class="col-xs-10 col-sm-5" />
					</div>
                </div>
				<div class="form-group">
				    <label class="col-sm-3 control-label no-padding-right" for="form-field-added">date et heure d ajout</label>
                    <div class="col-sm-9">
					    <input type="text" name="added" id="form-field-added" value="' . $catsrow["added"] . '" class="col-xs-10 col-sm-5" />
					</div>
                </div>
                <div class="form-group">
				    <label class="col-sm-3 control-label no-padding-right" for="form-field-newend">date et heure de fin de nouveautés</label>
                    <div class="col-sm-9">
					    <input type="text" name="newend" id="form-field-newend" value="' . $catsrow["newend"] . '" class="col-xs-10 col-sm-5" />
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
                SQL_Query_exec("DELETE FROM produits WHERE id=$id");
                echo "votre produit a bien etait supprimé";
            } else {
                echo 'tu tente quoi la ??';
            }

        } else {
            echo 'Mauvais identifiant de produit';
        }
    } else {
        echo 'Vous allez supprimer le produit ' . $id;
        echo '<br /><br />';
        echo '<a class="red" href="produitad.php?action=supprimer&do=supprimer&id=' . $id . '">Cliquez ici</a> si vous voulez effectuer cette action.';
    }
} else {
    ?>
    <div class="row">
    <div class="col-xs-12">
    <h3 class="header smaller lighter blue">Produits
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            <a href="produitad.php?action=ajouter">Ajouter un Produit</a>
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
        <th>description</th>
        <th>image 1</th>
        <th>image 2</th>
        <th>prix</th>
        <th>categorie</th>
        <th>date d'ajout</th>
        <th>type</th>
        <th>comments</th>
        <th>nombre de vue</th>
        <th>visible</th>
        <th>mis en ligne par :</th>
        <th>nombre de vote</th>
        <th>vote</th>
        <th></th>
    </tr>
    </thead>

    <tbody>
    <?php
    $catsquery = SQL_Query_exec("SELECT * FROM produits ORDER BY categorie");
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
                                <td>' . $catsrow["description"] . '</td>
                                <td>' . $catsrow["img"] . '</td>
                                <td>' . $catsrow["img2"] . '</td>
                                <td >' . $catsrow["prix"] . '</td>
                                <td >' . $catsrow["categorie"] . '</td>
                                <td >' . $catsrow["added"] . '</td>
                                <td >' . $catsrow["type"] . '</td>
                                <td >' . $catsrow["comments"] . '</td>
                                <td >' . $catsrow["views"] . '</td>
                                <td >' . $catsrow["visible"] . '</td>
                                <td >' . $catsrow["owner"] . '</td>
                                <td >' . $catsrow["numrating"] . '</td>
                                <td >' . $catsrow["ratingnum"] . '</td>';
        echo '<td>
                                    <div class="hidden-sm hidden-xs action-buttons">
                                        <a class="green" href="produitad.php?action=modifier&id=' . $catsrow["id"] . '">
                                            <i class="ace-icon fa fa-pencil bigger-130"></i>
                                         </a>

                                         <a class="red" href="produitad.php?action=supprimer&id=' . $catsrow["id"] . '">
                                            <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                        </a>
                                    </div>

                                </td>
                            </tr>';
    }
    echo '




                                </div>
                            </div>
                        </td>
                    </tr>
                          
                          ';

} ?>

    </tbody>
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