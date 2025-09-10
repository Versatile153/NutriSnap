<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\CustomEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AdminEmailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $users = User::select('id', 'name', 'email')->get();
        $nonSubscribedUsers = User::whereDoesntHave('subscriptions')->count();
        $inactiveUsers = User::where('last_login_at', '<', now()->subWeek())
            ->orWhereNull('last_login_at')->count();
        $newUsers = User::where('created_at', '>=', now()->subWeek())->count();
        $noMealUsers = User::whereDoesntHave('meals')->count();

        return view('admin.email', compact(
            'users',
            'nonSubscribedUsers',
            'inactiveUsers',
            'newUsers',
            'noMealUsers'
        ));
    }

    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recipient_type' => 'required|in:individual,selected,non_subscribed,inactive,new_users,no_meal',
            'recipient_email' => 'required_if:recipient_type,individual|nullable|exists:users,id',
            'recipients' => 'required_if:recipient_type,selected|nullable|array',
            'recipients.*' => 'exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed for email send', ['errors' => $validator->errors()]);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $recipients = $this->getRecipients($request);

        if ($recipients->isEmpty()) {
            Log::error('No recipients found for email send', ['request' => $request->all()]);
            return redirect()->back()->with('error', __('No recipients found.'));
        }

        try {
            foreach ($recipients as $recipient) {
                Mail::to($recipient->email)->queue(new CustomEmail(
                    $request->subject,
                    $request->message,
                    $recipient->name
                ));
                Log::info('Email queued for user', ['email' => $recipient->email, 'subject' => $request->subject]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to queue emails', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', __('Failed to queue emails.'));
        }

        return redirect()->back()->with('success', __('Emails queued successfully.'));
    }

    protected function getRecipients(Request $request)
    {
        switch ($request->recipient_type) {
            case 'individual':
                return User::where('id', $request->recipient_email)->get();
            case 'selected':
                return User::whereIn('id', $request->recipients)->get();
            case 'non_subscribed':
                return User::whereDoesntHave('subscriptions')->get();
            case 'inactive':
                return User::where('last_login_at', '<', now()->subWeek())
                    ->orWhereNull('last_login_at')->get();
            case 'new_users':
                return User::where('created_at', '>=', now()->subWeek())->get();
            case 'no_meal':
                return User::whereDoesntHave('meals')->get();
            default:
                return collect([]);
        }
    }
}