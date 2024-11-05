<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use  Illuminate\Support\srt;
use Illuminate\Validation\Rules\Password as RulesPassword;

use function Laravel\Prompts\password;

class ResetPaswordcontroller extends Controller
{
    
    public function ResetPassword(Request $request){
        $request->validate([
            "email "=>"requeri|email"
        ]);


       // $stoken = Str::Random(64)

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if($status==password::RESET_LINK_SENT){
            return["status" =>__($status)];
        }
     
       
    }



    public function changePassword(Request $request)
    
   {

        $request->validate([
            "token"=>"required",
            "email"=>"required|email",
            "password"=>["required", RulesPassword::default()]
        ]);


        $status = password::reset(
            $request->only("email", "password" ,"password_confirmation","token"),
            function(User $user, string $password ){
                $user->forcefill([
                    "password"=> Hash::make($password),
                ])->SetRememberToken(Str::randon (60));

                $user->save ();
                 
                event(new PasswordReset($user));
            }

        );
        if($status === password::PASSWORD_RESET){
            return response(["mensaje" => "lacontraseña ha sido modificada!!!"]);
        }

        return response(["mensaje"=>__($status)],500);
   }

}
