<?php

namespace App\Http\Controllers\Api;

use App\Models\Blog;
use App\Models\Comment;
use App\Models\Country;
use App\Models\ExtraInfo;
use App\Models\FrontPage;
use App\Models\HelpPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentsResource;
use App\Http\Requests\CommentStoreRequest;
use App\Http\Requests\CommentUpdateRequest;

class CommonController extends Controller
{
    /**
     * @param Request $request
     * @return array
     */
    public function getPartner(Request $request): array
    {
        $partner = FrontPage::query()
            ->select('id', 'title', 'image', 'description')
            ->where('type', 'Partner')
            ->when($request->query('country_id'), function ($q) use ($request) {
                return $q->where('country_id', $request->query('country_id'));
            })
            ->first();

        $items = ExtraInfo::query()
            ->select('id', 'name', 'image', 'url')
            ->where('type', 'Partner')
            ->get();
        return [
            'info' => $partner,
            'partners' => $items,
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getHelpPage(Request $request): array
    {
        $help_page = HelpPage::query()
            ->select('id', 'description')
            ->when($request->query('country_id'), function ($q) use ($request) {
                return $q->where('country_id', $request->query('country_id'));
            })
            ->get();
        return [
            'pages' => $help_page,
        ];
    }


    /**
     * @param Country $country
     * @return JsonResponse
     */
    public function getCountryWiseCity(Country $country): JsonResponse
    {
        $country->load(['states', 'states.cities']);
        return response()->json([
            'states' => $country->states
        ]);
    }
}
