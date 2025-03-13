<li class="list-group-item clearfix">
    @if ($errors->has('nfdi-id'))
        <p class="text-danger">{{ $errors->first('nfdi-id') }}</p>
    @endif
    <img style="width: 153px" src="{{ cachebust_asset('vendor/auth-nfdi/IAM_9-1.png') }}">
    @if (\Biigle\Modules\AuthNfdi\NfdiLoginId::where('user_id', $user->id)->exists())
        <span class="label label-success" title="Your account is connected with NFDI Login">connected</span>
    @else
        <span class="label label-default" title="Your account is not connected with NFDI Login">not connected</span>
        <a href="{{ route('nfdi-redirect') }}" title="Connect your account with NFDI Login" class="btn btn-default pull-right">
            Connect
        </a>
    @endif
</li>

