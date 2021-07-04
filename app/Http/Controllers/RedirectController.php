<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectController extends Controller
{
    //
    public function index()
    {
        if (Auth::user()->hasRole(User::SUPER_ADMIN)) {
            return redirect()->route('users.index');
        } elseif (Auth::user()->hasRole(User::EMPLOYEE)) {
            die('Вы успешно вошли, можете пользоваться кнопкой WhatsApp из СБИС');
        } else {
            return redirect()->route('whatsapp.index');
        }
    }
}
