<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use App\Models\AdminPlumberNotification;
use Illuminate\Support\Facades\DB;

class PlumberReceivedGift extends Model
{
    use HasFactory;

    // Disable automatic timestamps
    public $timestamps = false;

    // Specify the custom names for the timestamp columns
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $guarded = ['id'];

    protected $table = 'plumber_received_gifts';

    public function plumber_gift(): BelongsTo
    {
        return $this->belongsTo(PlumberGift::class, 'gift_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
protected static function boot()
    {
        parent::boot();

        // ✅ Handle status updates (Refund points when rejected)
        static::updating(function ($gift) {
            if ($gift->isDirty('status') && $gift->status === 'Rejected') {
                try {
                    DB::transaction(function () use ($gift) {
                        $plumber = Plumber::where('user_id', $gift->user_id)->first();

                        if ($plumber && $gift->plumber_gift) {
                            $refundPoints = $gift->plumber_gift->points_required;

                            // ✅ Refund points to the plumber
                            $plumber->increment('gift_points', $refundPoints);

                            Log::info("✅ Points refunded: {$refundPoints} to Plumber ID: {$plumber->id}");
                        } else {
                            Log::error("❌ Plumber or Gift details not found for refund.");
                        }
                    });
                } catch (\Exception $e) {
                    Log::error("❌ Error refunding points: " . $e->getMessage());
                }
            }
        });

        // ✅ Handle new gift creation (Create notification when status is "Pending")
        static::created(function ($gift) {
            Log::info("New gift created with status: " . $gift->status);

            if ($gift->status === 'Pending') {
                try {
                    AdminPlumberNotification::create([
                        'title' => 'Gift Pending Approval',
                        'subject' => 'Plumber Received a Gift (Pending)',
                        'message' => "User ID: {$gift->user_id} has received a gift (Gift ID: {$gift->gift_id}) with status: Pending.",
                        'firebase_id' => null, // Firebase can be added later
                        'data' => json_encode([
                            'received_gift_id' => $gift->id,
                            'user_id' => $gift->user_id,
                            'gift_id' => $gift->gift_id,
                            'status' => $gift->status,
                        ]),
                        'read' => false,
                    ]);

                    Log::info("✅ Notification saved for pending gift ID: " . $gift->id);
                } catch (\Exception $e) {
                    Log::error("❌ Failed to create notification for pending gift: " . $e->getMessage());
                }
            }
        });
    }
}