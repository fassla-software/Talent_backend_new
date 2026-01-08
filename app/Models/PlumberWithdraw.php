<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use App\Models\AdminPlumberNotification;

class PlumberWithdraw extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'plumber_withdraw_requests';

    public function plumber(): BelongsTo
    {
        return $this->belongsTo(Plumber::class, 'requestor_id', 'user_id');
    }

    public function plumber_withdraw_gifts(): HasMany
    {
        return $this->hasMany(PlumberReceivedGift::class, 'user_id', 'requestor_id');
    }

    // ✅ Automatically create a notification when a new withdraw request with "Pending" status is created
    protected static function boot()
    {
        parent::boot();

        static::created(function ($withdraw) {
            if (strtolower(trim($withdraw->status)) === 'pending') { // ✅ Ensuring case-insensitive check
                try {
                    AdminPlumberNotification::create([
                        'title' => 'New Withdraw Request',
                        'subject' => 'Pending Withdraw Approval',
                        'message' => "Plumber ID: {$withdraw->requestor_id} has requested a withdrawal, awaiting approval.",
                        'firebase_id' => null, // Firebase can be added later
                        'data' => json_encode([
                            'withdraw_id' => $withdraw->id,
                            'requestor_id' => $withdraw->requestor_id,
                            'status' => $withdraw->status,
                        ]),
                        'read' => false,
                    ]);

                    Log::info("Notification saved for pending withdraw request ID: " . $withdraw->id);
                } catch (\Exception $e) {
                    Log::error("Failed to create notification for pending withdraw request: " . $e->getMessage());
                }
            }
        });

        // ✅ Restore money when status is changed to "rejected"
        static::updated(function ($withdraw) {
            if (strtolower(trim($withdraw->status)) === 'rejected') {
                try {
                    $plumber = $withdraw->plumber;

                    if ($plumber) {
                        // ✅ Restore instant_withdrawal and withdraw_money
                        $plumber->increment('instant_withdrawal', $withdraw->amount);
                        $plumber->increment('withdraw_money', $withdraw->amount);
                        $plumber->save();

                        Log::info("Restored funds for plumber ID: " . $plumber->user_id . " due to rejected withdraw request.");
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to restore plumber funds for rejected withdraw request: " . $e->getMessage());
                }
            }
        });
    }
}
