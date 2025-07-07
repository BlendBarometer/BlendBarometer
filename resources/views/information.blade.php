<x-progress-step section="Gegevens" title="Wie vult de barometer in"
                 description="We willen graag weten wie dit invult en voor welke module het bedoeld is."
                 current_step_name="information">

    <form method="post" action="{{ route('information') }}">
        @csrf {{-- Required for form security --}}

        <h1>Gegevens</h1>

        <section class="py-4">
            <h2 class="fs-4">Persoonlijke gegevens</h2>
            <div class="row gx-4">
                <div class="col">
                    <label for="name">Naam</label>
                    <input class="form-control" type="text" name="name" id="name" required placeholder="bv. Ids de Jong"
                           value='{{ old('name', session('name')) }}'>
                </div>
                <div class="col">
                    <label for="email">E-mailadres</label>
                    <input class="form-control" type="email" name="email" id="email" required
                           placeholder="bv. voorbeeld@avans.nl" value='{{ old('email', session('email')) }}' disabled>
                </div>
            </div>
        </section>

        <section class="py-4">
            <h2 class="fs-4">Opleiding & Academie gegevens</h2>
            <div class="row gx-4">
                <div class="col">
                    <label for="course">Opleiding</label>
                    <input class="form-control" type="text" name="course" id="course" required
                           placeholder="bv. Software Ontwikkeling" value='{{ old('course', session('course')) }}'>
                </div>
                <div class="col">
                    <label for="academy">Academie</label>
                    <select class="form-select" name="academy" id="academy" required>
                        @php
                            $academyChoice = old('academy', session('academy'));
                        @endphp
                        @if (!$academyChoice)
                            <option value="" disabled selected>Naam van de academie</option>
                        @endif

                        @foreach ($academies as $academy)
                            @if ($academy == $academyChoice)
                                <option value="{{ $academy->name }}" selected>{{ $academy->abbreviation }} - {{ $academy->name }}</option>
                            @else
                                <option value="{{ $academy->name }}">{{ $academy->abbreviation }} - {{ $academy->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </section>

        <section class="py-4">
            <h2 class="fs-4">Module gegevens</h2>
            <div class="row w-50">
                <div class="col pe-0">
                    <label for="module">Module</label>
                    <input class="form-control" name="module" type="text" id="module" required
                           placeholder="bv. Programmeren" value='{{ old('module', session('module')) }}'>
                </div>
            </div>

            <div class="mt-4">
                <label for="summary">Samenvatting</label>
                <textarea class="form-control @error('summary') is-invalid @enderror" rows="4" name="summary"
                          id="summary" maxlength="2000"
                          placeholder="bv. Studenten leren programmeren in Java">{{ old('summary', session('summary')) }}</textarea>
                @error('summary')
                <div class="invalid-feedback">
                    tekst mag maximaal 2000 tekens bevatten
                </div>
                @enderror
            </div>
        </section>

        <x-navigation-buttons-with-submit :previous="$previous ?? route('intermediate.view', 'gegevens')"/>
    </form>
</x-progress-step>

<script>
    document.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            const form = document.querySelector('form');

            if (form.reportValidity()) {
                form.submit();
            }
        }
    })
</script>
