<?php

namespace Tests\Feature;

use App\Models\Reminder;
use App\Models\Scholarship;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ScholarshipPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_home_page_loads(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Temukan Peluang')
            ->assertSee('Beasiswa Segera Tutup');
    }

    public function test_scholarship_index_loads_and_filters_by_level(): void
    {
        $this->get('/beasiswa')->assertOk()->assertSee('LPDP');

        $this->get('/beasiswa?jenjang=SMP')
            ->assertOk()
            ->assertSee('Jenjang SMP/MTs')
            ->assertDontSee('LPDP');
    }

    public function test_scholarship_index_searches_by_name_and_organizer(): void
    {
        $this->get('/beasiswa?q=Djarum')
            ->assertOk()
            ->assertSee('Djarum Beasiswa Plus')
            ->assertDontSee('Salemba');

        $this->get('/beasiswa?q=Tanoto')
            ->assertOk()
            ->assertSee('Beasiswa Tanoto Foundation');
    }

    public function test_scholarship_index_ignores_invalid_filters(): void
    {
        $this->get('/beasiswa?jenjang=XYZ&status=abc&q[]=oops')->assertOk();
    }

    public function test_scholarship_detail_shows_grouped_requirements(): void
    {
        $this->get('/beasiswa/lpdp-reguler')
            ->assertOk()
            ->assertSee('Persyaratan Akademik')
            ->assertSee('Persyaratan Administrasi')
            ->assertSee('Persyaratan Umum')
            ->assertSee('Daftar di Situs Resmi');

        $this->get('/beasiswa/tidak-ada')->assertNotFound();
    }

    public function test_calendar_loads_and_falls_back_on_invalid_month(): void
    {
        $this->get('/kalender')->assertOk()->assertSee('Kalender Pendaftaran');
        $this->get('/kalender?bulan=2026-12')->assertOk();
        $this->get('/kalender?bulan=bukan-bulan')->assertOk();
    }

    public function test_document_guide_page_loads(): void
    {
        $this->get('/panduan-dokumen')
            ->assertOk()
            ->assertSee('Motivation Letter')
            ->assertSee('Surat Keterangan Tidak Mampu');
    }

    public function test_deadline_countdown_and_badge(): void
    {
        $today = Carbon::today();

        $scholarship = Scholarship::create([
            'name' => 'Beasiswa Uji',
            'slug' => 'beasiswa-uji',
            'organizer' => 'Penyelenggara Uji',
            'level' => 'Umum',
            'summary' => 'Ringkasan',
            'description' => 'Deskripsi',
            'benefits' => ['Manfaat'],
            'open_date' => $today->copy()->subDay(),
            'deadline' => $today->copy()->addDays(5),
            'official_url' => 'https://example.com',
        ]);

        $this->assertSame(5, $scholarship->days_left);
        $this->assertSame('closing_soon', $scholarship->status);
        $this->assertSame('Tutup dalam 5 hari', $scholarship->countdown_label);
        $this->assertSame('text-bg-danger', $scholarship->badge_class);
    }

    public function test_user_can_register_and_update_reminder(): void
    {
        $scholarship = Scholarship::where('slug', 'pip-smp')->firstOrFail();

        $this->post(route('reminders.store', $scholarship), [
            'email' => 'Siswa@Example.com',
            'days_before' => 3,
        ])->assertRedirect();

        $this->assertDatabaseHas('reminders', [
            'scholarship_id' => $scholarship->id,
            'email' => 'siswa@example.com',
            'days_before' => 3,
        ]);

        // Email yang sama tidak menggandakan data, hanya memperbarui.
        $this->post(route('reminders.store', $scholarship), [
            'email' => 'siswa@example.com',
            'days_before' => 7,
        ]);

        $this->assertSame(1, Reminder::count());
        $this->assertSame(7, Reminder::first()->days_before);
    }

    public function test_reminder_requires_valid_email(): void
    {
        $scholarship = Scholarship::where('slug', 'pip-smp')->firstOrFail();

        $this->post(route('reminders.store', $scholarship), [
            'email' => 'bukan-email',
            'days_before' => 7,
        ])->assertSessionHasErrors('email');

        $this->assertSame(0, Reminder::count());
    }

    public function test_reminder_command_only_notifies_when_due(): void
    {
        $due = Scholarship::where('slug', 'pip-smp')->firstOrFail();        // tutup +5 hari
        $notDue = Scholarship::where('slug', 'kip-kuliah')->firstOrFail();  // tutup +13 hari

        $dueReminder = Reminder::create(['scholarship_id' => $due->id, 'email' => 'a@example.com', 'days_before' => 7]);
        $notDueReminder = Reminder::create(['scholarship_id' => $notDue->id, 'email' => 'b@example.com', 'days_before' => 1]);

        $this->artisan('reminders:send')->assertSuccessful();

        $this->assertNotNull($dueReminder->fresh()->notified_at);
        $this->assertNull($notDueReminder->fresh()->notified_at);
    }
}
