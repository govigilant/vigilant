<?php

namespace Vigilant\Crawler\Http\Controllers;

use Illuminate\Routing\Controller;
use Vigilant\Crawler\Models\Crawler;

class CrawlerController extends Controller
{
    public function index(Crawler $crawler): mixed
    {
        return view('crawler::crawler.index', [
            'crawler' => $crawler,
        ]);
    }
}
