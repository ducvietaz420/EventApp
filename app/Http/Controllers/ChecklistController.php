<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\Event;
use App\Exports\ChecklistsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class ChecklistController extends Controller
{
    /**
     * Constructor - Yêu cầu authentication
     */
    public function __construct()
    {
        $this->middleware(['auth', 'check.user.status']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Event $event = null): View
    {
        $query = Checklist::query();
        
        // Nếu có sự kiện cụ thể, lọc checklist theo sự kiện đó
        if ($event) {
            $query->where('event_id', $event->id);
        }
        
        // Áp dụng các bộ lọc tìm kiếm
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }
        
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }
        
        if ($request->has('assigned_to') && $request->assigned_to) {
            $query->where('assigned_to', $request->assigned_to);
        }
        
        // Sắp xếp theo thời gian tạo mới nhất
        $query->orderBy('created_at', 'desc');
        
        $checklists = $query->with('event')->paginate(15);
        $events = Event::all();
        
        // Thống kê
        $stats = [
            'total' => Checklist::count(),
            'completed' => Checklist::where('status', 'completed')->count(),
            'pending' => Checklist::where('status', 'pending')->count(),
            'overdue' => Checklist::overdue()->count(),
            'high_priority' => Checklist::highPriority()->count()
        ];
        
        // Danh mục để filter
        $categories = Checklist::distinct('category')->pluck('category')->filter();
        
        // Lấy danh sách người được gán để filter
        $assignees = Checklist::distinct('assigned_to')
                              ->whereNotNull('assigned_to')
                              ->pluck('assigned_to')
                              ->filter();
        
        return view('checklists.index', compact('checklists', 'event', 'events', 'stats', 'categories', 'assignees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Event $event = null): View
    {
        $events = Event::all();
        
        return view('checklists.create', compact('events', 'event'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'status' => 'nullable|string|in:pending,in_progress,completed,cancelled,on_hold',
            'due_date' => 'nullable|date',
            'reminder_date' => 'nullable|date|before:due_date',
            'assigned_to' => 'nullable|string|max:255',
            'estimated_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'requires_approval' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        
        // Nếu không có sort_order, đặt ở cuối
        if (!isset($validated['sort_order'])) {
            $validated['sort_order'] = Checklist::where('event_id', $validated['event_id'])->max('sort_order') + 1;
        }
        
        $checklist = Checklist::create($validated);
        
        return redirect()->route('checklists.show', $checklist)
                         ->with('success', 'Checklist đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Checklist $checklist): View
    {
        $checklist->load('event');
        
        // Lấy các checklist liên quan trong cùng event
        $relatedChecklists = Checklist::where('event_id', $checklist->event_id)
                                     ->where('id', '!=', $checklist->id)
                                     ->orderBy('sort_order')
                                     ->limit(5)
                                     ->get();
        
        return view('checklists.show', compact('checklist', 'relatedChecklists'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Checklist $checklist): View
    {
        $events = Event::all();
        
        return view('checklists.edit', compact('checklist', 'events'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Checklist $checklist): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'status' => 'nullable|string|in:pending,in_progress,completed,cancelled,on_hold',
            'due_date' => 'nullable|date',
            'reminder_date' => 'nullable|date|before:due_date',
            'assigned_to' => 'nullable|string|max:255',
            'estimated_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'requires_approval' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        
        // Nếu status chuyển thành completed, ghi lại thời gian hoàn thành
        if ($validated['status'] === 'completed' && $checklist->status !== 'completed') {
            $validated['completed_at'] = now();
            $validated['completed_by'] = 'System'; // Có thể thay bằng user hiện tại
        }
        
        $checklist->update($validated);
        
        return redirect()->route('checklists.show', $checklist)
                         ->with('success', 'Checklist đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Checklist $checklist): RedirectResponse
    {
        $eventId = $checklist->event_id;
        $checklist->delete();
        
        return redirect()->route('events.show', $eventId)
                         ->with('success', 'Checklist đã được xóa thành công!');
    }
    
    /**
     * Complete a checklist item
     */
    public function complete(Request $request, Event $event, Checklist $checklist): JsonResponse
    {
        $validated = $request->validate([
            'actual_cost' => 'nullable|numeric|min:0',
            'completion_notes' => 'nullable|string'
        ]);
        
        $checklist->status = 'completed';
        $checklist->completed_at = now();
        $checklist->completed_by = 'System'; // Có thể thay bằng user hiện tại
        
        if (isset($validated['actual_cost'])) {
            $checklist->actual_cost = $validated['actual_cost'];
        }
        
        if (isset($validated['completion_notes'])) {
            $checklist->notes = ($checklist->notes ? $checklist->notes . "\n\n" : '') . 
                               '[Hoàn thành ' . now()->format('d/m/Y H:i') . '] ' . $validated['completion_notes'];
        }
        
        $checklist->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Checklist đã được đánh dấu hoàn thành!',
            'checklist' => $checklist
        ]);
    }
    
    /**
     * Update status of checklist item
     */
    public function updateStatus(Request $request, Checklist $checklist): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,in_progress,completed,cancelled,on_hold'
        ]);
        
        $oldStatus = $checklist->status;
        $checklist->status = $validated['status'];
        
        // Nếu chuyển thành completed, ghi lại thời gian
        if ($validated['status'] === 'completed' && $oldStatus !== 'completed') {
            $checklist->completed_at = now();
            $checklist->completed_by = 'System';
        }
        
        // Nếu chuyển từ completed sang status khác, xóa thông tin hoàn thành
        if ($oldStatus === 'completed' && $validated['status'] !== 'completed') {
            $checklist->completed_at = null;
            $checklist->completed_by = null;
        }
        
        $checklist->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Trạng thái đã được cập nhật!',
            'checklist' => $checklist
        ]);
    }
    
    /**
     * Reorder checklist items
     */
    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:checklists,id',
            'items.*.sort_order' => 'required|integer|min:0'
        ]);
        
        foreach ($validated['items'] as $item) {
            Checklist::where('id', $item['id'])
                     ->update(['sort_order' => $item['sort_order']]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Thứ tự checklist đã được cập nhật!'
        ]);
    }
    
    /**
     * Duplicate checklist item
     */
    public function duplicate(Checklist $checklist): RedirectResponse
    {
        $newChecklist = $checklist->replicate();
        $newChecklist->title = $checklist->title . ' (Copy)';
        $newChecklist->status = 'pending';
        $newChecklist->completed_at = null;
        $newChecklist->completed_by = null;
        $newChecklist->sort_order = Checklist::where('event_id', $checklist->event_id)->max('sort_order') + 1;
        $newChecklist->save();
        
        return redirect()->route('checklists.edit', $newChecklist)
                         ->with('success', 'Checklist đã được sao chép thành công!');
    }

    /**
     * Xuất danh sách checklist ra file Excel
     */
    public function exportChecklists(Request $request)
    {
        $eventId = $request->get('event_id');
        $status = $request->get('status');
        $category = $request->get('category');
        $priority = $request->get('priority');

        $filename = 'danh-sach-checklist-' . now()->format('Y-m-d-H-i-s') . '.xlsx';

        return Excel::download(
            new ChecklistsExport($eventId, $status, $category, $priority),
            $filename
        );
    }
}
