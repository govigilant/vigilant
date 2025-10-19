<?php

namespace Vigilant\Uptime\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Vigilant\Uptime\Enums\OutpostStatus;
use Vigilant\Uptime\Models\Outpost;

class CheckUnavailableOutpostJob implements ShouldBeUnique, ShouldQueue
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
        try {
            $response = Http::timeout(5)->get("{$this->outpost->url()}/health");

            if ($response->successful()) {
                $this->outpost->update([
                    'status' => OutpostStatus::Available,
                    'unavailable_at' => null,
                    'last_available_at' => now(),
                ]);
            } else {
                $this->outpost->delete();
            }
        } catch (ConnectionException) {
            $this->outpost->delete();
        }
    }

    public function uniqueId(): int
    {
        return $this->outpost->id;
    }
}
