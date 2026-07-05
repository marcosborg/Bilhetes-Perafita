<?php

namespace Tests\Feature;

use App\Models\Brother;
use App\Models\ServiceGroup;
use App\Models\Ticket;
use App\Models\User;
use App\Models\TicketFamily;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_redirects_to_admin(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/admin');
    }

    public function test_group_portal_only_shows_that_group_families_and_brothers(): void
    {
        [$groupOne] = $this->ticketForGroup(1, 'Família A', 'Irmão A');
        $this->ticketForGroup(2, 'Família B', 'Irmão B');

        $response = $this->get(route('groups.portal', $groupOne));

        $response
            ->assertOk()
            ->assertSee('Família A')
            ->assertSee('Irmão A')
            ->assertDontSee('Família B')
            ->assertDontSee('Irmão B');
    }

    public function test_group_responsible_can_mark_ticket_as_sent(): void
    {
        [$group, $ticket] = $this->ticketForGroup(1, 'Família A', 'Irmão A');

        $this->post(route('groups.tickets.sent', [$group, $ticket]))
            ->assertRedirect();

        $ticket->refresh();

        $this->assertSame(Ticket::STATUS_SENT, $ticket->status);
        $this->assertNotNull($ticket->sent_at);
        $this->assertDatabaseHas('ticket_events', [
            'ticket_id' => $ticket->id,
            'type' => 'sent',
        ]);
    }

    public function test_ticket_download_uses_secure_token_and_private_storage(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('tickets/example.pdf', '%PDF-1.4 test');
        [, $ticket] = $this->ticketForGroup(1, 'Família A', 'Irmão A', ['pdf_path' => 'tickets/example.pdf']);

        $this->get(route('tickets.download', $ticket->public_token))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_admin_can_open_user_management_pages(): void
    {
        $user = User::create([
            'name' => 'Admin',
            'responsibility' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $this->actingAs($user)
            ->get('/admin/users')
            ->assertOk()
            ->assertSee('Utilizadores');

        $this->actingAs($user)
            ->get('/admin/users/create')
            ->assertOk()
            ->assertSee('Responsabilidade');
    }

    public function test_group_user_lists_default_to_their_service_group(): void
    {
        [$groupOne] = $this->ticketForGroup(1, 'Família A', 'Irmão A');
        $this->ticketForGroup(2, 'Família B', 'Irmão B');
        $user = User::create([
            'name' => 'Ajudante Grupo 1',
            'responsibility' => 'Ajudante',
            'service_group_id' => $groupOne->id,
            'email' => 'ajudante@example.com',
            'password' => 'password',
        ]);

        $this->actingAs($user)
            ->get('/admin/brothers')
            ->assertOk()
            ->assertSee('Irmão A')
            ->assertDontSee('Irmão B');
    }

    public function test_magic_link_opens_responsive_ticket_portal_for_seven_days(): void
    {
        [$group] = $this->ticketForGroup(1, 'Família A', 'Irmão A');
        $user = User::create([
            'name' => 'Ajudante',
            'responsibility' => 'Ajudante',
            'service_group_id' => $group->id,
            'email' => 'magic@example.com',
            'password' => 'password',
        ]);
        $token = $user->generateMagicLoginToken();

        $this->get(route('magic-portal', [$user, $token]))
            ->assertOk()
            ->assertSee('Enviar bilhetes')
            ->assertSee('Irmão A')
            ->assertSee('WhatsApp');

        $this->assertGuest();
        $user->refresh();
        $this->assertTrue($user->magic_login_expires_at->isFuture());
        $this->assertNotNull($user->magic_login_sent_at);
    }

    public function test_expired_magic_login_link_is_rejected(): void
    {
        $user = User::create([
            'name' => 'Ajudante',
            'responsibility' => 'Ajudante',
            'email' => 'expired@example.com',
            'password' => 'password',
        ]);
        $token = $user->generateMagicLoginToken();
        $user->forceFill(['magic_login_expires_at' => now()->subMinute()])->save();

        $this->get(route('magic-portal', [$user, $token]))
            ->assertForbidden();

        $this->assertGuest();
    }

    public function test_magic_login_whatsapp_message_uses_user_phone_and_expiry(): void
    {
        $user = User::create([
            'name' => 'Supervisor Grupo 1',
            'responsibility' => 'Superintendente de grupo',
            'phone' => '912 345 678',
            'email' => 'supervisor@example.com',
            'password' => 'password',
        ]);
        $token = $user->generateMagicLoginToken();
        $loginUrl = route('magic-login', [$user, $token]);

        $this->assertSame('351912345678', $user->whatsappPhone());
        $this->assertStringContainsString($loginUrl, $user->magicLoginWhatsappText($loginUrl));
        $this->assertStringContainsString('válido até', $user->magicLoginWhatsappText($loginUrl));
    }

    private function ticketForGroup(int $number, string $familyName, string $brotherName, array $ticketAttributes = []): array
    {
        $group = ServiceGroup::create([
            'number' => $number,
            'name' => "Grupo {$number}",
        ]);

        $family = TicketFamily::create([
            'service_group_id' => $group->id,
            'name' => $familyName,
        ]);

        $brother = Brother::create([
            'service_group_id' => $group->id,
            'ticket_family_id' => $family->id,
            'name' => $brotherName,
        ]);

        $ticket = Ticket::create(array_merge([
            'service_group_id' => $group->id,
            'ticket_family_id' => $family->id,
            'brother_id' => $brother->id,
            'pdf_filename' => "{$number}-{$brotherName}.pdf",
            'pdf_path' => 'tickets/example.pdf',
            'internal_code' => (string) (1000 + $number),
            'status' => Ticket::STATUS_ASSIGNED,
        ], $ticketAttributes));

        return [$group, $ticket];
    }
}
