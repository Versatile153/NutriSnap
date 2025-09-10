<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'message' => 'required|string',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            Contact::create([
                'name' => $request->name,
                'email' => $request->email,
                'message' => $request->message,
            ]);

            $redirectUrl = url()->previous() . '?status=success&message=' . urlencode('Message sent successfully!');
            Log::debug('Redirecting to: ' . $redirectUrl);
            return redirect()->to($redirectUrl);
        } catch (\Exception $e) {
            Log::error('Contact form submission failed: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            $redirectUrl = url()->previous() . '?status=error&message=' . urlencode('Server error occurred. Please try again later.');
            Log::debug('Redirecting to: ' . $redirectUrl);
            return redirect()->to($redirectUrl)->withInput();
        }
    }
}