<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_dapat_mengakses_halaman_notifikasi(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $user->notify(new SystemNotification('Test Notifikasi', 'Pesan notifikasi baru', 'info', '#'));

        $response = $this->actingAs($user)->get(route('notifications.page'));

        $response->assertStatus(200);
        $response->assertSee('Pusat Notifikasi');
        $response->assertSee('Test Notifikasi');
        $response->assertSee('Pesan notifikasi baru');
    }

    public function test_dropdown_notifikasi_membatasi_maksimal_5_item_dan_memiliki_link_lihat_selengkapnya(): void
    {
        $user = User::factory()->create(['role' => 'owner']);

        for ($i = 1; $i <= 8; $i++) {
            $user->notify(new SystemNotification("Judul $i", "Pesan $i", 'info', '#'));
        }

        $response = $this->actingAs($user)->get(route('notifications.dropdown'));

        $response->assertStatus(200);
        $response->assertSee('Lihat Selengkapnya');
        $response->assertSee(route('notifications.page'));
    }

    public function test_user_dapat_memfilter_notifikasi_berdasarkan_status_baca(): void
    {
        $user = User::factory()->create(['role' => 'potong']);
        $user->notify(new SystemNotification('Notif Belum Dibaca', 'Pesan 1', 'info', '#'));

        $responseUnread = $this->actingAs($user)->get(route('notifications.page', ['filter' => 'unread']));
        $responseUnread->assertStatus(200);
        $responseUnread->assertSee('Notif Belum Dibaca');
    }

    public function test_user_dapat_menandai_semua_notifikasi_sebagai_dibaca(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $user->notify(new SystemNotification('Notif 1', 'Pesan 1', 'info', '#'));
        $user->notify(new SystemNotification('Notif 2', 'Pesan 2', 'info', '#'));

        $this->assertEquals(2, $user->unreadNotifications()->count());

        $response = $this->actingAs($user)->post(route('notifications.read-all'));

        $response->assertSessionHas('success');
        $this->assertEquals(0, $user->unreadNotifications()->count());
    }
}
