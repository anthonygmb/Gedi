-- ==========================
-- Base de données : GEDI
-- ==========================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ====================================================================================================
-- Structure de la table Utilisateur
--

CREATE TABLE IF NOT EXISTS Utilisateur (
	id_utilisateur INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id utilisateur', -- id de l'utilisateur
	login VARCHAR(50) NOT NULL COMMENT 'login utilisateur', -- login utilisateur, correspond à l'adresse email
	mot_de_passe VARCHAR(255) NOT NULL COMMENT 'mot de passe hashé', -- mot de passe hashé
	nom VARCHAR(50) NOT NULL COMMENT 'nom utilisateur', -- nom de l'utilisateur
	prenom VARCHAR(50) NOT NULL COMMENT 'prénom utilisateur', -- prénom de l'utilisateur
	salt VARCHAR(23) NOT NULL COMMENT 'cle de sablage', -- clé de sablage du mot de passe
	role ENUM('ROLE_USER', 'ROLE_ADMIN') DEFAULT 'ROLE_USER' NOT NULL COMMENT 'role utilisateur', -- role utilisateur
	actif BOOLEAN DEFAULT false NOT NULL COMMENT 'actif', -- indique si l'utilisateur est actif
	date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'date de création utilisateur', -- date de création utilisateur
	date_modification TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'date de modification utilisateur', -- date de modification utilisateur
	CONSTRAINT pk_id_utilisateur PRIMARY KEY (id_utilisateur), -- contrainte de clé primaire sur id_utilisateur
	CONSTRAINT un_utilisateur UNIQUE (login) -- contrainte unicite sur login
) ENGINE=InnoDB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

DELIMITER $$
	CREATE TRIGGER trg_utilisateur_insert BEFORE INSERT ON Utilisateur
	FOR EACH ROW
		BEGIN
			-- trigger pour valider le format de l'adresse email
			IF NEW.login REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$' = 0
				THEN
				SIGNAL SQLSTATE '44000'
				SET MESSAGE_TEXT = 'adresse email non valide';
			END IF;
			-- trigger de mise en forme du nom et du prénom, première lettre en majuscule et le reste en minuscule
   			 SET NEW.nom = CONCAT(UCASE(LEFT(NEW.nom, 1)), SUBSTRING(LOWER(NEW.nom), 2));
   			 SET NEW.prenom = CONCAT(UCASE(LEFT(NEW.prenom, 1)), SUBSTRING(LOWER(NEW.prenom), 2));
			-- trigger de génération aléatoire de clé salt
			SET NEW.salt = SUBSTR(CONCAT(MD5(RAND()),MD5(RAND())),1,23);
		END $$

	CREATE TRIGGER trg_utilisateur_update BEFORE UPDATE ON Utilisateur
	FOR EACH ROW
		BEGIN
			-- trigger pour valider le format de l'adresse email
			IF NEW.login REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$' = 0
				THEN
				SIGNAL SQLSTATE '44000'
				SET MESSAGE_TEXT = 'adresse email non valide';
			END IF;
			-- trigger de mise en forme du nom et du prénom, première lettre en majuscule et le reste en minuscule
   			 SET NEW.nom = CONCAT(UCASE(LEFT(NEW.nom, 1)), SUBSTRING(LOWER(NEW.nom), 2));
   			 SET NEW.prenom = CONCAT(UCASE(LEFT(NEW.prenom, 1)), SUBSTRING(LOWER(NEW.prenom), 2));
		END $$
	DELIMITER ;

-- ====================================================================================================
-- Structure de la table Projet
--

CREATE TABLE IF NOT EXISTS Projet (
	id_projet INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id projet', -- id projet
	nom VARCHAR(50) NOT NULL COMMENT 'nom du projet', -- nom du projet
	path VARCHAR(255) NOT NULL COMMENT 'chemin du projet', -- chemin du projet
	date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'date de création projet', -- date de création projet
	date_modification TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'date de modification projet', -- date de modification projet
	id_utilisateur_fk_projet INT(11) UNSIGNED DEFAULT NULL COMMENT 'propriétaire du projet', -- propriétaire du projet
	parent_id INT(11) UNSIGNED DEFAULT NULL COMMENT 'projet parent', -- projet parent
	CONSTRAINT pk_id_projet PRIMARY KEY (id_projet), -- contrainte de clé primaire sur id_projet
	CONSTRAINT fk_utilisateur_projet FOREIGN KEY (id_utilisateur_fk_projet) REFERENCES Utilisateur (id_utilisateur) ON DELETE CASCADE ON UPDATE CASCADE, -- contrainte de clé étrangère sur id_utilisateur de la table Utilisateur, quand un utilisateur est supprimé, ses projets sont supprimés
	CONSTRAINT un_path_titre_type_doc UNIQUE (path, nom) -- contrainte d'unicité sur path et nom
) ENGINE=InnoDB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

ALTER TABLE Projet ADD FOREIGN KEY (parent_id) REFERENCES Projet(id_projet);

-- ====================================================================================================
-- Structure de la table Document
--

CREATE TABLE IF NOT EXISTS Document (
	id_document INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id document', -- id document
	nom VARCHAR(50) NOT NULL COMMENT 'nom du document', -- nom du document
	type_doc ENUM('ai', 'css', 'html', 'psd', 'mp4', 'flac', 'js', 'doc', 'xls', 'ppt', 'docx', 'xlsx', 'pptx', 'pdf', 'odt', 'ods', 'odp', 'rtf', 'txt', 'wps', 'sxw', 'jpeg', 'jpg', 'png', 'gif', 'tiff', 'tif', 'bmp', 'mp3', 'wav', 'ogg', 'wma', 'mid', 'rar', 'ace', '7z', 'tar', 'bz2', 'tar.bz2', 'gz', 'tar.gz', 'avi', 'mov', 'mpg', 'mpa', 'asf', 'mp2', 'm2p', 'wmv', 'vob', 'aac') NOT NULL COMMENT 'type du document', -- type du document
	date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'date de création document', -- date de création du document
	date_modification TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'date de modification document', -- date de modification du document
	tag VARCHAR(255) DEFAULT NULL COMMENT 'tags du document', -- tags du document
	resume TEXT DEFAULT NULL COMMENT 'resumé du document', -- résumé du document
	path VARCHAR(255) NOT NULL COMMENT 'chemin du document', -- chemin du document
	nb_download INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'nombre de téléchargements', -- nombre de téléchargements
	id_projet_fk_document INT(11) UNSIGNED DEFAULT NULL COMMENT 'foreign key id_projet', -- clé étrangère sur id_projet
	id_utilisateur_fk_document INT(11) UNSIGNED NOT NULL COMMENT 'propriétaire du document', -- propriétaire du document
	CONSTRAINT pk_id_document PRIMARY KEY (id_document), -- contrainte de clé primaire sur id_document
	CONSTRAINT fk_projet_document FOREIGN KEY (id_projet_fk_document) REFERENCES Projet (id_projet) ON DELETE CASCADE ON UPDATE CASCADE, -- contrainte de clé etrangère sur id_projet de la table id_projet, quand on supprime un projet, on supprime les documents contenus
	CONSTRAINT fk_utilisateur_document FOREIGN KEY (id_utilisateur_fk_document) REFERENCES Utilisateur (id_utilisateur) ON DELETE CASCADE ON UPDATE CASCADE, -- contrainte de cle étrangère sur id utilisateur, quand on supprime un utilisateur, on supprime les documents possédés
	CONSTRAINT un_path_titre_type_doc UNIQUE (path, nom, type_doc) -- contrainte d'unicité sur path, titre et type_doc
) ENGINE=InnoDB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

DELIMITER $$
	CREATE TRIGGER trg_document_insert BEFORE INSERT ON Document
	FOR EACH ROW
		BEGIN
		-- trigger de mise en forme du nom, première lettre en majuscule et le reste en minuscule
    SET NEW.nom = CONCAT(UCASE(LEFT(NEW.nom, 1)), SUBSTRING(LOWER(NEW.nom), 2));
		-- trigger de mise en forme du type, tout en minuscule
	SET NEW.type_doc = LOWER(NEW.type_doc);
	END $$

	CREATE TRIGGER trg_document_update BEFORE UPDATE ON Document
	FOR EACH ROW
		BEGIN
		-- trigger de mise en forme du nom, première lettre en majuscule et le reste en minuscule
    SET NEW.nom = CONCAT(UCASE(LEFT(NEW.nom, 1)), SUBSTRING(LOWER(NEW.nom), 2));
	-- trigger de mise en forme du type, tout en minuscule
	SET NEW.type_doc = LOWER(NEW.type_doc);
	END $$
	DELIMITER ;

-- ====================================================================================================
-- Structure de la table Groupe
--

CREATE TABLE IF NOT EXISTS Groupe (
	id_groupe INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id groupe', -- id groupe
	nom VARCHAR(50) NOT NULL COMMENT 'nom du groupe', -- nom du groupe
	date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'date de création groupe', -- date de création groupe
	date_modification TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'date de modification groupe', -- date de modification groupe
	id_utilisateur_fk_groupe INT(11) UNSIGNED NOT NULL COMMENT 'propriétaire du groupe', -- propriétaire du groupe
	CONSTRAINT pk_id_groupe PRIMARY KEY (id_groupe), -- contrainte de clé primaire sur id_groupe
	CONSTRAINT fk_utilisateur_groupe_1 FOREIGN KEY (id_utilisateur_fk_groupe) REFERENCES Utilisateur (id_utilisateur) ON DELETE CASCADE ON UPDATE CASCADE -- contrainte de cle étrangère sur id utilisateur, quand on supprime un utilisateur, on supprime les groupes possédés
) ENGINE=InnoDB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- ====================================================================================================
-- Structure de la table Utilisateur_Groupe
--

CREATE TABLE IF NOT EXISTS Utilisateur_Groupe (
	id_utilisateur_ug INT(11) UNSIGNED NOT NULL COMMENT 'cle etrangere sur utilisateur', -- clé étrangère sur utilisateur
	id_groupe_ug INT(11) UNSIGNED NOT NULL COMMENT 'cle etrangere sur groupe', -- clé étrangère sur groupe
	CONSTRAINT pk_id_utilisateur_id_groupe PRIMARY KEY (id_utilisateur_ug, id_groupe_ug), -- contrainte de cle primaire sur id utilisateur et id groupe
	CONSTRAINT fk_utilisateur_groupe FOREIGN KEY (id_utilisateur_ug) REFERENCES Utilisateur (id_utilisateur) ON DELETE CASCADE ON UPDATE CASCADE, -- contrainte de cle étrangère sur id utilisateur
	CONSTRAINT fk_groupe_utilisateur FOREIGN KEY (id_groupe_ug) REFERENCES Groupe (id_groupe) ON DELETE CASCADE ON UPDATE CASCADE -- contrainte de cle étrangère sur id groupe
) ENGINE=InnoDB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- ====================================================================================================
-- Structure de la table Groupe_Document
--

CREATE TABLE IF NOT EXISTS Groupe_Document (
	habilitation ENUM('ADMIN', 'USER', 'GUEST') DEFAULT 'GUEST' NOT NULL COMMENT 'habilitation d\'un groupe sur un document', -- habilitation d'un groupe sur un document
	id_groupe_gd INT(11) UNSIGNED NOT NULL COMMENT 'cle etrangere sur groupe', -- clé étrangère sur groupe
	id_document_gd INT(11) UNSIGNED NOT NULL COMMENT 'cle etrangere sur document', -- clé étrangère sur document
	CONSTRAINT pk_id_groupe_id_document PRIMARY KEY (id_groupe_gd, id_document_gd), -- contrainte de cle primaire sur id groupe et id document
	CONSTRAINT fk_groupe_document FOREIGN KEY (id_groupe_gd) REFERENCES Groupe (id_groupe) ON DELETE CASCADE ON UPDATE CASCADE, -- contrainte de cle étrangère sur id groupe
	CONSTRAINT fk_document_groupe FOREIGN KEY (id_document_gd) REFERENCES Document (id_document) ON DELETE CASCADE ON UPDATE CASCADE -- contrainte de cle étrangère sur id document
) ENGINE=InnoDB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- ====================================================================================================
-- Structure de la table Document_Utilisateur
--

CREATE TABLE IF NOT EXISTS Document_Utilisateur (
	habilitation ENUM('ADMIN', 'USER', 'GUEST') DEFAULT 'GUEST' NOT NULL COMMENT 'habilitation d\'un utilisateur sur un document', -- habilitation d'un utilisateur sur un document
	id_document_du INT(11) UNSIGNED NOT NULL COMMENT 'cle etrangere sur document', -- clé étrangère sur document
	id_utilisateur_du INT(11) UNSIGNED NOT NULL COMMENT 'cle etrangere sur utilisateur', -- clé étrangère sur utilisateur
	CONSTRAINT pk_id_utilisateur_id_document PRIMARY KEY (id_utilisateur_du, id_document_du), -- contrainte de cle primaire sur id utilisateur et id document
	CONSTRAINT fk_utilisateur_document2 FOREIGN KEY (id_utilisateur_du) REFERENCES Utilisateur (id_utilisateur) ON DELETE CASCADE ON UPDATE CASCADE, -- contrainte de cle étrangère sur id utilisateur
	CONSTRAINT fk_document_utilisateur2 FOREIGN KEY (id_document_du) REFERENCES Document (id_document) ON DELETE CASCADE ON UPDATE CASCADE -- contrainte de cle étrangère sur id document
) ENGINE=InnoDB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- ====================================================================================================
-- Structure de la table Groupe_Projet
--

CREATE TABLE IF NOT EXISTS Groupe_Projet (
	habilitation ENUM('ADMIN', 'USER', 'GUEST') DEFAULT 'GUEST' NOT NULL COMMENT 'habilitation d\'un groupe sur un projet', -- habilitation d'un groupe sur un projet
	id_groupe_gp INT(11) UNSIGNED NOT NULL COMMENT 'cle etrangere sur groupe', -- clé étrangère sur groupe
	id_projet_gp INT(11) UNSIGNED NOT NULL COMMENT 'cle etrangere sur projet', -- clé étrangère sur projet
	CONSTRAINT pk_id_groupe_id_projet PRIMARY KEY (id_groupe_gp, id_projet_gp), -- contrainte de cle primaire sur id groupe et id projet
	CONSTRAINT fk_groupe_projet FOREIGN KEY (id_groupe_gp) REFERENCES Groupe (id_groupe) ON DELETE CASCADE ON UPDATE CASCADE, -- contrainte de cle étrangère sur id groupe
	CONSTRAINT fk_projet_groupe FOREIGN KEY (id_projet_gp) REFERENCES Projet (id_projet) ON DELETE CASCADE ON UPDATE CASCADE -- contrainte de cle étrangère sur id projet
) ENGINE=InnoDB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
