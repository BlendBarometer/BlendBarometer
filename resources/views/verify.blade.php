<x-layout>
    @section('styles')
        <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    @endsection
    @section('scripts')
        <script src="{{ asset('js/auth.js') }}" defer></script>
    @endsection

    <div class="position-relative vh-100">
        <div class="position-absolute top-0 start-0 w-100">
            <div class="container mt-5">
                <a href="{{ route('home') }}" class="d-block mb-4">
                    <img src="{{ asset('images/logo.svg') }}" alt="BlendBarometer">
                </a>
            </div>
        </div>
        <main class="d-flex justify-content-center align-items-center h-100">
            <div class="d-flex flex-column">
                <h1 class="fw-bold">Verifieer uw e-mail</h1>
                <p>We hebben een verificatiecode verstuurd <br>naar <strong>{{ session('email') }}</strong>.</p>
                <form action="{{ route('verify.submit') }}" method="POST" id="codeForm" class="d-flex flex-column">
                    @csrf

                    <label for="code">Verificatie code</label>
                    <section class="d-flex justify-content-center gap-2 mb-2">
                        <input type="text" required maxlength="1" class="form-control text-center rectangle">
                        <input type="text" required maxlength="1" class="form-control text-center rectangle">
                        <input type="text" required maxlength="1" class="form-control text-center rectangle">
                        <input type="text" required maxlength="1" class="form-control text-center rectangle">
                        <input type="text" required maxlength="1" class="form-control text-center rectangle">
                        <input type="text" required maxlength="1" class="form-control text-center rectangle">
                    </section>
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <span class="text-danger fw-bold small">{{ $error }}</span>
                        @endforeach
                    @endif
                    <div class="d-flex flex-column align-items-center mb-2">
                        <button type="submit" class="btn btn-primary mt-3 w-100">Verifieer</button>
                        <button type="submit" form="resend" class="btn btn-link mt-2 text-reset p-0">
                            <small>Opnieuw sturen</small>
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <form action="{{ route('login.submit') }}" method="POST" id="resend">
        @csrf
        <input type="hidden" name="email" value="{{ session('email') }}">
    </form>
</x-layout>
