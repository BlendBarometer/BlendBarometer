@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<div class="modal fade" id="createQuestionModal" tabindex="-1" aria-labelledby="createQuestionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="createQuestionForm" method="POST">
                @csrf
                <input type="hidden" id="categoryId" name="question_category_id" value="">
                <input type="hidden" id="subCategoryId" name="sub_category_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="CreateQuestionModalLabel">Vraag toevoegen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sluiten"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="questionText" class="form-label">Vraag</label>
                        <input type="text" class="form-control" id="questionText" name="text" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="description">Extra informatie</label>
                        <textarea class="form-control" id="extraInfoText" name="description" placeholder="Voeg hier extra informatie toe"></textarea>
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
        let createModal = document.getElementById('createQuestionModal');
        createModal.addEventListener('show.bs.modal', function(event) {
            let button = event.relatedTarget;
            let action = button.getAttribute('data-action')
            let catId = button.getAttribute('data-category-id');
            let subCatId = button.getAttribute('data-subcategory-id');
            let checked = button.getAttribute('data-description');
            let form = document.getElementById('createQuestionForm');
            form.action = action;

            document.getElementById('categoryId').value = catId || '';
            document.getElementById('subCategoryId').value = subCatId || '';
        });
    });
</script>
