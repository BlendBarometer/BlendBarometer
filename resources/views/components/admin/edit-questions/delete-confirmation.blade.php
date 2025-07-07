<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content p-4" style="border-radius: 12px;">
      <div class="modal-body text-center">
        <h4 class="fw-bold mb-3">Weet je zeker dat je dit wilt verwijderen?</h4>
        <p class="mb-4 text-muted">Deze actie kan niet ongedaan gemaakt worden.</p>
        <form id="deleteConfirmationForm" method="POST">
          @csrf
          @method('DELETE')
          <div class="d-flex justify-content-center gap-3">
            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Annuleren</button>
            <button type="submit" class="btn btn-danger px-4" style="font-weight:500;">Verwijder</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var deleteModal = document.getElementById('deleteConfirmationModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var action = button.getAttribute('data-action');
        var form = document.getElementById('deleteConfirmationForm');
        form.action = action;
    });
});
</script>