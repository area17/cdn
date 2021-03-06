<?php

namespace A17\CDN\Services\Akamai;

use A17\CDN\CDN;
use A17\CDN\Services\BaseService;
use A17\CDN\Contracts\CDNService;
use Illuminate\Support\Collection;
use A17\CDN\Services\TagsContainer;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response;
use Akamai\Open\EdgeGrid\Authentication as AkamaiAuthentication;

class Service extends BaseService implements CDNService
{
    protected array $tags = [];

    protected function getApiPath(): string
    {
        return '/ccu/v3/invalidate/tag/production';
    }

    /**
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    protected function getHost(): ?string
    {
        return config('cdn.services.akamai.host');
    }

    public function getInvalidationURL(): string
    {
        return 'https://' . $this->getHost() . $this->getApiPath();
    }

    public function invalidate(Collection $items): bool
    {
        $body = [
            'objects' => collect($items)
                ->map(fn($item) => is_object($item) ? $item->tag : $item)
                ->unique()
                ->toArray(),
        ];

        Http::withHeaders([
            'Authorization' => $this->getAuthHeaders($body),
        ])->post($this->getInvalidationURL(), $body);

        return true;
    }

    public function invalidateAll(): bool
    {
        return $this->createInvalidationRequest(
            config('cdn.services.akamai.invalidate_all_paths'),
        );
    }

    /**
     * @param mixed $body
     * @return string
     */
    public function getAuthHeaders($body): string
    {
        $auth = new AkamaiAuthentication();

        $auth->setHost($this->getHost());

        $auth->setBody(is_string($body) ? $body : json_encode($body));

        $auth->setHttpMethod('POST');

        $auth->setAuth(
            config('cdn.services.akamai.client_token'),
            config('cdn.services.akamai.client_secret'),
            config('cdn.services.akamai.access_token'),
        );

        $auth->setPath($this->getApiPath());

        return $auth->createAuthHeader();
    }
}
