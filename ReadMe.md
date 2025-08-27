# Blog Révélations

## 📖 Présentation

**Blog Révélations** est une plateforme de blog développée avec Symfony permettant aux utilisateurs de :

📰 **Consulter** des articles organisés par catégories
❤️ **Aimer** des publications via un système de likes interactif (AJAX)
💬 **Commenter** les publications et répondre aux commentaires
🔍 **Rechercher** du contenu par mots-clés ou catégories
✍️ **Créer** et **publier** des articles avec gestion d'images

L'application intègre une interface responsive moderne et une expérience utilisateur soignée.

## 🚀 Installation

### Prérequis

PHP 8.1 ou supérieur
Composer
Symfony CLI
MySQL ou PostgreSQL
Node.js et npm

### Étapes d'installation

#### 1. Cloner le dépôt

```bash
git clone https://github.com/Caro639/Blog-R-v-lations.git
cd Blog-R-v-lations
```

#### 2. Installer les dépendances

```bash
# Installation des dépendances PHP
composer install

# Installation des dépendances JavaScript
npm install
```

#### 3. Configuration de la base de données

Créez un fichier `.env.local` à la racine du projet et configurez votre connexion à la base de données :

```env
DATABASE_URL="mysql://user:password@127.0.0.1:3306/blog_revelations?serverVersion=8.0.32&charset=utf8mb4"
```

#### 4. Créer la base de données

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

#### 5. Charger les fixtures (données de démonstration)

```bash
php bin/console doctrine:fixtures:load
```

#### 6. Compiler les assets

```bash
npm run watch
```

#### 7. Lancer le serveur Symfony

```bash
symfony serve
```

## ✨ Fonctionnalités principales

**Système d'authentification** complet (inscription, connexion, réinitialisation de mot de passe)
**Gestion de catégories** hiérarchiques pour organiser les articles
**Système de likes AJAX** pour une interaction sans rechargement de page
**Commentaires imbriqués** avec réponses
**Barre de recherche unifiée** sur titres, contenus et catégories
**Pagination** pour naviguer facilement entre les articles
**Upload d'images** pour illustrer les articles
**Interface responsive** adaptée à tous les appareils
**Protection CSRF** sur tous les formulaires

## 🧰 Technologies utilisées

**Symfony 6+** : Framework PHP
**Doctrine ORM** : Couche d'abstraction de base de données
**Twig** : Moteur de templates
**Bootstrap 5** : Framework CSS responsive
**JavaScript ES6** : Interactivité côté client
**Webpack Encore** : Compilation des assets
**Tom Select** : Gestion des sélections multiples
