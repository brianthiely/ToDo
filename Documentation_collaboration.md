# Documentation du Projet Symfony ToDo&Co

### Introduction et Objectif

Ce document est destiné à guider les développeurs travaillant sur le projet Symfony ToDo. L'objectif du projet est de continuer à réduire la dette technique et de veiller à ce que le système reste performant et de qualité. La documentation couvre le processus de collaboration, les bonnes pratiques, les outils, les conventions de codage, et les étapes de validation et de déploiement.

### Processus de Collaboration

- **Contrôle de Version :** Git est utilisé pour le contrôle de version.
- **Contrôle Qualité :** Codacy est utilisé pour assurer la qualité du code.
- **Intégration Continue :** Travis CI est utilisé pour l'intégration continue.

### Bonnes Pratiques de Développement

- **Test-Driven Development (TDD) :** Les développeurs doivent écrire les tests avant de coder la fonctionnalité, s'assurer que le test échoue, coder la fonctionnalité et s'assurer que le test passe.
- **Code DRY :** Le code doit être refactorisé pour éviter les répétitions.

### Conventions de Codage

- Les tests fonctionnels doivent être dans le dossier `tests/Functional`.
- Les tests unitaires doivent être dans le dossier `tests/Unit`.
- Les tests doivent être nommés `NomDeLaClasseTest.php`.
- Les tests doivent être nommés `testNomDeLaMéthode()`.
- Pour jouer les tests, utiliser la commande `vendor/bin/phpunit`.

### Étapes de Validation et de Déploiement

- **Travis CI :** Il s'assure à chaque commit et push que tous les tests passent.
- **Git et Pull Requests :** Une PR peut être faite sur la branche principale une fois que tous les tests sont passés.

### Performance et Qualité

- **Codacy :** Aucune erreur de niveau moyen ou critique ne doit être présente.
- **Profiler Symfony :** Utilisé pour s'assurer qu'il n'y a pas de problèmes de performance, comme des appels trop longs à la base de données.
- **Tests :** Avoir une couverture de code de 70%.

Pour voir la couverture de code :
```php
vendor/bin/phpunit --coverage-html public/test-coverage
```
