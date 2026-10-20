<?php

namespace App\Notifications;

use App\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InterviewRescheduleRequested extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Interview $interview) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $job = $this->interview->application->jobPosting;
        $scheduled = $this->interview->scheduled_at->setTimezone('Asia/Jakarta');
        $tanggal = $scheduled->translatedFormat('l, d M Y H:i').' WIB';
        $durasi = $this->interview->duration_minutes.' menit';
        $mode = $this->interview->mode->label();

        return (new MailMessage)
            ->subject('Pelamar minta jadwal ulang interview: '.$job->title)
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line('Pelamar minta jadwal ulang interview untuk posisi **'.$job->title.'**.')
            ->line('Waktu: **'.$tanggal.'**')
            ->line('Durasi: **'.$durasi.'** | Mode: **'.$mode.'**')
            ->when($this->interview->meeting_url,
                fn ($m) => $m->line('Tautan meeting: **'.$this->interview->meeting_url.'**'))
            ->when($this->interview->location && ! $this->interview->meeting_url,
                fn ($m) => $m->line('Lokasi: **'.$this->interview->location.'**'))
            ->action('Lihat detail', route('applicant.applications.show', $this->interview->application))
            ->line('Terima kasih sudah menggunakan '.config('app.name').'.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'InterviewRescheduleRequested',
            'interview_id' => $this->interview->id,
            'application_id' => $this->interview->application_id,
            'job_title' => $this->interview->application->jobPosting->title,
            'scheduled_at' => $this->interview->scheduled_at->toIso8601String(),
        ];
    }
}
