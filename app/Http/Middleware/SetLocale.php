<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * The available application locales.
     *
     * @var array
     */
    protected $locales = ['en', 'vi'];

    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->header('Accept-Language');

        if (! $locale || ! in_array($locale, $this->locales)) {
            $locale = $request->session()->get('locale');
        }

        if (! $locale || ! in_array($locale, $this->locales)) {
            $locale = $request->query('locale');
        }

        if ($locale && in_array($locale, $this->locales)) {
            App::setLocale($locale);
            $request->session()->put('locale', $locale);
        }

        return $next($request);
    }
}
