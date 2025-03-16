<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('login')]
class LoginPage extends Component
{
    public function render()
    {
        return view('livewire.auth.login-page');
    }
}
