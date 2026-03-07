<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailRecipient;
use Illuminate\Http\Request;

class EmailRecipientController extends Controller
{
    /**
     * Display a listing of the email recipients.
     */
    public function index()
    {
        $recipients = EmailRecipient::orderBy('role_type')->get();
        return view('admin.email_recipients.index', compact('recipients'));
    }

    /**
     * Store a newly created email recipient in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email_address' => 'required|email|max:255',
            'role_type' => 'required|string|in:soup,cop,test',
            'province' => 'nullable|string|max:10',
            'municipality' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        EmailRecipient::create($validated);

        return redirect()->route('admin.emails.index')->with('success', 'Email aggiunta con successo.');
    }

    /**
     * Update the specified email recipient in storage.
     */
    public function update(Request $request, string $id)
    {
        $recipient = EmailRecipient::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email_address' => 'required|email|max:255',
            'role_type' => 'required|string|in:soup,cop,test',
            'province' => 'nullable|string|max:10',
            'municipality' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $recipient->update($validated);

        return redirect()->route('admin.emails.index')->with('success', 'Email aggiornata con successo.');
    }

    /**
     * Remove the specified email recipient from storage.
     */
    public function destroy(string $id)
    {
        $recipient = EmailRecipient::findOrFail($id);
        $recipient->delete();

        return redirect()->route('admin.emails.index')->with('success', 'Email eliminata con successo.');
    }
}
