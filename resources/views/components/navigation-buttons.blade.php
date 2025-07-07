<div class="d-flex gap-3 justify-content-end mt-2">
    <a id="previous" href="{{ $previous }}" class="btn btn-flat">
        <i class="bi bi-arrow-left pe-2"></i>
        Vorige
    </a>
    <a id="next" href="{{ $next }}" class="btn btn-primary">
        {{ $nextLabel ?? 'Afronden' }}
        <i class="bi bi-arrow-right ps-2"></i>
    </a>
</div>
