<meta name="csrf-token" content="{{ csrf_token() }}">

<x-progress-step section="Resultaten" title="Resultaten" description="Het resultaat van de ingevulde vragen."
                 current_step_name="results">
    <div class="alert alert-warning">
        Uw gegevens zijn nog niet verstuurd. Als u dit venster sluit gaan uw gegevens verloren.
    </div>

    <h1>Resultaten</h1>
    <p>
        Bedankt voor het invullen. Op deze pagina zie je grafieken met de resultaten. We zullen deze resultaten in een
        gesprek bespreken en de grafieken dan toelichten. Klik op direct afronden om door te gaan, of bekijk de
        resultaten en klik onderaan op afronden.
    </p>
    <x-navigation-buttons :previous="$previous ?? route('intermediate.view', 'resultaten')"
                          :next="route('overview-and-send')" nextLabel='Direct Afronden'/>
    <hr class="my-5">
    <div class="card graph-card p-3">
        <div class="row">
            <div class="col">
                <canvas id="lessonLevel" class="bg-white rounded mb-2" role="img"></canvas>
            </div>
            <div class="col">
                <strong>Lesniveau - Algemeen</strong>
                <p class="mb-0">{{ $lessonLevelGeneralDescription[0]->description }}</p>
            </div>
        </div>
    </div>
    <hr class="my-5"/>
    <div class="d-flex flex-row gap-4">
        <div class="d-flex flex-column gap-3 w-50">
            <h2>Fysieke leeractiviteiten</h2>
            @foreach ($lessonLevelPhysicalSubcategories as $i => $category)
                <div class="card graph-card p-3">
                    <canvas id="physical-{{ $category->id }}" class="bg-white rounded mb-2" role="img"></canvas>
                    <strong>{{ $category->name }}</strong>
                    <p class="mb-0">{{ $lessonLevelPhysicalDescriptions[$i]->description }}</p>
                </div>
            @endforeach
        </div>
        <div class="d-flex flex-column gap-3 w-50">
            <h2>Online leeractiviteiten</h2>
            @foreach ($lessonLevelOnlineSubcategories as $i => $category)
                <div class="card graph-card p-3">
                    <canvas id="online-{{ $category->id }}" class="bg-white rounded mb-2" role="img"></canvas>
                    <strong>{{ $category->name }}</strong>
                    <p class="mb-0">{{ $lessonLevelOnlineDescriptions[$i]->description }}</p>
                </div>
            @endforeach
        </div>
    </div>
    <hr class="my-5"/>
    <div class="card graph-card p-3">
        <div class="d-flex justify-content-center">
            <img id="moduleLevelGraphImage" src="/images/barometer.png" alt="de barometer" width="60%"/>
            <canvas id="moduleLevelDataGraph" class="rounded mb-2 position-absolute"></canvas>
            <canvas id="moduleLevelCategoriesGraph" class="rounded mb-2 position-absolute"
                    style="pointer-events: none;"></canvas>
        </div>
        <div class="align-self-end" style="width: fit-content">
            @foreach ($legends as $legend)
                <div class="mb-1 d-flex align-items-center" style="width: fit-content">
                    <span class="p-3 border border-solid me-2" style="background-color: {{ $legend->color }};"></span>
                    <span>{{ $legend->description }}</span>
                </div>
            @endforeach
        </div>
        <strong>Moduleniveau</strong>
        <p class="mb-2">{{ $moduleLevelGeneralDescription[0]->description }}</p>
        <div class="d-flex justify-content-center row">
            <?php $index = 1; ?>
            @foreach ($moduleLevelCategories as $category => $descriptionArray)
                @foreach ($descriptionArray as $description)
                    <div class="col-5">
                        <b>{{ $index }}.</b> {{ $description }}
                    </div>
                        <?php $index++; ?>
                @endforeach
            @endforeach
        </div>
    </div>
    <x-navigation-buttons :previous="$previous ?? route('intermediate.view', 'resultaten')"
                          :next="route('overview-and-send')"/>

</x-progress-step>
<script>
    const legendColors = @json(
        $legends->map(function ($legend) {
            return $legend->color;
        }));
    const lessonLevelSubcategories = {!! json_encode($lessonLevelPhysicalSubcategories) !!};
    const lessonLevelOnlineSubcategories = {!! json_encode($lessonLevelOnlineSubcategories) !!};

    const lessonLevelPhysicalQuestions = {!! json_encode($lessonLevelPhysicalQuestions) !!};
    const lessonLevelOnlineQuestions = {!! json_encode($lessonLevelOnlineQuestions) !!};

    const moduleLevelCategories = {!! json_encode($moduleLevelCategories) !!};

    const moduleLevelCategoriesArray = [];
    for (const [_, item] of Object.entries(moduleLevelCategories)) {
        for (const [_, item2] of Object.entries(item)) {
            moduleLevelCategoriesArray.push(item2);
        }
    }

    const lessonLevelDataOnline = {!! json_encode($lessonLevelDataOnline) !!};
    const lessonLevelDataPhysical = {!! json_encode($lessonLevelDataPhysical) !!};
    const lessonLevelDataAll = {!! json_encode($lessonLevelDataAll) !!};
    const moduleLevelData = {!! json_encode(session()->get('moduleLevelData')) !!};
    const moduleLevelLabels = {!! json_encode($moduleLevelLabels) !!};
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.2.0/chartjs-plugin-datalabels.min.js"
        integrity="sha512-JPcRR8yFa8mmCsfrw4TNte1ZvF1e3+1SdGMslZvmrzDYxS69J7J49vkFL8u6u8PlPJK+H3voElBtUCzaXj+6ig=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
<script src={{ URL::asset('js/custom-tooltip.js') }}></script>
<script src={{ URL::asset('js/results-graphs.js') }}></script>

<script>
    document.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            document.querySelector('#next').click();
        }
    })
</script>
