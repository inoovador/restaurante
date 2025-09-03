<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class CacheMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $minutes
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $minutes = 5)
    {
        // Solo cachear para métodos GET
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        // No cachear para usuarios autenticados con roles específicos
        if (Auth::check() && Auth::user()->hasRole(['admin', 'manager'])) {
            return $next($request);
        }

        // Crear clave de caché única
        $cacheKey = $this->makeCacheKey($request);

        // Verificar si ya existe en caché
        $cachedResponse = Cache::get($cacheKey);
        
        if ($cachedResponse) {
            return response($cachedResponse['content'])
                ->header('Content-Type', $cachedResponse['content_type'])
                ->header('X-Cache', 'HIT')
                ->header('Cache-Control', 'public, max-age=' . ($minutes * 60));
        }

        // Procesar la respuesta
        $response = $next($request);

        // Solo cachear respuestas exitosas
        if ($response->getStatusCode() === 200) {
            $contentType = $response->headers->get('Content-Type', 'text/html');
            
            Cache::put($cacheKey, [
                'content' => $response->getContent(),
                'content_type' => $contentType,
                'created_at' => now()
            ], now()->addMinutes($minutes));

            $response->header('X-Cache', 'MISS');
        }

        return $response->header('Cache-Control', 'public, max-age=' . ($minutes * 60));
    }

    /**
     * Crear clave de caché única
     */
    private function makeCacheKey(Request $request): string
    {
        $url = $request->fullUrl();
        $user = Auth::id() ?? 'guest';
        
        return 'page_cache:' . md5($url . $user);
    }

    /**
     * Limpiar caché específico
     */
    public static function clearCache($pattern = null)
    {
        if ($pattern) {
            $keys = Cache::get('cache_keys', []);
            foreach ($keys as $key) {
                if (str_contains($key, $pattern)) {
                    Cache::forget($key);
                }
            }
        } else {
            Cache::flush();
        }
    }
}