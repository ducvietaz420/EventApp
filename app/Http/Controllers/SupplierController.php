<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Event $event = null): View
    {
        $query = Supplier::query();
        
        // Áp dụng các bộ lọc tìm kiếm
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }
        
        if ($request->has('type') && $request->type) {
            $query->byType($request->type);
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('verified') && $request->verified !== '') {
            $query->where('is_verified', $request->verified);
        }
        
        if ($request->has('preferred') && $request->preferred !== '') {
            $query->where('is_preferred', $request->preferred);
        }
        
        // Lọc theo khoảng giá
        if ($request->has('min_budget') && $request->min_budget) {
            $query->where('min_budget', '>=', $request->min_budget);
        }
        
        if ($request->has('max_budget') && $request->max_budget) {
            $query->where('max_budget', '<=', $request->max_budget);
        }
        
        // Sắp xếp
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        if ($sortBy === 'rating') {
            $query->orderByRating($sortDirection);
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }
        
        $suppliers = $query->paginate(12);
        
        // Thống kê
        $stats = [
            'total' => Supplier::count(),
            'verified' => Supplier::verified()->count(),
            'preferred' => Supplier::preferred()->count(),
            'available' => Supplier::where('status', 'active')->count(),
        ];
        
        // Danh mục để filter
        $types = Supplier::distinct('type')->pluck('type')->filter();
        
        return view('suppliers.index', compact('suppliers', 'event', 'stats', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:catering,decoration,photography,venue,entertainment,transportation,flowers,equipment,other',
            'company_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'specialties' => 'nullable|string',
            'min_budget' => 'nullable|numeric|min:0',
            'max_budget' => 'nullable|numeric|min:0',
            'rating' => 'nullable|numeric|min:0|max:10',
            'total_reviews' => 'nullable|integer|min:0',
            'status' => 'nullable|string|in:active,inactive,blacklisted',
            'notes' => 'nullable|string',
            'is_verified' => 'boolean',
            'is_preferred' => 'boolean',
            'last_worked_date' => 'nullable|date',
        ]);
        
        $supplier = Supplier::create($validated);
        
        return redirect()->route('suppliers.show', $supplier)
                         ->with('success', 'Nhà cung cấp đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier): View
    {
        $supplier->load(['budgets.event', 'events']);
        
        // Lấy các supplier liên quan cùng category
        $relatedSuppliers = Supplier::byType($supplier->type)
                                   ->where('id', '!=', $supplier->id)
                                   ->verified()
                                   ->limit(5)
                                   ->get();
        
        return view('suppliers.show', compact('supplier', 'relatedSuppliers'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:catering,decoration,photography,venue,entertainment,transportation,flowers,equipment,other',
            'company_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'specialties' => 'nullable|string',
            'min_budget' => 'nullable|numeric|min:0',
            'max_budget' => 'nullable|numeric|min:0',
            'rating' => 'nullable|numeric|min:0|max:10',
            'total_reviews' => 'nullable|integer|min:0',
            'status' => 'nullable|string|in:active,inactive,blacklisted',
            'notes' => 'nullable|string',
            'is_verified' => 'boolean',
            'is_preferred' => 'boolean',
            'last_worked_date' => 'nullable|date',
        ]);
        
        $supplier->update($validated);
        
        return redirect()->route('suppliers.show', $supplier)
                         ->with('success', 'Nhà cung cấp đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete();
        
        return redirect()->route('suppliers.index')
                         ->with('success', 'Nhà cung cấp đã được xóa thành công!');
    }
    
    /**
     * API endpoint để tìm kiếm supplier
     */
    public function search(Request $request): JsonResponse
    {
        $query = Supplier::query();
        
        if ($request->has('term') && $request->term) {
            $query->search($request->term);
        }
        
        if ($request->has('type') && $request->type) {
            $query->byType($request->type);
        }
        
        $suppliers = $query->verified()
                          ->where('status', 'active')
                          ->limit(10)
                          ->get();
        
        return response()->json($suppliers);
    }
    
    /**
     * Attach supplier to event
     */
    public function attach(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'service_type' => 'required|string|max:255',
            'contract_amount' => 'nullable|numeric|min:0',
            'contract_date' => 'nullable|date',
            'status' => 'nullable|string|in:pending,confirmed,cancelled'
        ]);
        
        $event->suppliers()->attach($validated['supplier_id'], [
            'service_type' => $validated['service_type'],
            'contract_amount' => $validated['contract_amount'] ?? null,
            'contract_date' => $validated['contract_date'] ?? null,
            'status' => $validated['status'] ?? 'pending',
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Nhà cung cấp đã được thêm vào sự kiện!'
        ]);
    }
    
    /**
     * Detach supplier from event
     */
    public function detach(Event $event, Supplier $supplier): JsonResponse
    {
        $event->suppliers()->detach($supplier->id);
        
        return response()->json([
            'success' => true,
            'message' => 'Nhà cung cấp đã được xóa khỏi sự kiện!'
        ]);
    }
    
    /**
     * Toggle verified status
     */
    public function toggleVerified(Supplier $supplier): JsonResponse
    {
        $supplier->is_verified = !$supplier->is_verified;
        $supplier->save();
        
        return response()->json([
            'success' => true,
            'message' => $supplier->is_verified ? 'Đã xác minh nhà cung cấp!' : 'Đã hủy xác minh nhà cung cấp!',
            'is_verified' => $supplier->is_verified
        ]);
    }
    
    /**
     * Toggle preferred status
     */
    public function togglePreferred(Supplier $supplier): JsonResponse
    {
        $supplier->is_preferred = !$supplier->is_preferred;
        $supplier->save();
        
        return response()->json([
            'success' => true,
            'message' => $supplier->is_preferred ? 'Đã thêm vào ưu tiên!' : 'Đã xóa khỏi ưu tiên!',
            'is_preferred' => $supplier->is_preferred
        ]);
    }
}
