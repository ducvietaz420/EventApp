<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Event;
use App\Models\Supplier;
use App\Models\ExpenseLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Event $event = null)
    {
        $query = Budget::query();
        
        // Nếu có sự kiện cụ thể, lọc ngân sách theo sự kiện đó
        if ($event) {
            $query->where('event_id', $event->id);
        }
        
        // Áp dụng các bộ lọc tìm kiếm
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('item_name', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Sắp xếp kết quả
        $query->orderBy('created_at', 'desc');
        
        $budgets = $query->paginate(10);
        $events = Event::all();
        
        return view('budgets.index', compact('budgets', 'event', 'events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Event $event = null)
    {
        $events = Event::all();
        $suppliers = Supplier::all();
        
        return view('budgets.create', compact('events', 'suppliers', 'event'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'category' => 'required|string',
            'item_name' => 'required|string|max:255',
            'description' => 'required|string',
            'estimated_cost' => 'required|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'status' => 'nullable|string',
            'is_essential' => 'boolean',
            'priority' => 'nullable|integer|min:1|max:5',
            'allocation_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);
        
        $budget = Budget::create($validated);
        
        return redirect()->route('events.show', $budget->event_id)
                         ->with('success', 'Ngân sách đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Budget $budget)
    {
        // Load expense logs cho budget
        $budget->load('expenseLogs');
        
        // Tính toán tỷ lệ sử dụng ngân sách
        $percentage = $budget->estimated_cost > 0 ? ($budget->actual_cost / $budget->estimated_cost) * 100 : 0;
        
        // Lấy các ngân sách liên quan (cùng event, khác budget hiện tại)
        $relatedBudgets = Budget::where('event_id', $budget->event_id)
                                ->where('id', '!=', $budget->id)
                                ->limit(5)
                                ->get();
        
        return view('budgets.show', compact('budget', 'percentage', 'relatedBudgets'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Budget $budget)
    {
        $events = Event::all();
        $suppliers = Supplier::all();
        
        return view('budgets.edit', compact('budget', 'events', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Budget $budget)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'category' => 'required|string',
            'item_name' => 'required|string|max:255',
            'description' => 'required|string',
            'estimated_cost' => 'required|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'allocated_date' => 'nullable|date',
            'deadline' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);
        
        $budget->update($validated);
        
        return redirect()->route('budgets.show', $budget)
                         ->with('success', 'Ngân sách đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Budget $budget)
    {
        $eventId = $budget->event_id;
        $budget->delete();
        
        return redirect()->route('events.show', $eventId)
                         ->with('success', 'Ngân sách đã được xóa thành công!');
    }
    
    /**
     * Cập nhật chi tiêu cho ngân sách (AJAX endpoint)
     */
    public function updateSpent(Request $request, Budget $budget)
    {
        try {
            $validated = $request->validate([
                'spent_amount_change' => 'required|numeric|min:0',
                'notes' => 'nullable|string'
            ]);
            
            // Tạo expense log để lưu chi tiết chi tiêu
            $budget->expenseLogs()->create([
                'amount' => $validated['spent_amount_change'],
                'description' => $validated['notes'] ?? 'Chi tiêu không có mô tả'
            ]);
            
            // Cập nhật số tiền đã chi
            $budget->actual_cost += $validated['spent_amount_change'];
            
            // Cập nhật ghi chú nếu có
            if (!empty($validated['notes'])) {
                $currentNotes = $budget->notes ?? '';
                $newNote = '[' . now()->format('d/m/Y H:i') . '] ' . $validated['notes'];
                $budget->notes = $currentNotes ? $currentNotes . "\n\n" . $newNote : $newNote;
            }
            
            $budget->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Chi tiêu đã được cập nhật thành công!',
                'budget' => [
                    'estimated_cost' => $budget->estimated_cost,
                    'actual_cost' => $budget->actual_cost,
                    'notes' => $budget->notes
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API endpoint để lấy tổng hợp ngân sách
     */
    public function summary(Request $request)
    {
        $data = [
            'total_budget' => Budget::sum('estimated_cost'),
            'total_spent' => Budget::sum('actual_cost'),
            'categories' => Budget::selectRaw('category, SUM(estimated_cost) as total_budget, SUM(actual_cost) as total_spent')
                                ->groupBy('category')
                                ->get()
        ];
        
        return response()->json($data);
    }
}
