CREATE DATABASE cuicui DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE cuicui;

CREATE TABLE message (
  IdMessage bigint NOT NULL,
  IdExpediteur bigint NOT NULL,
  IdDestinataire bigint NOT NULL,
  DateMessage datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  Message varchar(280) NOT NULL,
  PRIMARY KEY (IdMessage)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO message (IdMessage, IdExpediteur, IdDestinataire, DateMessage, Message) VALUES
(1, 2, 3, '2026-02-15 14:50:00', 'Bonjour, je tente de faire fonctionner cette super application que nous développons en LO07. '),
(2, 3, 2, '2026-02-15 14:53:00', 'Super tout fonctionne ! Enfin je parle de la partie corrigée par nos enseignants ! '),
(3, 1, 2, '2026-02-19 09:54:00', 'Douglas ADAMS (dougadams) aimerait être ami avec vous !'),
(4, 1, 2, '2026-02-19 09:55:00', 'Jean-Claude DUSSE (jcdusse) aimerait être ami avec vous !'),
(5, 1, 3, '2026-03-05 13:27:42', 'John DOE (johndoe) aimerait être ami avec vous.'),
(6, 1, 8, '2026-03-05 13:28:35', 'Jean-Claude DUSSE (jcdusse) aimerait être ami avec vous.'),
(7, 3, 4, '2026-04-27 14:40:21', 'Salut Doug, peut-on se voir cet AM ?');

CREATE TABLE relation (
  IdRelation bigint NOT NULL,
  IdDemandeur bigint NOT NULL,
  IdAmi bigint NOT NULL,
  Date datetime NOT NULL,
  RelationAccepte tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (IdRelation),
  UNIQUE (IdDemandeur,IdAmi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO relation (IdRelation, IdDemandeur, IdAmi, `Date`, RelationAccepte) VALUES
(1, 2, 3, '2026-02-14 09:33:05', 1),
(2, 3, 2, '2026-02-14 11:22:15', 1),
(3, 3, 4, '2026-02-15 14:45:00', 1),
(4, 3, 5, '2026-02-15 18:11:00', 1),
(5, 5, 3, '2026-02-15 18:43:00', 1),
(6, 6, 5, '2026-02-16 14:23:00', 1),
(7, 4, 3, '2026-02-16 20:27:07', 1),
(8, 5, 6, '2026-02-16 22:13:19', 1),
(9, 4, 2, '2026-02-19 09:53:54', 0),
(10, 5, 2, '2026-02-19 09:54:55', 0),
(11, 9, 4, '2026-02-20 11:19:24', 1),
(12, 4, 9, '2026-02-22 15:20:19', 1),
(13, 8, 6, '2026-02-23 16:17:30', 1),
(14, 6, 8, '2026-02-25 19:11:51', 1),
(15, 9, 3, '2026-03-05 13:27:41', 0),
(16, 5, 8, '2026-03-05 13:28:35', 0);

CREATE TABLE utilisateur (
  IdUtilisateur bigint NOT NULL,
  Sexe char(1) NOT NULL,
  Nom varchar(100) NOT NULL,
  Prenom varchar(100) NOT NULL,
  Mail varchar(250) NOT NULL,
  Telephone varchar(20) DEFAULT NULL,
  Pseudo varchar(10) NOT NULL,
  Password varchar(255) NOT NULL,
  AboNewsletter tinyint(1) NOT NULL DEFAULT '0',
  Commentaire text,
  PRIMARY KEY (IdUtilisateur),
  UNIQUE (Pseudo),
  UNIQUE (Mail)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO utilisateur (IdUtilisateur, Sexe, Nom, Prenom, Mail, Telephone, Pseudo, `Password`, AboNewsletter, Commentaire) VALUES
(1, 'H', 'Administrateur', 'Administrateur', 'admin@univ-reims.fr', '03 26 91 30 00', 'admin', '$2y$10$nAxwKHv2rOUNc8/I1EyGNe5AZg0rli1ifImdIaSmsoA9zHItV6z2q', 0, NULL),
(2, 'H', 'Simpson', 'Bart', 'bartsim@springfield.net', NULL, 'bartsim', '$2y$10$fKop0Kf5c3Dpn7dYz6yYQeGOvx3wFE2Iqj2ckmuUjSkt/Hn6KiGru', 1, 'Ay, caramba!'),
(3, 'F', 'Stark', 'Arya', 'arya@winterfell.com', NULL, 'faceless', '$2y$10$iB4T4o3g5mCrrU.FVg9BY.SV0s4MxjlZ4m3c//0c2OiBNoSNcnXQ.', 1, 'Winter is coming !'),
(4, 'H', 'Adams', 'Douglas', 'adams@galaxy.com', NULL, 'dougadams', '$2y$10$BK9Of4ovwzD/7Ft9.DxrHeQnWyLdm23q4WaCnmqbpxYWs7mtozPEG', 0, NULL),
(5, 'H', 'Dusse', 'Jean-Claude', 'jc@clubmed.com', '+33 1 7892 4005 ', 'jcdusse', '$2y$10$IERnNDbZz0S90P7Sx4GRc.lBVBYgibtz74avhddHz4AEjd72GtcIq', 1, NULL),
(6, 'H', 'White', 'Walter', 'ww@polloshermanos.com', '1 505 842 9635', 'waltwhite', '$2y$10$5.poKsFWF1vFEbt6mjzONOir8uC3DgTtV0VUgENRj6x6o/EqdpCQu', 0, 'Stay out of my territory'),
(7, 'H', 'Mulder', 'Fox', 'fox@fbi.com', '+1 5405096995', 'mulder', '$2y$10$3YvU9mYbl7qpcOZR4tx.g.sm7jyNQoFoBZ6iIJir2pIM6XYeF.1xW', 0, NULL),
(8, 'F', 'Hofstadter', 'Penny', 'penny@pasadena.com', NULL, 'pennyh', '$2y$10$O7qGHTVYjAyMzsrl9K0V5eBbVAMZimGfupZpQ3yMtvTlRsoC4BBAS', 1, ''),
(9, 'H', 'Doe', 'John', 'johndoe@nowhere.com', NULL, 'johndoe', '$2y$10$O.y8QIxvyvLo7DmuTrGMOuP0FJjNJvMfIAj1HTVktB8jsrYV1.1r.', 1, NULL),
(10, 'F', 'Musquin', 'Marie-Ange', 'madame.musquin@sosamitie.fr', '01 02 03 04 05', 'mamusquin', '$2y$10$4MjBa1PPuEBEL9UJIgVsD.Q451EqcCpb7.vj/0FNklCZ1Q8wAlcVO', 0, 'Présidente de l\'association SOS Détresse Amitié'),
(11, 'F', 'Doe', 'Jane', 'janedoe@nowhere.com', NULL, 'janedoe', '$2y$10$Q92ZekUaJ3IQzRVhekNZyuhyOi3xTYqq5fH/tF6pz9MtmYvqaDVFW', 0, NULL);

ALTER TABLE message
  MODIFY IdMessage bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE relation
  MODIFY IdRelation bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

ALTER TABLE utilisateur
  MODIFY IdUtilisateur bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE message
  ADD FOREIGN KEY (IdExpediteur) REFERENCES utilisateur (IdUtilisateur),
  ADD FOREIGN KEY (IdDestinataire) REFERENCES utilisateur (IdUtilisateur);

ALTER TABLE relation
  ADD FOREIGN KEY (IdDemandeur) REFERENCES utilisateur (IdUtilisateur),
  ADD FOREIGN KEY (IdAmi) REFERENCES utilisateur (IdUtilisateur);