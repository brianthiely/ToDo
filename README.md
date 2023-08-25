# Instructions d'Installation

Suivez ces étapes pour installer et configurer le projet :

## Étape 1: Installer les Dépendances

Installez les dépendances nécessaires avec les commandes suivantes :

```bash
composer install
yarn install
yarn yarn encore dev
```

## Étape 2: Configurer la Base de Données

Créez un fichier `.env.local` pour configurer votre base de données. Assurez-vous que la même variable est écrite dans le fichier `.env`, de sorte que la variable vide du `.env` sera remplacée par la variable du `.env.local`.

### Créer la Base de Données

```bash
php bin/console d:d:c
```

## Étape 3: Créer les Données de Test (Fixtures)

Créez des utilisateurs et des tâches de test avec la commande suivante :

```bash
php bin/console d:f:l
```

### Associer les Tâches Anonymes

```bash
php bin/console task-anonymous
```

### Supprimer les Données Fictives

Une fois que vous avez testé et validé le bon fonctionnement de l'application, vous pouvez supprimer les données fictives de la base de données.

## Étape 4: Configurer le Compte Administrateur

Après avoir testé et supprimé les données fictives, créez votre compte administrateur avec la commande suivante :

```bash
php bin/console create:admin
```

Vous pouvez maintenant vous connecter et créer les comptes utilisateur nécessaires pour vos collaborateurs.