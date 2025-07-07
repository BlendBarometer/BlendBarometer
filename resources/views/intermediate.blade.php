<x-progress-step :section="$section" :title="$title" :description="$description" current_step_name="{{ $currentStepName }}">
    <div class="mb-3">
        <h1 class="fs-3 fw-bold mb-3">Uitleg {{ $name }}</h1>
        {!! str_replace('&nbsp;', ' ', $content->info) !!}
    </div>

    <x-navigation-buttons :previous="$previous" :next="$next" next-label="Volgende" />
</x-progress-step>

<script>
    document.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            document.querySelector('#next').click();
        }
    })
</script>
