@props(['section', 'title', 'description', 'current_step_name'])
<x-layout>
    <div class="d-flex">
        <aside class="sidebar d-none d-xl-block p-4 bg-white">
            <a href="{{ route('home') }}" class="d-block mb-4">
                <img src="{{ asset('images/logo.svg') }}" alt="BlendBarometer">
            </a>

            <div class="mb-5">
                <p class="text-primary fw-bold mb-0">{{ $section }}</p>
                <h1>{{ $title }}</h1>
                <p class="text-muted">{{ $description }}</p>
            </div>


            @php
                // Update the steps array with the desired step names and labels if needed
                $steps = [
                    [
                        'label' => 'Gegevens',
                        'name' => 'information',
                        'route' => route('information'),
                        'sessionName' => 'name',
                    ],
                    [
                        'label' => 'Les niveau - Fysiek',
                        'name' => 'lessonLevelPhysical',
                        'route' => route('lesson-level', ['id' => 1]),
                        'sessionName' => 'lessonLevelData',
                    ],
                    [
                        'label' => 'Les niveau - Online',
                        'name' => 'lessonLevelOnline',
                        'route' => route('lesson-level', ['id' => 7]),
                        'sessionName' => 'lessonLevelData',
                    ],
                    [
                        'label' => 'Module niveau',
                        'name' => 'moduleLevel',
                        'route' => route('module-level', ['categoryNr' => 1]),
                        'sessionName' => 'moduleLevelData',
                    ],
                    [
                        'label' => 'Overzicht & Resultaten',
                        'name' => 'results',
                        'route' => route('overview-and-results-info'),
                        'sessionName' => 'moduleLevelData',
                    ],
                ];
                $status = 'complete';
            @endphp

            <div>
                @foreach ($steps as $index => $step)
                    @php
                        if ($current_step_name == $step['name']) {
                            $status = 'active';
                        } elseif ($status == 'active') {
                            $status = 'to-do';
                        }
                    @endphp

                    <div class="step-vertical {{ $status }}">
                        <div class="step-vertical-icon">
                            @if ($status == 'active')
                                <div class="bg-white">
                                    <img src="{{ asset('images/doing-step.svg') }}" alt="Huidige stap"/>
                                </div>
                            @elseif ($status == 'complete')
                                <span
                                    class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-check2 fs-4 lh-1"></i>
                                </span>
                            @else
                                <span
                                    class="bg-light border border-2 border-secondary rounded-circle d-flex align-items-center justify-content-center p-2">
                                    <span class="bg-secondary rounded-circle p-2"></span>
                                </span>
                            @endif
                        </div>

                        <div class="step-vertical-content">
                            <strong>
                                @if (session()->has($step['sessionName']))
                                    <a href="{{ $step['route'] }}" class="text-decoration-none text-dark">
                                        {{ $step['label'] }}
                                    </a>
                                @else
                                    {{ $step['label'] }}
                                @endif
                            </strong>

                            @if ($status == 'active')
                                <p class="text-primary">Bezig</p>
                            @elseif ($status == 'complete')
                                <p class="text-success">Afgerond</p>
                            @else
                                <p>Te doen</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </aside>

        <main class="content min-vh-100 flex-grow-1 px-5 py-4 overflow-x-hidden d-none d-lg-block">
            {{ $slot }}
        </main>

        <div class="w-100 min-vh-100 d-flex d-lg-none align-items-center justify-content-center p-4">
            <div class="alert alert-warning" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill fs-2"></i>
                    <h5 class="alert-heading m-0">De BlendBarometer werkt niet op mobiel of kleine schermen</h5>
                </div>
                Gebruik een desktop of laptop om de BlendBarometer te in te vullen.
            </div>
        </div>
    </div>
</x-layout>
