<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CsvUserConvertController extends Controller
{
    public function download(Request $request)
    {
        $users = $this->fetchUsers($request);
        $csvData = $this->buildCsvData($users);

        return $this->generateAndDownloadCsv($csvData);
    }

    private function fetchUsers(Request $request)
    {
        $q = User::query()
            ->orderByDesc('created_at')
            // Cashier想定：subscriptions を eager load
            ->with(['subscriptions' => function ($sq) {
                $sq->select(['id', 'user_id', 'type', 'stripe_status', 'created_at', 'ends_at']);
            }]);

        // 必要カラムのみ（不要項目は除外）
        return $q->select([
            'id',
            'member_number',
            'name',
            'last_name',
            'first_name',
            'last_name_kana',
            'first_name_kana',
            'company_name',
            'postal_code',
            'prefecture',
            'address1',
            'address2',
            'address3',
            'phone',
            'role',
            'email',
        ])->get();
    }

    private function buildCsvData($users): array
    {
        $headers = [
            'id',
            'member_number',
            'name',
            'last_name',
            'first_name',
            'last_name_kana',
            'first_name_kana',
            'company_name',
            'postal_code',
            'prefecture',
            'address1',
            'address2',
            'address3',
            'phone',
            'role',
            'email',
            // 追加
            'is_subscribed',
            'joined_at',
        ];

        $rows = [];
        $rows[] = $headers;

        foreach ($users as $u) {
            // 1) 契約中か（Cashier: active() 判定に近い判定）
            // subscriptions.stripe_status が 'active' or 'trialing' を契約中扱い
            $isSubscribed = $u->subscriptions
                ->where('type', 'default')
                ->contains(fn($s) => in_array($s->stripe_status, ['active', 'trialing'], true));

            // 2) 入会日（最初の subscription created_at を採用）
            // 複数typeがある場合は運用に合わせて type='default' などに絞るのが安全
            $joinedAt = $u->subscriptions
                ->where('type', 'default')
                ->sortBy('created_at')
                ->first()?->created_at;

            $rows[] = [
                $u->id,
                $u->member_number,
                $u->name,
                $u->last_name,
                $u->first_name,
                $u->last_name_kana,
                $u->first_name_kana,
                $u->company_name,
                $u->postal_code,
                $u->prefecture,
                $u->address1,
                $u->address2,
                $u->address3,
                $u->phone,
                $u->role,
                $u->email,
                // 追加
                $isSubscribed ? 1 : 0,
                $joinedAt ? $joinedAt->format('Y-m-d H:i:s') : '',
            ];
        }

        return $rows;
    }

    private function generateAndDownloadCsv(array $csvData)
    {
        $filename = 'users_' . now()->format('Ymd_His') . '.csv';

        $tempDir = Storage::disk('local')->path('temp');
        $tempFilePath = $tempDir . '/' . $filename;

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        ob_start();
        $handle = fopen('php://output', 'w');

        // ヘッダ
        fputcsv($handle, array_map(fn($h) => mb_convert_encoding($h, 'SJIS-win', 'UTF-8'), $csvData[0]));

        // データ行
        foreach (array_slice($csvData, 1) as $row) {
            $encoded = array_map(function ($field) {
                $field = $field === null ? '' : (string)$field;
                return mb_convert_encoding($field, 'SJIS-win', 'UTF-8');
            }, $row);

            fputcsv($handle, $encoded);
        }

        fclose($handle);

        $csvContent = ob_get_clean();
        file_put_contents($tempFilePath, $csvContent);

        return response()->download($tempFilePath, $filename)->deleteFileAfterSend(true);
    }
}
