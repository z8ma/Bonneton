# Abonnements vendeurs (plan complet)

Ce document regroupe les decisions, le schema BDD et les etapes pour implementer
les abonnements vendeurs dans Bonneton.

## Decisions validees
- Compte vendeur uniquement si abonnement actif (`accounttype = s`).
- Renouvellement automatique.
- Vrai paiement (modele, integration PSP a definir).
- UX: page abonnements accessible via menu + CTA sur accueil.
- Essai: 2 semaines, limite 3 articles.
- Expiration: articles masques 1 semaine (grace), puis archives en base.

## Plans proposes (simples)
Essai (gratuit):
- 3 articles max
- 2 semaines
- stats basiques

Classique:
- 20 articles max
- badge vendeur
- mise en avant hebdo

Premium:
- 100 articles max
- mise en avant prioritaire (accueil / selection)
- boutique dediee

Options "a venir":
- coupons/promos
- import/edition en masse
- support prioritaire

## Duree, prix, renouvellement (a confirmer)
- Duree payante: mensuel.
- Auto-renew: actif par defaut (resiliation possible).
- Essai: non renouvelable (auto_renew = 0) ou conversion en classique.
- Prix placeholder (a definir): Classique 19 EUR, Premium 49 EUR.

## Changement BDD (proposition)

### 1) Nouvelle table `subscriptions`
```sql
CREATE TABLE subscriptions (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) NOT NULL,
  plan enum('trial','classic','premium') NOT NULL,
  status enum('active','grace','expired','cancelled') NOT NULL DEFAULT 'active',
  started_at datetime NOT NULL,
  ends_at datetime NOT NULL,
  grace_ends_at datetime DEFAULT NULL,
  auto_renew tinyint(1) NOT NULL DEFAULT 1,
  provider varchar(50) DEFAULT NULL,
  provider_ref varchar(100) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (id),
  KEY user_id (user_id),
  CONSTRAINT subscriptions_ibfk_1 FOREIGN KEY (user_id) REFERENCES user(id)
    ON DELETE CASCADE ON UPDATE CASCADE
);
```

### 2) Ajout dans `article`
- Ajouter `visibility_status` ou reutiliser `status` pour archiver.
Exemple: `status` deja present => `active`, `out_of_stock`, `archived`.
- Lorsqu'un abonnement expire: passer les articles a `archived`.

### 3) Meta limites
Option simple: une table `plan_limits` statique dans le code
ou un champ JSON en BDD si tu veux le rendre editable.

Exemple (code):
```
$PLAN_LIMITS = [
  'trial' => ['max_articles' => 3, 'badge' => false, 'spotlight' => false],
  'classic' => ['max_articles' => 20, 'badge' => true, 'spotlight' => 'weekly'],
  'premium' => ['max_articles' => 100, 'badge' => true, 'spotlight' => 'priority'],
];
```

## Changement pages / flux

### A) Inscription (register)
- Formulaire ne propose plus `vendeur`, uniquement acheteur.
- Apres inscription: redirection vers page `abonnements.php`.
- Sur `abonnements.php`, expliquer: "pour vendre, choisir un plan".

### B) Page abonnements
- Affiche Essai / Classique / Premium, avec avantages clairs.
- CTA: "Commencer l'essai" / "S'abonner".
- Si abonnement actif: page affiche statut + date de fin + bouton resilier.
- Si abonnement expire/grace: message + CTA pour reactiver.

### C) CTA sur accueil
- Section "Rejoignez la communaute" avec 3 plans.
- Lien vers `abonnements.php`.

### D) Espace vendeur
- Accessible seulement si abonnement actif.
- Si non actif: redirection vers `abonnements.php`.
- Afficher un message si statut `grace` (J-7 avant masquage).

### E) Cron / job d'etat
- Verification quotidienne des abonnements:
  - si `ends_at < now()` et status `active`: status -> `grace`, grace_ends_at = now()+7j.
  - si `grace_ends_at < now()` et status `grace`: status -> `expired`, archive articles.
  - si paiement renouvelle: status -> `active`, ends_at + 1 mois, desarchiver articles.

## Endpoints a creer (proposition)
- `public/abonnements.php`: page listing + status.
- `public/actions/start-trial.php`: creer subscription trial.
- `public/actions/checkout-subscription.php`: init paiement (provider).
- `public/actions/subscription-cancel.php`: resilier (auto_renew = 0).
- `public/actions/subscription-reactivate.php`: relancer paiement.
- `public/actions/subscription-webhook.php`: callback provider.

## Regles d'acces (backend)
- Middleware simple: `require_active_subscription()` pour pages vendeur.
- Ajouter checks dans `ajoutArticle.php`, `vendeur.php`, actions vendeur.
- Desactiver boutons "ajouter article" si limite atteinte.

## Etats et transitions
- `active` -> `grace` quand `ends_at` depasse.
- `grace` -> `expired` quand `grace_ends_at` depasse.
- `active` + `auto_renew = 0` -> `cancelled` a la fin de periode payee.
- `expired` + paiement ok -> `active` + restauration articles.

## Actions sur articles
- En `grace`: masquer articles en boutique, visibles au vendeur.
- En `expired`: passer `status = archived` (non visible).
- En `active`: restaurer `status` a `active` si archive.

## UI detaillee (abonnements)
- Cartes plans avec prix, duree, 3 bullets max.
- Badge "Recommande" sur Classique.
- CTA principal: "Commencer l'essai" / "Passer en Classique".
- Bloc "Mon statut": plan actuel, date fin, auto-renew on/off.
- Lien "Resilier" + confirmation.

## Migration / retro-compat
- Si un utilisateur est deja vendeur sans abonnement:
  - creer une subscription `trial` ou `classic` par defaut.
  - forcer `accounttype = s` si subscription active.

## QA / tests manuels
- Inscription acheteur -> page abonnements.
- Activation essai -> acces vendeur + quota 3.
- Depassement quota -> blocage ajout article.
- Fin essai -> grace 7j, articles masques.
- Expiration -> articles archives (non visibles).
- Reactivation -> articles restaures.


## Regles metier
- `accounttype = s` uniquement si subscription `status = active`.
- `trial` ne renouvelle pas automatiquement (ou auto-renew = 0) si tu veux le separer.
- Si passage de `classic`/`premium` a `cancelled`:
  - garder actif jusqu'a la fin de periode payee.
- Articles masques pendant la grace:
  - visibles uniquement au vendeur (dashboard), pas aux acheteurs.
- En cas de reprise d'abonnement:
  - restauration automatique des articles archives.

## Changement code (checklist)
- `public/register.php` + `actions/traitement_inscription.php`
  - supprimer champ `accounttype` vendeur.
  - rediriger vers `abonnements.php` apres creation.
- `public/abonnements.php` (nouvelle page)
  - affichage plans + CTA + statut utilisateur.
- `public/accueil.php`
  - bloc "Rejoignez la communaute".
- `public/vendeur.php` + pages vendeur
  - verif abonnement actif.
- `public/includes/menu.php`
  - lien "Abonnements" dans menu/compte.
- `public/includes/config.php`
  - helper `require_active_subscription()` (optionnel).

## Paiement (modele)
Integration recommandee: Stripe (Checkout).
Champs a stocker: `provider`, `provider_ref`, `status`.
Si paiement reussi -> activer subscription + passer accounttype a `s`.

## UX / Texte (suggestion)
- "Essai 2 semaines, 3 articles max."
- "Passez en Classique pour vendre plus et etre mis en avant."
- "Premium = priorite sur l'accueil + boutique dediee."

## Points encore a trancher
- Duree payante: mensuel uniquement ? annuel ?
- Prix et display (EUR).
- Quota exact Classique/Premium.
- Est-ce que l'essai auto-renew ou non.
