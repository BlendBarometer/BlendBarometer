<x-progress-step section="Resultaten" title="Overzicht en versturen"
                 description="Het versturen van de ingevulde antwoorden" current_step_name="results">
    <div class="alert alert-warning">
        Uw gegevens zijn nog niet verstuurd. Als u dit venster sluit gaan uw gegevens verloren.
    </div>

    <h1>Overzicht en versturen</h1>
    <p>Je staat op het punt de resultaten van dit formulier definitief te versturen</p>

    <form method="get" action="{{ route('send') }}">
        @csrf

        <div class="d-flex gap-3 justify-content-end mt-2">
            <a href="{{ route('results') }}" class="btn btn-flat">
                <i class="bi bi-arrow-left pe-2"></i>
                Vorige
            </a>
            <button type="submit" class="btn btn-primary" id="submit-button">
                Versturen
                <i class="bi bi-arrow-right ps-2"></i>
            </button>
        </div>
    </form>

    <script>
        document.querySelector('form').addEventListener('submit', () => {
            const submitButton = document.querySelector('#submit-button');
            submitButton.disabled = true;
            submitButton.innerHTML = '<div class="spinner-border" style="height: 1rem; width: 1rem" role="status"></div><span class="ms-2">Bezig...</span>';
        });
        document.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
                const submitButton = document.querySelector('button.btn-primary');

                if (submitButton) {
                    submitButton.click();
                }
            }
        })
    </script>
</x-progress-step>
