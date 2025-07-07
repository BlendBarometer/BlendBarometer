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
4. Maak je .env bestand aan met een app-key\
Om dit gemakkelijker te maken staat er in de root van het project een `.env.example` bestand. Deze kun je kopiëren en renamen naar `.env` voor een head-start.\
Doe dat handmatig of run het volgende:\
```copy .env.example .env```\
Vul daarna de app-key in\
```php artisan key:generate```
5. Maak de database aan\
```php artisan migrate```\
(En druk op enter op de vraag "Would you like to create it?")
6. Update de fout in php.ini\
Run ```php --ini``` om het pad te krijgen naar je php.ini bestand.
Ctrl+F daar naar "variables_order". Die heeft een waarde "EGPCS". Verander die naar "GPCS" zonder de 'E'.
Uncomment daarna `;extension=gd` door de regel te veranderen naar `extension=gd`
8. Start de server\
```composer run dev```
9. De website zou nu te zien moeten zijn op `http://localhost:8000/`!
