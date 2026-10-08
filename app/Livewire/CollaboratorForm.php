<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;

class CollaboratorForm extends Component
{
    use WithFileUploads;

    public $user_id;
    public $name;
    public $cpf;
    public $whatsapp;
    public $roles = [];
    public $photo;
    public $is_active = true;
    public $work_start_time = '08:00';
    public $work_end_time = '19:30';
    public $commission_type = 'percentage';
    public $commission_value;

    public function mount($user = null)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Acesso não autorizado.');

        if ($user) {
            $userModel = User::findOrFail($user);
            $this->user_id = $userModel->id;
            $this->name = $userModel->name;
            $this->cpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $userModel->cpf);
            
            if ($userModel->whatsapp) {
                $this->whatsapp = preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $userModel->whatsapp);
            }

            $this->roles = $userModel->roles ?? [];
            $this->is_active = $userModel->is_active;
            $this->work_start_time = substr($userModel->work_start_time, 0, 5);
            $this->work_end_time = substr($userModel->work_end_time, 0, 5);
            $this->commission_type = $userModel->commission_type ?? 'percentage';
            $this->commission_value = number_format($userModel->commission_value ?? 50, 2, ',', '.');
        } else {
            $this->commission_value = '50,00';
        }
    }

    public function save()
    {
        $cpfLimpo = preg_replace('/[^0-9]/', '', $this->cpf);
        $zapLimpo = preg_replace('/[^0-9]/', '', $this->whatsapp);
        $cleanCommission = str_replace(['.', ','], ['', '.'], $this->commission_value);

        $this->validate([
            'name' => 'required|string|max:255',
            'cpf' => [
                'required',
                'string',
                Rule::unique('users', 'cpf')->ignore($this->user_id),
            ],
            'whatsapp' => 'nullable|string|max:20',
            'roles' => 'required|array|min:1',
            'photo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'work_start_time' => 'required',
            'work_end_time' => 'required',
            'commission_type' => 'required|in:percentage,fixed',
            'commission_value' => 'required',
        ]);

        if (!is_numeric($cleanCommission) || $cleanCommission < 0) {
            $this->addError('commission_value', 'O valor da comissão é inválido.');
            return;
        }

        $photoPath = null;
        if ($this->photo) {
            $photoPath = $this->photo->store('collaborators', 'public');
        }

        if ($this->user_id) {
            $user = User::findOrFail($this->user_id);
            $user->name = $this->name;
            $user->cpf = $cpfLimpo;
            $user->whatsapp = $zapLimpo;
            $user->roles = $this->roles;
            $user->is_active = $this->is_active;
            $user->work_start_time = $this->work_start_time;
            $user->work_end_time = $this->work_end_time;
            $user->commission_type = $this->commission_type;
            $user->commission_value = $cleanCommission;
            
            if ($photoPath) {
                $user->photo_path = $photoPath;
            }

            $user->save();
            session()->flash('success', 'Colaborador atualizado com sucesso!');
        } else {
            $senha = substr($cpfLimpo, 0, 6);
            
            User::create([
                'name' => $this->name,
                'cpf' => $cpfLimpo,
                'whatsapp' => $zapLimpo,
                'password' => Hash::make($senha),
                'roles' => $this->roles,
                'photo_path' => $photoPath,
                'is_active' => $this->is_active,
                'work_start_time' => $this->work_start_time,
                'work_end_time' => $this->work_end_time,
                'commission_type' => $this->commission_type,
                'commission_value' => $cleanCommission,
            ]);
            session()->flash('success', 'Colaborador cadastrado com sucesso!');
        }

        return redirect()->route('collaborators.index');
    }

    public function render()
    {
        return view('livewire.collaborator-form')->layout('components.layouts.app');
    }
}
