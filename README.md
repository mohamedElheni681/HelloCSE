# HelloCSE Test- Projet Laravel avec Docker

Bienvenue dans **HelloCSE Test**, un projet Laravel conteneurisé avec Docker. Ce guide vous fournira les instructions nécessaires pour configurer, démarrer et utiliser l'application.

## Prérequis

Avant de commencer, assurez-vous d'avoir les outils suivants installés sur votre machine :

- **Docker** : Pour la création et la gestion des conteneurs.
- **Docker Compose** : Pour orchestrer les conteneurs Docker.

## Étapes d'installation et de démarrage

### 1. Créer le fichier `.env`

Copiez le fichier `.env.example` en `.env` pour configurer les variables d'environnement nécessaires à l'application.

```bash
cp .env.example .env

```

### 2. Lancer Docker Compose

Lancez Docker Compose pour construire et démarrer les conteneurs en arrière-plan.

```bash
docker-compose up --build -d

```

### 3. Installer les dépendances PHP

Installez les dépendances Laravel via Composer à l'intérieur du conteneur Docker.

```bash
docker-compose run app composer install

```


### 4. Générer la clé de l'application

Générez la clé secrète de l'application Laravel.

```bash
docker-compose exec app php artisan key:generate

```


### 5. Mettre en cache la configuration

Mettez en cache les configurations de l'application

```bash
docker-compose exec app php artisan config:cache

```


### 6. Publier les ressources de L5 Swagger

Publiez les ressources nécessaires pour Swagger à l'aide du fournisseur L5Swagger.

```bash
docker-compose exec app php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"

```


### 7. Générer la documentation Swagger

Générez la documentation Swagger pour l'API.

```bash
docker-compose exec app php artisan l5-swagger:generate

```


### 8. Lancer les migrations

Appliquez les migrations pour configurer la base de données.

```bash
docker-compose exec app php artisan migrate

```

### 9. Seed de la base de données

Remplissez la base de données avec des données de test.

```bash
docker-compose exec app php artisan db:seed

```

## Utilisation de l'API via Swagger
Pour utiliser l'API via Swagger, accédez à l'interface Swagger générée à l'adresse suivante : http://localhost:8000/api/documentation.

Note : Pour les endpoints nécessitant une authentification, assurez-vous de fournir le token dans l'en-tête Authorization sous la forme :

```bash
Bearer <votre_token>

```