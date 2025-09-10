@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-4">Food Details</h2>
        <div class="grid grid-cols-1 gap-4">
            <div>
                <p><strong>ID:</strong> {{ $food->id }}</p>
                <p><strong>Name:</strong> {{ $food->name }}</p>
                <p><strong>Calories per 100g:</strong> {{ $food->calories_per_100g }}</p>
                <p><strong>Nutrients:</strong> {{ json_encode($food->nutrients) }}</p>
            </div>
            <div>
                <a href="{{ route('admin.foods.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Back to Foods</a>
            </div>
        </div>
    </div>
@endsection