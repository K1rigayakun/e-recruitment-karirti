<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationReceived extends Notification implements ShouldQueue
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
        $company = $job->companyProfile;

        return (new MailMessage)
            ->subject('Lamaran kamu ke '.$job->title.' diterima')
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line('Lamaran kamu untuk posisi **'.$job->title.'** di **'.$company->company_name.'** sudah kami terima.')
            ->line('Pantau status lamaranmu kapan saja lewat dashboard.')
            ->action('Lihat lamaran', route('applicant.applications.show', $this->application))
            ->line('Semoga berhasil!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'application_received',
            'application_id' => $this->application->id,
            'job_title' => $this->application->jobPosting->title,
            'company_name' => $this->application->jobPosting->companyProfile->company_name,
        ];
    }
}
