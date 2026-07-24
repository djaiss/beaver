<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Services\CollectionStatistics;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    public function index(Request $request): View
    {
        $statistics = new CollectionStatistics(collection: $request->attributes->get('collection'));

        return view('app.statistics.index', [
            'totals' => $statistics->totals(),
            'sets' => $statistics->setsCompletion(),
            'valueOverTime' => $statistics->valueOverTime(),
            'acquisitions' => $statistics->acquisitionsPerMonth(),
            'categories' => $statistics->byCategory(),
            'conditions' => $statistics->byCondition(),
            'locations' => $statistics->valueByLocation(),
            'topItems' => $statistics->topItems(),
        ]);
    }
}
