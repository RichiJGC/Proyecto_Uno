<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class VeryfiController extends Controller
{

    /**
     * Muestra la vista de verificación de código.
     *
     * @return \Illuminate\View\View
     */

     public function create(Request $request)
    {

        $email = $request->email;
        $user = User::where('email', $request->email)->first();
        if ($user) {
            // Pasar los valores de correo electrónico y contraseña a la vista
            return view('auth.verification', ['email' => $email]);
        } else {
            return redirect()->route('login.index');
        }
    }






    /**
     * Verifica el código de verificación ingresado por el usuario 
     * y realiza las acciones correspondientes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'verification_code' => ['required', 'numeric'],
            ]);

            $user = User::where('email', $request->input('email'))->first();

            $encryptedCode = $user->codem;

            if (password_verify($request->input('verification_code'), $encryptedCode)) {
                // Si coincide, limpiar el código de verificación en la base de datos
                $user->codem = '';
                $user->save();

                Auth::login($user);

                return redirect()->route('home')->with('success', 'Código de confirmación correcto');
            } else {
                return back()->withErrors([
                    'message' => 'Código incorrecto',
                ]);
            }
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error desconocido: ' . $e->getMessage());
            return back()->withErrors(['message' => 'Se produjo un error inesperado. Por favor, inténtalo de nuevo.']);
        }
    }
}
