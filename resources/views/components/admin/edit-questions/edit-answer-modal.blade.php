<div class="modal fade" id="editAnswerModal" tabindex="-1" aria-labelledby="editAnswerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editAnswerForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="oldAnswer" name="old_answer">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAnswerModalLabel">Antwoord bewerken</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sluiten"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="answerText" class="form-label">Antwoord</label>
                        <input type="text" class="form-control" id="answerText" name="text" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="extraInfoAnswerText">Extra informatie</label>
                        <textarea class="form-control" id="extraInfoAnswerText" name="description" placeholder="Voeg hier extra informatie toe"></textarea>
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
        let editModal = document.getElementById('editAnswerModal');
        editModal.addEventListener('show.bs.modal', function(event) {
            let button = event.relatedTarget;
            let answer = button.getAttribute('data-answer');
            let description = button.getAttribute('data-description');
            console.log('description:', description);
            let action = button.getAttribute('data-action');

            let form = document.getElementById('editAnswerForm');
            form.action = action;

            document.getElementById('oldAnswer').value = answer || '';
            document.getElementById('answerText').value = answer || '';
            document.getElementById('extraInfoAnswerText').value = description || '';
        });
    });
</script>
