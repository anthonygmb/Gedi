<?php

namespace Gedi\UserBundle\Controller;

use Gedi\BaseBundle\Entity\Document;
use Gedi\BaseBundle\Entity\Groupe;
use Gedi\BaseBundle\Entity\Projet;
use Gedi\BaseBundle\Resources\Enum\BaseEnum;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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
            $response = new JsonResponse();
            $sel = $_POST['data'];

            if ($sel == null || $sel == "") {
                throw new Exception('La selection est nulle');
            }

            switch ($_POST['typeAction']) {
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
                    $this->get('document.service')->download($sel);
                    $rows = "download";
                    break;
                case BaseEnum::ENREGISTREMENT:
                    if (!isset($_POST['typeEntite'])) {
                        throw new Exception('typeEntite n\'est pas defini');
                    } else {
                        switch ($_POST['typeEntite']) {
                            case BaseEnum::GROUPE:
                                $objet = $this->get('groupe.service')->create($sel);
                                break;
                            case BaseEnum::PROJET:
                                $objet = $this->get('projet.service')->create($sel);
                                break;
                            case BaseEnum::DOCUMENT:
                                $objet = $this->get('document.service')->create($sel);
                                break;
                            default:
                                throw new Exception('typeEntite n\'est pas reconnu');
                        }
                    }
                    break;
                case BaseEnum::SUPPRESSION:
                    if (!isset($_POST['typeEntite'])) {
                        throw new Exception('typeEntite n\'est pas defini');
                    } else {
                        switch ($_POST['typeEntite']) {
                            case BaseEnum::GROUPE:
                                $rows = $this->get('groupe.service')->delete($sel);
                                break;
                            case BaseEnum::PROJET:
                                $rows = $this->get('projet.service')->delete($sel);
                                break;
                            case BaseEnum::DOCUMENT:
                                $rows = $this->get('document.service')->delete($sel);
                                break;
                            default:
                                throw new Exception('typeEntite n\'est pas reconnu');
                        }
                    }
                    break;
                case BaseEnum::MODIFICATION:
                    if (!isset($_POST['typeEntite'])) {
                        throw new Exception('typeEntite n\'est pas defini');
                    } else {
                        switch ($_POST['typeEntite']) {
                            case BaseEnum::GROUPE:
                                $objet = $this->get('groupe.service')->update($sel);
                                break;
                            case BaseEnum::PROJET:
                                $objet = $this->get('projet.service')->update($sel);
                                break;
                            case BaseEnum::DOCUMENT:
                                $objet = $this->get('document.service')->update($sel);
                                break;
                            default:
                                throw new Exception('typeEntite n\'est pas reconnu');
                        }
                    }
                    break;
                case BaseEnum::UTILISATEUR:
                    $tmp = $this->get('groupe.service')->getChildren($sel, $_POST['typeAction']);

                    if (sizeof($tmp) > 0) {
                        foreach ($tmp as $child) {
                            array_push($rows, '<li class="list-group-item">' . $child->getNom() . " " .
                                $child->getPrenom() . " - " . $child->getUsername() . '</li>');
                        }
                    } else {
                        array_push($rows, '<li class="list-group-item">... vide</li>');
                    }
                    break;
                case BaseEnum::PROJET:
                    $tmp = $this->get('utilisateur.service')->getChildren($sel, $_POST['typeAction']);

                    if (sizeof($tmp) > 0) {
                        foreach ($tmp as $child) {
                            array_push($rows, '<a id="list-activable-item-project-' . $child->getidProjet() .
                                '" href="#" onclick="addProject(' . $child->getidProjet() .
                                ')" class="list-group-item list-activable-item"><span class="glyphicon glyphicon-folder-close"></span> ' .
                                $child->getNom() . '</a>');
                        }
                    } else {
                        array_push($rows, '<a href="#" class="list-group-item">... vide</a>');
                    }
                    break;
                case BaseEnum::DOCUMENT:
                    $tmp = $this->get('projet.service')->getChildren($sel, $_POST['typeAction']);

                    if (sizeof($tmp) > 0) {
                        foreach ($tmp as $child) {
                            array_push($rows, '<li class="list-group-item">' . $child->getNom() . " " .
                                $child->getTypeDoc() . " - " . $child->getTag() . '</li>');
                        }
                    } else {
                        array_push($rows, '<li class="list-group-item">... vide</li>');
                    }
                    break;
                default:
                    throw new Exception('Typeaction n\'est pas reconnu');
            }

            if ($_POST['typeAction'] == BaseEnum::UPLOAD || $_POST['typeAction'] == BaseEnum::MODIFICATION) {
                $rows = [
                    "ck" => 'data-checkbox="true"',
                    "id" => $objet->getIdDocument(),
                    "nom" => $objet->getNom(),
                    "type" => '<span class="label label-default">' . $objet->getTypeDoc() . '</span>',
                    "datec" => date_format($objet->getDateCreation(), 'Y-m-d H:i:s'),
                    "datem" => date_format($objet->getDateModification(), 'Y-m-d H:i:s'),
                    "nbdownload" => $objet->getNbDownload(),
                    "projet" => $objet->getidProjetFkDocument()->getNom(),
                    "propio" => $objet->getIdUtilisateurFkDocument()->getNom() . " " . $objet->getIdUtilisateurFkDocument()->getPrenom(),
                    "ctrl" => '<span data-toggle="tooltip" data-placement="bottom" title="Editer le document">' .
                        '<button type="button" class="btn btn-default btn-warning round-button" data-toggle="modal" ' .
                        'data-target="#popup-add" onclick="edit(\'{&quot;idDocument&quot;:' . $objet->getIdDocument() .
                        ',&quot;nom&quot;:&quot;' . $objet->getNom() .
                        '&quot;,&quot;typeDoc&quot;:&quot;' . $objet->getTypeDoc() .
                        '&quot;,&quot;tag&quot;:&quot;' . $objet->getTag() .
                        '&quot;,&quot;resume&quot;:&quot;' . $objet->getResume() . '&quot;}\');">' .
                        '<span class="glyphicon glyphicon-pencil"></span></button></span>',
                ];
            }
            $response->setData(array('reponse' => (array)$rows));
            return $response;
        }

        // importation de tous les groupes
        $tab_groups = $this->get('groupe.service')->read();
        // importation de tous les projets
        $tab_projects = $this->get('projet.service')->read();
        // importation de tous les documents
        $tab_docs = $this->get('document.service')->read();

        return $this->render('GediUserBundle:User:home_user.html.twig', array(
            'groupe' => $groupe,
            'projet' => $projet,
            'document' => $document,
            'groupeForm' => $groupeForm->createView(),
            'projetForm' => $projetForm->createView(),
            'documentForm' => $documentForm->createView(),
            'tab_groups' => $tab_groups,
            'tab_projects' => $tab_projects,
            'tab_docs' => $tab_docs,
        ));
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
}
