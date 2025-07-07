<x-admin.edit-questions.lesson-sidebar :subCategories="$lessonSubCategories" :formSections="$formSections">
    <x-admin.edit-questions.edit-lesson-question-modal />
    <x-admin.edit-questions.create-lesson-question-modal />
    <x-admin.edit-questions.delete-confirmation />

    <h1 class="mb-3">Les niveau</h1>

    @foreach ($lessonCategories as $cat)
        <section id="category-{{ $cat->id }}">
            <h3 class="mt-4">{{ $cat->name }}</h3>

            @foreach ($lessonSubCategories->where('question_category_id', $cat->id) as $subCat)
                <section id="subcategory-{{ $subCat->id }}" class="mt-4">
                    <h4>{{ $subCat->name }}</h4>

                    @php
                        if (isset($lessonQuestions)) {
                            $questions = $lessonQuestions->where('sub_category_id', $subCat->id);
                        } else {
                            $questions = collect();
                        }
                    @endphp

                    @forelse ($questions as $question)
                        <div
                            class="d-flex align-items-center justify-content-between border rounded px-3 py-2 mb-2 bg-white">
                            <div class="fw-semibold">
                                {{ $question->text }}
                                @if ($question->description)
                                    <span data-bs-toggle="tooltip" data-bs-title="{{ $question->description }}"
                                        style="cursor: pointer;">
                                        <i class="bi bi-info-circle-fill"></i>
                                    </span>
                                @endif
                            </div>
                            <div class="d-flex align-items-center">
                                <a href="{{ route('admin.edit-lesson-questions') }}" class="btn btn-primary btn-sm me-3"
                                    style="padding: 0.4rem 1.1rem;" data-bs-toggle="modal"
                                    data-bs-target="#editQuestionModal" data-question-id="{{ $question->id }}"
                                    data-question="{{ $question->text }}" data-has-label="0"
                                    data-description="{{ $question->description }}"
                                    data-action='/admin/vragen-bewerken/lesniveau/{{ $question->id }}/update'>Bewerken</a>
                                <div class="position-relative">
                                    <button
                                        class="btn btn-sm btn-link text-dark p-0 toggle-delete-menu text-decoration-none"
                                        type="button">
                                        <span style="font-size: 1.2rem;">&#8226;&#8226;&#8226;</span>
                                    </button>

                                    <div class="delete-menu position-absolute end-0 mt-2 p-2 bg-white shadow rounded d-none"
                                        style="z-index: 1000;">
                                        <a href="{{ route('admin.edit-lesson-questions') }}" class="btn btn-danger"
                                            style="border-radius: 0.5rem; padding: 0.4rem 1.1rem;"
                                            data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"
                                            data-action="/admin/vragen-bewerken/lesniveau/{{ $question->id }}/verwijder">Verwijder</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted fst-italic">Nog geen vragen toegevoegd</p>
                    @endforelse

                    <div class="mt-2">
                        <a href="{{ route('admin.edit-lesson-questions') }}"
                            class="btn btn-sm btn-outline-primary border-2" style="padding: 0.4rem 1.1rem;"
                            data-bs-toggle="modal" data-bs-target="#createQuestionModal"
                            data-category-id="{{ $cat->id }}" data-subcategory-id="{{ $subCat->id }}"
                            data-action="/admin/vragen-bewerken/lesniveau/create">Vraag toevoegen</a>
                    </div>
                </section>
            @endforeach
        </section>
    @endforeach
    <script>
        document.querySelectorAll('.toggle-delete-menu').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const menu = this.nextElementSibling;
                document.querySelectorAll('.delete-menu').forEach(m => {
                    if (m !== menu) m.classList.add('d-none');
                });
                menu.classList.toggle('d-none');
                e.stopPropagation();
            });
        });

        document.addEventListener('click', function() {
            document.querySelectorAll('.delete-menu').forEach(menu => menu.classList.add('d-none'));
        });
    </script>

</x-admin.edit-questions.lesson-sidebar>
