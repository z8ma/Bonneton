# Base de donnees (Bonneton)

Schema principal (MariaDB/MySQL).

## Tables principales
- `user` : comptes utilisateurs (accounttype: `a` admin, `s` vendeur, `b` acheteur).
- `article` : produits (prix en `price_cents`, `currency`, stock, status).
- `article_images` : images produit (primary + position).
- `categories` / `article_categories` : categories et mapping N-N.
- `commentaires` : commentaires par article (image optionnelle, note, compteur de likes).
- `comment_likes` : likes par commentaire (unique par user/comment).
- `panier` : articles en panier (qty).
- `addresses` : adresses livraison/facturation.
- `orders` : commandes (status, total, addresses).
- `order_items` : lignes de commande.
- `payments` : paiements (mock).
- `favorites` : favoris par utilisateur.

## Flux principales

### Panier -> Commande -> Paiement
1) Ajout au panier: insert/update `panier`.
2) Validation: creation d'une ligne `orders` + `payments` + `order_items`.
3) Paiement (mock): `payments.status = paid`, `orders.status = paid`.

### Favoris
- Toggle via `favorites` (unique par user/article).
- Suppression automatique si article supprime (FK cascade).

### Commentaires & likes
- `comment_likes` pour les likes (unique par user/comment).
- `commentaires.likes_count` conserve le compteur.
- Classement quotidien via `commentaires.rank_score` (mis a jour une fois/jour).

### Vendeur
- Ajout d'article dans `article`.
- Image principale dans `article_images` + categories via `article_categories`.

## Statuts utilises
- `orders.status`: `pending`, `paid`, `shipped`, `cancelled`, `refunded`, `delivered` (affichage).
- `payments.status`: `pending`, `paid`, `failed`, `refunded`.
- `article.status`: `active`, `out_of_stock`, `inactive`.

## A noter
- Prix en cents pour eviter les erreurs de flottants.
- Cle unique sur `favorites` et `panier` pour eviter les doublons.
