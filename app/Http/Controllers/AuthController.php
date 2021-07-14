<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use  App\Models\User;
use App\Models\mail_verif;


use App\Mail\TestEmail;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
     */

     private function genOTP($len){

       return bin2hex(random_bytes($len));
     }

    public function create(Request $request, $created_by = null){

      $out = new \Symfony\Component\Console\Output\ConsoleOutput();
      $out->writeln("heyyy");

      if($oldExist = User::where('email',$request->input('email'))->first()){
        if ($oldExist->verified){
          return $response = ['message'=>'This email is already registered. Try logging in or use a different Email Account.'];
        }
        // If not verifed delete the credentials and start over again with new inouts

        $verifs = mail_verif::where('user_id',$oldExist->id)->get();

        foreach ($verifs as $singleotp) {
          $singleotp->delete();
        }

        $oldExist->delete();

      }

      try {

          $user = new User;
          $user->name = $request->input('name');
          $user->email = $request->input('email');
          $plainPassword = $request->input('password');
          $user->password = app('hash')->make($plainPassword);

          $user->verified = false;
          $user->role = 'USER';
          $user->save();

          if ($created_by == null){
          $user->created_by_id = $user->id;

        }
          $user->save();

          //return successful response
          $verif = new mail_verif;
          $verif->user_id = $user->id;
          $verif->otp = $this->genOTP(4);
          $verif->save();

          $url = url("verify/{$user->id}/otp/{$verif->otp}");

          $data = ['url' => $url];

          Mail::to($request->input('email'))->send(new TestEmail($data));

          return $response= ["success"=>"Verification Email Sent! Check Inbox"];

      } catch (\Exception $e) {
          //return error message
          return $response = $e;
      }

    }

    public function register(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);


        $message = $this->create($request);


      return response()->json(['message'=>$message]);
    }


    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {
          //validate incoming request
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {

            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // $email = $request->input('email');
        $user = auth()->user();
        // return response()->json($user);

        if ($user->verified){

          return $this->respondWithToken($token);

        }
        return response()->json(['message'=>'Please Verify Email First']);

    }


    public function me()
    {
        $user = auth()->user();

        return response()->json($user);
    }

    public function logout()
        {
            Auth::logout();
            return response()->json(['message' => 'User logged out'], 201);
        }

    public function verifiymail($id, $otp)
      {

      $user = User::where('id',$id)->first();

      try {

        if($user->verified == false){

            $verif = mail_verif::find($user->id);
            $ttl = strtotime('1 hour');

            if (strtotime('now') < $ttl + strtotime($verif->created_at)) {
                if ($verif->otp == $otp){
                  $user->verified = true;
                  $verif->delete();
                  $user->save();
                  return response()->json(['message' => 'User verified successfully. You can now Login.'],201);

                }
            }
            else{
              $verif->delete();
              return response()->json(['message'=>'Token Expired']);
            }

        }

        else if($user->verified == true){
          return response()->json(['message' => 'User Email already verified.'],201);
          }
      }

      catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Doesnot exist or token expired'], 409);
        }




          return response()->json(['message'=>'Incorrect OTP']);


      }

    //   public function logout()
    // {
    //     Auth::logout();
    //     return response()->json(['message' => 'User logged out successfully'], 201);
    // }

}
