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

- Il n'y a pas de conventions de codage spécifiques à suivre dans ce projet.

### Étapes de Validation et de Déploiement

- **Travis CI :** Il s'assure à chaque commit et push que tous les tests passent.
- **Git et Pull Requests :** Une PR peut être faite sur la branche principale une fois que tous les tests sont passés.

### Performance et Qualité

- **Codacy :** Aucune erreur de niveau moyen ou critique ne doit être présente.
- **Profiler Symfony :** Utilisé pour s'assurer qu'il n'y a pas de problèmes de performance, comme des appels trop longs à la base de données.