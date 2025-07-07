<x-layout>
    <div class="container d-none d-lg-block">
        <div class="position-absolute top-0 start-0 w-100">
            <div class="container mt-5">
                <a href="{{ route('admin.edit-content') }}" class="d-block mb-4">
                    <img src="{{ asset('images/logo.svg') }}" alt="BlendBarometer">
                </a>
            </div>
        </div>

        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <h1 class="card-title mb-2 fw-bold">Inloggen</h1>
                <form method="POST" action="{{ route('admin.submit') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email">E-mail</label>
                        <input type="email" class="form-control p-2" id="email"
                               name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                               placeholder="Vul email in">
                    </div>

                    <div class="mb-3">
                        <label for="password">Wachtwoord</label>
                        <input type="password" class="form-control p-2 @error('password') is-invalid @enderror"
                               id="password" name="password" required autocomplete="current-password"
                               placeholder="Vul wachtwoord in">
                    </div>

                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <span class="text-danger fw-bold small">{{ $error }}</span>
                        @endforeach
                    @endif

                    <div>
                        <button type="submit" class="btn btn-primary mt-2 pl-5 pr-5 w-100">Log in</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="min-vh-100 d-flex d-lg-none align-items-center justify-content-center p-4">
        <div class="alert alert-warning" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-2"></i>
                <h5 class="alert-heading m-0">Let op! Werkt niet op kleine schermen</h5>
            </div>
            Niet alle functionaliteiten werken op mobiel of een klein scherm.<br>
            Gebruik een desktop of laptop om in de admin-omgeving te werken.
        </div>
    </div>
</x-layout>
