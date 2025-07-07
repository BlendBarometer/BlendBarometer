<form action="{{ route('admin.edit-content.chart-update') }}" method="POST" class="w-100 h-100" onreset="hideGraphButtons()">
    @csrf
    @method('PUT')
    <div class="d-flex pb-5">
        <div class="d-flex flex-column gap-5 w-100 px-3">
            @foreach ($lessonLevelPhysicalSubcategories as $category)
                <div>
                    <h3 class="h5">Grafiek fysiek - {{ $category->name }}</h3>
                    <p class="my-1">Uitleg</p>
                    <input type="hidden" name="physical[{{ $loop->index }}][id]" value="{{ $category->id }}">
                    <textarea oninput="showGraphButtons()" style="height: 110px;" class="form-control p-2" name="physical[{{ $loop->index }}][description]">{{ $category->description }}</textarea>
                </div>
            @endforeach
        </div>
        <div class="d-flex flex-column gap-5 w-100 ps-3">
            @foreach ($lessonLevelOnlineSubcategories as $category)
                <div>
                    <h3 class="h5">Grafiek online - {{ $category->name }}</h3>
                    <p class="my-1">Uitleg</p>
                    <input type="hidden" name="online[{{ $loop->index }}][id]" value="{{ $category->id }}">
                    <textarea oninput="showGraphButtons()" style="height: 110px;" class="form-control p-2" name="online[{{ $loop->index }}][description]">{{ $category->description }}</textarea>
                </div>
            @endforeach
        </div>
    </div>
    
    <div class="d-flex flex-column gap-3 w-100 ps-3">
        <div class="h-100">
            <h3 class="h5">Lesniveau algemeen</h3>
            <p class="my-1">Uitleg</p>
            <input type="hidden" name="general_lesson_level[id]" value="{{ $generalLessonLevelDescription->id }}">
            <textarea oninput="showGraphButtons()" style="height: 110px;" class="form-control p-2" name="general_lesson_level[description]">{{ $generalLessonLevelDescription->description }}</textarea>
        </div>
        <div class="h-100">
            <h3 class="h5">Moduleniveau algemeen</h3>
            <p class="my-1">Uitleg</p>
            <input type="hidden" name="general_module[id]" value="{{ $generalModuleDescription->id }}">
            <textarea oninput="showGraphButtons()" style="height: 110px;" class="form-control p-2" name="general_module[description]">{{ $generalModuleDescription->description }}</textarea>
        </div>
    </div>

    <div class="d-flex flex-row justify-content-end mt-3" id="form-buttons">
        <button id="reset-button" type="reset" class="btn btn-outline-primary mb-5 me-2 d-none">Annuleren</button>
        <button id="save-button" type="submit" class="btn btn-primary mb-5 me-2 d-none">Opslaan</button>
    </div>
</form>

<script>
    function showGraphButtons() {
        const resetButton = document.getElementById('reset-button');
        const saveButton = document.getElementById('save-button');
        
        resetButton.classList.remove('d-none');
        saveButton.classList.remove('d-none');
    }

    function hideGraphButtons() {
        const resetButton = document.getElementById('reset-button');
        const saveButton = document.getElementById('save-button');
        
        resetButton.classList.add('d-none');
        saveButton.classList.add('d-none');
    }
</script>
