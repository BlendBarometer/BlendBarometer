<x-admin.layout>
    <div class="container">
        <div class="col-lg-8 mx-auto">
            <h1>E-mail regels</h1>
            <p class="text-muted mb-5">
                Beslis welk rapport naar welke e-mail verstuurd moet worden.
                Alleen academies met een regel staan hieronder. Met
                <em>"Nieuwe regel toevoegen"</em> maak je een regel voor een academie zonder regel.
            </p>

            <x-admin.email-rules.rule
                title="Standaard e-mailadressen"
                :academyName="null"
                :emails="$rules->get('_default', collect())"
                :academies="$allAcademies"
                :usedNames="$usedNames" />

            @foreach ($rules->except('_default') as $academyName => $emails)
                <x-admin.email-rules.rule
                    :title="$academyName"
                    :academyName="$academyName"
                    :emails="$emails"
                    :academies="$allAcademies"
                    :usedNames="$usedNames" />
            @endforeach

            <button id="add-rule-btn"
                    class="btn btn-outline-primary btn-lg w-100 mb-4">
                + Nieuwe regel toevoegen
            </button>

            <template id="new-rule-template">
                <div class="border-bottom pb-4 mb-4 new-rule">
                    <div class="row g-3 align-items-start">
                        <div class="col-md-4">
                            <h2 class="h6 fw-semibold mb-2">Nieuwe regel</h2>

                            <select class="form-select academy-select">
                                <option selected disabled value="">Kies academie …</option>
                                @foreach ($availableAcademies as $academy)
                                    <option value="{{ $academy->name }}">
                                        {{ $academy->abbreviation }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-8">
                            <form method="POST"
                                  action="{{ route('admin.email-rules.store') }}"
                                  class="row gy-2 gx-2 align-items-center">
                                @csrf
                                <input type="hidden" name="academy_name">

                                <div class="col-9">
                                    <input type="email"
                                           name="email"
                                           class="form-control email-input"
                                           placeholder="Vul e-mail in"
                                           disabled
                                           required>
                                </div>

                                <div class="col-3 d-grid">
                                    <button type="submit"
                                            class="btn btn-primary"
                                            disabled>
                                        Toevoegen
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <script type="module">
    document.addEventListener('DOMContentLoaded', () => {
        const addBtn   = document.getElementById('add-rule-btn');
        const template = document.getElementById('new-rule-template');
        if (!addBtn || !template) return;

        addBtn.addEventListener('click', () => {
            const frag = template.content.cloneNode(true);
            addBtn.parentNode.insertBefore(frag, addBtn);

            const rule       = addBtn.previousElementSibling;
            const select     = rule.querySelector('.academy-select');
            const emailInput = rule.querySelector('.email-input');
            const submitBtn  = rule.querySelector('button[type="submit"]');
            const hidden     = rule.querySelector('input[name="academy_name"]');

            select.addEventListener('change', () => {
                hidden.value = select.value;
                emailInput.disabled = false;
                submitBtn.disabled  = false;
            });
        });
    });
    </script>

</x-admin.layout>