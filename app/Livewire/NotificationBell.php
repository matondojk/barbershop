<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationBell extends Component
{
    public $isOpen = false;

    public function toggle()
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen) {
            auth()->user()->unreadNotifications->markAsRead();
        }
    }

    public function render()
    {
        return view('livewire.notification-bell', [
            'notifications' => auth()->user()->notifications()->latest()->take(5)->get(),
            'unreadCount' => auth()->user()->unreadNotifications()->count(),
        ]);
    }
}
