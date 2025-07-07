<div class="mb-5">
    <div class="mb-2">
        <a class="btn btn-sm btn-danger me-2" href="{{ route('lesson-level.delete', [$currentStep, $fieldName]) }}">
            <i class="bi bi-trash"></i>
        </a>
        <label for="{{ $fieldName }}" class="fw-semibold">
            {{ $question->text }}
        </label>
    </div>

    <div class="row gap-4 mx-0" role="group">
        <input type="radio" class="form-check-input visually-hidden" name="{{ $fieldName }}" id="{{ $fieldName }}_nooit" value="0" autocomplete="off" {{ $selectedAnswer === '0' ? 'checked' : '' }} required
            aria-label="{{ 'Nooit - ' . $question->text }}">
        <label class="form-check-label border-2 border rounded col py-4 d-flex flex-column justify-content-center align-items-center explicit-focus-visible" for="{{ $fieldName }}_nooit">
            <img src="{{ asset('images/emoji/nooit.png') }}" alt="Emoji met hand voor mond" style="width: 50px; height: 50px;">
            <span class="mt-2 text-nowrap">Nooit</span>
        </label>

        <input type="radio" class="form-check-input visually-hidden" name="{{ $fieldName }}" id="{{ $fieldName }}_af_en_toe" value="1" autocomplete="off" {{ $selectedAnswer === '1' ? 'checked' : '' }} required
            aria-label="{{ 'Af en toe - ' . $question->text }}">
        <label class="form-check-label border-2 border rounded col py-4 d-flex flex-column justify-content-center align-items-center explicit-focus-visible" for="{{ $fieldName }}_af_en_toe">
            <img src="{{ asset('images/emoji/af-en-toe.png') }}" alt="Emoji met lichte glimlach" style="width: 50px; height: 50px;">
            <span class="mt-2 text-nowrap">Af en toe</span>
        </label>

        <input type="radio" class="form-check-input visually-hidden" name="{{ $fieldName }}" id="{{ $fieldName }}_vaak" value="2" autocomplete="off" {{ $selectedAnswer === '2' ? 'checked' : '' }} required
            aria-label="{{ 'Vaak - ' . $question->text }}">
        <label class="form-check-label border-2 border rounded col py-4 d-flex flex-column justify-content-center align-items-center explicit-focus-visible" for="{{ $fieldName }}_vaak">
            <img src="{{ asset('images/emoji/vaak.png') }}" alt="Emoji met grote glimlach" style="width: 50px; height: 50px;">
            <span class="mt-2 text-nowrap">Vaak</span>
        </label>
    </div>
</div>
