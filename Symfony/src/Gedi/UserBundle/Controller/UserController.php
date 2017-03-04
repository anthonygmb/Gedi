<?php

namespace Gedi\UserBundle\Controller;

use Gedi\BaseBundle\Entity\Document;
use Gedi\BaseBundle\Entity\Groupe;
use Gedi\BaseBundle\Entity\Projet;
use Gedi\BaseBundle\Entity\Utilisateur;
use Gedi\BaseBundle\Resources\Enum\BaseEnum;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

define('TYPE_ICON', '.png');
define('FOLDER_ICON', 'folder.png');

class UserController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER') or has_role('ROLE_ADMIN')")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function homeAction(Request $request, $id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') &&
            $this->getUser()->getIdUtilisateur() != $id
        ) {
            throw new Exception("Vous n'êtes pas authorisé à consulter cette page");
        }

        $groupe = new Groupe();
        $projet = new Projet();
        $document = new Document();
        $groupeForm = $this->createForm('Gedi\BaseBundle\Form\GroupeType', $groupe);
        $groupeForm->handleRequest($request);
        $projetForm = $this->createForm('Gedi\BaseBundle\Form\ProjetType', $projet);
        $projetForm->handleRequest($request);
        $documentForm = $this->createForm('Gedi\BaseBundle\Form\DocumentType', $document);
        $documentForm->handleRequest($request);

        if ($request->isMethod('POST') && isset($_POST['data']) && isset($_POST['typeAction'])) {
            $rows = [];
            $parent = null;
            $response = new JsonResponse();
            $sel = $_POST['data'];

            if ($sel == null || $sel == "") {
                throw new Exception('La selection est nulle');
            }

            switch ($_POST['typeAction']) {
                case BaseEnum::GET:
                    if (!isset($_POST['typeEntite'])) {
                        throw new Exception('typeEntite n\'est pas defini');
                    } else {
                        /* @var $objet Projet|Document|Groupe|Utilisateur */
                        $objet = $this->get($_POST['typeEntite'] . '.service')->findOne($sel);
                        $rows = $objet->toArray();
                    }
                    break;
                case BaseEnum::UPLOAD:
                    if (!isset($_FILES['fichier'])) {
                        throw new Exception('Le fichier n\'est pas uploadé');
                    } else {
                        $file = $_FILES['fichier'];
                        if ($file == null || $file == "") {
                            throw new Exception('Le fichier est nul');
                        }
                        $uploadedFile = new UploadedFile($file['tmp_name'], $file['name'], $file['type'], $file['size']);
                        $objet = $this->get('document.service')->create($sel, $uploadedFile);
                    }
                    break;
                case BaseEnum::DOWNLOAD:
                    $tmp = [$sel];
                    $this->get('document.service')->download($tmp);
                    $rows = "download";
                    break;
                case BaseEnum::ENREGISTREMENT:
                    if (!isset($_POST['typeEntite'])) {
                        throw new Exception('typeEntite n\'est pas defini');
                    } else {
                        $objet = $this->get($_POST['typeEntite'] . '.service')->create($sel);
                        return $this->createArborescence($this->getParent($objet, $_POST['typeEntite']));
                    }
                    break;
                case BaseEnum::SUPPRESSION:
                    if (!isset($_POST['typeEntite'])) {
                        throw new Exception('typeEntite n\'est pas defini');
                    } else {
                        $objet = $this->get($_POST['typeEntite'] . '.service')->findOne($sel);
                        array_push($rows, array('id' => $sel));
                        $this->get($_POST['typeEntite'] . '.service')->delete($rows);
                        return $this->createArborescence($this->getParent($objet, $_POST['typeEntite']));
                    }
                    break;
                case BaseEnum::MODIFICATION:
                    if (!isset($_POST['typeEntite'])) {
                        throw new Exception('typeEntite n\'est pas defini');
                    } else {
                        $objet = $this->get($_POST['typeEntite'] . '.service')->update($sel);
                        return $this->createArborescence($this->getParent($objet, $_POST['typeEntite']));
                    }
                    break;
                case BaseEnum::UTILISATEUR:
                    $tmp = $this->get('groupe.service')->getChildren($sel, $_POST['typeAction']);

                    if (sizeof($tmp) > 0) {
                        /* @var $child Utilisateur */
                        foreach ($tmp as $child) {
                            array_push($rows, '<li class="list-group-item">' . $child->getNom() . " " .
                                $child->getPrenom() . " - " . $child->getUsername() . '</li>');
                        }
                    } else {
                        array_push($rows, '<li class="list-group-item">... vide</li>');
                    }
                    break;
                case BaseEnum::DOCUMENT_PROJET:
                    $parent = $this->get('projet.service')->findOne($sel);
                    return $this->createArborescence($parent);
                    break;
                default:
                    throw new Exception('Typeaction n\'est pas reconnu');
            }

            $response->setData(array('reponse' => (array)$rows));
            return $response;
        }

        // importation des groupes de l'utilisateur
        $tab_groups = $this->get('utilisateur.service')->
        getChildren($this->getUser()->getIdUtilisateur(), BaseEnum::GROUPE);
        // importation des projets de l'utilisateur
        $tab_projects = $this->get('utilisateur.service')->
        getChildren($this->getUser()->getIdUtilisateur(), BaseEnum::PROJET)[0];

        return $this->render('GediUserBundle:User:home_user.html.twig', array(
            'groupe' => $groupe,
            'projet' => $projet,
            'document' => $document,
            'groupeForm' => $groupeForm->createView(),
            'projetForm' => $projetForm->createView(),
            'documentForm' => $documentForm->createView(),
            'tab_groups' => $tab_groups,
            'tab_projects' => $tab_projects,
        ));
    }

    /**
     * @param $parent Projet
     * @return JsonResponse
     */
    public function createArborescence($parent)
    {
        $rows = [];
        $response = new JsonResponse();
        $projets = $this->get('projet.service')->getChildren($parent->getIdProjet(), BaseEnum::PROJET);
        $documents = $this->get('projet.service')->getChildren($parent->getIdProjet(), BaseEnum::DOCUMENT);

        if (sizeof($projets) > 0) {
            /* @var $child Projet */
            foreach ($projets as $child) {
                array_push($rows, '<div class="col-md-2 container-data"><div class="panel full-transparent"><a id="' . $child->getIdProjet() .
                    '" class="content-user" href="#" onclick="openFolder(' . $child->getIdProjet() . ');" 
                                oncontextmenu="menuContext(true, this.id);"><img src="' . $this->getParameter('image_folder_directory') . FOLDER_ICON . '" alt="' .
                    $child->getNom() . '"/><p class="text-white"><strong>' . $child->getNom() .
                    '</strong></p></a></div></div>');
            }
        }
        if (sizeof($documents) > 0) {
            /* @var $child Document */
            foreach ($documents as $child) {
                array_push($rows, '<div class="col-md-2 container-data"><div class="panel full-transparent"><a id="' . $child->getIdDocument() .
                    '" class="content-user" href="#" oncontextmenu="menuContext(false, this.id);"><img src="' . $this->getParameter('image_icon_directory') .
                    $child->getTypeDoc() . TYPE_ICON . '" alt="' . $child->getNom() .
                    '"/><p class="text-white"><strong>' . $child->getNom() . '</strong></p></a></div></div>');
            }
        }

        $parent_line = '<li id="list-item-' . $parent->getIdProjet() . '"><a onclick="openBreadcrumb(' . $parent->getIdProjet() . ');">' .
            '<span class="glyphicon glyphicon-folder-close"></span> ' . $parent->getNom() . '</a></li>';
        $response->setData(array('reponse' => (array)$rows, 'fdparent' => $parent_line, 'idparent' => $parent->getIdProjet()));
        return $response;
    }

    /**
     * @param $objet Projet|Document
     * @param $typeEntite string
     * @return Projet
     */
    public function getParent($objet, $typeEntite)
    {
        $parent = null;
        switch ($typeEntite) {
            case BaseEnum::PROJET:
                $parent = $objet->getParent();
                break;
            case BaseEnum::DOCUMENT:
                $parent = $objet->getIdProjetFkDocument();
                break;
            default:
                throw new Exception('typeEntite n\'est pas reconnu');
        }
        return $parent;
    }

    /**
     * @Security("has_role('ROLE_USER') or has_role('ROLE_ADMIN')")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function accountAction(Request $request, $id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') &&
            $this->getUser()->getIdUtilisateur() != $id
        ) {
            throw new Exception("Vous n'êtes pas authorisé à consulter cette page");
        }
        return $this->render('GediUserBundle:User:account_user.html.twig');
    }

    /**
     * @Security("has_role('ROLE_USER') or has_role('ROLE_ADMIN')")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function sharedAction(Request $request, $id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') &&
            $this->getUser()->getIdUtilisateur() != $id
        ) {
            throw new Exception("Vous n'êtes pas authorisé à consulter cette page");
        }
        return $this->render('GediUserBundle:User:shared_user.html.twig');
    }

    /**
     * @Security("has_role('ROLE_USER') or has_role('ROLE_ADMIN')")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function recentAction(Request $request, $id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') &&
            $this->getUser()->getIdUtilisateur() != $id
        ) {
            throw new Exception("Vous n'êtes pas authorisé à consulter cette page");
        }
        return $this->render('GediUserBundle:User:recent_user.html.twig');
    }

    /**
     * @Security("has_role('ROLE_USER') or has_role('ROLE_ADMIN')")
     * Page de téléchargment des fichiers de l'application
     */
    public function downloadAction()
    {
        header('Content-Type: application/octet-stream');
        header('Content-disposition: attachment; filename=archive.zip');
        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        readfile($this->getParameter('fichiers_directory') . 'archive.zip');
    }
}
