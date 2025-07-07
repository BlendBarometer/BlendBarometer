<x-layout>
    <div class="container">
        <div class="card my-3 my-lg-5 my-xl-10 my-custom-card">
            <div class="card-body">
                <table class="w-100">
                    <tr>
                        <td class="align-middle">
                            <img src="cid:logoCID" alt="Logo" class="d-block" height="40px" width="40px">
                        </td>
                        <td class="align-middle ps-3 pt-1">
                            <h1 class="mb-2">Resultaten BlendBarometer</h1>
                        </td>
                    </tr>
                </table>
                <hr>
                <p class="mb-2">Beste Icto coach,</p>
                <p class="mb-2">Bijgaand de rapportage van de resultaten die zijn voortgekomen uit het invullen van de Blendbarometer. Hieronder de details:</p>
                <ul class="custom-list">
                    <li><strong>Naam:</strong> {{ $name }}</li>
                    <li><strong>Email:</strong> {{ $emailParticipant }}</li>
                    <li><strong>Academie:</strong> {{ $academy }}</li>
                    <li><strong>Module:</strong> {{ $module }}</li>
                    <li><strong>Datum:</strong> {{ $date }}</li>
                </ul>
                <p><strong>Module Samenvatting:</strong> <br>{{ $summary }}</p>
                <br>
                <p class="mb-2">Het rapport staat in de bijlage van deze email.</p>
                <hr>
                <p class="mb-2">Met vriendelijke groet,</p>
                <p class="text-muted">De BlendBarometer website</p>
            </div>
        </div>
    </div>
</x-layout>