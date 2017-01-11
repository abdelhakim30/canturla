<?PHP
require_once("template/headerad.php");
require_once("template/asidead.php");
require_once("template/bridcrumsad.php");
?>
<div class="row">
    <div class="col-xs-12">
        <form class="form-horizontal" role="form" method="post" action="profilsad.php">
            <input type="hidden" name="action" value="modifier">
            <input type="hidden" name="do" value="modifier">
            <input type="hidden" name="id" value="' . $id . '">
            <div class="tabbable">
                <ul class="nav nav-tabs padding-16">
                    <li class="active">
                        <a data-toggle="tab" href="#edit-basic">
                            <i class="green ace-icon fa fa-pencil-square-o bigger-125"></i>
                            General
                        </a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#edit-password">
                            <i class="blue ace-icon fa fa-key bigger-125"></i>
                            Mot-de-Passe
                        </a>
                    </li>
                </ul>

                <div class="tab-content profile-edit-tab-content">
                    <div id="edit-basic" class="tab-pane in active">
                        <h4 class="header blue bolder smaller">General</h4>

                        <div class="row">
                            <div class="col-xs-12 col-sm-4">
                                <input type="file"/>
                            </div>

                            <div class="vspace-12-sm"></div>

                            <div class="col-xs-12 col-sm-8">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-username">Pseudo</label>

                                    <div class="col-sm-8">
                                        <input class="col-xs-12 col-sm-10" type="text" ' . $catsrow["username"] . '
                                        id="form-field-username" value="' . $username . '" />
                                    </div>
                                </div>

                                <div class="space-4"></div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label no-padding-right" for="form-field-first">Nom/Prénom</label>

                                    <div class="col-sm-8">
                                        <input class="input-small" type="text" ' . $catsrow["prenom"] . '
                                        id="form-field-first" value="' . $prenom . '" />
                                        <input class="input-small" type="text" ' . $catsrow["nom"] . '
                                        id="form-field-last" value="' . $nom . '" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space"></div>
                        <h4 class="header blue bolder smaller">Contact</h4>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="form-field-email">Email</label>

                            <div class="col-sm-9">
																	<span class="input-icon input-icon-right">
																		<input type="email" id="form-field-email" ' . $catsrow["email"] . ' value="' . $email . '" />
																		<i class="ace-icon fa fa-envelope"></i>
																	</span>
                            </div>
                        </div>

                        <div class="space-4"></div>

                        <div class="space-4"></div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right" for="form-field-phone">Phone</label>

                            <div class="col-sm-9">
																	<span class="input-icon input-icon-right">
																		<input class="input-medium input-mask-phone" ' . $catsrow["tel"] . ' value="' . $tel . '" id="form-field-phone" />
																		<i class="ace-icon fa fa-phone fa-flip-horizontal"></i>
																	</span>
                            </div>
                        </div>


                        <div id="edit-password" class="tab-pane">
                            <div class="space-10"></div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="form-field-pass1">Nouveau
                                    mot-de-passe</label>

                                <div class="col-sm-9">
                                    <input type="password" id="form-field-pass1" ' . $catsrow["password"] . ' value="' .
                                    $password . '"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix form-actions">
                        <div class="col-md-offset-3 col-md-9">
                            <button class="btn btn-info" type="button">
                                <i class="ace-icon fa fa-check bigger-110"></i>
                                savgarder
                            </button>

                            &nbsp; &nbsp;
                            <button class="btn" type="reset">
                                <i class="ace-icon fa fa-undo bigger-110"></i>
                                Reset
                            </button>
                        </div>
                    </div>
        </form>
    </div><!-- /.span -->
</div><!-- /.user-profile -->
</div>';

<?php
require_once("template/footerad.php");
?>
