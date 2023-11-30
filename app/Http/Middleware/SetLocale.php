<?php

namespace App\Http\Middleware;

use App\Models\Country;
use Closure;
use Illuminate\Support\Facades\URL;

class SetLocale
{

    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next): mixed
    {
        $current_locale_query = $request->query('locale');
        $languages = Country::query()->where('is_active', 1)->pluck('iso2')->map(fn($item) => strtolower($item))->toArray();
        if ($current_locale_query && in_array($current_locale_query, $languages)) {
            $request['default_country'] = Country::query()->where('iso2', $current_locale_query)->first();
        } else {
            $request['default_country'] = Country::query()->where('is_default', 1)->first();
        }
        if ($current_locale_query !== 'all' && isset($request['default_country']) && isset($request['default_country']['iso2'])) {
            $request->merge(['locale' => strtolower($request['default_country']['iso2'])]);
        }
        return $next($request);
    }
}
