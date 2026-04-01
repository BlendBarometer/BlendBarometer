<h5>Module gegevens vragen</h5>
<p class="text-muted">
    Bewerk per tekstveld de titel en de instructie/placeholder die invullers zien op de gegevenspagina.
</p>

<style>
    .module-info-active-toggle,
    .module-info-active-toggle:checked {
        --bs-form-check-bg-image: var(--bs-form-switch-bg);
    }
</style>

<form action="{{ route('admin.edit-content.module-information-create') }}" method="POST" class="mb-3">
    @csrf
    <button type="submit" class="btn btn-outline-primary">Veld toevoegen</button>
</form>

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        Er zijn validatiefouten gevonden. Controleer de velden en probeer opnieuw.
    </div>
@endif

<form action="{{ route('admin.edit-content.module-information-update') }}" method="POST" class="w-100">
    @csrf
    @method('PUT')

    @forelse ($fields as $field)
        @php
            $oldActive = old("fields.{$field->id}.is_active");
            $isActive = $oldActive === null
                ? (bool) $field->is_active
                : ($oldActive === 'true' || $oldActive === true || $oldActive === '1');
        @endphp

        <section class="border rounded bg-white p-3 mb-3">
            <div class="row g-3 align-items-center">
                <div class="col-12 col-md-6">
                    <label for="field-title-{{ $field->id }}" class="form-label">Module Titel</label>
                    <input
                        id="field-title-{{ $field->id }}"
                        type="text"
                        name="fields[{{ $field->id }}][title]"
                        class="form-control"
                        value="{{ old("fields.{$field->id}.title", $field->title) }}"
                        required
                    >
                </div>

                <div class="col-12 col-md-3">
                    <label for="field-sort-{{ $field->id }}" class="form-label">De Volgorde</label>
                    <input
                        id="field-sort-{{ $field->id }}"
                        type="number"
                        min="0"
                        name="fields[{{ $field->id }}][sort_order]"
                        class="form-control"
                        value="{{ old("fields.{$field->id}.sort_order", $field->sort_order) }}"
                        required
                    >
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label d-none d-md-block">Verwijderen</label>
                    <button
                        type="button"
                        class="btn btn-outline-danger module-info-delete-button w-100"
                        data-delete-url="{{ route('admin.edit-content.module-information-delete', ['field' => $field->id]) }}"
                    >
                        Veld verwijderen
                    </button>
                </div>
            </div>

            <div class="mt-3">
                <label for="field-placeholder-{{ $field->id }}" class="form-label">Instructie tekst</label>
                <textarea
                    id="field-placeholder-{{ $field->id }}"
                    rows="3"
                    name="fields[{{ $field->id }}][placeholder]"
                    class="form-control"
                >{{ old("fields.{$field->id}.placeholder", $field->placeholder) }}</textarea>
            </div>

            <input
                type="hidden"
                name="fields[{{ $field->id }}][maxlength]"
                value="{{ old("fields.{$field->id}.maxlength", $field->maxlength) }}"
            >

            <div class="form-check form-switch mt-3">
                <input
                    class="form-check-input module-info-active-toggle"
                    type="checkbox"
                    role="switch"
                    id="field-active-{{ $field->id }}"
                    {{ $isActive ? 'checked' : '' }}
                >
                <label id="field-active-label-{{ $field->id }}" class="" for="field-active-{{ $field->id }}">
                    {{ $isActive ? 'Actief' : 'Inactief' }}
                </label>
            </div>

            <input
                type="hidden"
                name="fields[{{ $field->id }}][is_active]"
                id="field-active-input-{{ $field->id }}"
                value="{{ $isActive ? 'true' : 'false' }}"
            >
        </section>
    @empty
        <p class="text-muted fst-italic">Er zijn nog geen module-gegevens velden beschikbaar.</p>
    @endforelse

    @if ($fields->isNotEmpty())
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Alles Opslaan</button>
            <button type="button" class="btn btn-outline-primary" onclick="window.location.reload()">Annuleren</button>
        </div>
    @endif

</form>

<br />

@once
    <script src="{{ asset('js/module-information-fields.js') }}" defer></script>
@endonce
