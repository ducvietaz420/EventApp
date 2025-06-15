<?php

namespace App\Http\Controllers;

use App\Models\EventReport;
use App\Models\Event;
use App\Models\Budget;
use App\Models\Timeline;
use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class EventReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Event $event = null): View
    {
        $query = EventReport::query();
        
        // Nếu có sự kiện cụ thể, lọc báo cáo theo sự kiện đó
        if ($event) {
            $query->where('event_id', $event->id);
        }
        
        // Áp dụng các bộ lọc tìm kiếm
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('summary', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->has('report_type') && $request->report_type) {
            $query->byReportType($request->report_type);
        }
        
        if ($request->has('status') && $request->status) {
            $query->byStatus($request->status);
        }
        
        if ($request->has('visibility') && $request->visibility) {
            $query->byVisibility($request->visibility);
        }
        
        // Sắp xếp theo thời gian tạo mới nhất
        $query->orderBy('created_at', 'desc');
        
        $reports = $query->with('event')->paginate(15);
        $events = Event::all();
        
        // Thống kê
        $stats = [
            'total' => $query->count(),
            'published' => EventReport::published()->count(),
            'drafts' => EventReport::drafts()->count(),
            'pending_review' => EventReport::pendingReview()->count(),
        ];
        
        return view('event-reports.index', compact('reports', 'event', 'events', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Event $event = null): View
    {
        $events = Event::all();
        
        return view('event-reports.create', compact('events', 'event'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'report_type' => 'required|string|in:summary,financial,attendance,feedback,final',
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'visibility' => 'nullable|string|in:public,private,internal',
            'status' => 'nullable|string|in:draft,under_review,approved,published,rejected,archived',
            'tags' => 'nullable|array',
        ]);
        
        $validated['status'] = $validated['status'] ?? 'draft';
        $validated['visibility'] = $validated['visibility'] ?? 'private';
        $validated['generated_by'] = 'System'; // Có thể thay bằng user hiện tại
        
        $report = EventReport::create($validated);
        
        return redirect()->route('event-reports.show', $report)
                         ->with('success', 'Báo cáo đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(EventReport $eventReport): View
    {
        $eventReport->load('event');
        
        // Lấy các báo cáo liên quan
        $relatedReports = EventReport::where('event_id', $eventReport->event_id)
                                    ->where('id', '!=', $eventReport->id)
                                    ->orderBy('created_at', 'desc')
                                    ->limit(5)
                                    ->get();
        
        return view('event-reports.show', compact('eventReport', 'relatedReports'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EventReport $eventReport): View
    {
        $events = Event::all();
        
        return view('event-reports.edit', compact('eventReport', 'events'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EventReport $eventReport): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'report_type' => 'required|string|in:summary,financial,attendance,feedback,final',
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'visibility' => 'nullable|string|in:public,private,internal',
            'status' => 'nullable|string|in:draft,under_review,approved,published,rejected,archived',
            'tags' => 'nullable|array',
            'rating' => 'nullable|numeric|min:1|max:5',
            'success_score' => 'nullable|numeric|min:0|max:100',
            'roi_percentage' => 'nullable|numeric',
        ]);
        
        $eventReport->update($validated);
        
        return redirect()->route('event-reports.show', $eventReport)
                         ->with('success', 'Báo cáo đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventReport $eventReport): RedirectResponse
    {
        $eventId = $eventReport->event_id;
        $eventReport->delete();
        
        return redirect()->route('events.show', $eventId)
                         ->with('success', 'Báo cáo đã được xóa thành công!');
    }
    
    /**
     * Generate automatic report for an event
     */
    public function generate(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'report_type' => 'required|string|in:summary,financial,attendance,feedback,final',
        ]);
        
        try {
            // Load dữ liệu liên quan
            $event->load(['budgets', 'timelines', 'checklists']);
            
            // Tạo báo cáo tự động dựa trên loại
            $reportData = $this->generateReportData($event, $validated['report_type']);
            
            $report = EventReport::create([
                'event_id' => $event->id,
                'report_type' => $validated['report_type'],
                'title' => $reportData['title'],
                'summary' => $reportData['summary'],
                'content' => $reportData['content'],
                'metrics' => $reportData['metrics'],
                'financial_summary' => $reportData['financial_summary'] ?? null,
                'status' => 'draft',
                'visibility' => 'private',
                'generated_by' => 'System',
                'success_score' => $reportData['success_score'] ?? null,
                'roi_percentage' => $reportData['roi_percentage'] ?? null,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Báo cáo đã được tạo tự động!',
                'report_id' => $report->id
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo báo cáo: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update report status
     */
    public function updateStatus(Request $request, EventReport $eventReport): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:draft,under_review,approved,published,rejected,archived',
            'notes' => 'nullable|string'
        ]);
        
        $eventReport->status = $validated['status'];
        
        // Ghi lại thời gian cho các trạng thái quan trọng
        switch ($validated['status']) {
            case 'under_review':
                $eventReport->reviewed_at = now();
                $eventReport->reviewed_by = 'System';
                break;
            case 'approved':
                $eventReport->approved_at = now();
                $eventReport->approved_by = 'System';
                break;
            case 'published':
                $eventReport->published_at = now();
                break;
        }
        
        $eventReport->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Trạng thái báo cáo đã được cập nhật!',
            'report' => $eventReport
        ]);
    }
    
    /**
     * Export report to PDF
     */
    public function exportPdf(EventReport $eventReport): \Symfony\Component\HttpFoundation\Response
    {
        // Sử dụng DomPDF hoặc thư viện PDF khác
        // Đây là implementation cơ bản
        
        $html = view('event-reports.pdf', compact('eventReport'))->render();
        
        // Trả về response để download
        return response($html)
               ->header('Content-Type', 'text/html')
               ->header('Content-Disposition', 'attachment; filename="report-' . $eventReport->id . '.html"');
    }
    
    /**
     * Duplicate report
     */
    public function duplicate(EventReport $eventReport): RedirectResponse
    {
        $newReport = $eventReport->replicate();
        $newReport->title = $eventReport->title . ' (Copy)';
        $newReport->status = 'draft';
        $newReport->published_at = null;
        $newReport->reviewed_at = null;
        $newReport->approved_at = null;
        $newReport->save();
        
        return redirect()->route('event-reports.edit', $newReport)
                         ->with('success', 'Báo cáo đã được sao chép thành công!');
    }
    
    /**
     * Generate report data based on type
     */
    private function generateReportData(Event $event, string $type): array
    {
        switch ($type) {
            case 'financial':
                return $this->generateFinancialReport($event);
            case 'summary':
                return $this->generateSummaryReport($event);
            case 'final':
                return $this->generateFinalReport($event);
            default:
                return $this->generateBasicReport($event, $type);
        }
    }
    
    /**
     * Generate financial report
     */
    private function generateFinancialReport(Event $event): array
    {
        $budgets = $event->budgets;
        $totalEstimated = $budgets->sum('estimated_cost');
        $totalActual = $budgets->sum('actual_cost');
        $variance = $totalActual - $totalEstimated;
        $variancePercent = $totalEstimated > 0 ? ($variance / $totalEstimated) * 100 : 0;
        
        $financial_summary = [
            'total_estimated' => $totalEstimated,
            'total_actual' => $totalActual,
            'variance' => $variance,
            'variance_percent' => $variancePercent,
            'budget_categories' => $budgets->groupBy('category')->map(function($items, $category) {
                return [
                    'estimated' => $items->sum('estimated_cost'),
                    'actual' => $items->sum('actual_cost'),
                    'variance' => $items->sum('actual_cost') - $items->sum('estimated_cost')
                ];
            })
        ];
        
        $content = "# Báo cáo tài chính sự kiện: {$event->name}\n\n";
        $content .= "## Tổng quan tài chính\n";
        $content .= "- Ngân sách dự kiến: " . number_format($totalEstimated, 0, ',', '.') . " VNĐ\n";
        $content .= "- Ngân sách thực tế: " . number_format($totalActual, 0, ',', '.') . " VNĐ\n";
        $content .= "- Chênh lệch: " . number_format($variance, 0, ',', '.') . " VNĐ (" . number_format($variancePercent, 1) . "%)\n\n";
        
        $content .= "## Chi tiết theo danh mục\n";
        foreach ($financial_summary['budget_categories'] as $category => $data) {
            $content .= "### {$category}\n";
            $content .= "- Dự kiến: " . number_format($data['estimated'], 0, ',', '.') . " VNĐ\n";
            $content .= "- Thực tế: " . number_format($data['actual'], 0, ',', '.') . " VNĐ\n";
            $content .= "- Chênh lệch: " . number_format($data['variance'], 0, ',', '.') . " VNĐ\n\n";
        }
        
        return [
            'title' => "Báo cáo tài chính - {$event->name}",
            'summary' => "Báo cáo chi tiết về tình hình tài chính của sự kiện",
            'content' => $content,
            'financial_summary' => $financial_summary,
            'metrics' => [
                'total_budget' => $totalEstimated,
                'total_spent' => $totalActual,
                'budget_variance' => $variance,
                'budget_efficiency' => $totalEstimated > 0 ? ($totalActual / $totalEstimated) * 100 : 0
            ],
            'roi_percentage' => $this->calculateROI($event, $totalActual),
            'success_score' => $this->calculateFinancialScore($variance, $totalEstimated)
        ];
    }
    
    /**
     * Generate summary report
     */
    private function generateSummaryReport(Event $event): array
    {
        $timelines = $event->timelines;
        $checklists = $event->checklists;
        $budgets = $event->budgets;
        
        $timelineStats = [
            'total' => $timelines->count(),
            'completed' => $timelines->where('status', 'completed')->count(),
            'in_progress' => $timelines->where('status', 'in_progress')->count(),
            'overdue' => $timelines->filter(function($t) { return $t->is_overdue; })->count()
        ];
        
        $checklistStats = [
            'total' => $checklists->count(),
            'completed' => $checklists->where('status', 'completed')->count(),
            'pending' => $checklists->where('status', 'pending')->count()
        ];
        
        $content = "# Báo cáo tổng quan sự kiện: {$event->name}\n\n";
        $content .= "## Thông tin cơ bản\n";
        $content .= "- Ngày bắt đầu: " . ($event->start_date ? $event->start_date->format('d/m/Y') : 'Chưa xác định') . "\n";
        $content .= "- Ngày kết thúc: " . ($event->end_date ? $event->end_date->format('d/m/Y') : 'Chưa xác định') . "\n";
        $content .= "- Địa điểm: " . ($event->location ?? 'Chưa xác định') . "\n";
        $content .= "- Số người tham dự dự kiến: " . ($event->expected_attendees ?? 'Chưa xác định') . "\n\n";
        
        $content .= "## Tiến độ thực hiện\n";
        $content .= "### Timeline\n";
        $content .= "- Tổng số mốc: {$timelineStats['total']}\n";
        $content .= "- Đã hoàn thành: {$timelineStats['completed']}\n";
        $content .= "- Đang thực hiện: {$timelineStats['in_progress']}\n";
        $content .= "- Trễ hạn: {$timelineStats['overdue']}\n\n";
        
        $content .= "### Checklist\n";
        $content .= "- Tổng số công việc: {$checklistStats['total']}\n";
        $content .= "- Đã hoàn thành: {$checklistStats['completed']}\n";
        $content .= "- Chưa hoàn thành: {$checklistStats['pending']}\n\n";
        
        $completionRate = $timelineStats['total'] > 0 ? 
                         ($timelineStats['completed'] / $timelineStats['total']) * 100 : 0;
        
        return [
            'title' => "Báo cáo tổng quan - {$event->name}",
            'summary' => "Báo cáo tổng quan về tiến độ và tình hình thực hiện sự kiện",
            'content' => $content,
            'metrics' => [
                'timeline_completion' => $completionRate,
                'checklist_completion' => $checklistStats['total'] > 0 ? 
                                        ($checklistStats['completed'] / $checklistStats['total']) * 100 : 0,
                'total_tasks' => $timelineStats['total'] + $checklistStats['total'],
                'completed_tasks' => $timelineStats['completed'] + $checklistStats['completed']
            ],
            'success_score' => $completionRate
        ];
    }
    
    /**
     * Generate final report
     */
    private function generateFinalReport(Event $event): array
    {
        $summaryData = $this->generateSummaryReport($event);
        $financialData = $this->generateFinancialReport($event);
        
        $content = "# Báo cáo tổng kết sự kiện: {$event->name}\n\n";
        $content .= $summaryData['content'];
        $content .= "\n" . $financialData['content'];
        
        $content .= "\n## Đánh giá tổng quan\n";
        $content .= "- Điểm thành công: " . number_format($summaryData['success_score'], 1) . "/100\n";
        $content .= "- ROI: " . number_format($financialData['roi_percentage'] ?? 0, 1) . "%\n";
        
        return [
            'title' => "Báo cáo tổng kết - {$event->name}",
            'summary' => "Báo cáo tổng kết toàn diện về sự kiện",
            'content' => $content,
            'financial_summary' => $financialData['financial_summary'],
            'metrics' => array_merge($summaryData['metrics'], $financialData['metrics']),
            'success_score' => ($summaryData['success_score'] + $financialData['success_score']) / 2,
            'roi_percentage' => $financialData['roi_percentage']
        ];
    }
    
    /**
     * Generate basic report
     */
    private function generateBasicReport(Event $event, string $type): array
    {
        $content = "# Báo cáo {$type} - {$event->name}\n\n";
        $content .= "Báo cáo này được tạo tự động vào " . now()->format('d/m/Y H:i') . "\n\n";
        $content .= "## Thông tin sự kiện\n";
        $content .= "- Tên: {$event->name}\n";
        $content .= "- Mô tả: " . ($event->description ?? 'Không có') . "\n";
        
        return [
            'title' => "Báo cáo {$type} - {$event->name}",
            'summary' => "Báo cáo {$type} cho sự kiện {$event->name}",
            'content' => $content,
            'metrics' => [],
            'success_score' => 50
        ];
    }
    
    /**
     * Calculate ROI
     */
    private function calculateROI(Event $event, float $totalCost): float
    {
        // Giả sử có thể tính được lợi nhuận/giá trị từ sự kiện
        // Đây là implementation cơ bản, có thể tùy chỉnh theo nghiệp vụ
        $estimatedValue = $event->estimated_budget ?? $totalCost;
        
        if ($totalCost > 0) {
            return (($estimatedValue - $totalCost) / $totalCost) * 100;
        }
        
        return 0;
    }
    
    /**
     * Calculate financial success score
     */
    private function calculateFinancialScore(float $variance, float $estimated): float
    {
        if ($estimated <= 0) return 50;
        
        $variancePercent = abs($variance / $estimated) * 100;
        
        if ($variancePercent <= 5) return 95;
        if ($variancePercent <= 10) return 85;
        if ($variancePercent <= 15) return 75;
        if ($variancePercent <= 25) return 65;
        
        return 50;
    }
}
