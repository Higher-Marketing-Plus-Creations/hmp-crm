<?php

namespace App\Http\Middleware;

use App\Models\Website;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureValidWebsiteApiRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->headers->get('Origin');
        $originHost = $origin ? parse_url($origin, PHP_URL_HOST) : null;

        if ($request->isMethod('OPTIONS')) {
            if ($this->isLocalTestingDomain($originHost)) {
                return $this->corsResponse(response()->noContent(), $origin);
            }

            $website = Website::query()
                ->where('status', 'active')
                ->get()
                ->first(fn (Website $website) => $website->matchesDomain($originHost));

            if (! $website) {
                return response()->json(['message' => 'Origin not allowed.'], 403);
            }

            return $this->corsResponse(response()->noContent(), $origin);
        }

        $apiKey = (string) $request->header('X-API-KEY');

        if ($apiKey === '') {
            return response()->json(['message' => 'Unauthorized request.'], 401);
        }

        $website = Website::query()
            ->where('api_key', $apiKey)
            ->where('status', 'active')
            ->first();

        if (! $website) {
            return response()->json(['message' => 'Unauthorized request.'], 401);
        }

        $requestHost = parse_url((string) $request->input('page_url'), PHP_URL_HOST);
        $referrerHost = parse_url((string) $request->headers->get('referer'), PHP_URL_HOST);

        if ($this->hasLocalTestingOrigin([$originHost, $requestHost, $referrerHost])) {
            $request->attributes->set('website', $website);

            return $this->corsResponse($next($request), $origin);
        }

        $domainMatches = collect([$originHost, $requestHost, $referrerHost])
            ->filter()
            ->contains(fn (string $domain) => $website->matchesDomain($domain));

        if (! $domainMatches) {
            return response()->json(['message' => 'Origin not allowed.'], 403);
        }

        $request->attributes->set('website', $website);

        return $this->corsResponse($next($request), $origin);
    }

    protected function corsResponse(Response $response, ?string $origin): Response
    {
        if ($origin) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Vary', 'Origin');
        }

        $response->headers->set('Access-Control-Allow-Methods', 'POST, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-API-KEY, X-Requested-With');

        return $response;
    }

    protected function hasLocalTestingOrigin(array $hosts): bool
    {
        return collect($hosts)
            ->filter()
            ->contains(fn (string $host) => $this->isLocalTestingDomain($host));
    }

    protected function isLocalTestingDomain(?string $host): bool
    {
        if (! app()->isLocal() || ! $host) {
            return false;
        }

        $host = Str::lower($host);

        return in_array($host, ['localhost', '127.0.0.1', '::1'], true);
    }
}
