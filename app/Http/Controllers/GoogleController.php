<?php
namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle(Request $request)
    {
        if($request->has('bookings_date')){
            $request->session()->put('google_bookings_date', $request->bookings_date);
            $request->session()->save();
        }
        if($request->has('bookings_room_id')){
            $request->session()->put('google_bookings_room_id', $request->bookings_room_id);
            $request->session()->save();
        }
        return Socialite::driver('google')->with([
            'approval_prompt' => config('services.google.approval_prompt'),
            'access_type' => config('services.google.access_type'),
            'include_granted_scopes' => config('services.google.include_granted_scopes'),
        ])->scopes(['https://www.googleapis.com/auth/calendar'])->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback(Request $request)
    {
        $googleUser = Socialite::driver('google')->user();
        $user = User::where('email', $googleUser->getEmail());
        if(!$user->exists()){
            if(session('google_bookings_date')){
                return redirect()->route('bookings.create', ['id' => session('google_bookings_room_id')])->with('error', 'Email user tidak dapat ditemukan di database!');
            }
        }

        if(session('google_bookings_date')){
            $request->session()->put('google_access_token', $googleUser->token);
            $request->session()->save();
            return redirect()->route('bookings.create', [
                'id' => session('google_bookings_room_id'),
                'date' => session('google_bookings_date')
            ]);
        }

        return redirect()->route('home');
    }
}
