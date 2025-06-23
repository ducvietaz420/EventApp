<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Hiển thị form đăng nhập
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ], [
            'username.required' => 'Vui lòng nhập tên đăng nhập',
            'password.required' => 'Vui lòng nhập mật khẩu',
        ]);

        $credentials = $request->only('username', 'password');
        
        // Tìm user theo username
        $user = User::where('username', $credentials['username'])->first();
        
        if (!$user) {
            return back()->withErrors([
                'username' => 'Tên đăng nhập không tồn tại trong hệ thống.',
            ])->withInput();
        }
        
        // Kiểm tra tài khoản có bị khóa không
        if (!$user->isActive()) {
            return back()->withErrors([
                'username' => 'Tài khoản của bạn đã bị tạm khóa. Vui lòng liên hệ quản trị viên.',
            ])->withInput();
        }
        
        // Kiểm tra mật khẩu
        if (!Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'password' => 'Mật khẩu không chính xác.',
            ])->withInput();
        }
        
        // Đăng nhập thành công
        Auth::login($user, $request->boolean('remember'));
        
        // Cập nhật thời gian đăng nhập cuối
        $user->update(['last_login_at' => now()]);
        
        $request->session()->regenerate();
        
        return redirect()->intended(route('events.index'))
            ->with('success', 'Đăng nhập thành công! Chào mừng ' . $user->name);
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Đã đăng xuất thành công');
    }
}
