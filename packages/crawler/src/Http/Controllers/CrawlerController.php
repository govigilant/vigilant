<?php

namespace Vigilant\Crawler\Http\Controllers;

use Illuminate\Routing\Controller;
use Vigilant\Crawler\Models\Crawler;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;

class CrawlerController extends Controller
{
    use DisplaysAlerts;

    public function index(Crawler $crawler): mixed
    {
        return view('crawler::crawler.index', [
            'crawler' => $crawler,
        ]);
    }

    public function delete(Crawler $crawler): mixed
    {
        $crawler->delete();

        $this->alert(
            __('Deleted'),
            __('Crawler was successfully deleted'),
            AlertType::Success
        );

        return response()->redirectToRoute('crawler.index');
    }
}
