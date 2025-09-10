<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    public function index()
    {
        $foods = Food::paginate(10);
        return view('admin.index', compact('foods'));
    }

    public function show(Food $food)
    {
        // Since we're using modals, this can be removed or redirected
        return redirect()->route('admin.foods.index')->with('success', 'Use modals to view food details.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:foods,name',
            'calories_per_100g' => 'required|numeric|min:0',
            'nutrients' => 'required|json',
        ]);

        Food::create($validated);
        return response()->json(['success' => true, 'message' => 'Food added successfully']);
    }

    public function destroy(Food $food)
    {
        $food->delete();
        return response()->json(['success' => true, 'message' => 'Food deleted successfully']);
    }
}