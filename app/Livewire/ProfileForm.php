<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfileForm extends Component
{
    use WithFileUploads;

    public $name;
    public $photo;
    public $password;
    public $password_confirmation;

    public function mount()
    {
        $this->name = auth()->user()->name;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user = auth()->user();
        
        $user->name = $this->name;

        if ($this->photo) {
            $user->photo_path = $this->photo->store('collaborators', 'public');
        }

        if ($this->password) {
            $user->password = Hash::make($this->password);
        }

        $user->save();

        session()->flash('success', 'Perfil atualizado com sucesso!');
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.profile-form')->layout('components.layouts.app');
    }
}
