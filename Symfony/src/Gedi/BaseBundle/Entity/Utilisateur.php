<?php

namespace Gedi\BaseBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Utilisateur
 *
 * @ORM\Table(name="Utilisateur", uniqueConstraints={@ORM\UniqueConstraint(name="un_utilisateur", columns={"login"})})
 * @ORM\Entity
 */
class Utilisateur implements UserInterface
{
    /**
     * Id utilisateur
     * @var integer
     *
     * @ORM\Column(name="id_utilisateur", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idUtilisateur;

    /**
     * login de l'utilisateur
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=50, nullable=false)
     */
    private $username;

    /**
     * Mot de passe utilisateur, crypté en bcrypt
     * @var string
     *
     * @ORM\Column(name="mot_de_passe", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * Nom de l'utilisateur
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50, nullable=false)
     */
    private $nom;

    /**
     * Prenom de l'utilisateur
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=50, nullable=false)
     */
    private $prenom;

    /**
     * Clé de salage de mot de passe
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=23, nullable=true)
     */
    private $salt;

    /**
     * Role de l'utilisateur
     * @var string
     *
     * @ORM\Column(name="role", type="string", nullable=false)
     */
    private $role = 'ROLE_USER';

    /**
     * Boolean servant à dire si l'utilisateur est activé ou pas
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean", nullable=false)
     */
    private $actif = false;

    /**
     * Date de création de l'utilisateur
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="datetime", nullable=false)
     */
    private $dateCreation = 'CURRENT_TIMESTAMP';

    /**
     * Date de modification de l'utilisateur
     * @var \DateTime
     *
     * @ORM\Column(name="date_modification", type="datetime", nullable=true)
     */
    private $dateModification;

    /**
     * Documents de l'utilisateur
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Document", inversedBy="idUtilisateurDu", cascade={"all"})
     * @ORM\JoinTable(name="Document_Utilisateur",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_utilisateur_du", referencedColumnName="id_utilisateur")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_document_du", referencedColumnName="id_document")
     *   }
     * )
     */
    private $idDocumentDu;

    /**
     * Groupes de l'utilisateur
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Groupe", inversedBy="idUtilisateurUg", cascade={"all"})
     * @ORM\JoinTable(name="Utilisateur_Groupe",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_utilisateur_ug", referencedColumnName="id_utilisateur")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_groupe_ug", referencedColumnName="id_groupe")
     *   }
     * )
     */
    private $idGroupeUg;

    /**
     * Projets dont l'utilisateur est propriétaire
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Projet", mappedBy="idUtilisateurFkProjet", cascade={"all"})
     */
    private $idUtilisateurFkProjet;

    /**
     * Groupes dont l'utilisateur est propriétaire
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Groupe", mappedBy="idUtilisateurFkGroupe", cascade={"all"})
     */
    private $idUtilisateurFkGroupe;

    /**
     * Documents dont l'utilisateur est propriétaire
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Document", mappedBy="idUtilisateurFkDocument", cascade={"all"})
     */
    private $idUtilisateurFkDocument;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idDocumentDu = new ArrayCollection();
        $this->idGroupeUg = new ArrayCollection();
        $this->idUtilisateurFkDocument = new ArrayCollection();
        $this->idUtilisateurFkProjet = new ArrayCollection();
        $this->dateCreation = new \DateTime();
        $this->dateModification = new \DateTime();
    }

    /**
     * Get idUtilisateur
     *
     * @return integer
     */
    public function getIdUtilisateur()
    {
        return $this->idUtilisateur;
    }

    /**
     * @param int $idUtilisateur
     */
    public function setIdUtilisateur($idUtilisateur)
    {
        $this->idUtilisateur = $idUtilisateur;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return Utilisateur
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Utilisateur
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
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
     * @return Utilisateur
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Utilisateur
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return Utilisateur
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     * @return array (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return array($this->getRole());
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return Utilisateur
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Utilisateur
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

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
     * @return Utilisateur
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
     * @return Utilisateur
     */
    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    /**
     * Add idDocumentDu
     *
     * @param $idDocumentDu
     *
     * @return Utilisateur
     */
    public function addIdDocumentDu($idDocumentDu)
    {
        $this->idDocumentDu[] = $idDocumentDu;

        return $this;
    }

    /**
     * Remove idDocumentDu
     *
     * @param $idDocumentDu
     */
    public function removeIdDocumentDu($idDocumentDu)
    {
        $this->idDocumentDu->removeElement($idDocumentDu);
    }

    /**
     * Get idDocumentDu
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdDocumentDu()
    {
        return $this->idDocumentDu;
    }

    /**
     * Add idGroupeUg
     *
     * @param $idGroupeUg
     *
     * @return Utilisateur
     */
    public function addIdGroupeUg($idGroupeUg)
    {
        $this->idGroupeUg[] = $idGroupeUg;

        return $this;
    }

    /**
     * Remove idGroupeUg
     *
     * @param $idGroupeUg
     */
    public function removeIdGroupeUg($idGroupeUg)
    {
        $this->idGroupeUg->removeElement($idGroupeUg);
    }

    /**
     * Get idGroupeUg
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdGroupeUg()
    {
        return $this->idGroupeUg;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdUtilisateurFkProjet()
    {
        return $this->idUtilisateurFkProjet;
    }

    /**
     * Add idUtilisateurFkProjet
     *
     * @param $idUtilisateurFkProjet
     *
     * @return Utilisateur
     */
    public function addIdUtilisateurFkProjet($idUtilisateurFkProjet)
    {
        $this->idUtilisateurFkProjet[] = $idUtilisateurFkProjet;

        return $this;
    }

    /**
     * Remove idUtilisateurFkProjet
     *
     * @param $idUtilisateurFkProjet
     */
    public function removeIdUtilisateurFkProjet($idUtilisateurFkProjet)
    {
        $this->idUtilisateurFkProjet->removeElement($idUtilisateurFkProjet);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdUtilisateurFkGroupe()
    {
        return $this->idUtilisateurFkGroupe;
    }

    /**
     * Add idUtilisateurFkGroupe
     *
     * @param $idUtilisateurFkGroupe
     *
     * @return Utilisateur
     */
    public function addIdUtilisateurFkGroupe($idUtilisateurFkGroupe)
    {
        $this->idUtilisateurFkGroupe[] = $idUtilisateurFkGroupe;

        return $this;
    }

    /**
     * Remove idUtilisateurFkGroupe
     *
     * @param $idUtilisateurFkGroupe
     */
    public function removeIdUtilisateurFkGroupe($idUtilisateurFkGroupe)
    {
        $this->idUtilisateurFkGroupe->removeElement($idUtilisateurFkGroupe);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdUtilisateurFkDocument()
    {
        return $this->idUtilisateurFkDocument;
    }

    /**
     * Add idUtilisateurFkDocument
     *
     * @param $IdUtilisateurFkDocument
     *
     * @return Utilisateur
     */
    public function addIdUtilisateurFkDocument($IdUtilisateurFkDocument)
    {
        $this->idUtilisateurFkDocument[] = $IdUtilisateurFkDocument;

        return $this;
    }

    /**
     * Remove idUtilisateurFkDocument
     *
     * @param $IdUtilisateurFkDocument
     */
    public function removeIdUtilisateurFkDocument($IdUtilisateurFkDocument)
    {
        $this->idUtilisateurFkDocument->removeElement($IdUtilisateurFkDocument);
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * Transforme l'utilisateur en tableau associatif
     * @return array
     */
    public function toArray()
    {
        $array = array(
            "idUtilisateur" => $this->idUtilisateur,
            "username" => $this->username,
            "password" => $this->password,
            "nom" => $this->nom,
            "prenom" => $this->prenom,
            "actif" => $this->actif
        );
        return $array;
    }
}
