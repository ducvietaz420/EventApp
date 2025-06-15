<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Timeline;
use App\Models\Supplier;
use App\Models\Checklist;
use App\Models\AiSuggestion;
use App\Models\EventReport;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

/**
 * Controller quản lý sự kiện
 * Xử lý các thao tác CRUD cho sự kiện
 */
class EventController extends Controller
{
    /**
     * Hiển thị danh sách tất cả sự kiện
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = Event::with(['budgets', 'timelines', 'checklists']);
        
        // Lọc theo trạng thái nếu có
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Lọc theo loại sự kiện nếu có
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        // Tìm kiếm theo tên
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Sắp xếp theo ngày tạo mới nhất
        $events = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Trả về JSON nếu là API request
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $events,
                'message' => 'Danh sách sự kiện được tải thành công'
            ]);
        }
        
        return view('events.index', compact('events'));
    }

    /**
     * Hiển thị form tạo sự kiện mới
     */
    public function create(): View
    {
        return view('events.create');
    }

    /**
     * Lưu sự kiện mới vào database
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => ['required', Rule::in(['wedding', 'conference', 'party', 'corporate', 'exhibition', 'other'])],
            'status' => ['required', Rule::in(['planning', 'confirmed', 'in_progress', 'completed', 'cancelled'])],
            'event_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'venue' => 'nullable|string|max:255',
            'venue_address' => 'nullable|string',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:20',
            'client_email' => 'nullable|email|max:255',
            'expected_guests' => 'nullable|integer|min:1',
            // 'budget' => 'nullable|numeric|min:0', // Đã xóa trường budget
            'notes' => 'nullable|string',
            'deadline_design' => 'nullable|date',
            'deadline_booking' => 'nullable|date',
            'deadline_final' => 'nullable|date'
        ]);
        
        // Xử lý trường expected_guests để tránh lỗi database constraint
        if (is_null($validated['expected_guests']) || $validated['expected_guests'] === '') {
            $validated['expected_guests'] = 0;
        }
        
        $event = Event::create($validated);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $event,
                'message' => 'Sự kiện đã được tạo thành công'
            ], 201);
        }
        
        return redirect()->route('events.show', $event)
                        ->with('success', 'Sự kiện đã được tạo thành công!');
    }

    /**
     * Hiển thị chi tiết một sự kiện
     */
    public function show(Event $event): View|JsonResponse
    {
        $event->load([
            'budgets' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'timelines' => function($query) {
                $query->orderBy('start_time', 'asc');
            },
            'checklists' => function($query) {
                $query->orderBy('due_date', 'asc');
            },
            'aiSuggestions' => function($query) {
                $query->where('status', '!=', 'rejected')
                      ->orderBy('created_at', 'desc');
            },
            'reports'
        ]);
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $event,
                'message' => 'Chi tiết sự kiện được tải thành công'
            ]);
        }
        
        return view('events.show', compact('event'));
    }

    /**
     * Hiển thị form chỉnh sửa sự kiện
     */
    public function edit(Event $event): View
    {
        return view('events.edit', compact('event'));
    }

    /**
     * Cập nhật thông tin sự kiện
     */
    public function update(Request $request, Event $event): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => ['required', Rule::in(['wedding', 'conference', 'party', 'corporate', 'exhibition', 'concert', 'other'])],
            'status' => ['required', Rule::in(['planning', 'confirmed', 'in_progress', 'completed', 'cancelled', 'postponed'])],
            'event_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'venue' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:20',
            'client_email' => 'nullable|email|max:255',
            'expected_attendees' => 'nullable|integer|min:1',
            'actual_attendees' => 'nullable|integer|min:0',
            // 'budget' => 'nullable|numeric|min:0', // Đã xóa trường budget
            'actual_cost' => 'nullable|numeric|min:0',
            'special_requirements' => 'nullable|string',
            'notes' => 'nullable|string',
            'design_deadline' => 'nullable|date',
            'booking_deadline' => 'nullable|date',
            'setup_deadline' => 'nullable|date',
            'final_deadline' => 'nullable|date'
        ]);
        
        $event->update($validated);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $event->fresh(),
                'message' => 'Sự kiện đã được cập nhật thành công'
            ]);
        }
        
        return redirect()->route('events.show', $event)
                        ->with('success', 'Sự kiện đã được cập nhật thành công!');
    }

    /**
     * Cập nhật trạng thái của sự kiện.
     */
    public function updateStatus(Request $request, Event $event): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['planning', 'confirmed', 'in_progress', 'completed', 'cancelled'])],
        ]);

        $event->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $event->fresh(),
                'message' => 'Trạng thái sự kiện đã được cập nhật thành công.'
            ]);
        }

        return back()->with('success', 'Trạng thái sự kiện đã được cập nhật thành công!');
    }

    /**
     * Xóa sự kiện
     */
    public function destroy(Event $event): RedirectResponse|JsonResponse
    {
        $event->delete();
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Sự kiện đã được xóa thành công'
            ]);
        }
        
        return redirect()->route('events.index')
                        ->with('success', 'Sự kiện đã được xóa thành công!');
    }

    /**
     * Hiển thị dashboard tổng quan
     */
    public function dashboard(Request $request): View|JsonResponse
    {
        // Lấy thống kê tổng quan sự kiện
        $totalEvents = Event::count();
        $completedEvents = Event::where('status', 'completed')->count();
        $activeEvents = Event::where('status', 'in_progress')->count();
        $inProgressEvents = $activeEvents; // Thêm biến này
        $planningEvents = Event::where('status', 'planning')->count();
        $upcomingEvents = Event::where('event_date', '>', now())->count();
        
        // Thống kê Timeline
        $totalTimelines = Timeline::count();
        $completedTimelines = Timeline::where('status', 'completed')->count();
        $overdueTimelinesCount = Timeline::overdue()->count();
        $upcomingTimelines = Timeline::upcoming()->limit(5)->get();
        
        // Thống kê Suppliers
        $totalSuppliers = Supplier::count();
        $verifiedSuppliers = Supplier::verified()->count();
        $preferredSuppliers = Supplier::preferred()->count();
        $availableSuppliers = Supplier::where('status', 'active')->count();
        
        // Thống kê Checklist
        $totalChecklists = Checklist::count();
        $completedChecklists = Checklist::completed()->count();
        $pendingChecklists = Checklist::pending()->count();
        $overdueChecklists = Checklist::overdue()->count();
        
        // Thống kê AI Suggestions
        $totalSuggestions = AiSuggestion::count();
        $acceptedSuggestions = AiSuggestion::accepted()->count();
        $pendingSuggestions = AiSuggestion::pending()->count();
        $highConfidenceSuggestions = AiSuggestion::highConfidence()->count();
        
        // Thống kê Reports
        $totalReports = EventReport::count();
        $publishedReports = EventReport::published()->count();
        $draftReports = EventReport::drafts()->count();
        
        // Lấy dữ liệu cho hiển thị
        $recentEvents = Event::with(['budgets', 'timelines', 'checklists', 'aiSuggestions', 'reports'])
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();
                            
        $upcomingEventsList = Event::with(['budgets', 'timelines', 'checklists'])
                            ->where('event_date', '>', now())
                            ->orderBy('event_date', 'asc')
                            ->limit(5)
                            ->get();
        
        // Tasks cần chú ý - lấy từ Timeline và Checklist thực tế
        $overdueTimelines = Timeline::overdue()->with('event')->limit(3)->get();
        $overdueChecklists = Checklist::overdue()->with('event')->limit(3)->get();
        
        $pendingTasks = collect()
            ->merge($overdueTimelines->map(function($item) {
                $item->type = 'timeline';
                $item->urgency = 'overdue';
                $item->priority = $item->priority ?? 'medium';
                return $item;
            }))
            ->merge($overdueChecklists->map(function($item) {
                $item->type = 'checklist';
                $item->urgency = 'overdue';
                $item->priority = $item->priority ?? 'medium';
                return $item;
            }))
            ->sortBy('due_date')
            ->take(10);
        
        // Gợi ý AI mới nhất
        $recentSuggestions = AiSuggestion::with('event')
                                        ->orderBy('created_at', 'desc')
                                        ->limit(5)
                                        ->get();
        
        // Thống kê ngân sách thực tế
        $totalBudgetEstimated = Budget::sum('estimated_cost');
        $totalBudgetActual = Budget::sum('actual_cost');
        $totalBudget = $totalBudgetEstimated; // Sử dụng estimated cho hiển thị tổng
        $budgetVariance = $totalBudgetActual - $totalBudgetEstimated;
        
        // Dữ liệu thực cho biểu đồ sự kiện theo tháng (12 tháng gần nhất)
        $monthlyData = [];
        $monthlyLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyLabels[] = $date->format('M Y');
            $monthlyData[] = Event::whereYear('created_at', $date->year)
                                  ->whereMonth('created_at', $date->month)
                                  ->count();
        }
        
        // Dữ liệu thực cho biểu đồ phân bố loại sự kiện
        $eventTypes = Event::selectRaw('type, COUNT(*) as count')
                          ->groupBy('type')
                          ->pluck('count', 'type')
                          ->toArray();
        
        $typeLabels = [];
        $typeData = [];
        $typeDisplayMap = [
            'wedding' => 'Đám cưới',
            'conference' => 'Hội nghị',
            'party' => 'Tiệc',
            'corporate' => 'Doanh nghiệp',
            'exhibition' => 'Triển lãm',
            'workshop' => 'Workshop',
            'seminar' => 'Seminar',
            'other' => 'Khác'
        ];
        
        foreach ($eventTypes as $type => $count) {
            $typeLabels[] = $typeDisplayMap[$type] ?? ucfirst($type);
            $typeData[] = $count;
        }
        
        // Nếu không có dữ liệu, sử dụng dữ liệu trống thay vì dữ liệu giả
        if (empty($typeLabels)) {
            $typeLabels = ['Chưa có dữ liệu'];
            $typeData = [0];
        }
        
        // Nếu là API request, trả về JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'events' => [
                        'total' => $totalEvents,
                        'completed' => $completedEvents,
                        'active' => $activeEvents,
                        'planning' => $planningEvents,
                        'upcoming' => $upcomingEvents,
                    ],
                    'timelines' => [
                        'total' => $totalTimelines,
                        'completed' => $completedTimelines,
                        'overdue' => $overdueTimelinesCount,
                    ],
                    'suppliers' => [
                        'total' => $totalSuppliers,
                        'verified' => $verifiedSuppliers,
                        'preferred' => $preferredSuppliers,
                        'available' => $availableSuppliers,
                    ],
                    'checklists' => [
                        'total' => $totalChecklists,
                        'completed' => $completedChecklists,
                        'pending' => $pendingChecklists,
                        'overdue' => $overdueChecklists,
                    ],
                    'suggestions' => [
                        'total' => $totalSuggestions,
                        'accepted' => $acceptedSuggestions,
                        'pending' => $pendingSuggestions,
                        'high_confidence' => $highConfidenceSuggestions,
                    ],
                    'reports' => [
                        'total' => $totalReports,
                        'published' => $publishedReports,
                        'drafts' => $draftReports,
                    ],
                    'budget' => [
                        'estimated' => $totalBudgetEstimated,
                        'actual' => $totalBudgetActual,
                        'variance' => $budgetVariance,
                    ],
                    'charts' => [
                        'monthly_labels' => $monthlyLabels,
                        'monthly_data' => $monthlyData,
                        'type_labels' => $typeLabels,
                        'type_data' => $typeData,
                    ],
                    'recent_events' => $recentEvents,
                    'upcoming_events' => $upcomingEventsList,
                    'pending_tasks' => $pendingTasks,
                    'recent_suggestions' => $recentSuggestions,
                ],
                'message' => 'Thống kê dashboard được tải thành công'
            ]);
        }
        
        // Trả về view dashboard với tất cả dữ liệu thực
        return view('dashboard', compact(
            'totalEvents', 'completedEvents', 'activeEvents', 'inProgressEvents', 'planningEvents', 'upcomingEvents',
            'totalTimelines', 'completedTimelines', 'overdueTimelinesCount', 'upcomingTimelines',
            'totalSuppliers', 'verifiedSuppliers', 'preferredSuppliers', 'availableSuppliers',
            'totalChecklists', 'completedChecklists', 'pendingChecklists', 'overdueChecklists',
            'totalSuggestions', 'acceptedSuggestions', 'pendingSuggestions', 'highConfidenceSuggestions',
            'totalReports', 'publishedReports', 'draftReports',
            'totalBudgetEstimated', 'totalBudgetActual', 'totalBudget', 'budgetVariance',
            'recentEvents', 'upcomingEventsList', 'pendingTasks', 'recentSuggestions',
            'monthlyLabels', 'monthlyData', 'typeLabels', 'typeData'
        ));
    }
}
