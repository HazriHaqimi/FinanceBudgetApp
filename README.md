# FinanceBudgetApp
 
Une application web de gestion des finances personnelles et du budget.
 
---
 
## Installation du Projet
 
Suivez ces étapes pour installer et lancer l'application.
 
### Prérequis
 
- **XAMPP** (Apache + MySQL/MariaDB)
- **NetBeans** (avec le support PHP)
 
### Étape 1 — Décompresser le projet
 
Décompressez le fichier `.zip` du projet dans le dossier `htdocs` de XAMPP :
 
```
C:\xampp\htdocs\projet-bintinorizan-binmohdmarlizan
```
 
### Étape 2 — Démarrer les services
 
Ouvrez le **Panneau de contrôle XAMPP** et démarrez **Apache** et **MySQL**.
 
### Étape 3 — Ouvrir le projet dans NetBeans
 
1. Dans NetBeans, allez dans **Fichier → Nouveau projet**
2. Choisissez **PHP → Application PHP avec des sources existantes**
3. Sélectionnez le dossier `projet-bintinorizan-binmohdmarlizan`
4. Validez pour créer le projet
 
### Étape 4 — Lancer l'application
 
Exécutez le fichier `index.php` pour démarrer l'application.
 
---
 
## Configuration de la Base de Données
 
Suivez ces étapes pour configurer la base de données avant de lancer l'application.
 
### Étape 1 — Créer la Base de Données
 
Ouvrez votre client MySQL (phpMyAdmin) et créez une nouvelle base de données avec les paramètres suivants :
 
| Paramètre | Valeur |
|---|---|
| Nom de la base de données | `budget_financier` |
| Interclassement | `utf8_general_ci` |
 
### Étape 2 — Importer le Fichier SQL
 
Une fois la base de données créée, importez le fichier SQL fourni pour charger le schéma et les données :
 
1. Sélectionnez la base de données `budget_financier`
2. Allez dans **Importer**
3. Choisissez le fichier `budget_financier.sql`
4. Cliquez sur **Exécuter**
---
 
## Comptes Utilisateurs
 
Les comptes suivants sont pré-chargés dans la base de données et prêts à l'emploi :
 
| Nom d'utilisateur | Mot de passe |
|-------------------|--------------|
| admin             |   121212     |
| sophea            |   121212     |
| hazri             |   121212     |
| orked             |   121212     |
  
---
 
