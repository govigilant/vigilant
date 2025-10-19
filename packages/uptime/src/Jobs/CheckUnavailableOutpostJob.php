<?php

namespace Vigilant\Uptime\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Vigilant\Uptime\Enums\OutpostStatus;
use Vigilant\Uptime\Models\Outpost;

class CheckUnavailableOutpostJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public Outpost $outpost)
    {
        $this->onQueue(config('uptime.queue'));
    }

    public function handle(): void
    {
        // Check if outpost has been unavailable for at least 15 minutes
        if ($this->outpost->unavailable_at === null ||
            $this->outpost->unavailable_at->diffInMinutes(now()) < 15) {
            return;
        }

        // Try to reach the outpost health endpoint
        try {
            $response = Http::timeout(5)->get("{$this->outpost->url()}/health");

            if ($response->successful()) {
                // Outpost is reachable again, mark it as available
                $this->outpost->update([
                    'status' => OutpostStatus::Available,
                    'unavailable_at' => null,
                    'last_available_at' => now(),
                ]);
            } else {
                // Still not available, delete it
                $this->outpost->delete();
            }
        } catch (\Exception $e) {
            // Failed to reach the outpost, delete it
            $this->outpost->delete();
        }
    }
}
