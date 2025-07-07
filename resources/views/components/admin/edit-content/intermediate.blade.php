<h5>Pagina tonen</h5>
<p>Moet deze pagina in beeld komen voor de invuller?</p>
<div class="form-check form-switch mb-4">
    <input
        class="form-check-input"
        type="checkbox"
        role="switch"
        id="show-{{ $section }}"
        {{ $show === '1' ? 'checked' : '' }}
        onchange="document.getElementById('show-input-{{ $section }}').value = this.checked ? 'true' : 'false'; showButtons()">
    <label for="show-{{ $section }}">Tonen</label>
</div>
@error('show') <p class="text-danger fw-bold small">{{ $message }}</p> @enderror

<h5>Content</h5>
<form action="{{ route('admin.edit-content.intermediate-update', ['section' => $section]) }}" method="POST"
      class="form w-100 d-flex justify-content-end">
    @csrf
    @method('PUT')

    <input type="hidden" name="show" id="show-input-{{ $section }}" value="{{ $show === '1' ? 'true' : 'false' }}">

    <div class="editor mb-2 bg-white">
        {!! $content !!}
    </div>
</form>
@error('content') <p class="text-danger fw-bold small">{{ $message }}</p> @enderror
