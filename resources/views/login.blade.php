<x-layout>
    @section('styles')
        <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
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
            <div id="help" class="d-flex flex-column">
                <h1 class="fw-bold">Verifieer uw e-mail</h1>
                <p><em>Alleen een @avans.nl e-mail wordt geaccepteerd.</em></p>
                <form action="{{ route('login.submit') }}" method="POST" id="emailForm">
                    @csrf
                    <label for="email">E-mail</label>
                    <input id="email" name="email" type="email" class="form-control p-2 mb-2" value="{{ old('email') }}" placeholder="bv. voorbeeld@avans.nl" value="{{ old('email') }}" required>
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <span class="text-danger fw-bold small">{{ $error }}</span>
                        @endforeach
                    @endif
                    <button type="submit" class="btn btn-primary mt-3 w-100">Verder</button>
                </form>
            </div>
        </main>
    </div>
</x-layout>
