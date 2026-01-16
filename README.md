# Bonneton

Site e-commerce PHP classique (sans framework) avec base MariaDB/MySQL.

## Fonctionnalites
- Boutique (liste, filtre par categories, recherche).
- Fiches produit avec galerie, commentaires, notes et likes.
- Panier, commandes, paiement (mock).
- Adresses, commandes, details de commande.
- Favoris (coeur + page favoris).
- Espace vendeur (ajout d'articles).
- Admin (categories, commandes, moderation commentaires).

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

## Comptes de demo
- Admin: `admin@bonneton.com`
- Vendeur: `vendeur@bonneton.com`
Mot de passe: voir le dump `db/site (1).sql` (hash deja present).

## Structure
- `public/` : pages PHP, `actions/`, `includes/`, `assets/`, `img/`
- `db/` : dump SQL
- `docs/` : documents de reference

## Documentation
- Schema et fonctionnement de la base: `docs/DB.md`

## Parcours utilisateur
1) Arrive sur l'accueil, decouvre la boutique et les nouveautes.
2) Consulte une fiche produit, lit les commentaires, note et/ou ajoute aux favoris.
3) Ajoute au panier, valide et choisit une adresse.
4) Simule le paiement et retrouve la commande dans "Mes commandes".

## Notes
- Projet volutif, pas de framework MVC.
- Paiement simule (pas d'integration PSP).
