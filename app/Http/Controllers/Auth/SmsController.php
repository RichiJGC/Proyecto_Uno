<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class SmsController extends Controller
{

    /**
     * Muestra el formulario de verificación de teléfono o 
     * envía un código de verificación si ya existe un número de teléfono asociado al usuario autenticado.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        $user = User::where('email', $request->email)->first();

        if ($user) {
            // Pasar los valores de correo electrónico y contraseña a la vista
            return view('auth.phone', ['email' => $email, 'password' => $password]);
        } else {
            return redirect()->route('login.index');
        }
    }
    /**
     * Almacena el número de teléfono proporcionado por el usuario, 
     * genera y envía un código de verificación por SMS.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'phone_number' => ['required', 'numeric'],
            ]);

            $rawCode = mt_rand(100000, 999999);

            $code = password_hash($rawCode, PASSWORD_DEFAULT);

            $user = User::where('email', $request->input('email'))->first();
            Log::info('Correo electrónico: ' . $request->input('email'));
            Log::info('Contraseña: ' . $request->input('password'));

            $phoneNumber = $request->input('phone_number');
            $phoneNumber = strpos($phoneNumber, '+') === 0 ? $phoneNumber : '+' . $phoneNumber;
            $phoneNumberWithCountryCode = '+52' . substr($phoneNumber, 1);

            $user->update(['codem' => $code]);
            $user->update(['phone_number' => $phoneNumberWithCountryCode]);

            $this->sendSms($phoneNumberWithCountryCode, "Su código de verificación: $rawCode");

            return redirect()->route('auth.verification', ['email' => $user->email]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (TwilioException $e) {
            Log::error('Error al enviar SMS: ' . $e->getMessage());
            return back()->withErrors(['message' => 'Error al enviar SMS. Por favor, inténtalo de nuevo.']);
        } catch (\Exception $e) {
            Log::error('Error desconocido: ' . $e->getMessage());
            return back()->withErrors(['message' => 'Se produjo un error inesperado. Por favor, inténtalo de nuevo.']);
        }
    }

    /**
     * Envía un mensaje de texto (SMS) con el código de 
     * verificación al número de teléfono especificado.
     *
     * @param  string  $to
     * @param  string  $body
     * @return void
     */
    protected function sendSms($to, $body)
    {

        $twilioSid = env('TWILIO_SID');
        $twilioToken = env('TWILIO_AUTH_TOKEN');
        $twilioNumber = env('TWILIO_PHONE_NUMBER');

        $twilio = new Client($twilioSid, $twilioToken);

        $message = $twilio->messages
            ->create(
                $to,
                [
                    'from' => $twilioNumber,
                    'body' => $body,
                ]
            );
    }
}
