<form action="{{ route('admin.edit-content.legenda-update') }}" method="POST" class="w-100 h-100"
      onreset="hideLegendaButtons()">
    @csrf
    @method('PUT')
    <div class="d-flex flex-column gap-4 w-100 px-3">
        @foreach ($legenda as $row)
            <div class="border rounded p-3">
                @php
                    $mla = isset($moduleLevelAnswers) ? $moduleLevelAnswers->firstWhere('id', $row->module_level_answer_id) : null;
                @endphp
                <h4>{{ $mla?->answer}}</h4>
                <div class="mb-2">
                    <label class="form-label">Kleur</label>
                    <input oninput="showLegendaButtons()" type="color" name="legenda[{{ $row->id }}][color]"
                           value="{{ $row->color }}" class="form-control form-control-color"
                           style="width: 60px; height: 40px;" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Naam</label>
                    <input oninput="showLegendaButtons()" type="text" name="legenda[{{ $row->id }}][name]"
                           value="{{ $row->name }}" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Beschrijving</label>
                    <textarea oninput="showLegendaButtons()" name="legenda[{{ $row->id }}][description]"
                              class="form-control" rows="2" required>{{ $row->description }}</textarea>
                </div>
            </div>
        @endforeach
    </div>
    <div class="d-flex flex-row justify-content-end mt-3" id="form-buttons">
        <button id="reset-button-legenda" type="reset" class="btn btn-outline-primary mb-5 me-2 d-none">Annuleren
        </button>
        <button id="save-button-legenda" type="submit" class="btn btn-primary mb-5 me-2 d-none">Opslaan</button>
    </div>
</form>

<script>
    function showLegendaButtons() {
        document.getElementById('reset-button-legenda').classList.remove('d-none');
        document.getElementById('save-button-legenda').classList.remove('d-none');
    }

    function hideLegendaButtons() {
        document.getElementById('reset-button-legenda').classList.add('d-none');
        document.getElementById('save-button-legenda').classList.add('d-none');
    }

</script>
