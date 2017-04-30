<?php

namespace Gedi\BaseBundle\Services;

use Doctrine\ORM\EntityManager;
use Gedi\BaseBundle\Entity\Document;
use Gedi\BaseBundle\Entity\Projet;
use ZipArchive;

/**
 * Service permettant de manipuler les documents
 * Class DocumentService
 * @package Gedi\BaseBundle\Services
 */
class DocumentService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var FileService
     */
    private $fs;

    /**
     * @var string
     */
    private $targetDir;

    /**
     * DocumentService constructor.
     * @param EntityManager $entityManager
     * @param FileService $fileService
     * @param $targetDir
     */
    public function __construct(EntityManager $entityManager, FileService $fileService, $targetDir)
    {
        $this->em = $entityManager;
        $this->fs = $fileService;
        $this->targetDir = $targetDir;
    }

    /**
     * Enregistre un document dans la BDD
     * @param $sel
     * @param $file
     * @return Document
     * @internal param $formData
     */
    public function create($sel, $file)
    {
        $objet = new Document();
        $objet->setNom($sel['nom']);
        $objet->setTypeDoc($sel['typeDoc']);
        $objet->setTag(($sel['tag'] == "") ? null : $sel['tag']);
        $objet->setResume(($sel['resume'] == "") ? null : $sel['resume']);
        $utilisateur = $this->em->find('GediBaseBundle:Utilisateur', $sel['idUtilisateurFkDocument']);
        $utilisateur->addIdUtilisateurFkDocument($objet);
        $objet->setIdUtilisateurFkDocument($utilisateur);
        /* @var $projet Projet */
        $projet = $this->em->find('GediBaseBundle:Projet', $sel['idProjetFkDocument']);
        $projet->addIdProjetFkDocument($objet);
        $objet->setIdProjetFkDocument($projet);
        $path = $this->targetDir . $projet->getIdUtilisateurFkProjet()->getIdUtilisateur() . "/";
        $projetPath = $this->fs->createPath($projet);
        $fileName = $this->fs->upload($file, $path . $projetPath);
        $objet->setFichier($fileName);
        $this->em->persist($objet);
        $this->em->flush();
        return $objet;
    }

    /**
     * Retourne tous les documents en BDD
     * @return array
     */
    public function read()
    {
        return $this->em->getRepository('GediBaseBundle:Document')->findAll();
    }

    /**
     * Retourne l'entite correspondant à l'id $sel
     * @param $sel
     * @return Document
     */
    public function findOne($sel)
    {
        /* @var $objet Document */
        $objet = $this->em->find('GediBaseBundle:Document', $sel);
        return $objet;
    }

    /**
     * Retourne les N derniers éléments
     * @param $max
     * @return array
     */
    public function readLast($max)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('u')
            ->from('GediBaseBundle:Document', 'u')
            ->orderBy('u.dateCreation', 'DESC')
            ->setMaxResults($max);
        $query = $qb->getQuery();
        return $query->getResult();
    }

    /**
     * Retourne les N derniers éléments d'un utilisateur
     * @param $max
     * @param $id
     * @return array
     */
    public function readLastByUser($max, $id)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('u')
            ->from('GediBaseBundle:Document', 'u')
            ->where('u.idUtilisateurFkDocument =' . $id)
            ->orderBy('u.dateCreation', 'DESC')
            ->setMaxResults($max);
        $query = $qb->getQuery();
        return $query->getResult();
    }

    /**
     * Retourne le nombre d'entités en BDD
     * @return mixed
     */
    public function count()
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('count(u.idDocument)');
        $qb->from('GediBaseBundle:Document', 'u');
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function sumDownload()
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('sum(u.nbDownload)');
        $qb->from('GediBaseBundle:Document', 'u');
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Télécharge les fichiers selectionnés dans une archive zip
     * @param $sel
     */
    public function download($sel)
    {
        $zip = new ZipArchive();
        $filename = $this->targetDir . "archive.zip";

        if (file_exists($filename)) {
            unlink($filename);
        }

        if ($zip->open($filename, ZipArchive::CREATE) !== TRUE) {
            exit("Impossible d'ouvrir le fichier <$filename>\n");
        }

        for ($i = 0; $i <= count($sel) - 1; $i++) {
            /* @var $document Document */
            $document = $this->em->find('GediBaseBundle:Document', $sel[$i]);
            $document->addNbDownload();
            if ($document->getTypeDoc() != "") {
                $zip->addFile($this->targetDir . $this->makePath($document, true), $document->getNom() . '.' . $document->getTypeDoc());
            } else {
                $zip->addFile($this->targetDir . $this->makePath($document, true), $document->getNom());
            }
            $this->em->merge($document);
        }
        $zip->close();
        $this->em->flush();
    }

    /**
     * Fonction pour obtenir le path d'un document
     * @param $document Document
     * @param $withDocName boolean
     * @return string
     */
    public function makePath($document, $withDocName)
    {
        if ($document->getIdProjetFkDocument() != null) {
            if ($withDocName == true) {
                return $document->getIdUtilisateurFkDocument()->getIdUtilisateur() . "/" .
                    $this->fs->createPath($document->getIdProjetFkDocument()) . $document->getFichier();
            } else {
                return $document->getIdUtilisateurFkDocument()->getIdUtilisateur() . "/" .
                    $this->fs->createPath($document->getIdProjetFkDocument());
            }
        } else {
            return null;
        }
    }

    /**
     * Met à jour un document
     * @param $sel
     * @return Document
     */
    public function update($sel)
    {
        /* @var $objet Document */
        $objet = $this->em->find('GediBaseBundle:Document', $sel[0]['value']);
        $objet->setNom($sel[1]['value']);
        $objet->setTypeDoc($sel[2]['value']);
        $objet->setTag(($sel[3]['value'] == "") ? null : $sel[3]['value']);
        $objet->setResume(($sel[4]['value'] == "") ? null : $sel[4]['value']);
        $this->em->merge($objet);
        $this->em->flush();
        return $objet;
    }

    /**
     * Supprime un ou plusieurs documents
     * @param $sel
     * @return string
     */
    public function delete($sel)
    {
        for ($i = 0; $i <= count($sel) - 1; $i++) {
            /* @var $toDel Document */
            $toDel = $this->em->find('GediBaseBundle:Document', $sel[$i]['id']);
            unlink($this->targetDir . $this->makePath($toDel, true));
            $this->em->remove($toDel);
        }
        $this->em->flush();
        return "OK";
    }
}
