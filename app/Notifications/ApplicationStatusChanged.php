<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Application $application) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $job = $this->application->jobPosting;
        $status = $this->application->status;

        return (new MailMessage)
            ->subject('Status lamaran kamu: '.$status->label())
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line('Ada pembaruan status untuk lamaran posisi **'.$job->title.'**.')
            ->line('Status terkini: **'.$status->label().'**')
            ->action('Lihat detail lamaran', route('applicant.applications.show', $this->application))
            ->line('Terima kasih sudah menggunakan '.config('app.name').'.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'application_status_changed',
            'application_id' => $this->application->id,
            'job_title' => $this->application->jobPosting->title,
            'status' => $this->application->status->value,
            'status_label' => $this->application->status->label(),
        ];
    }
}
