<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\Return_;
use Symfony\Component\HttpKernel\HttpCache\ResponseCacheStrategy;

class AuthController extends Controller
{       //validamos
    public function funlogin(Request $request){

         //validar
        $credenciales=$request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        //autenticamos
        if(!Auth::attempt($credenciales)){
            return response()->json(["mensaje" =>"credenciales incorecta"], 401);
        }
        //odtener el ussuario autenticado
        $usuario = $request->user();
        //generammos  token

        $token=$usuario->createToken("Token auth")->plainTextToken;

        return response()->json([
            "access_token"=>$token,
            "usuario"=> $usuario
        ], 201);

    }

    public function funRegister(Request $request){
        //validar

        $request->validate([
            "name" =>"required|string",
            "email" => "required|email|unique:users",
            "password" => "required|same:c_password",
        ]);

        //guardar

        $usuario = new User ();
        $usuario->name=$request->name;
        $usuario->email=$request->email;
        $usuario->password=$request->password;
        $usuario->save();




        //VERIFICACION POR CORREO

        event( new Registered($usuario));


        //genera respuesta

       return Response()->json(["mensaje" =>"usuario registrado",201]);
        
    }

    
    public function funProfile(Request $request){
        //odtener el ussuario autenticado
        $usuario = $request->user();

        return response()->json($usuario);
    }

    public function funlogout(Request $request){
        $request->user()->tokens()->delete();

        return response()->json(["mensaje" =>"logout"]);
        
    }

    public function verify($user_id, Request $request){
        if(!$request->hasValidSignature()){
            return response()->json(["mesnsaje"=>"URL EXPIRADO"],401);
        }

        $user = User::findOrfail($user_id);

        if (!$user ->hasVerifiedEmail()){
            $user->markEmailAsVerified();
        }
        return redirect()->to("/");

    }

    public function reset(Request $request){

        if($request->user()->hasVerifiedEmail()){ 
            return response()->json(["mesaje" =>"el correo ya esta verificado"],400);
        }

        $request->user()->sendEmailVerifitionNotification();

        return response()->json(["mensaje"=> "se ha enviar un email de verificacion"]);


        
    }
}
