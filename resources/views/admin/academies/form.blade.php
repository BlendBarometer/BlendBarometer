<x-admin.layout>
    <div class="container">
        <h1 class="h3 mb-4">
            {{ $academy->exists ? 'Academie bewerken' : 'Nieuwe academie' }}
        </h1>

        <form method="POST"
              action="{{ $academy->exists
                    ? route('admin.academies.update', $academy)
                    : route('admin.academies.store') }}">
            @csrf
            @if($academy->exists) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label" for="abbreviation">Afkorting</label>
                <input id="abbreviation"
                       type="text"
                       name="abbreviation"
                       value="{{ old('abbreviation', $academy->abbreviation) }}"
                       class="form-control @error('abbreviation') is-invalid @enderror"
                       maxlength="10"
                       required>
                @error('abbreviation') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="name">Naam</label>
                <input id="name"
                       type="text"
                       name="name"
                       value="{{ old('name', $academy->name) }}"
                       class="form-control @error('name') is-invalid @enderror"
                       required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.academies.index') }}" class="btn btn-flat">
                    <i class="bi bi-arrow-left"></i> Terug
                </a>
                <button class="btn btn-primary">
                    {{ $academy->exists ? 'Opslaan' : 'Toevoegen' }}
                </button>
            </div>
        </form>
    </div>
</x-admin.layout>
