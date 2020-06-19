<?php

namespace App\Http\Controllers;
use App\User;
use App\Team;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;


class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
  
    //Fungsi Login Panitia
    public function login(Request $request){
        
        $this->validate($request,[
            'email' => 'required',
            'password' => 'required'
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email',$email)->first();
        $team = Team::where('user_id',$user->id)->first();

        if(!$user && !$team){
            $pesan = [
                'message' => 'login panitia gagal',
                'code' => 401,
                'result' => [
                    'token' => 'null',
                ]
            ];
            return response()->json($pesan,$pesan['code']);

        }
        // else if(!$user){
        //     $pesan = [
        //         'message' => 'login peserta gagal',
        //         'code' => 401,
        //         'result' => [
        //             'token' => 'null',
        //         ]
        //     ];
        // }

        if(Hash::check($password,$user->password_hash)){
            $newToken = $this->getRandomString();

            $user->update([
                'auth_key' => $newToken
            ]);

            $pesan = [
                'message' => 'login success',
                'code' => 200,
                'result' => [
                    'token' => $newToken,
                    'role_team' => $team->team_role ?? 'tidak ada',
                    'user_id' => $team->user_id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'name_team' => $team->name_team
                ]
            ];

        }else{

            $pesan = [
                'message' => 'login failed wrong password',
                'code' => 401,
                'result' => [
                    'token' => null,
                ]
            ];

        }

        return response()->json($pesan,$pesan['code']);
            
    }

    public function loginParticipant(Request $request){
        
        $this->validate($request,[
            'email' => 'required',
            'password' => 'required'
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email',$email)->where('role','participant')->first();

        if(!$user){

            $pesan = [
                'message' => 'login peserta gagal',
                'code' => 401,
                'result' => [
                    'token' => 'null'
                ]
            ];
            return response()->json($pesan,$pesan['code']);
        }

        if(Hash::check($password, $user->password_hash)){

            $newToken = $this->getRandomString();
            $user->update(['auth_key' => $newToken]);

            $result = [

                'result' => [

                    'token' => $newToken,
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'role' => $user->role,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar,
                    'regencies_id' => $user->regencies_id
                ]
                
            ];

            return response()->json($result);
        }else{
            $pesan = [
                'message' => 'Password tidak valid',
                'code' => 401,
                'result' => [
                    'token' => 'null'
                ]
            ];
            return response()->json($pesan,$pesan['code']);
        }
    }

    //Fungsi Register
    public function register(Request $request){
        $this->validate($request,[
            'email' => 'required|unique:user',
            'password' => 'required|min:8'
        ]);

        $email = $request->input('email');
        $password = $request->input('password');
        $name = $request->input('name');
        $phone = $request->input('phone');
        $regencies_id = $request->input('regencies_id');

        $hasPassword = Hash::make($password);

        $data = [
            'email' => $email,
            'password_hash' => $hasPassword,
            'name' => $name,
            'role' => "participant",
            'phone' => $phone,
            'regencies_id' => $regencies_id,
            'avatar' => "upload/images/blank.jpg"
        ];

        if(User::create($data)){
            $pesan = [
                'message' => 'register success',
                'code' => 201
            ];
        }else{
            $pesan = [
                'message' => 'register gagal',
                'code' => 404
            ];
        }

        return response()->json($pesan,$pesan['code']);

    }

    //Fungsi Random String
    public function getRandomString($panjang = 32){
        $karakter = '012345678dssd9abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $panjang_karakter = strlen($karakter);
        $randomString = '';
        for ($i = 0; $i < $panjang; $i++) {
            $randomString .= $karakter[rand(0, $panjang_karakter - 1)];
        }
        return $randomString;
    }

   
}
