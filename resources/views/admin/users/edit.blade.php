@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="bg-white shadow-lg rounded-xl p-6">
        <h1 class="text-3xl font-bold text-cyan-500 mb-6">Edit User</h1>
        
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                       required>
            </div>
            
            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                       required>
            </div>
            
            <!-- Password (optional) -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password (leave blank to keep current)</label>
                <input type="password" name="password" id="password" 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
            </div>
            
            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
            </div>
            
            <!-- Roles -->
            <div>
                <label for="roles" class="block text-sm font-medium text-gray-700 mb-2">Roles</label>
                <select name="roles[]" id="roles" multiple 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                    <option value="admin" {{ $user->roles->contains('name', 'admin') ? 'selected' : '' }}>Admin</option>
                    <option value="kasir" {{ $user->roles->contains('name', 'kasir') ? 'selected' : '' }}>Kasir</option>
                    <option value="user" {{ $user->roles->contains('name', 'user') ? 'selected' : '' }}>User</option>
                </select>
                <p class="mt-1 text-sm text-gray-500">Hold Ctrl (or Cmd on Mac) to select multiple roles.</p>
            </div>
            
            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('admin.users.index') }}" 
                   class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-5 py-2.5 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg shadow transition">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection