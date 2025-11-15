# Plateforme de Gestion de Flotte Automobile

Une plateforme web complète de gestion de flotte automobile développée avec Laravel, inspirée de la solution DIGIPARC.

## Vue d'ensemble

Cette plateforme offre une solution complète pour la gestion de flotte automobile, couvrant tous les aspects depuis l'acquisition des véhicules jusqu'à leur réforme.

### Objectifs

- Gestion centralisée de tous les véhicules du parc automobile
- Optimisation de la maintenance préventive et curative
- Suivi complet des ressources humaines
- Gestion des documents administratifs et conformité réglementaire
- Contrôle des achats, stocks et gestion financière
- Géolocalisation en temps réel
- Gestion du transport et location de véhicules
- Tableaux de bord et reporting avancé

## Architecture de la Base de Données

✅ **Plus de 70 tables** créées et migrées couvrant tous les modules
✅ **Système RBAC** avec Spatie Laravel Permission
✅ **Support multi-sites**
✅ **Soft deletes** pour les données importantes
✅ **Audit trail** pour la journalisation

### Modules Implémentés (Base de données)

1. **Gestion de Flotte** (13 tables)
2. **Maintenance GMAO** (9 tables)
3. **Ressources Humaines** (8 tables)
4. **Documents Juridiques** (4 tables)
5. **Achats et Stocks** (11 tables)
6. **Gestion Financière** (9 tables)
7. **Transport TMS** (8 tables)
8. **Location Véhicules** (3 tables)
9. **Géolocalisation GPS** (4 tables)
10. **Système** (3 tables)

## Prérequis

- PHP >= 8.4
- Composer
- SQLite / MySQL / PostgreSQL
- Node.js & NPM

## Installation Rapide

```bash
# Cloner le projet
git clone <repository-url>
cd haythem

# Installer les dépendances
composer install

# Configurer l'environnement
cp .env.example .env
php artisan key:generate

# Migrer la base de données
php artisan migrate

# Installer les assets
npm install && npm run dev

# Lancer le serveur
php artisan serve
```

## Technologies Utilisées

- **Laravel 12** - Framework PHP
- **Spatie Laravel Permission** - RBAC
- **Laravel UI** - Authentification Bootstrap
- **DomPDF** - Génération PDF
- **Laravel Excel** - Import/Export Excel
- **Intervention Image** - Traitement d'images

## État du Projet

### ✅ Complété

- Architecture base de données (70+ tables)
- Migrations complètes
- Système d'authentification
- RBAC (Rôles et Permissions)
- Configuration packages
- Modèle Vehicle (exemple complet)
- Documentation

### 📝 À Développer

- 70+ Modèles Eloquent
- Contrôleurs pour chaque module
- Vues Blade (interface utilisateur)
- Routes web et API
- Seeders et Factories
- Logique métier
- Tests
- Tableaux de bord
- Internationalisation

**Temps estimé**: 8-12 mois selon cahier des charges

## Documentation

Voir `database/migrations/2025_11_15_220538_create_fleet_management_tables.php` pour le schéma complet.

Le modèle `app/Models/Vehicle.php` est un exemple complet avec relations, scopes et accessors.

## Licence

MIT

