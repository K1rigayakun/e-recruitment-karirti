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
        $isCompany = ($notifiable->role?->value ?? $notifiable->role ?? null) === 'company';

        if ($isCompany) {
            $applicantName = $this->application->applicantProfile?->user?->name ?? 'Kandidat';

            return (new MailMessage)
                ->subject('Lamaran Baru: '.$job->title)
                ->greeting('Halo, Tim '.$company->company_name.'!')
                ->line('Ada pelamar baru (**'.$applicantName.'**) untuk posisi **'.$job->title.'**.')
                ->line('Silakan tinjau berkas lamaran, portofolio, dan CV kandidat melalui dashboard perusahaan.')
                ->action('Tinjau Lamaran', route('company.applicants.show', [$job->id, $this->application->id]))
                ->line('Terima kasih sudah menggunakan '.config('app.name').'.');
        }

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
        $isCompany = ($notifiable->role?->value ?? $notifiable->role ?? null) === 'company';
        $applicantName = $this->application->applicantProfile?->user?->name ?? 'Kandidat';

        return [
            'type' => $isCompany ? 'new_applicant_received' : 'application_received',
            'application_id' => $this->application->id,
            'job_id' => $this->application->job_posting_id,
            'job_title' => $this->application->jobPosting->title,
            'company_name' => $this->application->jobPosting->companyProfile->company_name,
            'applicant_name' => $applicantName,
            'message' => $isCompany
                ? 'Pelamar baru ('.$applicantName.') untuk posisi '.$this->application->jobPosting->title
                : 'Lamaran kamu untuk posisi '.$this->application->jobPosting->title.' sudah diterima.',
        ];
    }
}
