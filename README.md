# Bonneton

Site e-commerce PHP classique (sans framework) avec base MariaDB/MySQL.

## Fonctionnalites principales
- Boutique (liste, filtre par categories, recherche).
- Fiches produit avec galerie et commentaires (image en option).
- Panier + passage de commande + paiement (mock).
- Adresses, commandes, details de commande.
- Favoris (coeur + page favoris).
- Espace vendeur (ajout d'articles).
- Admin: categories et commandes.

## Prerequis
- PHP 8.x
- MariaDB ou MySQL

## Installation locale (macOS + Homebrew)
```bash
brew install php mariadb
brew services start mariadb
```

## Base de donnees
```bash
sudo mysql -e "CREATE DATABASE site;"
sudo mysql site < db/site\ \(1\).sql
```

Si vous utilisez un autre utilisateur, mettez a jour `public/includes/config.php`.

## Lancer le site
```bash
php -S localhost:8000 -t public
```
Puis ouvrir :
`http://localhost:8000/accueil.php`

## Structure
- `public/` : pages PHP, `actions/`, `includes/`, `assets/`, `img/`
- `db/` : dump SQL
- `docs/` : documents de reference

## Documentation
- Schema et fonctionnement de la base: `docs/DB.md`

## Notes
- Lien admin : via les pages PHP existantes (pas de panneau d'installation).
- Ce projet n'integre pas de framework MVC.
