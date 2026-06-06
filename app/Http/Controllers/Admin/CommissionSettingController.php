<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CommissionSettingUpdateRequest;
use App\Models\CommissionSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CommissionSettingController extends Controller
{
    public function index(): View
    {
        $setting = CommissionSetting::firstOrCreate(['id' => 1], [
            'commission_rate' => 20.00,
        ]);

        return view('admin.commission-settings.index', compact('setting'));
    }

    public function update(CommissionSettingUpdateRequest $request): RedirectResponse
    {
        $setting = CommissionSetting::firstOrCreate(['id' => 1], [
            'commission_rate' => 20.00,
        ]);

        $previousRate = $setting->commission_rate;

        $setting->update([
            'commission_rate' => $request->decimal('commission_rate', 2),
        ]);

        Cache::forget('commission_setting');
        Cache::forget('commission_rate');

        Log::info('admin.commission_rate_updated', [
            'admin_id'      => auth('admin')->id(),
            'previous_rate' => $previousRate,
            'new_rate'      => $setting->commission_rate,
            'ip'            => request()->ip(),
        ]);

        flash()->success('Comision de plataforma actualizada correctamente.');

        return redirect()->route('admin.commission-settings.index');
    }
}
