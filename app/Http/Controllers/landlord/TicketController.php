<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\Ticket;
use Illuminate\Support\Facades\DB;
use App\Models\landlord\Tenant;
use App\Models\landlord\MailSetting;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
    use \App\Traits\MailInfo;

    // List root tickets
    public function index()
    {
        if (!tenant()) {
            $tickets = Ticket::whereNull('parent_ticket_id')->latest()->get();
        } else {
            $tenantId = tenant()->id;
            tenancy()->central(function () use ($tenantId, &$tickets) {
                $tickets = Ticket::where('tenant_id', $tenantId)
                    ->whereNull('parent_ticket_id')
                    ->latest()
                    ->get();
            });
        }

        return view('tickets.index', compact('tickets'));
    }

    // Show create form
    public function create()
    {
        return view('tickets.create');
    }

    public function store(Request $request)
    {

        if (!config('app.demo_unlocked')) {
            return redirect()->back()->with('not_permitted', 'This feature is disabled for demo!');
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $tenantId = tenant()->id;
        tenancy()->central(function () use ($request, $tenantId, &$mail_setting, &$ticketUrl, &$toEmail) {
            $ticket = Ticket::create([
                'subject' => $request->subject,
                'description' => $request->description,
                'superadmin' => 0,
                'tenant_id' => $tenantId,
                'parent_ticket_id' => null,
            ]);

            $mail_setting = MailSetting::latest()->first();
            $ticketUrl = env('CENTRAL_DOMAIN') . "/superadmin/tickets/{$ticket->id}";
            $toEmail = DB::table('general_settings')->latest()->first()->email;
        });

        if ($mail_setting) {
            try {
                $this->setMailInfo($mail_setting);

                Mail::html(
                    __('db.new_support_ticket_mail_message')
                        . '<br><a href="' . $ticketUrl . '">' . __('db.reply_here') . '</a>',
                    function ($message) use ($toEmail) {
                        $message->to($toEmail)
                            ->subject(__('db.new_support_ticket_mail_subject'));
                    }
                );
            } catch (\Exception $e) {
            }
        }

        return redirect()->route('tickets.index')->with('message', __('db.ticket_created'));
    }

    // Show ticket + replies (chat-style)
    public function show($id)
    {

        $general_setting = DB::table('general_settings')->latest()->first();

        if (!tenant()) {
            $ticket = Ticket::findOrFail($id);
            $replies = Ticket::where('parent_ticket_id', $id)->get();
        } else {
            $tenantId = tenant()->id;
            tenancy()->central(function () use ($id, $tenantId, &$ticket, &$replies) {
                $ticket = Ticket::where('tenant_id', $tenantId)->findOrFail($id);
                $replies = Ticket::where('parent_ticket_id', $id)
                    ->where('tenant_id', $tenantId)
                    ->get();
            });
        }
        return view('tickets.show', compact('ticket', 'replies', 'general_setting'));
    }

    public function reply(Request $request, $id)
    {
        if (!config('app.demo_unlocked')) {
            return redirect()->back()->with('not_permitted', 'This feature is disabled for demo!');
        }

        $request->validate([
            'description' => 'required|string',
        ]);

        if (!tenant()) {
            // Superadmin
            $ticket = Ticket::findOrFail($id);
            Ticket::create([
                'subject' => null,
                'description' => $request->description,
                'superadmin' => 1,
                'tenant_id' => $ticket->tenant_id,
                'parent_ticket_id' => $ticket->id,
            ]);

            $mail_setting = MailSetting::latest()->first();
            $ticketUrl = $ticket->tenant_id . '.' . env('CENTRAL_DOMAIN') . "/tickets/{$ticket->id}";
            $toEmail = Tenant::find($ticket->tenant_id)->email;
        }
        else {
            // Tenant
            $tenantId = tenant()->id;
            tenancy()->central(function () use ($id, $request, $tenantId, &$mail_setting, &$ticketUrl, &$toEmail) {
                $ticket = Ticket::where('tenant_id', $tenantId)->findOrFail($id);
                Ticket::create([
                    'subject' => null,
                    'description' => $request->description,
                    'superadmin' => 0,
                    'tenant_id' => $tenantId,
                    'parent_ticket_id' => $ticket->id,
                ]);

                $mail_setting = MailSetting::latest()->first();
                $ticketUrl = env('CENTRAL_DOMAIN') . "/superadmin/tickets/{$ticket->id}";
                $toEmail = DB::table('general_settings')->latest()->first()->email;
            });
        }

        if ($mail_setting) {
            try {
                $this->setMailInfo($mail_setting);
                Mail::html(
                    __('db.reply_support_ticket_mail_message')
                        . '<br><a href="' . $ticketUrl . '">' . __('db.reply_here') . '</a>',
                    function ($message) use ($toEmail) {
                        $message->to($toEmail)
                            ->subject(__('db.new_support_ticket_mail_subject'));
                    }
                );
            } catch (\Exception $e) {
            }
        }
        return redirect()->back()->with('message', __('db.reply_sent'));
    }

    public function destroy($id)
    {
        if (!config('app.demo_unlocked')) {
            return redirect()->back()->with('not_permitted', 'This feature is disabled for demo!');
        }

        if (!tenant()) {
            // Superadmin context → delete directly
            $ticket = Ticket::findOrFail($id);

            // Delete all replies of this ticket
            Ticket::where('parent_ticket_id', $ticket->id)->delete();

            // Delete root ticket
            $ticket->delete();
        } else {
            // Tenant context → capture tenantId and delete in landlord DB
            $tenantId = tenant()->id;

            tenancy()->central(function () use ($id, $tenantId) {
                $ticket = Ticket::where('tenant_id', $tenantId)->findOrFail($id);

                // Delete replies belonging to this ticket
                Ticket::where('parent_ticket_id', $ticket->id)->delete();

                // Delete root ticket
                $ticket->delete();
            });
        }
        return redirect()->back()->with('not_permitted', __('db.ticket_deleted'));
    }
}
