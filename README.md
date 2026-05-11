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

## Handmatig deployen en updaten (via FTP/FileZilla)

Als je de applicatie handmatig update via een FTP client zoals FileZilla (zonder SSH/Terminal toegang), volg dan deze stappen.

### 1. Lokaal voorbereiden

Voordat je bestanden overzet, moet je zorgen dat je applicatie 'production-ready' is gebouwd:

- Run `npm run build` lokaal om de laatste frontend assets (CSS/JS) te genereren. Deze komen in de `public/build/` map terecht.

### 2. Welke bestanden transfereren?

Zet je lokale wijzigingen over naar de server, maar let goed op wat je **wel** en **niet** overschrijft!

**Wel uploaden (overschrijven op de server):**

- `app/` (Controllers, Models, etc.)
- `config/` (Configuratie bestanden)
- `database/migrations/` en `database/seeders/` (Voor database updates)
- `public/build/` (De vers gebouwde assets van stap 1)
- `public/` overige gewijzigde assets (zoals nieuwe plaatjes, index.php updaten)
- `resources/views/` (Je Blade templates)
- `routes/` (O.a. `web.php` en `console.php`)
- `vendor/` (Alleen overzetten als je nieuwe Composer packages hebt geïnstalleerd)

**NIET uploaden:**

- `.env` (Bevat je lokale instellingen, deze mag de productie/test server `.env` NOOIT overschrijven!)
- `node_modules/` (Niet nodig op de server, neemt gigantisch veel ruimte in)
- `tests/`, `.git/`, `.github/`, `.editorconfig` (Development bestanden)
- De **inhoud** van de `storage/` map, met name `storage/app/` (geüploade bestanden van gebruikers) mag je nooit overschrijven of wissen, anders raak je data kwijt.

### 3. Caches legen op de server

Als je de bestanden hebt overschreven, merken Laravel (en de bezoekers) dit niet altijd direct op in verband met caching op de server. Om dit op te lossen:

1. Bezoek de map `bootstrap/cache/` op de remote server.
2. Verwijder alle **`.php`** bestanden in deze map (zoals `routes-v7.php`, `routes.php`, `config.php`, `packages.php`).
3. Verwijder **niet** het `.gitignore` bestand in diezelfde map.
*Doordat je deze bestanden verwijdert, forceer je Laravel om de vernieuwde versies direct opnieuw en 'vers' in te laden.*

### 4. Database updaten (Migraties & Seeders)

Aangezien je geen terminal hebt op de server om `php artisan migrate` in te typen, doe je dit via een afgeschermde web-route.

1. Maak in je bestand `routes/web.php` (lokaal) tijdelijk een verborgen route aan met een moeilijke naam:

```php
use Illuminate\Support\Facades\Artisan;

Route::get('/mijn-geheime-update-route-123', function () {
    $output = [];
    
    // Voer de database migraties uit
    Artisan::call('migrate', ['--force' => true]);
    $output[] = 'Migraties uitgevoerd: ' . Artisan::output();

    return implode('<br>', $output);
});
```

1. Upload deze aangepaste `routes/web.php` map naar de server (Vergeet niet stap 3 uit te voeren: **caches legen!**).
2. Bezoek de opgezette URL in je browser (bijv. `https://blendbarometer.nl/mijn-geheime-update-route-123`). Je ziet nu de output van de migratie op je scherm.
3. **BELANGRIJK:** Haal deze tijdelijke route daarna meteen weer uit je lokale bestand, upload `web.php` opnieuw en wis voor een laatste keer de cache!
