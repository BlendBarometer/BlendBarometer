<x-layout>
    <div class="content min-vh-100">
        <header class="container py-4 mb-4">
            <a href="{{ route('home') }}">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo">
            </a>
        </header>

        @if(session('success'))
            <section class="container">
                <div class="row">
                    <div class="col-7 pe-5">
                        <h4 class="d-flex gap-2 align-items-center text-primary text-uppercase">
                            <i class="bi bi-check-circle-fill"></i>
                            Resultaten verstuurd
                        </h4>
                        <h1 class="mb-4">Bedankt voor het invullen van de BlendBarometer!</h1>
                        <p>
                            Bedankt voor het invullen. Je resultaten zijn opgeslagen en verstuurd naar de ICTO-coach met
                            het
                            e-mailadres: {{ $ictoCoach ?? 'Onbekend' }}.
                        </p>
                        <p>Je kunt deze pagina nu sluiten.</p>
                    </div>
                    <div class="bg-white p-4 rounded shadow-sm border col-5">
                        <h2>Overzicht</h2>
                        <div class="row">
                            <p class="col-4">Academie:</p>
                            <p class="col-8 fw-bold">{{ $academy ?? 'Onbekend' }}</p>
                        </div>
                        <div class="row">
                            <p class="col-4">Opleiding:</p>
                            <p class="col-8 fw-bold">{{ $course ?? 'Onbekend' }}</p>
                        </div>
                        <div class="row">
                            <p class="col-4">Module:</p>
                            <p class="col-8 fw-bold">{{ $module ?? 'Onbekend' }}</p>
                        </div>
                        <div class="row">
                            <p class="col-4">Docent:</p>
                            <p class="col-8 fw-bold">{{ $teacher ?? 'Onbekend' }}</p>
                        </div>
                        <div class="row">
                            <p class="col-4">ICTO-coach:</p>
                            <p class="col-8 fw-bold">{{ $ictoCoach ?? 'Onbekend' }}</p>
                        </div>
                        <div class="row">
                            <p class="col-4">Datum:</p>
                            <p class="col-8 fw-bold">{{ $date ?? 'Onbekend' }}</p>
                        </div>
                    </div>
                </div>
            </section>
        @else
            <section class="container">
                <h4 class="d-flex gap-2 align-items-center text-danger text-uppercase">
                    <i class="bi bi-x-circle-fill"></i>
                    Er is iets misgegaan
                </h4>
                <h1 class="mb-4">Oeps, de resultaten zijn niet verstuurd!</h1>
                @error('error')
                <div>
                    Er is een fout opgetreden bij het versturen van de resultaten.
                    <details class="small opacity-75 mt-2">
                        <summary>Details foutmelding</summary>
                        <p>{{ $message }}</p>
                    </details>
                </div>
                @enderror
                <a href="{{ route('home') }}" class="btn btn-flat mt-3">
                    <i class="bi bi-arrow-left"></i> Terug naar de homepagina
                </a>
            </section>
        @endif
    </div>
</x-layout>
