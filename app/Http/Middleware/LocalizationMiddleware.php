<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 2019/5/18
 * Time: 16:45
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\LanguageNegotiator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

class LocalizationMiddleware extends LocaleSessionRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // If the URL of the request is in exceptions.
        if ($this->shouldIgnore($request)) {
            return $next($request);
        }

        $params = explode('/', $request->path());
        $locale = session('locale', false);

        if (\count($params) > 0 && app('laravellocalization')->checkLocaleInSupportedLocales($params[0])) {
            session(['locale' => $params[0]]);

            return $next($request);
        }

        if (empty($locale) && app('laravellocalization')->hideUrlAndAcceptHeader()){
            // When default locale is hidden and accept language header is true,
            // then compute browser language when no session has been set.
            // Once the session has been set, there is no need
            // to negotiate language from browser again.
            $negotiator = new LanguageNegotiator(
                app('laravellocalization')->getDefaultLocale(),
                app('laravellocalization')->getSupportedLocales(),
                $request
            );
            $locale = $negotiator->negotiateLanguage();
            session(['locale' => $locale]);
        }

        if ($locale === false){
            $locale = app('laravellocalization')->getCurrentLocale();
        }

        if (
            $locale &&
            app('laravellocalization')->checkLocaleInSupportedLocales($locale) &&
            !(app('laravellocalization')->isHiddenDefault($locale))
        &&!$request->ajax()) {
            app('session')->reflash();
            $redirection = app('laravellocalization')->getLocalizedURL($locale);

            return new RedirectResponse($redirection, 302, ['Vary' => 'Accept-Language']);
        }

        return $next($request);
    }
}
