# BlendBarometer

Dit is de BlendBarometer! Een tool om te bepalen hoe "blended" je onderwijsmodule is.

## Installeren

### Benodigdheden

- Git
- PHP
- Composer
- Laravel

### Stappenplan

1. Clone de repository\
```git clone https://github.com/BlendBarometer/BlendBarometer```
2. Installeer de benodigde packages\
```composer install```\
**Tip**: Voeg voordat je dit doet de folder van het project (of een parent folder) toe aan de "exclusions" van Windows Defender. Dat maakt "Generating optimized autoload files" veel sneller.
Run daarna ```npm install```\ en ```npm run build``` om de benodigde packages van npm te installeren. (Voornamelijk vite)
3. Maak je .env bestand aan met een app-key\
Om dit gemakkelijker te maken staat er in de root van het project een `.env.example` bestand. Deze kun je kopiëren en renamen naar `.env` voor een head-start.\
Doe dat handmatig of run het volgende:\
```copy .env.example .env```\
Vul daarna de app-key in\
```php artisan key:generate```
4. Maak de database aan\
```php artisan migrate```\
(En druk op enter op de vraag "Would you like to create it?")
5. Update de fout in php.ini\
Run ```php --ini``` om het pad te krijgen naar je php.ini bestand.
Ctrl+F daar naar "variables_order". Die heeft een waarde "EGPCS". Verander die naar "GPCS" zonder de 'E'.
Uncomment daarna `;extension=gd` door de regel te veranderen naar `extension=gd`
6. Start de server\
```composer run dev```
7. De website zou nu te zien moeten zijn op `http://localhost:8000/`!

## Deployen naar productie (blendbarometer.nl)

Deze repository bevat nu:

- `scripts/deploy.sh` (server-side deploy script)
- `.github/workflows/deploy.yml` (GitHub Actions workflow)

### 1. Eenmalige server-setup

1. Clone deze repository op je productie server.
2. Zorg dat `.env` op de server op `APP_ENV=production` staat met correcte `DB_*` en `MAIL_*` waarden.
3. Zorg dat de webserver naar de `public/` map wijst.

### 2. Vereiste GitHub Secrets

Voeg in GitHub (Repository -> Settings -> Secrets and variables -> Actions) deze secrets toe:

- `PROD_HOST` (bijv. `blendbarometer.nl` of server IP)
- `PROD_PORT` (meestal `22`)
- `PROD_USER` (SSH user)
- `PROD_SSH_KEY` (private key voor SSH)
- `PROD_APP_PATH` (pad op server, bijv. `/var/www/blendbarometer`)

Optioneel:

- `PROD_PHP_BIN` (default: `php`)
- `PROD_COMPOSER_BIN` (default: `composer`)
- `PROD_NPM_BIN` (default: `npm`)
- `PROD_SKIP_NPM_BUILD` (`1` om frontend build over te slaan, anders `0`)
- `PROD_SKIP_MIGRATIONS` (`1` om migrations over te slaan, anders `0`)

### 3. Deployment uitvoeren

- Push naar `main` triggert automatische deployment.
- Of start handmatig via `Actions -> Deploy Blendbarometer -> Run workflow`.

De workflow logt in op de server en draait:

```bash
bash scripts/deploy.sh
```

Dat script doet o.a.:

- `php artisan down`
- `git pull`
- `composer install --no-dev`
- `npm ci && npm run build` (tenzij overgeslagen)
- `php artisan migrate --force` (tenzij overgeslagen)
- Laravel caches verversen
- `php artisan up`
