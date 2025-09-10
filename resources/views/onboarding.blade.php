@extends('layouts.app')
     @section('content')
         <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
             <h2 class="text-2xl font-bold mb-4">Complete Your Profile</h2>
             <form method="POST" action="{{ route('onboarding.store') }}">
                 @csrf
                 <div class="mb-4">
                     <label for="height" class="block text-sm font-medium">Height (cm)</label>
                     <input id="height" type="number" name="height" value="{{ old('height') }}" required class="w-full p-2 border rounded border-gray-300 focus:ring focus:ring-blue-200">
                     @error('height')
                         <span class="text-red-500 text-sm">{{ $message }}</span>
                     @enderror
                 </div>
                 <div class="mb-4">
                     <label for="weight" class="block text-sm font-medium">Weight (kg)</label>
                     <input id="weight" type="number" name="weight" value="{{ old('weight') }}" required class="w-full p-2 border rounded border-gray-300 focus:ring focus:ring-blue-200">
                     @error('weight')
                         <span class="text-red-500 text-sm">{{ $message }}</span>
                     @enderror
                 </div>
                 <div class="mb-4">
                     <label for="goal" class="block text-sm font-medium">Goal</label>
                     <select id="goal" name="goal" required class="w-full p-2 border rounded border-gray-300 focus:ring focus:ring-blue-200">
                         <option value="weight_loss" {{ old('goal') === 'weight_loss' ? 'selected' : '' }}>Lose Weight</option>
                         <option value="maintain" {{ old('goal') === 'maintain' ? 'selected' : '' }}>Maintain</option>
                         <option value="weight_gain" {{ old('goal') === 'weight_gain' ? 'selected' : '' }}>Gain Weight</option>
                     </select>
                     @error('goal')
                         <span class="text-red-500 text-sm">{{ $message }}</span>
                     @enderror
                 </div>
                 <div class="mb-4">
                     <label class="block text-sm font-medium">Health Conditions</label>
                     <label><input type="checkbox" name="conditions[]" value="diabetes" {{ in_array('diabetes', old('conditions', [])) ? 'checked' : '' }}> Diabetes</label>
                     <label><input type="checkbox" name="conditions[]" value="hypertension" {{ in_array('hypertension', old('conditions', [])) ? 'checked' : '' }}> Hypertension</label>
                 </div>
                 <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Save Profile</button>
             </form>
         </div>
     @endsection