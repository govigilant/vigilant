<?php

namespace Vigilant\Notifications\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Vigilant\Notifications\Models\Channel;

class ChannelController extends Controller
{
    public function index(): View
    {
        /** @var view-string $view */
        $view = 'notifications::channels';
        $hasChannels = Channel::query()->exists();

        return view($view, [
            'hasChannels' => $hasChannels,
        ]);
    }
}
