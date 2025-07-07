<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('content')->insert([
            [
                'section_name' => 'intro_description',
                'info' => '<h1>Een&nbsp;<span style="color: rgb(0, 102, 204);">meetinstrument</span>&nbsp;om&nbsp;de&nbsp;kwaliteit&nbsp;van&nbsp;een&nbsp;<span style="color: rgb(0, 102, 204);">Blended&nbsp;module</span>&nbsp;te&nbsp;meten</h1>
                <p>De&nbsp;BlendBarometer&nbsp;is&nbsp;een&nbsp;meetinstrument&nbsp;om&nbsp;de&nbsp;kwaliteit&nbsp;
                van&nbsp;de&nbsp;Blended&nbsp;module&nbsp;te&nbsp;meten.&nbsp;Hiermee&nbsp;is&nbsp;inzichtelijk&nbsp;wat&nbsp;de&nbsp;
                huidige&nbsp;status&nbsp;is&nbsp;en&nbsp;wat&nbsp;er&nbsp;nog&nbsp;nodig&nbsp;is&nbsp;om&nbsp;uiteindelijk&nbsp;tot&nbsp;
                een&nbsp;kwalitatieve&nbsp;en&nbsp;harmonieuze&nbsp;mix&nbsp;van&nbsp;leeractiviteiten&nbsp;te&nbsp;komen.</p>',
                'show' => true,
            ],
            [
                'section_name' => 'intermediate_information',
                'info' => '
                    <main class="d-flex flex-column">
                        <p class="fw-bold">
                            De BlendBarometer is een meetinstrument om de kwaliteit van een Blended module te meten.<br>
                            Hiermee is inzichtelijk wat de huidige status is en wat er nog nodig is om uiteindelijk tot een<br>
                            kwalitatieve en harmonieuze mix van leeractiviteiten te komen.
                        </p>
                        <article>
                            <h6 class="fw-normal"><u>Deel 1: Lesniveau</u></h6>
                            <p>
                                Inventariseer welke online tools en welke fysieke werkvormen je gebruikt in je<br>
                                onderwijsmodule op het gebied van: samenwerken, onderzoeken, informatie verwerven,<br>
                                discussiëren, oefenen en produceren. Dit zijn de 6 leertypes uit het ABC learning Design Model.<br>
                                Deze inventarisatie geeft een beeld van de kwantiteit van je Blend. Er is geen goed of fout.<br>
                                Werkwijze: Geef per leeractiviteit aan of je dit niet gebruikt, af en toe (docentafhankelijk) of<br>
                                vaak (ingericht voor alle docenten). De grafieken verschijnen op het volgende tabblad.
                            </p>
                        </article>
                        <article>
                            <h6 class="fw-normal"><u>Deel 2: Moduleniveau</u></h6>
                            <p>
                                Hier worden vragen gesteld over de verhoudingen binnen je huidige Blended Learning<br>
                                leerarrangement, op module niveau. Er is een onderverdeling gemaakt met vragen vanuit drie<br>
                                verschillende invalshoeken: de samenhang, de organiseerbaarheid en de didactische uitvoering.<br>
                                Werkwijze: Beoordeel de blokken met groen (ja dit doen we), oranje (dit kan beter) of rood (dit doen<br>
                                we niet tot weinig) door op het vak naast de tekst te klikken en te kiezen uit het drop-down menu.
                            </p>
                        </article>
                        <article>
                            <h6 class="fw-normal"><u>Deel 3: Inhoudsrijk gesprek</u></h6>
                            <p>
                                Tijdens het inhoudsrijke gesprek gaan we bespreken wat de status is van de huidige Blend.<br>
                                Waarom zijn er bepaalde keuzes gemaakt? Welke kansen zie je die je kunt oppakken?<br>
                                Wat vind je dat er goed gaat en waar zie je mogelijkheden tot verbetering?<br>
                                De uitkomst met de bijbehorende actiepunten wordenvormgegeven in een adviesrapportage.
                            </p>
                        </article>
                        <article>
                            <h6 class="fw-normal"><u>Deel 4: Adviesrapportage</u></h6>
                            <p>
                                In dit adviesrapport staat beschreven wat de huidige status is van de kwaliteit van de Blend,<br>
                                zowel tekstueel als visueel zodat in 1 oogopslag duidelijk is wat de uitkomst is van de meting.<br>
                                Tevens worden er adviespunten meegegeven om tot een optimale Blend te komen die aansluit<br>
                                bij de leeruitkomsten.
                            </p>
                        </article>
                    </main>
                ',
                'show' => true,
            ],
            [
                'section_name' => 'intermediate_lesson',
                'info' => 'Inventariseer welke online tools en welke fysieke werkvormen je gebruikt in je onderwijsmodule op het gebied van:<br>
                <ul>
                    <li>samenwerken</li>
                    <li>onderzoeken</li>
                    <li>informatie verwerven</li>
                    <li>discussiëren</li>
                    <li>oefenen</li>
                    <li>produceren</li>
                </ul>
                Dit zijn de 6 leertypes uit het <span lang="en"><strong>ABC learning Design Model</strong></span>.<br><br>
                Deze inventarisatie geeft een beeld van de kwantiteit van je Blend. Er is geen goed of fout.<br><br>
                <strong>Werkwijze:</strong> Geef per leeractiviteit aan of je dit:
                <ul>
                    <li>niet gebruikt</li>
                    <li>af en toe</li>
                    <li>vaak</li>
                </ul>',
                'show' => true,
            ],
            [
                'section_name' => 'intermediate_module',
                'info' => '<strong>Werkwijze:</strong> Beoordeel elk onderdeel van je huidige Blended Learning leerarrangement op module niveau. Kies per onderdeel één van de vier opties:<br>
                <ul>
                    <li>Verkennen</li>
                    <li>Toepassen</li>
                    <li>Duidelijk plan</li>
                    <li>Verankerd</li>
                </ul>',
                'show' => true,
            ],
            [
                'section_name' => 'intermediate_results',
                'info' => '<strong>Let op:</strong> Op de volgende pagina worden de grafieken weergegeven die de resultaten van de ingevulde onderdelen visualiseren. Deze grafieken bieden een overzicht van de huidige status en helpen bij het bepalen van de vervolgstappen.',
                'show' => true,
            ],
        ]);
    }
}
