<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PointReward;
use App\Models\PointRedemption;
use App\Services\PointService;

class PointRewardController extends Controller
{
    public function index(PointService $pointService)
    {
        $user = Auth::user();

        $balance = $pointService->balance($user);

        $rewards = PointReward::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('rewards.index', compact(
            'rewards',
            'balance'
        ));
    }

    public function redeem(
        Request $request,
        PointReward $reward,
        PointService $pointService
    )
    {
        $user = Auth::user();

        try {

            $pointService->redeem($user, $reward);

            return redirect()
                ->route('rewards.index')
                ->with('success', 'ポイント交換を申請しました。');

        } catch (\Throwable $e) {

            return redirect()
                ->route('rewards.index')
                ->with('error', $e->getMessage());

        }
    }

    public function history()
    {
        $user = Auth::user();

        $redemptions = PointRedemption::query()
            ->where('user_id', $user->id)
            ->with('reward')
            ->latest()
            ->get();

        return view('rewards.history', compact('redemptions'));
    }
}