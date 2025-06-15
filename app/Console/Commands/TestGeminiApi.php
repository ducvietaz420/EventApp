<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestGeminiApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gemini:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Gemini AI API connection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Đang kiểm tra kết nối Gemini API...');
        
        $apiKey = env('GEMINI_API_KEY');
        
        if (!$apiKey) {
            $this->error('GEMINI_API_KEY chưa được cấu hình trong file .env');
            $this->info('Vui lòng thêm GEMINI_API_KEY vào file .env để sử dụng tính năng này.');
            return 1;
        }
        
        $this->info('API Key: ' . substr($apiKey, 0, 10) . '...');
        
        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $apiKey, [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => 'Xin chào! Bạn có thể giới thiệu về bản thân không?'
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topP' => 0.8,
                        'topK' => 40,
                        'maxOutputTokens' => 1024,
                    ]
                ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                $this->info('✅ Kết nối thành công!');
                $this->info('Status: ' . $response->status());
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $this->info('Response từ Gemini:');
                    $this->line($data['candidates'][0]['content']['parts'][0]['text']);
                    return 0;
                } else {
                    $this->error('❌ Response không có nội dung text');
                    $this->info('Raw response: ' . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    return 1;
                }
            } else {
                $this->error('❌ Kết nối thất bại!');
                $this->error('Status: ' . $response->status());
                $this->error('Response: ' . $response->body());
                
                if ($response->status() == 400) {
                    $this->info('Có thể do API key không hợp lệ hoặc model không tồn tại.');
                } elseif ($response->status() == 403) {
                    $this->info('Có thể do API key không có quyền truy cập hoặc đã hết quota.');
                } elseif ($response->status() == 429) {
                    $this->info('Có thể do đã vượt quá rate limit.');
                }
                
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Lỗi khi gọi API: ' . $e->getMessage());
            return 1;
        }
    }
} 