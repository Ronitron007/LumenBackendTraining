<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Roles;
use App\Models\mail_verif;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Mail\TestEmail;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     *
     */
    protected $AuthController;

    public function __construct(AuthController $AuthController)
    {
      $this->AuthController = $AuthController;
    }

    public function deleteUser(Request $request,$id){

      $CurrUserId = $request->user()->id;
      
      if ($CurrUserId == $id){
        return response()->json(['error'=>'A user can not delete itself!'],409);
      }

      $user = User::where('id',$id)->first();
      if ($user){
        $user->delete();
        return response()->json(['success'=>'User deleted successfully'],200);

      }
      else{
        return response()->json(['error'=>'User Does not exist'],409);

      }

    }

    public function createUser(Request $request){

      $this->validate($request, [
          'name' => 'required|string',
          'email' => 'required|email',
          'password' => 'required|confirmed',
      ]);

      $message = $this->AuthController->create($request,$request->user()->id);

      return response()->json(['message'=>$message]);

    }

    public function listUsers(Request $request, $num = 'all'){

      if ($num =='all'){
      $userList = User::all();
      return response()->json($userList,200);
      }

      else {
        $userList = User::orderBy('id')->take($num)->get();
        return response()->json($userList,200);
      }

    }

    // public function makerole(Request $request){
    //   $Nrole = new Roles;
    //   $Nrole->role = "normal";
    //   // $Nrole->permissions = [];
    //   $Nrole->save();
    // }





    //
}
