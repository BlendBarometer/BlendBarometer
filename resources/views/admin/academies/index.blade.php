<x-admin.layout>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Academies</h1>
            <a href="{{ route('admin.academies.create') }}" class="btn btn-primary">
                + Nieuwe academie
            </a>
        </div>

        <form class="mb-3">
            <div class="input-group">
                <input name="q" class="form-control"
                       placeholder="Zoek op naam of afkorting"
                       value="{{ $search }}">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive shadow-sm rounded bg-white">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Afkorting</th>
                        <th scope="col">Naam</th>
                        <th scope="col" class="text-end">Acties</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($academies as $academy)
                        <tr>
                            <td class="fw-semibold">{{ $academy->abbreviation }}</td>
                            <td>{{ $academy->name }}</td>
                            <td class="text-end">
                            <a href="{{ route('admin.academies.edit', $academy) }}"
                                class="btn btn-sm btn-primary"
                                title="Bewerken">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <form action="{{ route('admin.academies.destroy', $academy) }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Weet je zeker dat je deze academie wilt verwijderen?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"
                                            title="Verwijder">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-muted text-center">Geen academies gevonden.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $academies->onEachSide(1)->links('components.pagination') }}
        </div>
    </div>
</x-admin.layout>
