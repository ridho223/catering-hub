<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class AdminAlert extends Notification
{    
    public function __construct(
        public string $kind,
        public string $title,
        public string $body,
        public string $route,
        public array $params = [],
    )   {}

    public function via(object $notifiable)
    {
        return ['database'];
    }

    public function toArray(object $notifiable)
    { 
        return [
            'kind' => $this->kind, 'title' => $this->title, 'body' => $this->body, 'route' => $this->route, 'params' => $this->params,
        ];
    }
}