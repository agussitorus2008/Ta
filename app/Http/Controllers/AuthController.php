<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('app.auth.main');
    }

    public function dologin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return redirect()->back()->withInput($request->only('email'))->withErrors(['msg' => 'Akun belum terdaftar']);
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return redirect()->back()->withInput($request->only('email'))->withErrors(['msg' => 'Username & Password salah']);
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user->role == 1) {
                return redirect()->route('admin.main');
            } else {
                return redirect()->route('siswa.main');
            }
        }
        return redirect()->back()->withErrors(['msg' => 'Login details are not valid']);
    }

    public function register()
    {
        return view('app.auth.register');
    }

    public function doregister(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'no_handphone' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ], [
            'nama.required' => 'Nama harus diisi.',
            'no_handphone.required' => 'Nomor handphone harus diisi.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Password tidak sesuai.',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->input('password'));
        $data['role'] = 0;
        $check = User::create($data);

        return redirect()->route("auth.login")->withSuccess('Silahkan Login');
    }

    public function dologout()
    {
        $user = Auth::user();
        Auth::logout($user);
        return redirect()->route("auth.login")->withSuccess('Anda telah logout');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function forget()
    {
        return view('app.auth.forget');
    }

    public function postForget(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('auth.forget-password')
                ->withErrors($validator)
                ->withInput();
        }

        $user = DB::table('users')->where('email', $request->email)->first();

        if ($user) {
            // Email terdaftar, arahkan ke form ganti password
            return view('app.auth.change')->with('email', $request->email);
        } else {
            // Email tidak terdaftar, kembalikan ke form lupa password dengan pesan
            return view('app.auth.forget')->with('error', 'Email tidak terdaftar.');
        }
    }


    public function change(Request $request)
    {
        $email = $request->email;
        return view('app.auth.change', compact('email'));
    }

    public function postChange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ], [
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'password.min' => 'Password minimal harus :min karakter.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('auth.change-password')
                ->withErrors($validator)
                ->withInput();
        }

        $user = DB::table('users')->where('email', $request->email)->first();

        if ($user) {
            // Update password
            DB::table('users')->where('email', $request->email)->update([
                'password' => Hash::make($request->password),
            ]);

            return redirect()->route('auth.login')->with('success', 'Password berhasil diubah. Silakan login.');
        } else {
            return redirect()->route('auth.change-password')->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
