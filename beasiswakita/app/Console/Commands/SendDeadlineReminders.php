<?php

namespace App\Console\Commands;

use App\Models\Reminder;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendDeadlineReminders extends Command
{
    protected $signature = 'reminders:send';

    protected $description = 'Kirim email pengingat untuk beasiswa yang tenggatnya sudah mendekati.';

    public function handle(): int
    {
        $sent = 0;

        Reminder::query()
            ->whereNull('notified_at')
            ->whereHas('scholarship', function ($query) {
                $query->where('is_active', true)
                    ->whereDate('deadline', '>=', Carbon::today()->toDateString());
            })
            ->with('scholarship')
            ->chunkById(100, function ($reminders) use (&$sent) {
                foreach ($reminders as $reminder) {
                    $scholarship = $reminder->scholarship;

                    // Belum waktunya diingatkan.
                    if ($scholarship->days_left > $reminder->days_before) {
                        continue;
                    }

                    try {
                        Mail::raw($this->body($reminder), function ($message) use ($reminder, $scholarship) {
                            $message->to($reminder->email)
                                ->subject("Pengingat: {$scholarship->countdown_label} - {$scholarship->name}");
                        });

                        $reminder->update(['notified_at' => now()]);
                        $sent++;
                    } catch (Throwable $e) {
                        // Satu email gagal tidak boleh menghentikan yang lain.
                        report($e);
                    }
                }
            });

        $this->info("Selesai. {$sent} email pengingat dikirim.");

        return self::SUCCESS;
    }

    private function body(Reminder $reminder): string
    {
        $scholarship = $reminder->scholarship;

        return implode("\n", [
            'Halo,',
            '',
            "{$scholarship->countdown_label} untuk beasiswa:",
            $scholarship->name,
            'Penyelenggara: '.$scholarship->organizer,
            'Tenggat pendaftaran: '.$scholarship->deadline->translatedFormat('d F Y'),
            '',
            'Detail & persyaratan: '.route('scholarships.show', $scholarship),
            'Daftar di situs resmi: '.$scholarship->official_url,
            '',
            'Segera siapkan dokumen pendaftaranmu. Semangat!',
            '',
            'BeasiswaKita - Temukan Peluang, Raih Masa Depan.',
        ]);
    }
}
