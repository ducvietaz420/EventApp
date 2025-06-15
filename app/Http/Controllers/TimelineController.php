<?php

namespace App\Http\Controllers;

use App\Models\Timeline;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class TimelineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Event $event = null): View
    {
        $query = Timeline::query();
        
        // Nếu có sự kiện cụ thể, lọc timeline theo sự kiện đó
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
        
        if ($request->has('milestone') && $request->milestone !== '') {
            $query->where('is_milestone', $request->milestone);
        }
        
        // Sắp xếp theo thời gian bắt đầu
        $query->orderBy('start_time', 'asc');
        
        $timelines = $query->with('event')->paginate(15);
        $events = Event::all();
        
        // Thống kê
        $stats = [
            'total' => $query->count(),
            'completed' => Timeline::where('status', 'completed')->count(),
            'in_progress' => Timeline::where('status', 'in_progress')->count(),
            'overdue' => Timeline::overdue()->count(),
            'milestones' => Timeline::milestones()->count()
        ];
        
        return view('timelines.index', compact('timelines', 'event', 'events', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Event $event = null): View
    {
        $events = Event::all();
        
        return view('timelines.create', compact('events', 'event'));
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
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'location' => 'nullable|string|max:255',
            'responsible_person' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:pending,in_progress,completed',
            'priority' => 'nullable|string|in:low,medium,high',
            'is_milestone' => 'boolean',
            'estimated_duration' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);
        
        $timeline = Timeline::create($validated);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Timeline đã được tạo thành công!',
                'timeline' => $timeline
            ]);
        }
        
        return redirect()->route('timelines.show', $timeline)
                         ->with('success', 'Timeline đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Timeline $timeline): View
    {
        $timeline->load('event');
        
        // Lấy các timeline liên quan trong cùng event
        $relatedTimelines = Timeline::where('event_id', $timeline->event_id)
                                   ->where('id', '!=', $timeline->id)
                                   ->orderBy('start_time')
                                   ->limit(5)
                                   ->get();
        
        return view('timelines.show', compact('timeline', 'relatedTimelines'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Timeline $timeline): View
    {
        $events = Event::all();
        
        return view('timelines.edit', compact('timeline', 'events'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Timeline $timeline): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'location' => 'nullable|string|max:255',
            'responsible_person' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:pending,in_progress,completed',
            'priority' => 'nullable|string|in:low,medium,high',
            'is_milestone' => 'boolean',
            'estimated_duration' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);
        
        // Nếu status chuyển thành completed, ghi lại thời gian hoàn thành
        if ($validated['status'] === 'completed' && $timeline->status !== 'completed') {
            $validated['completed_at'] = now();
            $validated['completed_by'] = 'System'; // Có thể thay bằng user hiện tại
        }
        
        $timeline->update($validated);
        
        return redirect()->route('timelines.show', $timeline)
                         ->with('success', 'Timeline đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Timeline $timeline): RedirectResponse
    {
        $eventId = $timeline->event_id;
        $timeline->delete();
        
        return redirect()->route('events.show', $eventId)
                         ->with('success', 'Timeline đã được xóa thành công!');
    }
    
    /**
     * API endpoint để lấy timeline sắp tới
     */
    public function upcoming(Request $request): JsonResponse
    {
        $timelines = Timeline::upcoming()
                           ->with('event')
                           ->orderBy('start_time')
                           ->limit(10)
                           ->get();
        
        return response()->json($timelines);
    }
    
    /**
     * Cập nhật trạng thái timeline
     */
    public function updateStatus(Request $request, Timeline $timeline): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,in_progress,completed,cancelled,delayed'
        ]);
        
        $oldStatus = $timeline->status;
        $timeline->status = $validated['status'];
        
        // Nếu chuyển thành completed, ghi lại thời gian
        if ($validated['status'] === 'completed' && $oldStatus !== 'completed') {
            $timeline->completed_at = now();
            $timeline->completed_by = 'System';
        }
        
        $timeline->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Trạng thái đã được cập nhật!',
            'timeline' => $timeline
        ]);
    }

    /**
     * Đánh dấu timeline là hoàn thành
     */
    public function markCompleted(Timeline $timeline): JsonResponse
    {
        $timeline->status = 'completed';
        $timeline->completed_at = now();
        $timeline->completed_by = 'System';
        $timeline->save();

        return response()->json([
            'success' => true,
            'message' => 'Mốc thời gian đã được đánh dấu hoàn thành!',
            'timeline' => $timeline
        ]);
    }

    /**
     * Hủy đánh dấu hoàn thành timeline
     */
    public function markUncompleted(Timeline $timeline): JsonResponse
    {
        $timeline->status = 'pending';
        $timeline->completed_at = null;
        $timeline->completed_by = null;
        $timeline->save();

        return response()->json([
            'success' => true,
            'message' => 'Đã hủy đánh dấu hoàn thành!',
            'timeline' => $timeline
        ]);
    }

    /**
     * Bulk đánh dấu hoàn thành
     */
    public function bulkComplete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:timelines,id'
        ]);

        Timeline::whereIn('id', $validated['ids'])->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completed_by' => 'System'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu hoàn thành ' . count($validated['ids']) . ' mốc thời gian!'
        ]);
    }

    /**
     * Bulk thay đổi độ ưu tiên
     */
    public function bulkPriority(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:timelines,id',
            'priority' => 'required|string|in:low,medium,high'
        ]);

        Timeline::whereIn('id', $validated['ids'])->update([
            'priority' => $validated['priority']
        ]);

        $priorityName = [
            'low' => 'Thấp',
            'medium' => 'Trung bình', 
            'high' => 'Cao'
        ];

        return response()->json([
            'success' => true,
            'message' => 'Đã thay đổi độ ưu tiên thành "' . $priorityName[$validated['priority']] . '" cho ' . count($validated['ids']) . ' mốc thời gian!'
        ]);
    }

    /**
     * Bulk xóa timeline
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:timelines,id'
        ]);

        Timeline::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa ' . count($validated['ids']) . ' mốc thời gian!'
        ]);
    }
}
