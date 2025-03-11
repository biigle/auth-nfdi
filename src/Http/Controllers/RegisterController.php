<?php

namespace Biigle\Modules\AuthNFDI\Http\Controllers;

use Biigle\Http\Controllers\Auth\RegisterController as BaseController;
use Biigle\Modules\AuthNFDI\NfdiLoginId;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class RegisterController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function showRegistrationForm()
    {
        if ($this->isRegistrationDisabled()) {
            abort(Response::HTTP_NOT_FOUND);
        }

        if (!session()->has('nfdilogin-token')) {
            return redirect()->route('register');
        }

        return view('auth-nfdi::register');
    }

    /**
     * {@inheritdoc}
     */
    public function register(Request $request)
    {
        if ($this->isRegistrationDisabled()) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $token = $request->session()->get('nfdilogin-token');
        if (!$token) {
            // Users should only arrive at the nfdi register form after completing the
            // authentication which sets the token. The case without token should not
            // happen but you never know...
            return redirect()->route('register');
        }

        try {
            $user = Socialite::driver('nfdilogin')->userFromToken($token);
        } catch (Exception $e) {
            $request->session()->forget('nfdilogin-token');

            return redirect()
                ->back()
                ->withErrors(['nfdi-id' => 'Could not retrieve user details from NFDI Login. Invalid token?']);
        }

        $request->merge([
            'id' => $user->id,
            'email' => $user->email,
            'firstname' => $user->given_name,
            'lastname' => $user->family_name,
            'password' => Str::random(8),
        ]);

        if (NfdiLoginId::where('id', $user->id)->exists()) {
            $request->session()->forget('nfdilogin-token');

            return redirect()
                ->back()
                ->withErrors(['nfdi-id' => 'The NFDI Login ID is already connected with an account.']);
        }

        // Do not use parent::register() because this may be disabled with
        // config('biigle.sso_registration_only').
        return $this->baseRegister($request);
    }

    /**
     * {@inheritdoc}
     */
    protected function validator(array $data)
    {
        $validator = parent::validator($data);

        $rules = $validator->getRules();
        unset($rules['website']);
        unset($rules['homepage']);

        $validator->setRules($rules);
        $validator->setCustomMessages([
            'email.unique' => 'The email has already been taken. You can connect your existing account to NFDI Login in the account authorization settings.',
        ]);

        return $validator;
    }

    /**
     * {@inheritdoc}
     */
    protected function registered(Request $request, $user)
    {
        NfdiLoginId::create([
            'id' => $request->input('id'),
            'user_id' => $user->id,
        ]);

        $request->session()->forget('nfdilogin-token');

        return parent::registered($request, $user);
    }
}
