--Créé par Sylvain Bourbousse le 20/10/2019

--Supprimer la base de donnée
drop database if exists Alpes_Drive;

--Créer la base de données
create database Alpes_Drive;

--Acceder à la base de donnée
use Alpes_Drive;

--Création des tables
source tables.sql;