<x-progress-step section="Les niveau" title="Vragen op les niveau" description=""
    current_step_name="{{ $subCategory->QuestionCategory->name == 'Fysieke leeractiviteiten' ? 'lessonLevelPhysical' : 'lessonLevelOnline' }}">

    <div class="mb-3">
        <div class="progress" style="height: 10px;">
            <div class="progress-bar bg-success" style="width: {{ 100 * ($currentStep / $totalSteps) }}%"
                aria-valuenow="{{ $currentStep }}" aria-valuemin="0" aria-valuemax="{{ $totalSteps }}"
                role="progressbar"></div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fs-2 fw-bold text-muted mb-1">{{ $currentStep }} van {{ $totalSteps }}
                - {{ $subCategory->QuestionCategory->name }}</h1>
            <h2 class="fs-3 fw-bold mb-1">{{ $subCategory->name }}</h2>
        </div>
        <button class="btn btn-secondary"
            onclick="window.location.href='{{ route('intermediate.view', 'lesniveau') }}'">Terug naar de uitleg
        </button>
    </div>

    <form method="POST" action="{{ route('lesson-level.submit', $subCategory->id) }}">
        <input type="hidden" name="current_step" value="{{ $currentStep }}">
        <p class="text-muted">Hoe vaak gebruik je ...</p>
        @csrf

        @foreach ($questions as $question)
            @php
                $fieldName = 'question_' . $question->id;
                $selectedAnswer = $answers[$subCategory->id][$question->id] ?? null;
                $description = $question->description ?? null;
            @endphp
            <x-lesson-question-component :question="$question" :selectedAnswer="$selectedAnswer" :fieldName="$fieldName" :description="$description" />
        @endforeach

        <div id="custom-question-container">
            @if (!empty($customQuestions))
                @foreach ($customQuestions as $key => $customQuestion)
                    @php
                        $fieldName = $key;
                        $selectedAnswer = $answers[$subCategory->id][$key] ?? null;
                        $questionText = ucfirst(
                            str_replace(
                                '_',
                                ' ',
                                preg_replace('/_\\d+$/', '', str_replace('custom_question_', '', $key)),
                            ),
                        );
                    @endphp
                    <x-lesson-custom-question-component :question="(object) ['id' => $key, 'text' => $questionText]"
                                                 :selectedAnswer="$selectedAnswer" :fieldName="$fieldName" :currentStep="$currentStep"/>
                @endforeach
            @endif
        </div>

        <div class="mb-5">
            <label class="form-label" for="custom_input">
                <strong>
                    Gebruik je een leeractiviteit die hier niet genoemd is?
                </strong>
            </label>
            <div class="input-group" style="width: fit-content">
                <input type="text" class="form-control" id="custom_input" name="custom_input"
                    placeholder="bv. Groepsopdracht">
                <button class="btn btn-primary" type="button" id="addCustomQuestionBtn">Toevoegen</button>
            </div>
        </div>

        <x-navigation-buttons-with-submit :previous="$previous ?? route('lesson-level.previous', $currentStep)" />
    </form>
    <script src="{{ asset('js/custom-question.js') }}"></script>
    <script src="https://unpkg.com/twemoji@latest/dist/twemoji.min.js" crossorigin="anonymous"></script>
</x-progress-step>
