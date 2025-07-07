<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editCategoryForm" method="POST" data-category-id="">
      @csrf
      @method('PUT')
      <input type="hidden" id="categoryName" name="category_name">
      {{-- <input type="hidden" id="categoryId" name="category_id"> --}}
        <div class="modal-header">
          <h5 class="modal-title" id="editCategoryModalLabel">Categorie bewerken</h5>
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
                    @foreach($formSections as $section)
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
document.addEventListener('DOMContentLoaded', function () {
    var editModal = document.getElementById('editCategoryModal');
    var form = document.getElementById('editCategoryForm');
    editModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var category = button.getAttribute('data-category');
        var categoryId = button.getAttribute('data-category-id');
        var formSectionId = button.getAttribute('data-form-section-id');
        var action = button.getAttribute('data-action');

        document.getElementById('formSectionSelect').value = formSectionId || 'test';        
        document.getElementById('categoryName').value = category || '';
        document.getElementById('categoryText').value = category || '';
        form.setAttribute('data-category-id', categoryId);
        form.action = action;
    });

    form.addEventListener('submit', function (e) {
        // Verwijder oude hidden input als die bestaat
        var oldInput = form.querySelector('input[name="category_id"]');
        if (oldInput) oldInput.remove();

        // Voeg nieuwe toe
        var categoryId = form.getAttribute('data-category-id');
        var hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'category_id';
        hiddenInput.value = categoryId;
        form.appendChild(hiddenInput);
    });
});
</script>