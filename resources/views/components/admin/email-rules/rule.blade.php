@props([
    'title',
    'academyName',
    'emails'    => collect(),
    'academies' => collect(),
    'usedNames' => collect(),
])

<div class="border-bottom pb-4 mb-4">
    <div class="row g-3 align-items-start">
        <div class="col-md-4">
            <h2 class="h6 fw-semibold mb-2">{{ $title }}</h2>

            @if (is_null($academyName))
                <p class="small text-muted">
                    Wanneer er geen mail voor een specifieke academie is gekozen,
                    gaat het rapport naar deze e-mailadressen.
                </p>
            @else
                <form method="POST"
                      action="{{ route('admin.email-rules.change') }}">
                    @csrf
                    @method('PATCH')

                    <input type="hidden" name="old_academy" value="{{ $academyName }}">

                    <select name="academy_name"
                            class="form-select"
                            onchange="this.form.submit()">
                        @foreach ($academies as $academy)
                            @php
                                $busy     = $usedNames->contains($academy->name);
                                $disabled = $busy && $academy->name !== $academyName;
                            @endphp
                            <option value="{{ $academy->name }}"
                                    @selected($academy->name === $academyName)
                                    {{ $disabled ? 'disabled' : '' }}>
                                {{ $academy->abbreviation }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>

        <div class="col-md-8">
            {{-- Toevoegen nieuwe e-mail --}}
            <form method="POST"
                  action="{{ route('admin.email-rules.store') }}"
                  class="row gy-2 gx-2 align-items-center mb-3">
                @csrf
                <input type="hidden" name="academy_name" value="{{ $academyName }}">

                <div class="col-9">
                    <input type="email"
                           name="email"
                           class="form-control"
                           placeholder="Vul e-mail in"
                           required>
                </div>

                <div class="col-3 d-grid">
                    <button class="btn btn-primary" type="submit">Toevoegen</button>
                </div>
            </form>

            {{-- Bestaande e-mails --}}
            @forelse ($emails as $rule)
                <form method="POST"
                      action="{{ route('admin.email-rules.destroy', $rule) }}"
                      class="d-flex justify-content-between align-items-center border rounded px-3 py-2 mb-2">
                    @csrf
                    @method('DELETE')
                    <span>{{ $rule->email }}</span>
                    <button class="btn btn-sm btn-danger" type="submit" title="Verwijder">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            @empty
                <p class="text-muted small mb-0">Nog geen e-mailadressen toegevoegd.</p>
            @endforelse
        </div>
    </div>
</div>
