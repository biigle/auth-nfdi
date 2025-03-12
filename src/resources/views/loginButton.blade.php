<div style="text-align: center; margin-top: 1em;">
    <a style="display: block;" href="{{ route('nfdi-redirect') }}" title="Log in via NFDI Login">
        <img style="width: 216px" src="{{ cachebust_asset('vendor/auth-nfdi/IAM_9-1.png') }}">
    </a>
    @if ($errors->has('nfdi-id'))
        <p class="text-danger text-center">{{ $errors->first('nfdi-id') }}</p>
    @endif
</div>
