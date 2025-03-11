@if ($errors->has('nfdi-id'))
    <p class="text-danger text-center">{{ $errors->first('nfdi-id') }}</p>
@endif
<a style="display: block; text-align: center;" href="{{ route('nfdi-redirect') }}" title="Log in via Nfdi Login">
    <img style="height: 48px" src="{{ cachebust_asset('vendor/auth-nfdi/IAM_9-1.png') }}">
</a>
