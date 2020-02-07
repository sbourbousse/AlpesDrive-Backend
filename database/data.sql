delete from cp;

load data local infile './laposte_hexasmal.csv' into table cp 
	CHARACTER SET utf8 
	fields terminated by ';'
	lines terminated by '\n';

load data local infile './utilisateur.csv' into table utilisateur 
	CHARACTER SET utf8 
	fields terminated by ';'
	lines terminated by '\n';

load data local infile './localisation.csv' into table localisation 
	CHARACTER SET utf8 
	fields terminated by ';'
	lines terminated by '\n';

load data local infile './point_relais_type.csv' into table point_relais_type 
	CHARACTER SET utf8 
	fields terminated by ';'
	lines terminated by '\n';

load data local infile './point_relais.csv' into table point_relais 
	CHARACTER SET utf8 
	fields terminated by ';'
	lines terminated by '\n';

load data local infile './producteur.csv' into table producteur 
	CHARACTER SET utf8
	fields terminated by ';'
	lines terminated by '\n';

load data local infile './client.csv' into table client 
	CHARACTER SET utf8 
	fields terminated by ';'
	lines terminated by '\n';

load data local infile './choisir.csv' into table choisir 
	CHARACTER SET utf8 
	fields terminated by ';'
	lines terminated by '\n';

load data local infile './proposer.csv' into table proposer 
	CHARACTER SET utf8 
	fields terminated by ';'
	lines terminated by '\n';

load data local infile './unite.csv' into table unite 
	CHARACTER SET utf8 
	fields terminated by ';'
	lines terminated by '\n';

load data local infile './categorie.csv' into table categorie 
	CHARACTER SET utf8 
	fields terminated by ';'
	lines terminated by '\n';

load data local infile './produit.csv' into table produit 
	CHARACTER SET utf8 
	fields terminated by ';'
	lines terminated by '\n';

load data local infile './variete.csv' into table variete 
	CHARACTER SET utf8 
	fields terminated by ';'
	lines terminated by '\n';

load data local infile './vente.csv' into table vente 
	CHARACTER SET utf8 
	fields terminated by ';'
	lines terminated by '\n';

