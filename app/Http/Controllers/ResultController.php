<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ResultController extends Controller
{
    public function showPublic($uuid)
    {
        $meal = Meal::where('uuid', $uuid)->firstOrFail();
        return view('meals.public', compact('meal'));
    }

    public function generateShareLink(Request $request, Meal $meal)
    {
        $validated = $request->validate([
            'platform' => 'required|in:twitter,facebook,instagram',
        ]);

        try {
            $baseUrl = config('app.url', 'https://bincone.apexjets.org');
            $publicUrl = "{$baseUrl}/results/{$meal->uuid}";
            $shareLink = ['public' => $publicUrl];

            $meal->update(['share_link' => $shareLink, 'platform' => $validated['platform']]);

            \Illuminate\Support\Facades\Log::info('Share link generated', [
                'user_id' => auth()->id(),
                'meal_id' => $meal->id,
                'platform' => $validated['platform'],
                'share_link' => $shareLink,
            ]);

            return response()->json([
                'success' => true,
                'share_link' => $publicUrl,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Share link generation failed', [
                'user_id' => auth()->id(),
                'meal_id' => $meal->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate share link: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function download($uuid)
    {
        try {
            $meal = Meal::where('uuid', $uuid)->firstOrFail();
            
            // Log image paths for debugging
            \Illuminate\Support\Facades\Log::info('Generating PDF for meal', [
                'meal_id' => $meal->id,
                'photo_url' => $meal->photo_url ? config('app.url') . Storage::url($meal->photo_url) : null,
                'leftover_photo_url' => $meal->leftover_photo_url ? config('app.url') . Storage::url($meal->leftover_photo_url) : null,
                'logo_path' => public_path('images/nutrisnap-logo.png'),
            ]);

            $pdf = Pdf::loadView('meals.pdf', compact('meal'));
            return $pdf->download('NutriSnap_Analysis_Report_' . $meal->id . '.pdf');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('PDF generation failed', [
                'meal_id' => $meal->id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $meal = Meal::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $meal->delete();
        return response()->json([
            'success' => true,
            'message' => 'Meal deleted successfully.',
        ]);
    }

    public function generateShareImage($uuid)
    {
        try {
            $meal = Meal::where('uuid', $uuid)->firstOrFail();
            $publicUrl = config('app.url', 'https://bincone.apexjets.org') . "/results/{$meal->uuid}";
            $imagePath = "share-images/meal-{$meal->id}.png";
            \Spatie\Browsershot\Browsershot::url($publicUrl)
                ->windowSize(1200, 800)
                ->save(Storage::path($imagePath));
            return response()->json([
                'success' => true,
                'image_url' => Storage::url($imagePath),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Share image generation failed', [
                'user_id' => auth()->id(),
                'meal_id' => $meal->id,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate share image: ' . $e->getMessage(),
            ], 500);
        }
    }
}
