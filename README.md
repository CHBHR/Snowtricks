# SnowTricks [Codacy Badge]([![Codacy Badge](https://app.codacy.com/project/badge/Grade/ae97c77951394e41b4740e0a93349c53)](https://www.codacy.com/gh/CHBHR/Snowtricks/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=CHBHR/Snowtricks&amp;utm_campaign=Badge_Grade))

Snowtricks est un site communautaire de partage d'information sur des figures de snowboard (tricks).
Une fois inscrit et connecté sur le site vous pouvez ajouter, modifier ou supprimer des figures ainsi que partager sur une figure en particulié grace aux commentaires.

### Versions

PHP 7.2

MySQL 5.7.8 

### Installer le projet

Principal
```
git clone git@github.com:CHBHR/SnowTricks.git
```

Installation des dépendances avec Composer
```
composer install
```

#### Database et fixtures
Dans le dossier `.env` présent à la racine du projet, remplacer les variables `db_user`, `db_password` et `db_name` sous `DATABASE_URL` par vos propres informations de configuration.

Pour créer la base de donnée exécutez la commander `php bin/console doctrine:database:create`. 
Pour mettre en place le 'mapping' de doctrine, exécutez cette commande:  `php bin/console doctrine:schema:update --force`

Ensuite, pour exécuter les migrations: `php bin/console doctrine:migrations:migrate'

Une fois que la base de donnée est créé, mapper et à jour, vous pouvez créer les fixtures inclusent avec : `php bin/console doctrine:fixtures:load
`

#### Mailer


## Resources 

Vous pourrez trouver les diagrammes du projet ici : 

La qualité du code est évaluée grace à [Codacy](https://app.codacy.com/project/CHBHR/SnowTricks/dashboard)

Les issues sont consultable ici:  [Github](https://github.com/CHBHR/SnowTricks/issues?q=is%3Aissue+is%3Aclosed)

## Projet 06

Ce site a été créé dans le cadre de la formation "Developpeur d'application PHP/Symfony" de Openclassrooms.

## Auteur

Rey Christopher
