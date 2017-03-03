// ======================================================================================================
// COMMUN
// ======================================================================================================

/**
 * Variable de selection d'entité
 * @var {object} sel
 */
var sel;

/**
 * Nom de l'entité affiché sur la page
 * @var {string} url
 */
var url;

/**
 * Enumération des types d'actions possibles sur le serveur
 * @type {{ENREGISTREMENT: string, UPLOAD: string, MODIFICATION: string, SUPPRESSION: string, UTILISATEUR: string, DOCUMENT: string, PROJET: string, GROUPE: string}}
 */
const types = {
    ENREGISTREMENT: "enregistré", // demande d'enregistrement
    UPLOAD: "uploadé", // demande d'enregistrement de fichier
    DOWNLOAD: "téléchargé", // demande de téléchargement de fichier
    MODIFICATION: "modifié", // demande de modification
    SUPPRESSION: "supprimé", // demande de suppression
    UTILISATEUR: "utilisateur", // demande d'optention des utilisateurs d'une entité || page users_admin
    DOCUMENT: "document", // demande d'optention des documents d'une entité || page docs_admin
    PROJET: "projet", // demande d'optention des projets d'une entité || page projects_admin
    GROUPE: "groupe", // demande d'optention des groupes d'une entité || page groups_admin
    DOCUMENT_PROJET: "document_projet", // demande d'optention des documents et des projets
    HOME_USER: "home_user", // page bureau utilisateur
    RECENT_USER: "recent_user", // page elements recents utilisateur
    SHARED_USER: "shared_user", // page elements partagés utilisateur
    ACCOUNT_USER: "account_user", // page account_user
    GET: "get" // demande d'informations sur une entité
};

/**
 * Fonction permettant d'activer le back to top
 */
function backToTop() {
    var $btp = $('#back-to-top');
    $(window).scroll(function () {
        if ($(this).scrollTop() > 50) {
            $btp.fadeIn();
        } else {
            $btp.fadeOut();
        }
    });
    $btp.click(function () {
        $btp.tooltip('hide');
        $('body,html').animate({
            scrollTop: 0
        }, 800);
        return false;
    });
    $btp.tooltip('show');
}

/**
 * Fonction pour activer les elements jquery
 */
function activeDefault() {
    $('[data-toggle="tooltip"]').tooltip(); // permet de jouer les tooltips
    $('[data-toggle="popover"]').popover(); // permet de jouer les popover
}


/**
 * Fonction d'affichage de popups de notification.
 * @param texte, texte à afficher
 * @param icon, icone bootstrap à afficher
 * @param type, type de notification à afficher [success, info, primary, warning, danger]
 */
function showNotify(texte, icon, type) {
    if (texte.length > 1) {
        $.notify({
            icon: icon,
            message: texte
        }, {
            type: type,
            placement: {
                from: "top",
                align: "left"
            },
            animate: {
                enter: 'animated bounceInDown',
                exit: 'animated bounceOutUp'
            }
        });
    }
}

/**
 * Fonction d'affichage de pop-up de contact
 */
function showNotifyContact() {
    var icon = '../img/contact_img.png';
    if (url == types.HOME_USER || url == types.SHARED_USER ||
        url == types.RECENT_USER || url == types.ACCOUNT_USER) {
        icon = '../../../img/contact_img.png';
    }
    $.notify({
        icon: icon,
        url: 'http://localhost/Gedi/Symfony/web/app_dev.php/contact'
    }, {
        type: 'null',
        icon_type: 'image',
        allow_dismiss: false,
        placement: {
            from: "top",
            align: "right"
        },
        offset: {
            x: -270,
            y: ($(window).height() / 2)
        },
        delay: 0,
        url_target: '_self',
        animate: {
            enter: 'animated bounceInRight'
        }
    });
}


// ======================================================================================================
// ADMIN
// ======================================================================================================

/**
 * Tableau des logins
 * @var {array} logins
 */
var logins;

/**
 * Fonction pour mettre à jour le nombre d'utilisateurs inactifs
 * et le nombre d'entités total
 */
function updateNbEntity() {
    var $table = $('#table_admin');

    // ne s'execute que si on est sur la page users_admin
    if (url == types.UTILISATEUR) {
        var nbInactifs = 0;
        // récupère les utilisateurs actifs
        var inactifs = $.map($table.bootstrapTable('getData'), function (rows) {
            return rows.actif;
        });
        for (var i = 0; i < inactifs.length; i++) {
            (inactifs[i] != "1") ? nbInactifs++ : nbInactifs;
        }

        $('#toggle-new-users').bootstrapToggle((nbInactifs > 0) ? 'enable' : 'disable');
        $('#span-nb-inactifs').html(nbInactifs); // inscrit le nombre d'utilisateurs inactifs dans le span
    }

    // inscrit le nombre d'entités total dans le span
    $('#span-nb-entity').html($table.bootstrapTable('getData').length);
}

/**
 * Fonction pour ajouter un utilisateurs en temps que propriétaire.
 * Ajoute l'id de l'entité enfant au formulaire de création
 * @param js_arg, , id de l'entité enfant
 */
function addUser(js_arg) {
    if (url == types.DOCUMENT) {
        $('#data_idProjetFkDocument').removeAttr('value');
        $('#data_idUtilisateurFk' + (url.charAt(0).toUpperCase() + url.slice(1))).val(js_arg);
    } else {
        $('#gedi_basebundle_' + url + '_idUtilisateurFk' + (url.charAt(0).toUpperCase() + url.slice(1))).val(js_arg);
    }
}

/**
 * Fonction pour ajouter un projet en temps que parent.
 * Ajoute l'id de l'entité enfant au formulaire de création
 * @param js_arg, id de l'entité enfant
 */
function addProject(js_arg) {
    $('#gedi_basebundle_projet_parent').val(js_arg);
    $('#data_idProjetFkDocument').val(js_arg);
    $('.list-activable-item').removeClass('active');
    $('#list-activable-item-project-' + js_arg).addClass('active');
}

/**
 * Fonction pour afficher ou cacher le span du nombre d'entités sélectionnés
 * des boutons de controle.
 * @param nbSel, nombre d'éléments selectionnés
 */
function hideOuShow(nbSel) {
    if (nbSel < 1) {
        $('.spanNbSel').hide();
        $('.bouton-desactive').prop('disabled', true);
    } else {
        $('.spanNbSel').show();
        $('.bouton-desactive').prop('disabled', false);
    }
}

/**
 * Fonction d'édition d'entité. La fonction renseigne les champs du formulaire.
 * @param js_object_arg, infos de l'entité à mettre dans le formulaire
 */
function edit(js_object_arg) {

    // récupération de l'entité au format json et transformation
    // en tableau javascript
    var js_object = JSON.parse(js_object_arg);

    // remplissages de champs des formulaires
    for (var key in js_object) {
        if (js_object.hasOwnProperty(key)) {
            if (key == 'actif' && js_object[key] == '1') {
                $('#gedi_basebundle_' + url + '_' + key).bootstrapToggle('on');
            } else if (key == 'password') {
                $('#gedi_basebundle_' + url + '_' + key + '_first').val(js_object[key]);
                $('#gedi_basebundle_' + url + '_' + key + '_second').val(js_object[key]);
            } else {
                if (url == types.DOCUMENT) {
                    $('#data_' + key).val(js_object[key]);
                } else {
                    $('#gedi_basebundle_' + url + '_' + key).val(js_object[key]);
                }
            }
        }
    }

    // modification du popup ajout / edition
    $('#popup-admin-add-titre').html('Modifier un ' + url);
    var $bsae = $('.bouton-submit-admin-entity');
    $bsae.val('Appliquer');
    $bsae.prop('disabled', false);
    $('#data_nom').prop('disabled', false);
    $('#data_typeDoc').prop('disabled', false);
    $('.assign-user').hide(); // masque le panel d'assignation d'utilisateur
    $('#data_fichier').prop('disabled', true); // desactive le bouton upload
}

/**
 * Fonction de vérification de formulaire
 */
function validForm() {
    if ($('.bouton-submit-admin-entity').val() == "Créer") {
        if (url == types.UTILISATEUR) {
            validFormUser();
        } else if (url == types.GROUPE) {
            validFormGP();
        } else if (url == types.PROJET) {
            validFormGP();
        } else if (url == types.DOCUMENT) {
            validFormDoc();
        }
    }
}

/**
 * Fonction de vérification de formulaire utilisateur
 */
function validFormUser() {
    var $bt = $('.bouton-submit-admin-entity');
    var $pass1 = $('#gedi_basebundle_utilisateur_password_first');
    var $pass2 = $('#gedi_basebundle_utilisateur_password_second');
    var $username = $('#gedi_basebundle_utilisateur_username');
    var $nom = $('#gedi_basebundle_utilisateur_nom');
    var $prenom = $('#gedi_basebundle_utilisateur_prenom');

    // test si le mot de passe concorde avec la confirmation
    if ($pass1.val() != $pass2.val() && ($pass1.val() != "" && $pass2.val() != "")) {
        $pass2.css('background-color', 'var(--color-error)');
        $bt.prop('disabled', true);
    } else if ($pass1.val() == $pass2.val() && ($pass1.val() != "" && $pass2.val() != "")) {
        $pass2.css('background-color', 'var(--color-success)');
        $bt.prop('disabled', false);
    } else {
        $pass2.css('background-color', 'var(--color-default)');
        $bt.prop('disabled', true);
    }

    // test si le login n'est pas déjà utilisé
    if (logins != null && logins.includes($username.val())) {
        $username.css('background-color', 'var(--color-error)');
        $bt.prop('disabled', true);
    } else {
        $username.css('background-color', 'var(--color-default)');
        $bt.prop('disabled', false);
    }

    // test si tous les champs sont remplis
    if ($username.val() == "" || $pass1.val() == "" || $pass2.val() == "" || $nom.val() == "" || $prenom.val() == "") {
        $bt.prop('disabled', true);
    }
}

/**
 * Fonction de vérification de formulaire groupe et projet
 */
function validFormGP() {
    var nomE = $('#gedi_basebundle_' + url + '_nom').val();
    var user = $('#gedi_basebundle_' + url + '_idUtilisateurFk' +
        (url.charAt(0).toUpperCase() + url.slice(1))).val();

    var $bt = $('.bouton-submit-admin-entity');
    // test si tous les champs sont remplis
    if (nomE == "" || user == "") {
        $bt.prop('disabled', true);
    } else {
        $bt.prop('disabled', false);
    }
}

/**
 * Fonction de vérification de formulaire document
 */
function validFormDoc() {
    var $bt = $('.bouton-submit-admin-entity');
    if ($('#data_typeDoc').val() == "" || $('#data_idUtilisateurFkDocument').val() == "" ||
        $('#data_nom').val() == "" || $('#data_idProjetFkDocument').val() == "") {
        $bt.prop('disabled', true);
    } else {
        $bt.prop('disabled', false);
    }
}

/**
 * Fonction executée sur le dashboard administrateur
 * @param counter
 */
function dashboardStart(counter) {
    if (counter < 9) {
        setTimeout(function () {
            var $p1 = $('#panel-' + counter);
            $p1.attr('style', 'visibility: visible');
            $p1.animateCss('bounceInUp');
            counter++;
            dashboardStart(counter);
        }, 200);
    } else {
        setTimeout(function () {
            var nbNewUsers = parseInt($('#nb_users_inactifs').html());
            if (nbNewUsers > 0) {
                showNotify('<strong>' + 'Vous avez ' + nbNewUsers +
                    ((nbNewUsers > 1) ? ' demandes' : ' demande') + ' d\'ajout' +
                    '</strong>', 'glyphicon glyphicon-info-sign', 'info');
            }
        }, 1500);
    }
}

// ======================================================================================================
// USER
// ======================================================================================================

var cache_parent;
var cache_type;
/**
 * Fonction d'édition d'entité. La fonction renseigne les champs du formulaire.
 * @param js_object, infos de l'entité à mettre dans le formulaire
 * @param typeEntite, type d'entité éditée
 */
function edit2(js_object, typeEntite) {

    // remplissages de champs des formulaires
    for (var key in js_object) {
        if (js_object.hasOwnProperty(key)) {
            if (key == 'password') {
                $('#gedi_basebundle_' + typeEntite + '_' + key + '_first').val(js_object[key]);
                $('#gedi_basebundle_' + typeEntite + '_' + key + '_second').val(js_object[key]);
            } else {
                if (typeEntite == types.DOCUMENT) {
                    $('#data_' + key).val(js_object[key]);
                } else {
                    $('#gedi_basebundle_' + typeEntite + '_' + key).val(js_object[key]);
                }
            }
        }
    }
    // $('#data_idProjetFkDocument').val(parent);

    // modification du popup ajout / edition
    $('.popup-admin-add-user').html('Modifier un ' + typeEntite);
    var $bsae = $('.bouton-submit-admin-entity');
    $bsae.val('Appliquer');
    $bsae.prop('disabled', false);
    $('#data_nom').prop('disabled', false);
    $('#data_typeDoc').prop('disabled', false);
    $('#data_fichier').prop('disabled', true); // desactive le bouton upload
}

/**
 * Fonction pour ouvrir les dossiers en ajax
 * @param id
 */
function openFolder(id) {
    $('body').ajaxSend(id, types.DOCUMENT_PROJET, null);
}

/**
 * Fonction pour revenir au dossier parent
 * @param id
 */
function openBreadcrumb(id) {
    $('body').ajaxSend(id, types.DOCUMENT_PROJET, null);
    var $fu = $('#breadcrumb-flag');
    if ($fu.next()) {
        $fu.nextAll().remove();
    }
}

/**
 * Appel du menu contextuel
 */
function menuContext(isFolder, id) {
    if (isFolder) {
        $(".context-file").hide();
        $(".context-folder").show();
    } else {
        $(".context-file").show();
        $(".context-folder").hide();
    }
    $("#contextMenu").children().val(id);
    $(".content-user").contextMenu({
        menuSelector: "#contextMenu"
    });
}

/**
 * =======================================================================
 * block jquery
 */
$(function () {
    var $table = $('#table_admin');

    /**
     * Elements lancés au chargement de la page
     */
    $(document).ready(function () {
        $('.bouton-desactive').prop('disabled', true); // désactive par défaut les boutons de la classe bouton-desactive
        $('#content').attr('style', 'visibility: visible');
        menuContext(); // active le menu contextuel

        // change la variable nom en fonction de la page courante
        // charge les animations sur les pages
        if (window.location.href.lastIndexOf("/") == window.location.href.length - 1) {
            var $jb = $('#jumbo1');
            $jb.attr('style', 'visibility: visible');
            $jb.animateCss('zoomInDown');
            showNotifyContact();
        } else if (window.location.href.indexOf("register") > -1) {
            var $pr = $('#panel-register');
            $pr.attr('style', 'visibility: visible');
            $pr.animateCss('bounceInUp');
            showNotifyContact();
        } else if (window.location.href.indexOf("contact") > -1) {
            var $pc = $('#panel-contact');
            $pc.attr('style', 'visibility: visible');
            $pc.animateCss('bounceInUp');
        } else if (window.location.href.indexOf("login") > -1) {
            showNotifyContact();
        } else if (window.location.href.indexOf("users_admin") > -1) {
            url = types.UTILISATEUR;
        } else if (window.location.href.indexOf("groups_admin") > -1) {
            url = types.GROUPE;
        } else if (window.location.href.indexOf("projects_admin") > -1) {
            url = types.PROJET;
        } else if (window.location.href.indexOf("docs_admin") > -1) {
            url = types.DOCUMENT;
        } else if (window.location.href.indexOf("home_admin") > -1) {
            dashboardStart(1);
        } else if (window.location.href.indexOf("home_user") > -1) {
            url = types.HOME_USER;
            showNotifyContact();
        } else if (window.location.href.indexOf("account_user") > -1) {
            url = types.ACCOUNT_USER;
            showNotifyContact();
        } else if (window.location.href.indexOf("recent_user") > -1) {
            url = types.RECENT_USER;
            showNotifyContact();
        } else if (window.location.href.indexOf("shared_user") > -1) {
            url = types.SHARED_USER;
            showNotifyContact();
        }
        updateNbEntity(); // met à jour le nombre d'entités
        activeDefault(); // active les éléments par défaut
        backToTop();
    });

    // ======================================================================================================
    // LISTENERS
    // ======================================================================================================

    /**
     * Listener sur les cases à cocher du tableau pour mettre à jour
     * la sélection.
     */
    $table.on({
        'check.bs.table': sr,
        'uncheck.bs.table': sr,
        'check-all.bs.table': sr,
        'uncheck-all.bs.table': sr
    });
    function sr() {
        sel = $table.bootstrapTable('getSelections');
        $('.spanNbSel').html(sel.length);
        hideOuShow(sel.length);
    }

    /**
     * Listener sur les éléments de la classe btn-view-entity
     * Permet de voir les entités contenus par une entité parente
     */
    $table.on('click', '.btn-view-entity', function () {
        var id = $(this).closest("tr").attr('data-uniqueid'); // récupère l'id de l'entité
        $('#liste-children').empty(); // vide la liste

        if (url == types.GROUPE) {
            $('body').ajaxSend(id, types.UTILISATEUR, null);
        } else if (url == types.PROJET) {
            $('body').ajaxSend(id, types.DOCUMENT, null);
        }
    });

    /**
     * Listener sur le bouton logout, joue l'animation
     */
    $('#btn-logout').click(function () {
        $('#content').animateCss('zoomOut');
        $('#submit-search').animateCss('fadeOut');
        $('#search-bar').animateCss('fadeOut');
    });

    /**
     * Fonction permettant de jouer les animations
     * @param animationName, nom de l'animation à jouer
     */
    $.fn.extend({
        animateCss: function (animationName) {
            var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
            this.addClass('animated ' + animationName).one(animationEnd, function () {
                $(this).removeClass('animated ' + animationName);
            });
        }
    });

    /**
     * Listener sur les elements de classe bouton-desactive
     * Met à jour du popup de suppression
     */
    $('.bouton-desactive').click(function () {
        var tmp = url;
        if (typeof sel != 'undefined') {
            (sel.length > 1) ? tmp += 's' : tmp;
            $('#nbSel').html('Vous êtes sur le point de supprimer définitivement ' +
                sel.length + ' ' + tmp);
        }
    });

    /**
     * Listener sur les elements de la classe list-activable-item.
     * Active l'element en couleur.
     */
    $('.list-activable-item').click(function (event) {
        $('.list-activable-item').removeClass('active');
        $('#' + event.currentTarget.id).addClass('active');
    });

    /**
     * Listener sur les elements de la classe list-group-item.
     * Affiche les projets d'un utilisateur
     */
    $('.list-users-item').click(function (event) {
        var tmp = event.currentTarget.id; // récupère l'id de la ligne de l'utilisateur
        $('body').ajaxSend(tmp.substring(20, tmp.length), types.PROJET, null);
    });

    /**
     * Listener sur le toggle de filtre des demandes d'ajout.
     * Filtre le tableau d'utilisateurs actifs | inactifs
     */
    $('#toggle-new-users').change(function () {
        if (!$('#toggle-new-users').prop('checked')) {
            $table.bootstrapTable('filterBy', {actif: ""});
        } else {
            $table.bootstrapTable('filterBy', {});
        }
    });

    /**
     * Listener sur le bouton d'upload de fichier.
     * Renseigne les champs nom de document et type.
     */
    $('#data_fichier').change(function () {
        var uf = $(this).val();
        $('#data_nom').val(uf.substring(uf.lastIndexOf('\\') + 1, uf.lastIndexOf('.')));
        $('#data_typeDoc').val(uf.substring(uf.lastIndexOf('.') + 1, uf.length));
    });

    /**
     * Listener sur le bouton download de fichier.
     * Télécharge les fichiers selectionnés
     */
    $('#bouton-download').click(function () {
        var sel = $.map($table.bootstrapTable('getSelections'), function (row) {
            return row.id;
        });
        $('body').ajaxSend(sel, types.DOWNLOAD, null);
    });

    /**
     * Listener sur le bouton supprimer du popup de suppression
     * envoi la selection à supprimer au controller via ajaxSend
     */
    $('#delete-entity').click(function () {
        if (url == types.HOME_USER) {
            $('body').ajaxSend(sel, types.SUPPRESSION, cache_type);
        } else {
            $('body').ajaxSend(sel, types.SUPPRESSION, null);
        }
    });

    /**
     * Listener sur les elements de la classe form-admin
     * Stope l'envoi normal du formulaire puis récupère le contenu du formulaire
     * et l'envoi au serveur avec la fonction ajaxSend
     */
    $('.form-admin').submit(function (event) {
        // Eviter le comportement par défaut (soumettre le formulaire)
        event.preventDefault();
        $('#data_nom').prop('disabled', false);
        $('#data_typeDoc').prop('disabled', false);

        // récupération des données du formulaire
        var $form = $(this);
        var selection = $form.serializeArray();
        var formdata = new FormData($form[0]);

        // ne s'execute que sur la page docs_admin, cas de l'upload de fichier
        if (url == types.DOCUMENT && selection[0].value == "") {
            var file = document.getElementById("data_fichier");
            formdata.append('typeAction', types.UPLOAD);
            formdata.append('fichier', file.files[0]);
            $('body').ajaxSend(formdata, types.UPLOAD, null);
        } else {
            // ne s'execute que sur la page users_admin
            if (url == types.UTILISATEUR) {
                // une checkbox non cochée dans un formulaire n'est n'apparait pas dans
                // le tableau après un serializeArray. On doit donc dire à la main qu'elle
                // n'est pas cochée
                if (!$('#gedi_basebundle_utilisateur_actif').prop('checked')) {
                    selection.splice(6, 0, {
                        name: "gedi_basebundle_utilisateur[actif]",
                        value: false
                    });
                } else {
                    selection.splice(6, 1, {
                        name: "gedi_basebundle_utilisateur[actif]",
                        value: true
                    });
                }
            }

            if (selection[0].value == "") {
                $('body').ajaxSend(selection, types.ENREGISTREMENT, null);
            } else {
                $('body').ajaxSend(selection, types.MODIFICATION, null);
            }
        }
    });

    /**
     * Listener sur le formulaire d'ajout d'utilisateur.
     * Apelle la fonction validFormUtilisateur.
     * Ne s'execute que sur la page users_admin
     */
    $('#popup-add').on({
        'keyup': validForm,
        'focusout': validForm,
        'click': validForm
    });

    /**
     * Listener sur les elements de classe bouton-dismiss-entity
     * Vide le formulaire de son contenu.
     */
    $(".bouton-dismiss-entity").click(function () {
        $('form').trigger("reset"); // reset le formulaire
        $('#gedi_basebundle_utilisateur_actif').bootstrapToggle('off'); // met le toggle sur off
        // remet le champ confirmation de mot de passe et login à la couleur par défaut
        $('#gedi_basebundle_utilisateur_password_second').css('background-color', 'var(--color-default)');
        $('#gedi_basebundle_utilisateur_username').css('background-color', 'var(--color-default)');
        logins = null; // vide la variable globale logins
    });

    /**
     * Listener sur le bouton créer une entité.
     * Le listener fire dès que l'on clique sur un élément
     * de la classe bouton-admin-popup-add
     * La fonction vide le formulaire de son contenu et met à jour le modal
     */
    $(".bouton-admin-popup-add").click(function () {

        // toutes les pages
        $('form').trigger("reset"); // reset le formulaire
        $('#popup-admin-add-titre').html('Créer un ' + url); // écrit le titre du modal
        var $bsae = $('.bouton-submit-admin-entity'); // récupère l'element de la classe bouton-submit-admin-entity
        $bsae.val('Créer'); // change la valeur de $bsae
        $('.assign-user').show(); // affiche le panel d'assignation d'utilisateur
        $bsae.prop('disabled', true); // desactive le bouton submit du formulaire
        $('#gedi_basebundle_' + url + '_idUtilisateurFk' + (url.charAt(0).toUpperCase() + url.slice(1))).removeAttr('value');
        $('#gedi_basebundle_' + url + '_id' + (url.charAt(0).toUpperCase() + url.slice(1))).removeAttr('value');

        // users_admin
        $('#gedi_basebundle_utilisateur_actif').bootstrapToggle('off'); // met le toggle sur off
        // récupère tous les login pour vérifier coté client si on a pas un doublon de login
        if (url == types.UTILISATEUR) {
            logins = $.map($table.bootstrapTable('getData'), function (rows) {
                return rows.login; // met la liste des logins du tableau dans la variable globale logins
            });
        }

        // projects_admin
        $('#gedi_basebundle_projet_parent').removeAttr('value');
        $('#liste-projets').empty(); // vide la liste des projets sur projects_admin

        // docs_admin
        $('#bouton-upload').show(); // affiche le bouton upload
        $('#data_nom').prop('disabled', true);
        $('#data_typeDoc').prop('disabled', true);
        $('#data_fichier').prop('disabled', false); // desactive le bouton upload
        $('#data_idUtilisateurFkDocument').removeAttr('value');
        $('#data_idProjetFkDocument').removeAttr('value');
        $('#data_idDocument').removeAttr('value');
    });

    $('.form-document').submit(function (event) {
        // Eviter le comportement par défaut (soumettre le formulaire)
        event.preventDefault();
        $('#data_nom').prop('disabled', false);
        $('#data_typeDoc').prop('disabled', false);

        // récupération des données du formulaire
        var $form = $(this);
        var selection = $form.serializeArray();
        var formdata = new FormData($form[0]);

        if (selection[0].value == "") {
            var file = document.getElementById("data_fichier");
            formdata.append('typeAction', types.UPLOAD);
            formdata.append('fichier', file.files[0]);
            $('body').ajaxSend(formdata, types.UPLOAD, types.DOCUMENT);
        } else {
            $('body').ajaxSend(selection, types.MODIFICATION, types.DOCUMENT);
        }
    });

    $('.form-projet').submit(function (event) {
        // Eviter le comportement par défaut (soumettre le formulaire)
        event.preventDefault();
        $('#data_nom').prop('disabled', false);
        $('#data_typeDoc').prop('disabled', false);

        // récupération des données du formulaire
        var $form = $(this);
        var selection = $form.serializeArray();

        if (selection[0].value == "") {
            $('body').ajaxSend(selection, types.ENREGISTREMENT, types.PROJET);
        } else {
            $('body').ajaxSend(selection, types.MODIFICATION, types.PROJET);
        }
    });

    $('.form-groupe').submit(function (event) {
        // Eviter le comportement par défaut (soumettre le formulaire)
        event.preventDefault();
        $('#data_nom').prop('disabled', false);
        $('#data_typeDoc').prop('disabled', false);

        // récupération des données du formulaire
        var $form = $(this);
        var selection = $form.serializeArray();

        if (selection[0].value == "") {
            $('body').ajaxSend(selection, types.ENREGISTREMENT, types.GROUPE);
        } else {
            $('body').ajaxSend(selection, types.MODIFICATION, types.GROUPE);
        }
    });

    /**
     * Ouvrir le dossier avec le menu contextuel
     */
    $("#context-open-folder").click(function (event) {
        $(this).ajaxSend(event.currentTarget.value, types.DOCUMENT_PROJET, null);
    });

    /**
     * Editer le projet
     */
    $("#context-edit-folder").click(function (event) {
        $(this).ajaxSend(event.currentTarget.value, types.GET, types.PROJET);
    });

    /**
     * Editer le document
     */
    $("#context-edit-file").click(function (event) {
        $(this).ajaxSend(event.currentTarget.value, types.GET, types.DOCUMENT);
    });

    /**
     * Supprimer un projet
     */
    $("#context-delete-folder").click(function (event) {
        $('#nbSel').html('Vous êtes sur le point de supprimer définitivement un ' + types.PROJET);
        cache_type = types.PROJET;
        sel = event.currentTarget.value;
    });

    /**
     * Supprimer un document
     */
    $("#context-delete-file").click(function (event) {
        $('#nbSel').html('Vous êtes sur le point de supprimer définitivement un ' + types.DOCUMENT);
        cache_type = types.DOCUMENT;
        sel = event.currentTarget.value;
    });

    /**
     * Télécharger le fichier avec le menu contextuel
     */
    $("#context-download-file").click(function (event) {
        $(this).ajaxSend(event.currentTarget.value, types.DOWNLOAD, null);
    });

    // ======================================================================================================
    // ENVOI AJAX CLIENT -> SERVEUR
    // ======================================================================================================

    /**
     * Fonction d'envoi en Ajax de la requete en POST.
     * La fonction envoi des données au serveur dans la variable selection
     * et le type d'action à faire dans la variable typeAction.
     * La reponse du serveur arrive dans la variable data.
     * @param selection, données à envoyer au serveur
     * @param typeAction, type d'action à faire coté serveur [enregistré, modifié, supprimé, children]
     */
    $.fn.extend({
        ajaxSend: function (selection, typeAction, typeEntite) {
            var ct, pd;

            // upload de fichiers
            if (typeAction == types.UPLOAD) {
                ct = false;
                pd = false;
            } else {
                // toutes les autres requetes
                selection = {'data': selection, 'typeAction': typeAction, 'typeEntite': typeEntite};
                ct = 'application/x-www-form-urlencoded; charset=UTF-8';
                pd = true;
            }

            $.ajax({
                type: 'POST', // type d'envoi
                url: window.location, // url d'envoi, ici ce sera toujours la page courante
                contentType: ct, // type de contenu
                processData: pd, // pré-traitement de jquery
                data: selection, // données à envoyer au serveur
                success: function (data) { // traitement en cas de succes
                    switch (typeAction) {
                        case types.ENREGISTREMENT:
                            $table.bootstrapTable('append', data.reponse);
                            completeRow(data); // finalise la création ou la modification
                            break;
                        case types.MODIFICATION:
                            if (url == types.HOME_USER) {
                                openBreadcrumb(data.idparent);
                                $('form').trigger("reset"); // reset le formulaire de création
                                $('.modal-backdrop').remove(); // enlève le modal-backdrop du formulaire
                                $('#popup-add-' + typeEntite).modal('toggle'); // fait disparaitre le modal de création
                                showNotify('<strong>' +(typeEntite.charAt(0).toUpperCase() + typeEntite.slice(1)) +
                                    ' modifié</strong>', 'glyphicon glyphicon-ok', 'success');
                                return 0;
                            } else {
                                $table.bootstrapTable('updateByUniqueId', {id: data.reponse.id, row: data.reponse});
                                completeRow(data); // finalise la création ou la modification
                            }
                            break;
                        case types.SUPPRESSION:
                            if (url == types.HOME_USER) {
                                openBreadcrumb(data.idparent);
                                showNotify('<strong>' +(typeEntite.charAt(0).toUpperCase() + typeEntite.slice(1)) +
                                    ' supprimé</strong>', 'glyphicon glyphicon-ok', 'success');
                                return 0;
                            } else {
                                deleteRow();
                            }
                            break;
                        case types.UPLOAD:
                            $table.bootstrapTable('append', data.reponse);
                            completeRow(data); // finalise la création ou la modification
                            break;
                        case types.DOWNLOAD:
                            $table.bootstrapTable('uncheckAll');
                            window.location = data.reponse;
                            if (url == types.HOME_USER) {
                                showNotify('<strong>Document téléchargé</strong>',
                                    'glyphicon glyphicon-ok', 'success');
                                return 0;
                            }
                            break;
                        case types.UTILISATEUR:
                            if (url == types.GROUPE) {
                                $('#liste-children').prepend(data.reponse);
                            }
                            return 0;
                            break;
                        case types.DOCUMENT:
                            if (url == types.PROJET) {
                                $('#liste-children').prepend(data.reponse);
                            }
                            return 0;
                            break;
                        case types.PROJET:
                            if (url == types.PROJET || url == types.DOCUMENT) {
                                var $lp = $('#liste-projets');
                                $lp.empty();
                                $lp.prepend(data.reponse);
                            }
                            return 0;
                            break;
                        case types.DOCUMENT_PROJET:
                            if (url == types.HOME_USER) {
                                console.log(data.reponse);
                                var $dk = $('#desktop');
                                $dk.empty();
                                $dk.append('<div class="row text-center">' + data.reponse + '</div>');//bug
                                var $fu = $('#footer_user');
                                $fu.append(data.fdparent);
                                cache_parent = data.idparent; // ici et la
                            }
                            menuContext();
                            return 0;
                            break;
                        case types.GET:
                            if (url == types.HOME_USER) {
                                edit2(data.reponse, typeEntite);
                            }
                            menuContext();
                            return 0;
                            break;
                        default:
                    }

                    sel = null; // remet la sélection à null
                    hideOuShow(0);
                    updateNbEntity();
                    activeDefault();

                    // affiche la notification de succes
                    showNotify('<strong>' + (url.charAt(0).toUpperCase() + url.slice(1)) +
                        ((typeAction == types.SUPPRESSION && selection.length > 1) ? 's ' + typeAction + 's' :
                            ' ' + typeAction) + '</strong>', 'glyphicon glyphicon-ok', 'success');
                },
                error: function () { // traitement en cas d'echec
                    // affiche la notification d'echec
                    showNotify('<strong>' + 'La requête n\'a pas abouti' + '</strong>',
                        'glyphicon glyphicon-remove', 'danger');
                }
            });
        }
    });

    // ======================================================================================================
    // FONCTIONS REPONSE SERVEUR -> CLIENT
    // ======================================================================================================

    /**
     * Fonction de suppression, la fonction supprime
     * les lignes du tableau qui ont été cochées pour la suppression
     */
    function deleteRow() {
        // récupère tous les id des lignes qui sont cochées
        var ids = $.map($table.bootstrapTable('getSelections'), function (row) {
            return row.id;
        });
        // supprime tous les id récupérés
        $table.bootstrapTable('remove', {
            field: 'id',
            values: ids
        });
    }

    /**
     * Fonction pour completer la création ou la modification d'entité.
     * Elle doit etre executée après un création ou une modification de
     * ligne dans le tableau.
     * @param data, objet reponse du serveur contenant un tableau reponse
     */
    function completeRow(data) {
        $table.bootstrapTable('filterBy', {}); // reset les filtres du tableau
        $table.bootstrapTable('scrollTo', 'bottom'); // scroll en bas du tableau
        // le code suivant ne s'execute que sur la page users_admin car les elements
        // suivants sont sur cette page uniquement
        if (url == types.UTILISATEUR) {
            if (data.reponse.actif != "1") { // test des lignes du tableau reponse, on test si l'utilisateur est actif
                // met la ligne en bleu si l'utilisateur est inactif
                $('tr[data-uniqueid=' + data.reponse.id + ']').addClass('info text-info'); //BUG #2
            } else {
                // laisse la ligne normal si l'utilisateur est actif
                $('tr[data-uniqueid=' + data.reponse.id + ']').removeClass('info text-info');
            }
            // remet le champ confirmation de mot de passe à la couleur par défaut
            $('#gedi_basebundle_utilisateur_password_second').css('background-color', 'var(--color-default)');
        }
        $('form').trigger("reset"); // reset le formulaire de création
        $('.modal-backdrop').remove(); // enlève le modal-backdrop du formulaire
        $('#popup-add').modal('toggle'); // fait disparaitre le modal de création
    }

    /**
     * Appel du menu contextuel
     */
    (function ($, window) {
        $.fn.contextMenu = function (settings) {
            return this.each(function () {
                // ouvrir le menu contextuel
                $(this).on("contextmenu", function (e) {
                    // return native menu if pressing control
                    if (e.ctrlKey) return;

                    // ouvrir le menu
                    var $menu = $(settings.menuSelector)
                        .data("invokedOn", $(e.target))
                        .show()
                        .css({
                            position: "absolute",
                            left: getMenuPosition(e.clientX, 'width', 'scrollLeft'),
                            top: getMenuPosition(e.clientY, 'height', 'scrollTop')
                        })
                        .off('click')
                        .on('click', 'a', function () {
                            $menu.hide();
                        });
                    return false;
                });

                // fermer le menu quand on clique en dehors
                $('html').click(function () {
                    $("#contextMenu").hide();
                });
            });

            function getMenuPosition(mouse, direction, scrollDir) {
                var win = $(window)[direction](),
                    scroll = $(window)[scrollDir](),
                    menu = $(settings.menuSelector)[direction](),
                    position = mouse + scroll;
                if (mouse + menu > win && menu < mouse)
                    position -= menu;
                return position;
            }
        };
    })(jQuery, window);
});
