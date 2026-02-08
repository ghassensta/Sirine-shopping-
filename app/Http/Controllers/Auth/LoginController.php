<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/sirine-shopping/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }



       protected function validateLogin(Request $request)
    {
        $request->validate(
            [
                $this->username() => 'required|email',
                'password'        => 'required|string|min:6',
            ],
            [
                // clé = "champ.règle"
                $this->username().'.required' => '⚠️ Votre adresse e-mail est obligatoire.',
                $this->username().'.email'    => '✉️ Merci de saisir une adresse e-mail valide.',
                'password.required'           => '🔒 Le mot de passe ne peut pas être vide.',
                'password.min'                => '🔒 Le mot de passe doit contenir au moins :min caractères.',
            ]
        );
    }
}
