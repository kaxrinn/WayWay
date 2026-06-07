@extends('layouts.admin')

@section('title', 'Manage Travel Agents')

@section('content')
<!-- Header -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-plane text-blue-500"></i>
            Manage Travel Agent
        </h1>
        <p class="text-gray-500 mt-2">Manage all travel agent accounts</p>
    </div>
    
    <!-- Add Button -->
    <a href="{{ route('admin.travel-agents.create') }}" 
       class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition flex items-center gap-2 font-semibold">
        <i class="fas fa-plus"></i>
        Add Travel Agent
    </a>
</div>

<!-- Success Alert -->
@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 shadow">
    <i class="fas fa-check-circle mr-2"></i>
    {{ session('success') }}
</div>
@endif

<!-- Travel Agents Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-primary to-blue-400 text-white">
                <tr>
                    <th class="px-6 py-3 text-center text-sm font-semibold w-12">No</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Full Name</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Email</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Phone</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold">Package</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold">Subscriptions</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($travelAgents as $key => $agent)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-center text-sm font-semibold text-gray-600">
                        {{ ($travelAgents->currentPage() - 1) * $travelAgents->perPage() + $key + 1 }}
                    </td>
                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $agent->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $agent->email }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $agent->no_telepon }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-bold">
                            {{ $agent->travel_agent_packages_count ?? 0 }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-xs font-bold">
                            {{ $agent->travel_agent_transactions_count ?? 0 }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex gap-2 justify-center">
                            <a href="{{ route('admin.travel-agents.show', $agent->id) }}" 
                               class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs font-semibold">
                                <i class="fas fa-eye mr-1"></i> See
                            </a>
                            <a href="{{ route('admin.travel-agents.edit', $agent->id) }}" 
                               class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs font-semibold">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('admin.travel-agents.destroy', $agent->id) }}" 
                                  onsubmit="return confirm('Sure to delete this travel agent?')" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold">
                                    <i class="fas fa-trash mr-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                        <i class="fas fa-inbox text-6xl mb-4"></i>
                        <p class="text-lg font-medium">No travel agents found</p>
                        <p class="text-sm">Click "Add Travel Agent" button to create a new account</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 border-t bg-gray-50">
        {{ $travelAgents->links() }}
    </div>
</div>
@endsection