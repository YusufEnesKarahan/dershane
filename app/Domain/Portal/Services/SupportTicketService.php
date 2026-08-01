<?php

namespace App\Domain\Portal\Services;

use App\Models\Institution;
use App\Models\PortalSupportTicket;
use App\Events\SupportTicketCreated;

class SupportTicketService
{
    public function createTicket(Institution $tenant, array $data, $userId = null): PortalSupportTicket
    {
        $ticket = PortalSupportTicket::create([
            'tenant_id' => $tenant->id,
            'user_id' => $userId,
            'subject' => $data['subject'],
            'message' => $data['message'],
            'priority' => $data['priority'] ?? 'medium',
        ]);

        app(\App\Core\Services\AuditService::class)->logSystemAction(
            action: 'portal_support_ticket_created',
            category: 'portal',
            severity: 'info',
            description: "Support ticket '{$ticket->subject}' created by tenant {$tenant->id}.",
            metadata: ['ticket_id' => $ticket->id]
        );

        event(new SupportTicketCreated($ticket));

        return $ticket;
    }

    public function changeStatus(PortalSupportTicket $ticket, string $status)
    {
        $ticket->update(['status' => $status]);
        return $ticket;
    }

    public function addReply(PortalSupportTicket $ticket, string $message, $userId = null)
    {
        // For simplicity, we could append to metadata or create a separate model.
        // Appending to metadata['replies'] array
        $metadata = $ticket->metadata ?? [];
        $replies = $metadata['replies'] ?? [];
        
        $replies[] = [
            'user_id' => $userId,
            'message' => $message,
            'created_at' => now()->toIso8601String(),
        ];
        
        $metadata['replies'] = $replies;
        $ticket->update(['metadata' => $metadata, 'status' => 'open']); // re-open if replied

        return $ticket;
    }
}
