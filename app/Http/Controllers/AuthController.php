<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    const MIN_CODE = 0;
    const MAX_CODE = 999999;
    const CODE_LENGTH = 6;
    const CODE_ZERO = '0';
    const CODE_EXPIRATION_MINUTES = 10;

    const EMAIL_COOLDOWN_MINUTES = 1;

    public function login()
    {
        if (app()->isLocal()) {
            $email = env('TEST_EMAIL');
            $user = User::where('email', $email)->first();
            if (!$user) {
                $user = User::create([
                    'email' => $email,
                    'email_verified_at' => now(),
                ]);
            }
            Auth::login($user);
            Session::regenerate();
            Session::put('email', $email);
            return redirect()->route('intermediate.view', 'gegevens');
        }

        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('login');
    }

    public function submitLogin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'ends_with:@avans.nl'],
        ]);

        $code = str_pad(random_int(self::MIN_CODE, self::MAX_CODE), self::CODE_LENGTH, self::CODE_ZERO, STR_PAD_LEFT);
        $hash = password_hash($code, PASSWORD_DEFAULT);
        Session::put('verification_code', $hash);
        Session::put('expires_at', now()->addMinutes(self::CODE_EXPIRATION_MINUTES));

        Session::put('email', $request->email);

        $lastSent = Session::get('last_sent');
        if ($lastSent && Carbon::parse($lastSent)->diffInMinutes(now()) < self::EMAIL_COOLDOWN_MINUTES) {
            return back()->withErrors(['cooldown' => 'Wacht even voordat je opnieuw een code aanvraagt.'])->withInput();
        }

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = env('MAIL_HOST');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = env('MAIL_PORT');
            $mail->Username = env('MAIL_USERNAME');
            $mail->Password = env('MAIL_PASSWORD');
            $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));

            $html = View::make('verification-email', ['code' => $code])->render();

            $mail->addAddress($request->email);
            $mail->isHTML(true);
            $mail->Subject = 'Verificatiecode';
            $mail->Body = $html;
            $mail->send();

            Session::put('last_sent', now());
        } catch (\Exception $e) {
            \Log::error('Mail send failed: ' . $e->getMessage());
            return back()->withErrors(['mail' => 'Er is een fout opgetreden bij het versturen van de e-mail. Probeer het later opnieuw.']);
        }

        return redirect()->route('verify');
    }

    public function verify()
    {
        if (!Session::get('email')) {
            return redirect()->route('login');
        } else if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('verify');
    }

    public function submitVerify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $email = Session::get('email');

        $original = Session::get('verification_code');
        $given = $request->code;

        if (Session::get('expires_at') < now()) {
            Session::forget(['verification_code', 'expires_at', 'verify_attempts']);
            return back()->withErrors(['expired' => 'De verificatiecode is verlopen.']);
        }

        $tries = Session::increment('verify_attempts');
        if ($tries > 5) {
            Session::forget(['verification_code', 'expires_at', 'verify_attempts']);
            return back()->withErrors(['verify' => 'Te vaak geprobeerd, vraag een nieuwe code aan.']);
        }

        if (password_verify($given, $original)) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                $user = User::create([
                    'email' => $email,
                    'email_verified_at' => now(),
                ]);
            }
            Auth::login($user);
            Session::regenerate();

            // Set a unique session identifier
            session()->put('session_uid', uniqid());

            return redirect()->route('intermediate.view', 'gegevens');
        } else {
            return back()->withErrors(['code' => 'De opgegeven code komt niet overeen.']);
        }
    }
}
