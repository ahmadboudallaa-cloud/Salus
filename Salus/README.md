# Salus API - Assistant Sante

API REST pour suivre les symptomes, prendre des rendez-vous et obtenir des conseils de bien-etre generes par IA.

**Stack**
- Laravel 12, PHP 8.2+
- Auth: Laravel Sanctum
- DB: MySQL ou PostgreSQL
- Docs API: Scribe (OpenAPI/Swagger)

**Format JSON unifie**
- Succes: `{"success": true, "data": { ... }, "message": "Operation reussie"}`
- Erreur: `{"success": false, "errors": { ... }, "message": "Erreur de validation"}`

**Installation**
1. `composer install`
2. `cp .env.example .env`
3. Configurer la base de donnees dans `.env`
4. `php artisan key:generate`
5. `php artisan migrate --seed`
6. `php artisan serve`

**Configuration IA**
- `.env`
- `AI_PROVIDER=openai` ou `AI_PROVIDER=gemini`
- `OPENAI_API_KEY`, `OPENAI_MODEL`, `GEMINI_API_KEY`, `GEMINI_MODEL`

**Documentation API (OpenAPI/Swagger)**
- Installer les dependances: `composer install`
- Publier la config: `php artisan vendor:publish --tag=scribe-config` (optionnel, deja fourni)
- Generer la doc: `php artisan scribe:generate`
- Les fichiers sont generes dans `resources/views/scribe`, `public/vendor/scribe` et `storage/app/private/scribe`
- Routes auto: `/docs`, `/docs.postman`, `/docs.openapi`

**UML**
- Diagramme de cas d'utilisation: `docs/uml/use-case.puml`
- Diagramme de classes: `docs/uml/class-diagram.puml`

**Endpoints**
- `POST /api/register`
- `POST /api/login`
- `POST /api/logout` (Auth)
- `GET /api/me` (Auth)

- `GET /api/symptoms` (Auth)
- `POST /api/symptoms` (Auth)
- `GET /api/symptoms/{id}` (Auth)
- `PUT /api/symptoms/{id}` (Auth)
- `DELETE /api/symptoms/{id}` (Auth)

- `GET /api/doctors`
- `GET /api/doctors/{id}`
- `GET /api/doctors/search?specialty=&city=`

- `GET /api/appointments` (Auth)
- `POST /api/appointments` (Auth)
- `GET /api/appointments/{id}` (Auth)
- `PUT /api/appointments/{id}` (Auth)
- `DELETE /api/appointments/{id}` (Auth)

- `POST /api/ai/health-advice` (Auth)
- `GET /api/ai/health-advice/history` (Auth)

**Deploiement AWS (EC2 + RDS)**
1. Creer une instance EC2 (Ubuntu) et configurer le security group (HTTP/HTTPS/SSH).
2. Creer une base RDS (MySQL ou PostgreSQL) et autoriser l'acces depuis l'EC2.
3. Installer PHP 8.2+, Nginx/Apache, Composer sur l'EC2.
4. Cloner le depot, configurer `.env` (DB, APP_KEY, IA, etc.).
5. Lancer `composer install`, `php artisan key:generate`, `php artisan migrate --force`.
6. Configurer le virtual host et pointer vers `public/`.

**Tests API**
- Importer les endpoints dans Postman ou Insomnia.
- Utiliser `Authorization: Bearer {token}` pour les routes protegees.
