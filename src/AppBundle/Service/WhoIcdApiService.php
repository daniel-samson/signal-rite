<?php

declare(strict_types=1);

namespace AppBundle\Service;

use Psr\Cache\CacheItemPoolInterface;

/**
 * WHO ICD API Service for fetching ICD-10 codes.
 *
 * Uses OAuth2 client credentials flow to authenticate with the WHO ICD API.
 * Provides methods for searching, autocoding, and browsing ICD-10 codes.
 * Implements caching to reduce API calls and improve response times.
 *
 * @see https://icd.who.int/icdapi
 * @see https://id.who.int/swagger/index.html
 */
class WhoIcdApiService
{
    private const TOKEN_ENDPOINT = 'https://icdaccessmanagement.who.int/connect/token';
    private const API_BASE_URL = 'https://id.who.int';
    private const API_VERSION = 'v2';
    private const TOKEN_SCOPE = 'icdapi_access';

    // Cache TTLs in seconds
    private const CACHE_TTL_RELEASES = 604800;   // 7 days - releases rarely change
    private const CACHE_TTL_CHAPTERS = 604800;   // 7 days - chapters rarely change
    private const CACHE_TTL_ENTITY = 86400;      // 24 hours - entity details
    private const CACHE_TTL_SEARCH = 3600;       // 1 hour - search results
    private const CACHE_TTL_AUTOCODE = 3600;     // 1 hour - autocode results

    /** @var string */
    private $clientId;

    /** @var string */
    private $clientSecret;

    /** @var CacheItemPoolInterface|null */
    private $cache;

    /** @var string|null */
    private $accessToken;

    /** @var int|null */
    private $tokenExpiresAt;

    /** @var string */
    private $language = 'en';

    /** @var bool */
    private $cacheEnabled = true;

    /**
     * @param string                      $clientId     WHO ICD API Client ID
     * @param string                      $clientSecret WHO ICD API Client Secret
     * @param CacheItemPoolInterface|null $cache        Optional cache pool
     */
    public function __construct(string $clientId, string $clientSecret, ?CacheItemPoolInterface $cache = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->cache = $cache;
    }

    /**
     * Enable or disable caching.
     *
     * @param bool $enabled
     * @return self
     */
    public function setCacheEnabled(bool $enabled): self
    {
        $this->cacheEnabled = $enabled;
        return $this;
    }

    /**
     * Set the language for API responses.
     *
     * @param string $language ISO language code (e.g., 'en', 'es', 'fr')
     * @return self
     */
    public function setLanguage(string $language): self
    {
        $this->language = $language;
        return $this;
    }

    // =========================================================================
    // Search & Autocode Endpoints
    // =========================================================================

    /**
     * Search for ICD entities using text query.
     *
     * @param string $query           Search text (supports "%" wildcard)
     * @param bool   $useFlexisearch  Enable flexible matching
     * @param bool   $flatResults     Return flat vs hierarchical results
     * @param int    $maxResults      Maximum results to return
     * @return array Search results
     * @throws \RuntimeException on API error
     */
    public function search(
        string $query,
        bool $useFlexisearch = true,
        bool $flatResults = true,
        int $maxResults = 20
    ): array {
        $cacheKey = $this->buildCacheKey('search', [
            'q' => $query,
            'flex' => $useFlexisearch,
            'flat' => $flatResults,
            'lang' => $this->language,
        ]);

        return $this->cached($cacheKey, self::CACHE_TTL_SEARCH, function () use ($query, $useFlexisearch, $flatResults, $maxResults) {
            $params = http_build_query([
                'q' => $query,
                'useFlexisearch' => $useFlexisearch ? 'true' : 'false',
                'flatResults' => $flatResults ? 'true' : 'false',
                'highlightingEnabled' => 'false',
            ]);

            $result = $this->request('/icd/entity/search?' . $params);

            // Limit results
            if (isset($result['destinationEntities']) && is_array($result['destinationEntities'])) {
                $result['destinationEntities'] = array_slice($result['destinationEntities'], 0, $maxResults);
            }

            return $result;
        });
    }

    /**
     * Autocode - match diagnostic text to ICD codes.
     *
     * @param string     $searchText     Diagnostic text to match
     * @param float|null $matchThreshold Minimum similarity score (0-1)
     * @return array Autocode results with matched codes and scores
     * @throws \RuntimeException on API error
     */
    public function autocode(string $searchText, ?float $matchThreshold = null): array
    {
        $cacheKey = $this->buildCacheKey('autocode', [
            'text' => $searchText,
            'threshold' => $matchThreshold,
            'lang' => $this->language,
        ]);

        return $this->cached($cacheKey, self::CACHE_TTL_AUTOCODE, function () use ($searchText, $matchThreshold) {
            $params = ['searchText' => $searchText];

            if ($matchThreshold !== null) {
                $params['matchThreshold'] = (string) $matchThreshold;
            }

            return $this->request('/icd/entity/autocode?' . http_build_query($params));
        });
    }

    /**
     * Search ICD-10 codes for autocomplete/dropdown.
     *
     * Returns simplified results suitable for frontend dropdowns.
     *
     * @param string $query      Search text
     * @param int    $maxResults Maximum results
     * @return array Array of ['code' => string, 'title' => string, 'uri' => string]
     * @throws \RuntimeException on API error
     */
    public function autocomplete(string $query, int $maxResults = 10): array
    {
        $cacheKey = $this->buildCacheKey('autocomplete', [
            'q' => $query,
            'limit' => $maxResults,
            'lang' => $this->language,
        ]);

        return $this->cached($cacheKey, self::CACHE_TTL_SEARCH, function () use ($query, $maxResults) {
            $result = $this->search($query, true, true, $maxResults);

            $items = [];

            if (isset($result['destinationEntities']) && is_array($result['destinationEntities'])) {
                foreach ($result['destinationEntities'] as $entity) {
                    $items[] = [
                        'code' => $entity['theCode'] ?? '',
                        'title' => $entity['title'] ?? '',
                        'uri' => $entity['id'] ?? '',
                        'score' => $entity['score'] ?? 0,
                    ];
                }
            }

            return $items;
        });
    }

    // =========================================================================
    // Browse Endpoints
    // =========================================================================

    /**
     * Get available ICD-10 releases.
     *
     * @return array List of available releases
     * @throws \RuntimeException on API error
     */
    public function getReleases(): array
    {
        $cacheKey = $this->buildCacheKey('releases', ['lang' => $this->language]);

        return $this->cached($cacheKey, self::CACHE_TTL_RELEASES, function () {
            return $this->request('/icd/release/10');
        });
    }

    /**
     * Get ICD-10 release details (includes chapters).
     *
     * @param string $releaseId Release year (e.g., '2019', '2016')
     * @return array Release details with chapters
     * @throws \RuntimeException on API error
     */
    public function getRelease(string $releaseId): array
    {
        $cacheKey = $this->buildCacheKey('release', [
            'id' => $releaseId,
            'lang' => $this->language,
        ]);

        return $this->cached($cacheKey, self::CACHE_TTL_RELEASES, function () use ($releaseId) {
            return $this->request("/icd/release/10/{$releaseId}");
        });
    }

    /**
     * Get ICD-10 chapters for a release.
     *
     * @param string $releaseId Release year
     * @return array Array of chapters with code and title
     * @throws \RuntimeException on API error
     */
    public function getChapters(string $releaseId): array
    {
        $cacheKey = $this->buildCacheKey('chapters', [
            'id' => $releaseId,
            'lang' => $this->language,
        ]);

        return $this->cached($cacheKey, self::CACHE_TTL_CHAPTERS, function () use ($releaseId) {
            $release = $this->getRelease($releaseId);
            $chapters = [];

            if (!empty($release['child'])) {
                foreach ($release['child'] as $chapterUri) {
                    try {
                        $chapter = $this->getByUri($chapterUri);
                        $chapters[] = [
                            'code' => $chapter['code'] ?? $chapter['codeRange'] ?? basename($chapterUri),
                            'title' => $this->extractTitle($chapter),
                            'uri' => $chapterUri,
                        ];
                    } catch (\RuntimeException $e) {
                        // Skip failed chapters
                    }
                }
            }

            return $chapters;
        });
    }

    /**
     * Get ICD-10 entity details by code.
     *
     * @param string $releaseId Release year (e.g., '2019')
     * @param string $code      ICD-10 code (e.g., 'A00', 'A00.0', 'A00-A09')
     * @return array Entity details including children
     * @throws \RuntimeException on API error
     */
    public function getEntity(string $releaseId, string $code): array
    {
        $cacheKey = $this->buildCacheKey('entity', [
            'release' => $releaseId,
            'code' => $code,
            'lang' => $this->language,
        ]);

        return $this->cached($cacheKey, self::CACHE_TTL_ENTITY, function () use ($releaseId, $code) {
            return $this->request("/icd/release/10/{$releaseId}/{$code}");
        });
    }

    /**
     * Get entity details formatted for frontend display.
     *
     * @param string $releaseId Release year
     * @param string $code      ICD-10 code
     * @return array Simplified entity data
     * @throws \RuntimeException on API error
     */
    public function getCode(string $releaseId, string $code): array
    {
        $cacheKey = $this->buildCacheKey('code', [
            'release' => $releaseId,
            'code' => $code,
            'lang' => $this->language,
        ]);

        return $this->cached($cacheKey, self::CACHE_TTL_ENTITY, function () use ($releaseId, $code) {
            $entity = $this->getEntity($releaseId, $code);

            $result = [
                'code' => $entity['code'] ?? $entity['codeRange'] ?? $code,
                'title' => $this->extractTitle($entity),
                'definition' => $entity['definition']['@value'] ?? null,
            ];

            // Add inclusions
            if (!empty($entity['inclusion'])) {
                $result['inclusions'] = array_map(function ($item) {
                    return $item['label']['@value'] ?? (is_string($item) ? $item : '');
                }, $entity['inclusion']);
            }

            // Add exclusions
            if (!empty($entity['exclusion'])) {
                $result['exclusions'] = array_map(function ($item) {
                    return $item['label']['@value'] ?? (is_string($item) ? $item : '');
                }, $entity['exclusion']);
            }

            // Add children codes
            if (!empty($entity['child'])) {
                $result['children'] = array_map(function ($uri) {
                    return basename($uri);
                }, $entity['child']);
            }

            // Add parent
            if (!empty($entity['parent'])) {
                $result['parent'] = is_array($entity['parent'])
                    ? basename($entity['parent'][0])
                    : basename($entity['parent']);
            }

            return $result;
        });
    }

    /**
     * Get chapter details by URI.
     *
     * @param string $uri Full chapter URI from release response
     * @return array Chapter details
     * @throws \RuntimeException on API error
     */
    public function getByUri(string $uri): array
    {
        $cacheKey = $this->buildCacheKey('uri', [
            'uri' => $uri,
            'lang' => $this->language,
        ]);

        return $this->cached($cacheKey, self::CACHE_TTL_ENTITY, function () use ($uri) {
            $path = parse_url($uri, PHP_URL_PATH);
            return $this->request($path);
        });
    }

    /**
     * Get children of a code.
     *
     * @param string $releaseId Release year
     * @param string $code      Parent code
     * @return array Array of child codes with title
     * @throws \RuntimeException on API error
     */
    public function getChildren(string $releaseId, string $code): array
    {
        $cacheKey = $this->buildCacheKey('children', [
            'release' => $releaseId,
            'code' => $code,
            'lang' => $this->language,
        ]);

        return $this->cached($cacheKey, self::CACHE_TTL_ENTITY, function () use ($releaseId, $code) {
            $entity = $this->getEntity($releaseId, $code);
            $children = [];

            if (!empty($entity['child'])) {
                foreach ($entity['child'] as $childUri) {
                    try {
                        $child = $this->getByUri($childUri);
                        $children[] = [
                            'code' => $child['code'] ?? basename($childUri),
                            'title' => $this->extractTitle($child),
                            'hasChildren' => !empty($child['child']),
                        ];
                    } catch (\RuntimeException $e) {
                        // Skip failed children
                    }
                }
            }

            return $children;
        });
    }

    /**
     * Extract title from entity (handles multiple formats).
     *
     * @param array $entity Entity data
     * @return string Title
     */
    private function extractTitle(array $entity): string
    {
        if (isset($entity['title']['@value'])) {
            return $entity['title']['@value'];
        }
        if (isset($entity['title']) && is_string($entity['title'])) {
            return $entity['title'];
        }
        return '';
    }

    /**
     * Make authenticated request to WHO ICD API.
     *
     * @param string $path API endpoint path
     * @return array Response data
     * @throws \RuntimeException on error
     */
    private function request(string $path): array
    {
        $this->ensureAccessToken();

        $url = self::API_BASE_URL . $path;

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => implode("\r\n", [
                    'Authorization: Bearer ' . $this->accessToken,
                    'Accept: application/json',
                    'Accept-Language: ' . $this->language,
                    'API-Version: ' . self::API_VERSION,
                ]),
                'timeout' => 30,
                'ignore_errors' => true,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            throw new \RuntimeException('Failed to connect to WHO ICD API: ' . $url);
        }

        // Check HTTP status from headers
        $statusCode = $this->getHttpStatusCode($http_response_header ?? []);
        if ($statusCode >= 400) {
            throw new \RuntimeException("WHO ICD API error ({$statusCode}): {$response}");
        }

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid JSON response from WHO ICD API');
        }

        return $data;
    }

    /**
     * Ensure we have a valid access token.
     *
     * @throws \RuntimeException on authentication failure
     */
    private function ensureAccessToken(): void
    {
        // Check if token is still valid (with 60 second buffer)
        if ($this->accessToken && $this->tokenExpiresAt && time() < ($this->tokenExpiresAt - 60)) {
            return;
        }

        $this->authenticate();
    }

    /**
     * Authenticate with WHO ICD API using OAuth2 client credentials.
     *
     * @throws \RuntimeException on authentication failure
     */
    private function authenticate(): void
    {
        $credentials = base64_encode($this->clientId . ':' . $this->clientSecret);

        $postData = http_build_query([
            'grant_type' => 'client_credentials',
            'scope' => self::TOKEN_SCOPE,
        ]);

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", [
                    'Authorization: Basic ' . $credentials,
                    'Content-Type: application/x-www-form-urlencoded',
                ]),
                'content' => $postData,
                'timeout' => 30,
                'ignore_errors' => true,
            ],
        ]);

        $response = @file_get_contents(self::TOKEN_ENDPOINT, false, $context);

        if ($response === false) {
            throw new \RuntimeException('Failed to connect to WHO authentication server');
        }

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid response from WHO authentication server');
        }

        if (isset($data['error'])) {
            throw new \RuntimeException('WHO authentication failed: ' . ($data['error_description'] ?? $data['error']));
        }

        if (!isset($data['access_token'])) {
            throw new \RuntimeException('No access token in WHO authentication response');
        }

        $this->accessToken = $data['access_token'];
        $this->tokenExpiresAt = time() + ($data['expires_in'] ?? 3600);
    }

    /**
     * Extract HTTP status code from response headers.
     *
     * @param array $headers Response headers
     * @return int Status code
     */
    private function getHttpStatusCode(array $headers): int
    {
        foreach ($headers as $header) {
            if (preg_match('/^HTTP\/\d\.\d\s+(\d{3})/', $header, $matches)) {
                return (int) $matches[1];
            }
        }
        return 200;
    }

    // =========================================================================
    // Caching Helpers
    // =========================================================================

    /**
     * Build a cache key from prefix and parameters.
     *
     * @param string $prefix Cache key prefix
     * @param array  $params Parameters to include in key
     * @return string Cache key
     */
    private function buildCacheKey(string $prefix, array $params): string
    {
        $key = 'icd10_' . $prefix . '_' . md5(serialize($params));

        // PSR-6 cache keys must match pattern: /^[a-zA-Z0-9_.]+$/
        return preg_replace('/[^a-zA-Z0-9_.]/', '_', $key);
    }

    /**
     * Get cached value or execute callback and cache result.
     *
     * @param string   $key      Cache key
     * @param int      $ttl      Time to live in seconds
     * @param callable $callback Callback to execute if not cached
     * @return array Cached or fresh data
     */
    private function cached(string $key, int $ttl, callable $callback): array
    {
        // If caching is disabled or no cache pool, just execute callback
        if (!$this->cacheEnabled || $this->cache === null) {
            return $callback();
        }

        try {
            $item = $this->cache->getItem($key);

            if ($item->isHit()) {
                return $item->get();
            }

            $data = $callback();

            $item->set($data);
            $item->expiresAfter($ttl);
            $this->cache->save($item);

            return $data;
        } catch (\Psr\Cache\InvalidArgumentException $e) {
            // If cache fails, just execute callback
            return $callback();
        }
    }

    /**
     * Clear all ICD-10 cache entries.
     *
     * @return bool True if successful
     */
    public function clearCache(): bool
    {
        if ($this->cache === null) {
            return false;
        }

        return $this->cache->clear();
    }
}
