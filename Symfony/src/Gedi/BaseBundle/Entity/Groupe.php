<?php

namespace Gedi\BaseBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Groupe
 *
 * @ORM\Table(name="Groupe", indexes={@ORM\Index(name="fk_utilisateur_groupe_1", columns={"id_utilisateur_fk_groupe"})})
 * @ORM\Entity
 */
class Groupe
{
    /**
     * Id du groupe
     * @var integer
     *
     * @ORM\Column(name="id_groupe", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idGroupe;

    /**
     * Nom du groupe
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50, nullable=false)
     */
    private $nom;

    /**
     * Date de création du groupe
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="datetime", nullable=false)
     */
    private $dateCreation = 'CURRENT_TIMESTAMP';

    /**
     * Date de modification du groupe
     * @var \DateTime
     *
     * @ORM\Column(name="date_modification", type="datetime", nullable=true)
     */
    private $dateModification;

    /**
     * Propriétaire du groupe
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_utilisateur_fk_groupe", referencedColumnName="id_utilisateur")
     * })
     */
    private $idUtilisateurFkGroupe;

    /**
     * Documents du groupe
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Document", inversedBy="idGroupeGd")
     * @ORM\JoinTable(name="Groupe_Document",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_groupe_gd", referencedColumnName="id_groupe")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_document_gd", referencedColumnName="id_document")
     *   }
     * )
     */
    private $idDocumentGd;

    /**
     * Projets du groupe
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Projet", inversedBy="idGroupeGp")
     * @ORM\JoinTable(name="Groupe_Projet",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_groupe_gp", referencedColumnName="id_groupe")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_projet_gp", referencedColumnName="id_projet")
     *   }
     * )
     */
    private $idProjetGp;

    /**
     * Membres du groupe
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Utilisateur", mappedBy="idGroupeUg")
     */
    private $idUtilisateurUg;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idDocumentGd = new ArrayCollection();
        $this->idProjetGp = new ArrayCollection();
        $this->idUtilisateurUg = new ArrayCollection();
        $this->dateCreation = new \DateTime();
        $this->dateModification = new \DateTime();
    }

    /**
     * Get idGroupe
     *
     * @return integer
     */
    public function getIdGroupe()
    {
        return $this->idGroupe;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Groupe
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Groupe
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateModification
     *
     * @return \DateTime
     */
    public function getDateModification()
    {
        return $this->dateModification;
    }

    /**
     * Set dateModification
     *
     * @param \DateTime $dateModification
     *
     * @return Groupe
     */
    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    /**
     * Get idUtilisateurFkGroupe
     *
     * @return Utilisateur
     */
    public function getIdUtilisateurFkGroupe()
    {
        return $this->idUtilisateurFkGroupe;
    }

    /**
     * Set idUtilisateurFkGroupe
     *
     * @param $idUtilisateurFkGroupe
     *
     * @return Groupe
     */
    public function setIdUtilisateurFkGroupe($idUtilisateurFkGroupe)
    {
        $this->idUtilisateurFkGroupe = $idUtilisateurFkGroupe;

        return $this;
    }

    /**
     * Add idDocumentGd
     *
     * @param $idDocumentGd
     *
     * @return Groupe
     */
    public function addIdDocumentGd($idDocumentGd)
    {
        $this->idDocumentGd[] = $idDocumentGd;

        return $this;
    }

    /**
     * Remove idDocumentGd
     *
     * @param $idDocumentGd
     */
    public function removeIdDocumentGd($idDocumentGd)
    {
        $this->idDocumentGd->removeElement($idDocumentGd);
    }

    /**
     * Get idDocumentGd
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdDocumentGd()
    {
        return $this->idDocumentGd;
    }

    /**
     * Add idProjetGp
     *
     * @param $idProjetGp
     *
     * @return Groupe
     */
    public function addIdProjetGp($idProjetGp)
    {
        $this->idProjetGp[] = $idProjetGp;

        return $this;
    }

    /**
     * Remove idProjetGp
     *
     * @param $idProjetGp
     */
    public function removeIdProjetGp($idProjetGp)
    {
        $this->idProjetGp->removeElement($idProjetGp);
    }

    /**
     * Get idProjetGp
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdProjetGp()
    {
        return $this->idProjetGp;
    }

    /**
     * Add idUtilisateurUg
     *
     * @param $idUtilisateurUg
     *
     * @return Groupe
     */
    public function addIdUtilisateurUg($idUtilisateurUg)
    {
        $this->idUtilisateurUg[] = $idUtilisateurUg;

        return $this;
    }

    /**
     * Remove idUtilisateurUg
     *
     * @param $idUtilisateurUg
     */
    public function removeIdUtilisateurUg($idUtilisateurUg)
    {
        $this->idUtilisateurUg->removeElement($idUtilisateurUg);
    }

    /**
     * Get idUtilisateurUg
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdUtilisateurUg()
    {
        return $this->idUtilisateurUg;
    }

    /**
     * Transforme le groupe en tableau associatif
     * @return array
     */
    public function toArray()
    {
        $array = array(
            "idGroupe" => $this->idGroupe,
            "nom" => $this->nom,
            "proprietaire" => $this->idUtilisateurFkGroupe->getIdUtilisateur()
        );
        return $array;
    }
}
