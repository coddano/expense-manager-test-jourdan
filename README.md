# Test Technique - Gestion des Notes de Frais

Test technique pour ReeWayy et AriMayi.

## 1. Installation

1.  Clonez ce dépôt (n'oubliez pas de mettre votre URL Git) :
    ```bash
    git clone https://github.com/coddano/expense-manager-test-jourdan.git expense-manager
    cd expense-manager
    ```

2.  Installez les dépendances PHP :
    ```bash
    composer install
    ```

3.  Copiez le fichier d'environnement :
    ```bash
    cp .env.example .env
    ```

4.  Configurez votre fichier `.env` avec vos accès à une base de données (MySQL, PostgreSQL, etc.).

5.  Générez la clé d'application :
    ```bash
    php artisan key:generate
    ```

6.  Lancez les migrations (création des tables) et les seeders (remplissage avec les données de test) :
    ```bash
    php artisan migrate:fresh --seed
    ```

## 2. Lancement

Le projet expose deux interfaces : une API REST (pour Postman/Insomnia) et un front Blade minimal (pour le navigateur).

### API & Serveur Local

Lancez le serveur de développement Laravel :
```bash
php artisan serve
```

### Accès
API: http://localhost:8000/api
Front: http://localhost:8000

## 3. Tests
Pour exécuter les tests de l'application, utilisez la commande suivante :
```bash
php artisan test
```

### Données de Test (seeders)

Les données de test sont générées via les seeders fournies dans le projet. Vous pouvez les modifier selon vos besoins.

Voici les données de test par défaut :
| Rôle       | Nom           | Email              | Mot de passe |
| Rôle       | Nom           | Email              | Mot de passe |
|------------|---------------|--------------------|--------------|
| Manager    | Manager Admin | manager@demo.com   | password123  |
| Employé    | Employee Alice | alice@demo.com     | password123  |
| Employé    | Employee Bob   | bob@demo.com       | password123  | 

## 4. Vues disponibles :
- Tableau de bord Manager : http://127.0.0.1:8000/manager
- Tableau de bord Employé (Bob) : http://127.0.0.1:8000/employee/2
- Tableau de bord Employé (Alice) : http://127.0.0.1:8000/employee/3

