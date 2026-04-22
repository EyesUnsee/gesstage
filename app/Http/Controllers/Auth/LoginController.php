<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            // Mettre à jour la dernière connexion
            $user->update(['last_login_at' => now()]);
            
            // Rediriger selon le rôle et le statut du dossier
            return $this->redirectBasedOnRole($user);
        }

        throw ValidationException::withMessages([
            'email' => ['Les identifiants sont incorrects.'],
        ]);
    }
    
    protected function redirectBasedOnRole($user)
    {
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
                
            case 'responsable':
                return redirect()->route('responsable.dashboard');
                
            case 'tuteur':
                return redirect()->route('tuteur.dashboard');
                
            case 'chef-service':
                return redirect()->route('chef-service.dashboard');
                
            case 'candidat':
                // Si le dossier n'est pas encore validé, rediriger vers le dashboard de dépôt
                if (!$user->dossier_valide) {
                    return redirect()->route('candidat.new.dashboard');
                }
                return redirect()->route('candidat.dashboard');
                
            default:
                return redirect('/');
        }
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}
