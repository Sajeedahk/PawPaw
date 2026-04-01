-- ============================================
--  Base de données Paw Paw v2
-- ============================================

CREATE DATABASE IF NOT EXISTS pawpaw
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE pawpaw;

-- ─────────────────────────────────────────────
--  users  (role : particulier | pro)
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nom        VARCHAR(100) NOT NULL,
    prenom     VARCHAR(100) NOT NULL,
    email      VARCHAR(191) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    role       ENUM('particulier','pro') NOT NULL DEFAULT 'particulier',
    avatar     VARCHAR(255) DEFAULT NULL,
    telephone  VARCHAR(20)  DEFAULT NULL,
    bio        TEXT         DEFAULT NULL,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────
--  profils_pro  (1 par pro)
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS profils_pro (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    user_id          INT          NOT NULL UNIQUE,
    nom_structure    VARCHAR(255) DEFAULT NULL,
    adresse          VARCHAR(255) NOT NULL,
    ville            VARCHAR(100) NOT NULL,
    code_postal      VARCHAR(10)  DEFAULT NULL,
    animaux_acceptes SET('chien','chat','lapin','oiseau','rongeur','reptile','poisson','autre') NOT NULL DEFAULT 'chien',
    capacite_max     INT          DEFAULT 1,
    photo            VARCHAR(255) DEFAULT NULL,
    actif            TINYINT(1)   DEFAULT 1,
    created_at       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────
--  tarifs  (prix/heure par type d'animal)
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS tarifs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    pro_id      INT          NOT NULL,
    animal_type ENUM('chien','chat','lapin','oiseau','rongeur','reptile','poisson','autre') NOT NULL,
    prix_heure  DECIMAL(8,2) NOT NULL,
    UNIQUE KEY (pro_id, animal_type),
    FOREIGN KEY (pro_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────
--  creneaux  (disponibilités publiées par un pro)
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS creneaux (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    pro_id     INT      NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin   DATETIME NOT NULL,
    statut     ENUM('disponible','reserve') DEFAULT 'disponible',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pro_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────
--  reservations
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS reservations (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    creneau_id   INT          NOT NULL,
    client_id    INT          NOT NULL,
    animal_nom   VARCHAR(100) NOT NULL,
    animal_type  ENUM('chien','chat','lapin','oiseau','rongeur','reptile','poisson','autre') NOT NULL,
    animal_photo VARCHAR(255) DEFAULT NULL,
    message      TEXT         DEFAULT NULL,
    statut       ENUM('en_attente','confirme','refuse','annule') DEFAULT 'en_attente',
    created_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (creneau_id) REFERENCES creneaux(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id)  REFERENCES users(id)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ajout colonne photo animal sur base existante (migration)
-- ALTER TABLE reservations ADD COLUMN IF NOT EXISTS animal_photo VARCHAR(255) DEFAULT NULL AFTER animal_type;


-- ============================================================
--  17 Professionnels Paw Paw - un par image
--  Mot de passe universel : password
--   Cheval et Chevre → categorie "autre" (non pris en charge nativement)
-- ============================================================

-- ─────────────────────────────────────────────
--  1. chat1.jpg - Chats - Paris
-- ─────────────────────────────────────────────
INSERT INTO users (nom, prenom, email, password, role, telephone, bio)
VALUES ('Martin', 'Sophie', 'sophie.martin@pawpaw.fr',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'pro', '06 11 11 11 11',
    'Felinophile depuis toujours, je prends soin de vos chats comme des miens dans mon appartement calme du 8e.');
SET @u1 = LAST_INSERT_ID();
INSERT INTO profils_pro (user_id, nom_structure, adresse, ville, code_postal, animaux_acceptes, capacite_max, photo, actif)
VALUES (@u1, 'Sophie & Chats', '14 rue de la Paix', 'Paris', '75008', 'chat', 4, 'chat1.jpg', 1);
INSERT INTO creneaux (pro_id, date_debut, date_fin, statut) VALUES
    (@u1, '2026-04-01 00:00:00', '2026-04-30 23:59:59', 'disponible'),
    (@u1, '2026-06-01 00:00:00', '2026-06-30 23:59:59', 'disponible'),
    (@u1, '2026-08-01 00:00:00', '2026-08-31 23:59:59', 'disponible');

-- ─────────────────────────────────────────────
--  2. chatYassine.jpg - Chats + lapins - Strasbourg
-- ─────────────────────────────────────────────
INSERT INTO users (nom, prenom, email, password, role, telephone, bio)
VALUES ('Miled', 'Yassine', 'Yassine@pawpaw.fr',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'pro', '06 22 22 22 22',
    'mon choubidou a la creme que j aime ');
SET @u2 = LAST_INSERT_ID();
INSERT INTO profils_pro (user_id, nom_structure, adresse, ville, code_postal, animaux_acceptes, capacite_max, photo, actif)
VALUES (@u2, NULL, '9 rue des Bouchers', 'Paris', '75011', 'chat,lapin', 3, 'chatYassine.jpg', 1);
INSERT INTO creneaux (pro_id, date_debut, date_fin, statut) VALUES
    (@u2, '2026-04-01 00:00:00', '2026-04-14 23:59:59', 'disponible'),
    (@u2, '2026-05-15 00:00:00', '2026-05-31 23:59:59', 'disponible'),
    (@u2, '2026-07-01 00:00:00', '2026-08-31 23:59:59', 'disponible');

-- ─────────────────────────────────────────────
--  3. chein6.jpg - Chiens - Rennes
-- ─────────────────────────────────────────────
INSERT INTO users (nom, prenom, email, password, role, telephone, bio)
VALUES ('Lebrun', 'Tonia', 'tonia.lebrun@pawpaw.fr',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'pro', '06 33 33 33 33',
    'Educatrice canine certifiee, je propose une garde active avec promenades matin et soir.');
SET @u3 = LAST_INSERT_ID();
INSERT INTO profils_pro (user_id, nom_structure, adresse, ville, code_postal, animaux_acceptes, capacite_max, photo, actif)
VALUES (@u3, 'Tonia Dog', '5 place de la Mairie', 'Rennes', '35000', 'chien', 2, 'chein6.jpg', 1);
INSERT INTO creneaux (pro_id, date_debut, date_fin, statut) VALUES
    (@u3, '2026-04-05 00:00:00', '2026-04-25 23:59:59', 'disponible'),
    (@u3, '2026-06-10 00:00:00', '2026-07-10 23:59:59', 'disponible');

-- ─────────────────────────────────────────────
--  4. Cheval1.jpg - Chevaux (autre) - Fontainebleau
-- ─────────────────────────────────────────────
INSERT INTO users (nom, prenom, email, password, role, telephone, bio)
VALUES ('Girard', 'Nathalie', 'nathalie.girard@pawpaw.fr',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'pro', '06 44 44 44 44',
    'Cavaliere professionnelle avec box et paddock prives. Pension equestre complete avec soins quotidiens.');
SET @u4 = LAST_INSERT_ID();
INSERT INTO profils_pro (user_id, nom_structure, adresse, ville, code_postal, animaux_acceptes, capacite_max, photo, actif)
VALUES (@u4, 'Ecurie Girard', '1 chemin des Ecuries', 'Fontainebleau', '77300', 'autre', 6, 'Cheval1.jpg', 1);
INSERT INTO creneaux (pro_id, date_debut, date_fin, statut) VALUES
    (@u4, '2026-04-01 00:00:00', '2026-12-31 23:59:59', 'disponible');

-- ─────────────────────────────────────────────
--  5. Chevre1.jpg - Chevres (autre) - Grenoble
-- ─────────────────────────────────────────────
INSERT INTO users (nom, prenom, email, password, role, telephone, bio)
VALUES ('Faure', 'Pauline', 'pauline.faure@pawpaw.fr',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'pro', '06 55 55 55 55',
    'Agricultrice a temps partiel, je dispose d''un grand enclos pour accueillir vos chevres et moutons.');
SET @u5 = LAST_INSERT_ID();
INSERT INTO profils_pro (user_id, nom_structure, adresse, ville, code_postal, animaux_acceptes, capacite_max, photo, actif)
VALUES (@u5, 'La Bergerie de Pauline', '12 route des Alpes', 'Grenoble', '38000', 'autre', 8, 'Chevre1.jpg', 1);
INSERT INTO creneaux (pro_id, date_debut, date_fin, statut) VALUES
    (@u5, '2026-04-01 00:00:00', '2026-06-30 23:59:59', 'disponible'),
    (@u5, '2026-09-01 00:00:00', '2026-11-30 23:59:59', 'disponible');

-- ─────────────────────────────────────────────
--  6. Chevre2.jpg - Chevres + rongeurs (autre) - Clermont-Ferrand
-- ─────────────────────────────────────────────
INSERT INTO users (nom, prenom, email, password, role, telephone, bio)
VALUES ('Morel', 'Claire', 'claire.morel@pawpaw.fr',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'pro', '06 66 66 66 66',
    'Veterinaire rurale, j''accueille tous les animaux de la ferme avec expertise et bienveillance.');
SET @u6 = LAST_INSERT_ID();
INSERT INTO profils_pro (user_id, nom_structure, adresse, ville, code_postal, animaux_acceptes, capacite_max, photo, actif)
VALUES (@u6, 'Ferme de Claire', '45 route de Riom', 'Clermont-Ferrand', '63000', 'autre,rongeur', 10, 'Chevre2.jpg', 1);
INSERT INTO creneaux (pro_id, date_debut, date_fin, statut) VALUES
    (@u6, '2026-05-01 00:00:00', '2026-05-31 23:59:59', 'disponible'),
    (@u6, '2026-07-01 00:00:00', '2026-08-31 23:59:59', 'disponible');

-- ─────────────────────────────────────────────
--  7. chien 1.jpg - Chiens - Bordeaux
-- ─────────────────────────────────────────────
INSERT INTO users (nom, prenom, email, password, role, telephone, bio)
VALUES ('Dubois', 'Marc', 'marc.dubois@pawpaw.fr',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'pro', '06 77 77 77 71',
    'Retraite passionne, je promene et garde vos chiens dans mon grand jardin bordelais.');
SET @u7 = LAST_INSERT_ID();
INSERT INTO profils_pro (user_id, nom_structure, adresse, ville, code_postal, animaux_acceptes, capacite_max, photo, actif)
VALUES (@u7, 'Marc & Compagnie', '7 allee des Chartrons', 'Bordeaux', '33300', 'chien', 3, 'chien 1.jpg', 1);
INSERT INTO creneaux (pro_id, date_debut, date_fin, statut) VALUES
    (@u7, '2026-04-01 00:00:00', '2026-04-30 23:59:59', 'disponible'),
    (@u7, '2026-07-15 00:00:00', '2026-08-15 23:59:59', 'disponible');

-- ─────────────────────────────────────────────
--  8. chien2.jpg - Chiens + chats - Nantes
-- ─────────────────────────────────────────────
INSERT INTO users (nom, prenom, email, password, role, telephone, bio)
VALUES ('Simon', 'Luca', 'luca.simon@pawpaw.fr',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'pro', '06 77 77 77 72',
    'Pet sitter diplome, j''adore chiens et chats a egalite. Promenades, jeux, calins garantis !');
SET @u8 = LAST_INSERT_ID();
INSERT INTO profils_pro (user_id, nom_structure, adresse, ville, code_postal, animaux_acceptes, capacite_max, photo, actif)
VALUES (@u8, 'Luca Pets', '18 rue du Calvaire', 'Nantes', '44000', 'chien,chat', 4, 'chien2.jpg', 1);
INSERT INTO creneaux (pro_id, date_debut, date_fin, statut) VALUES
    (@u8, '2026-04-01 00:00:00', '2026-12-31 23:59:59', 'disponible');

-- ─────────────────────────────────────────────
--  9. chien3.jpg - Chiens - Lyon
-- ─────────────────────────────────────────────
INSERT INTO users (nom, prenom, email, password, role, telephone, bio)
VALUES ('Leroy', 'Thomas', 'thomas.leroy@pawpaw.fr',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'pro', '06 77 77 77 73',
    'Sportif et amoureux des chiens, je propose des gardes actives avec jogging et activites outdoor.');
SET @u9 = LAST_INSERT_ID();
INSERT INTO profils_pro (user_id, nom_structure, adresse, ville, code_postal, animaux_acceptes, capacite_max, photo, actif)
VALUES (@u9, 'Thomas Dog Sitter', '3 avenue Jean Jaures', 'Lyon', '69007', 'chien', 2, 'chien3.jpg', 1);
INSERT INTO creneaux (pro_id, date_debut, date_fin, statut) VALUES
    (@u9, '2026-04-10 00:00:00', '2026-05-10 23:59:59', 'disponible'),
    (@u9, '2026-06-01 00:00:00', '2026-06-30 23:59:59', 'disponible'),
    (@u9, '2026-08-01 00:00:00', '2026-09-01 23:59:59', 'disponible');

-- ─────────────────────────────────────────────
--  10. chien4.jpg - Chiens - Toulouse
-- ─────────────────────────────────────────────
INSERT INTO users (nom, prenom, email, password, role, telephone, bio)
VALUES ('Blanc', 'Jules', 'jules.blanc@pawpaw.fr',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'pro', '06 77 77 77 74',
    'Maitre-chien benevole a la SPA, je connais toutes les races. Chiens petits ou grands, tous bienvenus !');
SET @u10 = LAST_INSERT_ID();
INSERT INTO profils_pro (user_id, nom_structure, adresse, ville, code_postal, animaux_acceptes, capacite_max, photo, actif)
VALUES (@u10, 'Jules & Toutous', '5 rue du Taur', 'Toulouse', '31000', 'chien', 5, 'chien4.jpg', 1);
INSERT INTO creneaux (pro_id, date_debut, date_fin, statut) VALUES
    (@u10, '2026-04-01 00:00:00', '2026-04-30 23:59:59', 'disponible'),
    (@u10, '2026-06-15 00:00:00', '2026-07-15 23:59:59', 'disponible'),
    (@u10, '2026-09-01 00:00:00', '2026-09-30 23:59:59', 'disponible');

-- ─────────────────────────────────────────────
--  11. chien5.jpg - Tous animaux - Marseille
-- ─────────────────────────────────────────────
INSERT INTO users (nom, prenom, email, password, role, telephone, bio)
VALUES ('Rousseau', 'Emma', 'emma.rousseau@pawpaw.fr',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'pro', '06 77 77 77 75',
    'Ancienne aide-soignante veterinaire. J''accepte tous les animaux, y compris reptiles et poissons.');
SET @u11 = LAST_INSERT_ID();
INSERT INTO profils_pro (user_id, nom_structure, adresse, ville, code_postal, animaux_acceptes, capacite_max, photo, actif)
VALUES (@u11, 'Emma All Pets', '17 La Canebiere', 'Marseille', '13001', 'chien,chat,lapin,oiseau,rongeur,reptile,poisson,autre', 8, 'chien5.jpg', 1);
INSERT INTO creneaux (pro_id, date_debut, date_fin, statut) VALUES
    (@u11, '2026-04-01 00:00:00', '2026-09-30 23:59:59', 'disponible');

-- ─────────────────────────────────────────────
--  12. chien6.jpg - Chiens - Lille
-- ─────────────────────────────────────────────
INSERT INTO users (nom, prenom, email, password, role, telephone, bio)
VALUES ('Moreau', 'Julie', 'julie.moreau@pawpaw.fr',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'pro', '06 77 77 77 76',
    'Proprietaire de deux golden retrievers, je comprends les besoins de votre chien mieux que personne.');
SET @u12 = LAST_INSERT_ID();
INSERT INTO profils_pro (user_id, nom_structure, adresse, ville, code_postal, animaux_acceptes, capacite_max, photo, actif)
VALUES (@u12, 'Julie Dog', '31 rue Faidherbe', 'Lille', '59000', 'chien', 3, 'chien6.jpg', 1);
INSERT INTO creneaux (pro_id, date_debut, date_fin, statut) VALUES
    (@u12, '2026-05-01 00:00:00', '2026-05-31 23:59:59', 'disponible'),
    (@u12, '2026-07-01 00:00:00', '2026-07-31 23:59:59', 'disponible');

-- ─────────────────────────────────────────────
--  13. chien7.jpg - Chiens + chats - Nice
-- ─────────────────────────────────────────────
INSERT INTO users (nom, prenom, email, password, role, telephone, bio)
VALUES ('Bernard', 'Celine', 'celine.bernard@pawpaw.fr',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'pro', '06 77 77 77 77',
    'Sur la Cote d''Azur, je garde vos compagnons avec vue sur mer. Promenades sur la promenade des Anglais incluses !');
SET @u13 = LAST_INSERT_ID();
INSERT INTO profils_pro (user_id, nom_structure, adresse, ville, code_postal, animaux_acceptes, capacite_max, photo, actif)
VALUES (@u13, 'Riviera Pet Sitting', '2 promenade des Anglais', 'Nice', '06000', 'chien,chat', 4, 'chien7.jpg', 1);
INSERT INTO creneaux (pro_id, date_debut, date_fin, statut) VALUES
    (@u13, '2026-04-01 00:00:00', '2026-04-30 23:59:59', 'disponible'),
    (@u13, '2026-07-01 00:00:00', '2026-08-31 23:59:59', 'disponible'),
    (@u13, '2026-10-01 00:00:00', '2026-10-31 23:59:59', 'disponible');

-- ─────────────────────────────────────────────
--  14. oiseau1.jpg - Oiseaux + lapins + rongeurs - Nantes
-- ─────────────────────────────────────────────
INSERT INTO users (nom, prenom, email, password, role, telephone, bio)
VALUES ('Fontaine', 'Lucie', 'lucie.fontaine@pawpaw.fr',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'pro', '06 77 77 77 78',
    'Veterinaire auxiliaire specialisee NAC. Perruches, perroquets, lapins, hamsters - tous accueillis avec soin.');
SET @u14 = LAST_INSERT_ID();
INSERT INTO profils_pro (user_id, nom_structure, adresse, ville, code_postal, animaux_acceptes, capacite_max, photo, actif)
VALUES (@u14, 'Plumes & Poils', '22 boulevard des Martyrs', 'Nantes', '44200', 'oiseau,lapin,rongeur', 6, 'oiseau1.jpg', 1);
INSERT INTO creneaux (pro_id, date_debut, date_fin, statut) VALUES
    (@u14, '2026-04-15 00:00:00', '2026-04-20 23:59:59', 'disponible'),
    (@u14, '2026-07-01 00:00:00', '2026-08-31 23:59:59', 'disponible');

-- ─────────────────────────────────────────────
--  15. oiseau2.jpg - Oiseaux - Montpellier
-- ─────────────────────────────────────────────
INSERT INTO users (nom, prenom, email, password, role, telephone, bio)
VALUES ('Perez', 'Isabelle', 'isabelle.perez@pawpaw.fr',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'pro', '06 77 77 77 79',
    'Ornithologiste amateure et passionnee. Je dispose d''une voliere spacieuse pour accueillir vos oiseaux.');
SET @u15 = LAST_INSERT_ID();
INSERT INTO profils_pro (user_id, nom_structure, adresse, ville, code_postal, animaux_acceptes, capacite_max, photo, actif)
VALUES (@u15, 'La Voliere d''Isabelle', '10 rue de l''Universite', 'Montpellier', '34000', 'oiseau', 8, 'oiseau2.jpg', 1);
INSERT INTO creneaux (pro_id, date_debut, date_fin, statut) VALUES
    (@u15, '2026-04-01 00:00:00', '2026-06-30 23:59:59', 'disponible'),
    (@u15, '2026-09-01 00:00:00', '2026-11-30 23:59:59', 'disponible');

-- ─────────────────────────────────────────────
--  16. poisson1.jpg - Poissons + reptiles + rongeurs - Lille
-- ─────────────────────────────────────────────
INSERT INTO users (nom, prenom, email, password, role, telephone, bio)
VALUES ('Dupuis', 'Nina', 'nina.dupuis@pawpaw.fr',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'pro', '06 77 77 77 80',
    'Biologiste de formation, je maitrise parfaitement les soins des animaux exotiques : reptiles, poissons, rongeurs.');
SET @u16 = LAST_INSERT_ID();
INSERT INTO profils_pro (user_id, nom_structure, adresse, ville, code_postal, animaux_acceptes, capacite_max, photo, actif)
VALUES (@u16, 'Exotic Pet Care', '31 rue Faidherbe', 'Lille', '59800', 'poisson,reptile,rongeur', 10, 'poisson1.jpg', 1);
INSERT INTO creneaux (pro_id, date_debut, date_fin, statut) VALUES
    (@u16, '2026-04-01 00:00:00', '2026-12-31 23:59:59', 'disponible');

-- ─────────────────────────────────────────────
--  17. lapin1.jpg - Lapins + rongeurs - Dijon
-- ─────────────────────────────────────────────
INSERT INTO users (nom, prenom, email, password, role, telephone, bio)
VALUES ('Clement', 'Marie', 'marie.clement@pawpaw.fr',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'pro', '06 77 77 77 81',
    'Proprietaire de 4 lapins beliers, je connais tous leurs besoins. Espace securise et jardin clos pour gambader.');
SET @u17 = LAST_INSERT_ID();
INSERT INTO profils_pro (user_id, nom_structure, adresse, ville, code_postal, animaux_acceptes, capacite_max, photo, actif)
VALUES (@u17, 'Lapiniere de Marie', '6 rue de la Liberte', 'Dijon', '21000', 'lapin,rongeur', 5, 'lapin1.jpg', 1);
INSERT INTO creneaux (pro_id, date_debut, date_fin, statut) VALUES
    (@u17, '2026-04-01 00:00:00', '2026-05-31 23:59:59', 'disponible'),
    (@u17, '2026-07-01 00:00:00', '2026-07-31 23:59:59', 'disponible'),
    (@u17, '2026-10-01 00:00:00', '2026-10-31 23:59:59', 'disponible');
