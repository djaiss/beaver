<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Services\CollectionStatistics;
use App\Traits\FindsItems;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    use FindsItems;

    public function index(Request $request, int $collection): View
    {
        $collectionModel = $this->findCollection($request, $collection);

        $statistics = new CollectionStatistics(collection: $collectionModel);

        return view('app.statistics.index', [
            'collection' => $collectionModel,
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
