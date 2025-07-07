<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="createCategoryForm" method="POST">
                @csrf
                <input type="hidden" id="categoryName" name="category_name">
                <div class="modal-header">
                    <h5 class="modal-title" id="createCategoryModalLabel">Categorie aanmaken</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sluiten"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categoryText" class="form-label">Categorienaam</label>
                        <input type="text" class="form-control" id="categoryText" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="formSectionSelect" class="form-label">Formuliersectie</label>
                        <select class="form-select" id="formSectionSelect" name="form_section_id" required>
                            <option value="" disabled selected>Kies een sectie</option>
                            @foreach ($formSections as $section)
                                <option value="{{ $section->id }}">{{ $section->description }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Annuleren</button>
                    <button type="submit" class="btn btn-primary">Opslaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var createModal = document.getElementById('createCategoryModal');
        createModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var category = button.getAttribute('data-category');

            var form = document.getElementById('createCategoryForm');
            var action = button.getAttribute('data-action');
            form.action = action;
        });
    });
</script>
