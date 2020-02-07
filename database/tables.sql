CREATE TABLE `parametre` (
  `version` float
);

CREATE TABLE `cp` (
  `codeINSEECP` char(5) PRIMARY KEY,
  `nomCP` varchar(50),
  `codePostalCP` char(5),
  `libAcheminementCP` varchar(50),
  `ancienneCommuneCP` varchar(30),
  `coorGPSCP` varchar(40)
);

CREATE TABLE `utilisateur` (
  `utilisateurId` int PRIMARY KEY,
  `utilisateurMail` varchar(128),
  `utilisateurMotDePasse` char(32),
  `utilisateurVerifie` bool,
  `utilisateurCleMail` int,
  `dateInscription` date
);

CREATE TABLE `localisation` (
  `localisationId` int,
  `localisationLatitude` double,
  `localisationLongitude` double
);

CREATE TABLE `entreprise` (
  `entrepriseId` char(14) PRIMARY KEY,
  `entrepriseLibelle` varchar(128),
  `entrepriseIBAN` char(34)
);

CREATE TABLE `jour` (
  `jourId` tinyint PRIMARY KEY,
  `jourLibelle` varchar(20)
);

CREATE TABLE `horaire` (
  `horaireId` int PRIMARY KEY,
  `horaireOuvertureMatin` time,
  `horaireFermeturMatin` time,
  `horaireOuvertureApresMidi` time,
  `horaireFermetureApresMidi` time,
  `pointRelaisId` int,
  `jourId` tinyint
);

CREATE TABLE `point_relais_type` (
  `pointRelaisTypeId` smallint PRIMARY KEY,
  `pointRelaisLibelle` varchar(64)
);

CREATE TABLE `point_relais` (
  `pointRelaisId` int PRIMARY KEY,
  `pointRelaisPrenomGerant` varchar(32),
  `pointRelaisNomGerant` varchar(32),
  `pointRelaisTel` char(10),
  `pointRelaisAdresse` varchar(128),
  `pointRelaisVille` varchar(128),
  `pointRelaisCodePostal` char(5),
  `supprime` bool,
  `pointRelaisTypeId` smallint,
  `localisationId` int,
  `entrepriseId` char(14),
  `utilisateurId` int
);

CREATE TABLE `producteur` (
  `prodId` int PRIMARY KEY,
  `prodPrenom` varchar(64),
  `prodNom` varchar(64),
  `prodTel` char(10),
  `prodAdresse` varchar(128),
  `prodVille` varchar(128),
  `prodCodePostal` char(5),
  `supprime` bool,
  `localisationId` int,
  `entrepriseId` char(14),
  `utilisateurId` int
);

CREATE TABLE `client` (
  `clientId` int PRIMARY KEY,
  `clientPrenom` varchar(64),
  `clientNom` varchar(64),
  `clientTel` char(10),
  `clientAdresse` varchar(128),
  `clientVille` varchar(128),
  `clientCodePostal` char(5),
  `supprime` bool,
  `localisationId` int,
  `utilisateurId` int
);

CREATE TABLE `choisir` (
  `clientId` int,
  `pointRelaisId` int,
  PRIMARY KEY (`clientId`, `pointRelaisId`)
);

CREATE TABLE `proposer` (
  `pointRelaisId` int,
  `prodId` int,
  PRIMARY KEY (`pointRelaisId`, `prodId`)
);

CREATE TABLE `unite` (
  `uniteId` tinyint PRIMARY KEY,
  `uniteLibelle` varchar(32),
  `uniteLettre` varchar(5),
  `uniteQuantiteVente` smallint
);

CREATE TABLE `categorie` (
  `categorieId` smallint PRIMARY KEY,
  `categorieLibelle` varchar(128),
  `categorieImage` varchar(256)
);

CREATE TABLE `produit` (
  `produitId` smallint PRIMARY KEY,
  `produitLibelle` varchar(128),
  `produitImage` varchar(256),
  `uniteId` tinyint,
  `categorieId` smallint
);

CREATE TABLE `variete` (
  `varieteId` int PRIMARY KEY,
  `varieteLibelle` varchar(128),
  `produitId` smallint
);

CREATE TABLE `vente` (
  `venteId` int PRIMARY KEY,
  `prix` float,
  `quantite` smallint,
  `dateAjout` datetime,
  `dateLimiteVente` date,
  `valide` bool,
  `prodId` int,
  `varieteId` int
);

CREATE TABLE `article` (
  `quantite` smallint,
  `panierDateAjout` datetime,
  `panierDateRecuperer` datetime,
  `clientId` int,
  `venteId` int,
  `pointRelaisId` int,
  `panierId` int
);

CREATE TABLE `panier` (
  `panierId` int PRIMARY KEY,
  `panierRegle` bool,
  `panierDateRegle` datetime
);

ALTER TABLE `jour` ADD FOREIGN KEY (`jourId`) REFERENCES `horaire` (`jourId`);

ALTER TABLE `point_relais` ADD FOREIGN KEY (`pointRelaisId`) REFERENCES `horaire` (`pointRelaisId`);

ALTER TABLE `localisation` ADD FOREIGN KEY (`localisationId`) REFERENCES `point_relais` (`localisationId`);

ALTER TABLE `entreprise` ADD FOREIGN KEY (`entrepriseId`) REFERENCES `point_relais` (`entrepriseId`);

ALTER TABLE `utilisateur` ADD FOREIGN KEY (`utilisateurId`) REFERENCES `point_relais` (`utilisateurId`);

ALTER TABLE `point_relais_type` ADD FOREIGN KEY (`pointRelaisTypeId`) REFERENCES `point_relais` (`pointRelaisTypeId`);

ALTER TABLE `localisation` ADD FOREIGN KEY (`localisationId`) REFERENCES `producteur` (`localisationId`);

ALTER TABLE `entreprise` ADD FOREIGN KEY (`entrepriseId`) REFERENCES `producteur` (`entrepriseId`);

ALTER TABLE `utilisateur` ADD FOREIGN KEY (`utilisateurId`) REFERENCES `producteur` (`utilisateurId`);

ALTER TABLE `localisation` ADD FOREIGN KEY (`localisationId`) REFERENCES `client` (`localisationId`);

ALTER TABLE `utilisateur` ADD FOREIGN KEY (`utilisateurId`) REFERENCES `client` (`utilisateurId`);

ALTER TABLE `client` ADD FOREIGN KEY (`clientId`) REFERENCES `choisir` (`clientId`);

ALTER TABLE `point_relais` ADD FOREIGN KEY (`pointRelaisId`) REFERENCES `choisir` (`pointRelaisId`);

ALTER TABLE `producteur` ADD FOREIGN KEY (`prodId`) REFERENCES `proposer` (`prodId`);

ALTER TABLE `point_relais` ADD FOREIGN KEY (`pointRelaisId`) REFERENCES `proposer` (`pointRelaisId`);

ALTER TABLE `unite` ADD FOREIGN KEY (`uniteId`) REFERENCES `produit` (`uniteId`);

ALTER TABLE `categorie` ADD FOREIGN KEY (`categorieId`) REFERENCES `produit` (`categorieId`);

ALTER TABLE `produit` ADD FOREIGN KEY (`produitId`) REFERENCES `variete` (`produitId`);

ALTER TABLE `producteur` ADD FOREIGN KEY (`prodId`) REFERENCES `vente` (`prodId`);

ALTER TABLE `variete` ADD FOREIGN KEY (`varieteId`) REFERENCES `vente` (`varieteId`);

ALTER TABLE `vente` ADD FOREIGN KEY (`venteId`) REFERENCES `article` (`venteId`);

ALTER TABLE `client` ADD FOREIGN KEY (`clientId`) REFERENCES `article` (`clientId`);

ALTER TABLE `point_relais` ADD FOREIGN KEY (`pointRelaisId`) REFERENCES `article` (`pointRelaisId`);

ALTER TABLE `panier` ADD FOREIGN KEY (`panierId`) REFERENCES `article` (`panierId`);
