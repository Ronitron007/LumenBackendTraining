<?php

namespace App\Http\Controllers;
use  App\Models\User;
use Illuminate\Support\Facades\Auth;
use  App\Models\forgotpass;
use Illuminate\Http\Request;

use App\Mail\forgotpassEmail;
use Illuminate\Support\Facades\Mail;

class ForgotController extends Controller
{
    //
    private function genOTP($len){

      return bin2hex(random_bytes($len));
    }

    public function sendOTP(Request $request){

      if($user = User::where('email',$request->input('email'))->first()){

        $verifs = forgotpass::where('user_id',$user->id)->get();

        foreach ($verifs as $singleotp) {
          $singleotp->delete();
        }

        $newotp = new forgotpass;
        $newotp->user_id = $user->id;
        $newotp->otp = $this->genOTP(8);
        $newotp->token = $this->genOTP(10);
        $newotp->save();

        $data = ['OTP' => $newotp->otp];

      Mail::to($request->input('email'))->send(new forgotpassEmail($data));
      }
      return response()->json(['token'=>$newotp->token,'message'=>'OTP sent to your mail'],201);

    }




    public function setNewPassword(Request $request){


      if($passContainer = forgotpass::where('token', $request->input('token'))->first()){
        $ttl = strtotime('24 hours');
        if (strtotime('now') < $ttl + strtotime($passContainer->created_at)) {
          if ($passContainer->otp == $request->input('otp')){
            $user = User::where('id',$passContainer->user_id)->first();
            $this->validate($request, [
              'password' => 'required|confirmed',]);

            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);
            $user->save();
            $passContainer->delete();
            return response()->json(['message'=>'Password successfully Reset']);
          }

          else {
            return response()->json(['message' => 'Incorrect OTP!'], 409);
          }
        }
        else {
          $passContainer->delete();
          return response()->json(['message' => 'OTP Expired.'], 409);
          }
    }
    // }catch (\Exception $e) {
    //     //return error message
    //     return response()->json(['message' => 'User Password Change Failed!'], 409);
    // }

    }





    }
