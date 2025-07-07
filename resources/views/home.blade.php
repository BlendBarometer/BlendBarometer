<x-layout>
    @section('styles')
        <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    @endsection
    <main class="container">
        <section class="intro px-4 min-vh-100">
            <div class="d-flex align-items-center justify-content-between py-5">
                <img src="{{ asset('images/logo.svg') }}" alt="BlendBarometer"/>
                <button class="btn btn-outline-secondary font-dyslexia"
                        onclick="toggleDyslexiaMode()">
                    <i class="bi bi-type me-0 me-md-2"></i>
                    <span class="d-none d-md-inline" style="font-size: 14px;">Dyslexie modus</span>
                </button>
            </div>
            <div class="description w-50">
                {!! str_replace('&nbsp;', ' ', $intro_description) !!}
            </div>
            <a href="{{ $continueRoute }}" class="btn btn-primary me-2 mt-3 d-none d-md-inline-block">
                {{ $buttonLabel }}
            </a>
            <div class="alert alert-warning d-md-none" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill fs-2"></i>
                    <h5 class="alert-heading m-0">Werkt niet op mobiel</h5>
                </div>
                Gebruik een desktop of laptop om de BlendBarometer te in te vullen.
            </div>
        </section>
    </main>
</x-layout>
<script>
    document.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            const submitButton = document.querySelector('#continue-button');

            if (submitButton) {
                submitButton.click();
            }
        }
    })
</script>
