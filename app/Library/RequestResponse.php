<?php

namespace App\Library;

use App\Models\RequestResponseLog;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

class RequestResponse
{
    public static function save(array $data = []): bool
    {
        try {
            DB::beginTransaction();

            $dailyLog = new RequestResponseLog();
            $dailyLog->user_id = $data['user_id'] ?? null;
            $dailyLog->type = $data['type'] ?? null;
            $dailyLog->action = $data['action'] ?? null;
            $dailyLog->end_point = $data['end_point'] ?? null;
            $dailyLog->request_params = isset($data['request_params']) ? json_encode($data['request_params']) : null;
            $dailyLog->response = isset($data['response']) ? json_encode($data['response']) : null;
            $dailyLog->extra = isset($data['extra']) ? json_encode($data['extra']) : null;
            $dailyLog->log_date = $data['log_date'] ?? now()->toDateString();
            $dailyLog->save();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to save request response log: ' . $e->getMessage());
            return false;
        }
    }
}
