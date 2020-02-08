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
                               `utilisateurDateInscription` date
);

CREATE TABLE `localisation` (
                                `localisationId` int PRIMARY KEY,
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

ALTER TABLE `horaire` ADD FOREIGN KEY (`jourId`) REFERENCES `jour` (`jourId`);

ALTER TABLE `horaire` ADD FOREIGN KEY (`pointRelaisId`) REFERENCES `point_relais` (`pointRelaisId`);

ALTER TABLE `point_relais` ADD FOREIGN KEY (`localisationId`) REFERENCES `localisation` (`localisationId`);

ALTER TABLE `point_relais` ADD FOREIGN KEY (`entrepriseId`) REFERENCES `entreprise` (`entrepriseId`);

ALTER TABLE `point_relais` ADD FOREIGN KEY (`utilisateurId`) REFERENCES `utilisateur` (`utilisateurId`);

ALTER TABLE `point_relais` ADD FOREIGN KEY (`pointRelaisTypeId`) REFERENCES `point_relais_type` (`pointRelaisTypeId`);

ALTER TABLE `producteur` ADD FOREIGN KEY (`localisationId`) REFERENCES `localisation` (`localisationId`);

ALTER TABLE `producteur` ADD FOREIGN KEY (`entrepriseId`) REFERENCES `entreprise` (`entrepriseId`);

ALTER TABLE `producteur` ADD FOREIGN KEY (`utilisateurId`) REFERENCES `utilisateur` (`utilisateurId`);

ALTER TABLE `client` ADD FOREIGN KEY (`localisationId`) REFERENCES `localisation` (`localisationId`);

ALTER TABLE `client` ADD FOREIGN KEY (`utilisateurId`) REFERENCES `utilisateur` (`utilisateurId`);

ALTER TABLE `choisir` ADD FOREIGN KEY (`clientId`) REFERENCES `client` (`clientId`);

ALTER TABLE `choisir` ADD FOREIGN KEY (`pointRelaisId`) REFERENCES `point_relais` (`pointRelaisId`);

ALTER TABLE `proposer` ADD FOREIGN KEY (`prodId`) REFERENCES `producteur` (`prodId`);

ALTER TABLE `proposer` ADD FOREIGN KEY (`pointRelaisId`) REFERENCES `point_relais` (`pointRelaisId`);

ALTER TABLE `produit` ADD FOREIGN KEY (`uniteId`) REFERENCES `unite` (`uniteId`);

ALTER TABLE `produit` ADD FOREIGN KEY (`categorieId`) REFERENCES `categorie` (`categorieId`);

ALTER TABLE `variete` ADD FOREIGN KEY (`produitId`) REFERENCES `produit` (`produitId`);

ALTER TABLE `vente` ADD FOREIGN KEY (`prodId`) REFERENCES `producteur` (`prodId`);

ALTER TABLE `vente` ADD FOREIGN KEY (`varieteId`) REFERENCES `variete` (`varieteId`);

ALTER TABLE `article` ADD FOREIGN KEY (`venteId`) REFERENCES `vente` (`venteId`);

ALTER TABLE `article` ADD FOREIGN KEY (`clientId`) REFERENCES `client` (`clientId`);

ALTER TABLE `article` ADD FOREIGN KEY (`pointRelaisId`) REFERENCES `point_relais` (`pointRelaisId`);

ALTER TABLE `article` ADD FOREIGN KEY (`panierId`) REFERENCES `panier` (`panierId`);
