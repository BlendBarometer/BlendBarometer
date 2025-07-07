<div class="row flex-nowrap mx-0 align-items-center" role="group">
    <div class="col-4 d-flex flex-row">
        <p class="mb-0">{{ $question->text }}</p>
        @if ($description)
            <span data-bs-toggle="tooltip" data-bs-title="{{ $description }}" style="cursor: pointer;">
                <i class="bi bi-info-circle-fill"></i>
            </span>
        @endif
    </div>

    @foreach ($descriptions as $des => $answer)
        <div class="col py-4 d-flex flex-column justify-content-center align-items-center ms-4 me-5">
            <input type="radio" class="module-input visually-hidden" name="{{ $fieldName }}"
                id="{{ $fieldName }}_{{ $answer }}" value="{{ $loop->index }}" autocomplete="off"
                {{ !is_null($selectedAnswer) && (int) $selectedAnswer === (int) $loop->index ? 'checked' : '' }} required
                aria-label="{{ $answer }} - {{ $question->text }}">
            <label
                class="module-label border-2 border rounded-circle shadow-sm col py-4 d-flex flex-column justify-content-center align-items-center"
                for="{{ $fieldName }}_{{ $answer }}" style="width:50px; height:50px; max-height:50px">
            </label>
        </div>
    @endforeach
</div>
