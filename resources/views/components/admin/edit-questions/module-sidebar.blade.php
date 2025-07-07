@props(['categories', 'formSections'])
<x-admin.layout :noNavbarMargin="true">
    <x-admin.edit-questions.edit-category-modal :formSections="$formSections" />
    <x-admin.edit-questions.create-category-modal :formSections="$formSections" />

    <div class="d-flex">
        <aside class="sidebar d-none d-xl-block p-4 bg-white shadow-sm">
            <a class="text-decoration-none text-dark">
                <h4 class="mt-2">Module niveau</h4>
            </a>

            @foreach ($categories->where('form_section_id', 2) as $cat)
                <div class="d-flex align-items-center justify-content-between">
                    <a href="#category-{{ $cat->id }}"
                        class="d-flex align-items-center justify-content-between text-dark">{{ $cat->name }}</a>
                    <div class="position-relative">
                        <button class="btn btn-sm btn-link text-dark p-0 toggle-delete-menu text-decoration-none"
                            type="button">
                            <span style="font-size: 1.2rem;">&#8226;&#8226;&#8226;</span>
                        </button>

                        <div class="delete-menu position-absolute end-0 mt-2 p-2 bg-white shadow rounded d-none"
                            style="z-index: 1000;">
                            <a href="{{ route('admin.edit-module-questions') }}"
                                class="btn btn-primary mb-2 w-100"style="border-radius: 0.5rem; padding: 0.4rem 1.1rem;"
                                data-bs-toggle="modal" data-bs-target="#editCategoryModal"
                                data-category-id="{{ $cat->id }}" data-category="{{ $cat->name }}"
                                data-form-section-id="2"
                                data-action= '/admin/vragen-bewerken/moduleniveau/categorie-bewerken/{{ $cat->id }}/update';>Bewerken</a>

                            <a href="{{ route('admin.edit-module-questions') }}" class="btn btn-danger"
                                style="border-radius: 0.5rem; padding: 0.4rem 1.1rem;" data-bs-toggle="modal"
                                data-bs-target="#deleteConfirmationModal"
                                data-action="/admin/vragen-bewerken/moduleniveau/categorie-bewerken/{{ $cat->id }}/verwijder">Verwijder</a>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="mt-2">
                <a href="{{ route('admin.edit-module-questions') }}"
                    class="btn btn-sm btn-outline-primary border-2 mb-2" style="padding: 0.4rem 1.1rem;"
                    data-bs-toggle="modal" data-bs-target="#createCategoryModal"
                    data-action="/admin/vragen-bewerken/moduleniveau/categorie-bewerken/create">Categorie toevoegen</a>
            </div>
        </aside>

        <main class="content flex-grow-1 px-5 py-4 overflow-x-hidden">
            {{ $slot }}
        </main>
    </div>
</x-admin.layout>
