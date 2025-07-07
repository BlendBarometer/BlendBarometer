<x-layout>
    @section('styles')
        <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet"/>
        <style>
            body {
                background-color: #F4F9F7;
            }
        </style>
    @endsection
    @section('scripts')
        <script src="https://cdn.jsdelivr.net/npm/dompurify@3.0.5/dist/purify.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js" defer></script>
        <script src="{{ asset('js/text-editor.js') }}" defer></script>
    @endsection
    <header>
        @php
            $navbarClasses = 'navbar navbar-expand-lg shadow-sm bg-white';
            if (!isset($noNavbarMargin)) {
                $navbarClasses .= ' mb-4';
            }
        @endphp

        <nav class="{{ $navbarClasses }}">
            <div class="container">
                <a href="{{ route('admin.edit-content') }}" class="navbar-brand">
                    <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="logo">
                </a>

                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarContent"
                        aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 gap-2">
                        <li class="nav-item fw-medium">
                            <a class="nav-link" href="{{ route('admin.academies.index') }}">Academies</a>
                        </li>
                        <li class="nav-item fw-medium">
                            <a class="nav-link" href="{{ route('admin.email-rules.index') }}">E-mail regels</a>
                        </li>
                        <li class="nav-item fw-medium">
                            <a class="nav-link" href="{{ route('admin.edit-lesson-questions') }}">Lesniveau</a>
                        </li>
                        <li class="nav-item fw-medium">
                            <a class="nav-link" href="{{ route('admin.edit-module-questions') }}">Moduleniveau</a>
                        </li>
                        <li class="nav-item fw-medium">
                            <a class="nav-link" href="{{ route('admin.edit-content') }}">Content bewerken</a>
                        </li>
                        <li class="nav-item fw-medium">
                            <a class="nav-link text-danger" href="{{ route('admin.logout') }}">
                                <i class="bi bi-box-arrow-right"></i> Uitloggen
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

    </header>

    <main class="d-none d-lg-block">
        {{ $slot }}
    </main>

    <div class="d-flex d-lg-none align-items-center justify-content-center p-4">
        <div class="alert alert-warning" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-2"></i>
                <h5 class="alert-heading m-0">De admin-omgeving werkt niet op mobiel of kleine schermen</h5>
            </div>
            Gebruik een desktop of laptop om in de admin-omgeving te werken.
        </div>
    </div>
</x-layout>
