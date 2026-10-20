<?php

namespace App\Notifications;

use App\Models\CompanyProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompanyVerified extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly CompanyProfile $company,
        public readonly bool $approved,
        public readonly ?string $note = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $msg = (new MailMessage)
            ->greeting('Halo, '.$notifiable->name.'!');

        if ($this->approved) {
            $msg->subject('Akun perusahaan kamu telah diverifikasi')
                ->line('Selamat! Akun **'.$this->company->company_name.'** telah diverifikasi.')
                ->line('Kamu sekarang bisa mempublikasikan lowongan.')
                ->action('Buka dashboard', route('company.dashboard'));
        } else {
            $msg->subject('Verifikasi akun perusahaan ditolak')
                ->line('Maaf, verifikasi akun **'.$this->company->company_name.'** tidak dapat disetujui.')
                ->when($this->note, fn ($m) => $m->line('Alasan: **'.$this->note.'**'))
                ->line('Hubungi admin untuk informasi lebih lanjut.');
        }

        return $msg;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'company_verified',
            'approved' => $this->approved,
            'company_name' => $this->company->company_name,
        ];
    }
}
