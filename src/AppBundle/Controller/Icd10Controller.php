<?php

declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Service\WhoIcdApiService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * ICD-10 API Controller.
 *
 * Provides endpoints for searching, autocoding, and browsing ICD-10 codes.
 * Used by frontend for diagnosis code selection and validation.
 *
 * @Route("/api/icd10")
 */
class Icd10Controller extends Controller
{
    /** @var WhoIcdApiService */
    private $icdApi;

    public function __construct(WhoIcdApiService $icdApi)
    {
        $this->icdApi = $icdApi;
    }

    /**
     * Autocomplete endpoint for diagnosis code dropdown.
     *
     * @Route("/autocomplete", name="icd10_autocomplete", methods={"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     *
     * Query params:
     *   - q: Search query (required, min 2 chars)
     *   - limit: Max results (default 10, max 50)
     *   - lang: Language code (default 'en')
     *
     * Response:
     *   [{ "code": "A00.0", "title": "Cholera due to...", "score": 0.95 }, ...]
     */
    public function autocompleteAction(Request $request): JsonResponse
    {
        $query = trim($request->query->get('q', ''));
        $limit = min((int) $request->query->get('limit', 10), 50);
        $lang = $request->query->get('lang', 'en');

        if (strlen($query) < 2) {
            return new JsonResponse([
                'error' => 'Query must be at least 2 characters',
            ], 400);
        }

        try {
            $this->icdApi->setLanguage($lang);
            $results = $this->icdApi->autocomplete($query, $limit);

            return new JsonResponse($results);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Autocode endpoint - match diagnostic text to ICD codes.
     *
     * @Route("/autocode", name="icd10_autocode", methods={"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     *
     * Query params:
     *   - text: Diagnostic text to match (required)
     *   - threshold: Minimum match score 0-1 (optional)
     *   - lang: Language code (default 'en')
     *
     * Response:
     *   { "matchingText": "...", "theCode": "A00.0", "matchScore": 0.95, ... }
     */
    public function autocodeAction(Request $request): JsonResponse
    {
        $text = trim($request->query->get('text', ''));
        $threshold = $request->query->get('threshold');
        $lang = $request->query->get('lang', 'en');

        if (empty($text)) {
            return new JsonResponse([
                'error' => 'Text parameter is required',
            ], 400);
        }

        try {
            $this->icdApi->setLanguage($lang);
            $result = $this->icdApi->autocode(
                $text,
                $threshold !== null ? (float) $threshold : null
            );

            return new JsonResponse($result);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Search endpoint - full text search for ICD codes.
     *
     * @Route("/search", name="icd10_search", methods={"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     *
     * Query params:
     *   - q: Search query (required)
     *   - limit: Max results (default 20, max 100)
     *   - lang: Language code (default 'en')
     *   - flexisearch: Enable flexible matching (default true)
     */
    public function searchAction(Request $request): JsonResponse
    {
        $query = trim($request->query->get('q', ''));
        $limit = min((int) $request->query->get('limit', 20), 100);
        $lang = $request->query->get('lang', 'en');
        $flexisearch = $request->query->get('flexisearch', 'true') === 'true';

        if (empty($query)) {
            return new JsonResponse([
                'error' => 'Query parameter is required',
            ], 400);
        }

        try {
            $this->icdApi->setLanguage($lang);
            $result = $this->icdApi->search($query, $flexisearch, true, $limit);

            return new JsonResponse($result);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Get available ICD-10 releases.
     *
     * @Route("/releases", name="icd10_releases", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function releasesAction(): JsonResponse
    {
        try {
            $releases = $this->icdApi->getReleases();
            return new JsonResponse($releases);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Get chapters for a release.
     *
     * @Route("/{releaseId}/chapters", name="icd10_chapters", methods={"GET"})
     *
     * @param string $releaseId Release year (e.g., '2019')
     * @return JsonResponse
     */
    public function chaptersAction(string $releaseId): JsonResponse
    {
        try {
            $chapters = $this->icdApi->getChapters($releaseId);
            return new JsonResponse($chapters);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Get code details.
     *
     * @Route("/{releaseId}/code/{code}", name="icd10_code", methods={"GET"},
     *     requirements={"code"=".+"})
     *
     * @param string  $releaseId Release year
     * @param string  $code      ICD-10 code
     * @param Request $request
     * @return JsonResponse
     */
    public function codeAction(string $releaseId, string $code, Request $request): JsonResponse
    {
        $lang = $request->query->get('lang', 'en');

        try {
            $this->icdApi->setLanguage($lang);
            $result = $this->icdApi->getCode($releaseId, $code);

            return new JsonResponse($result);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Get children of a code.
     *
     * @Route("/{releaseId}/code/{code}/children", name="icd10_children", methods={"GET"},
     *     requirements={"code"=".+"})
     *
     * @param string  $releaseId Release year
     * @param string  $code      Parent code
     * @param Request $request
     * @return JsonResponse
     */
    public function childrenAction(string $releaseId, string $code, Request $request): JsonResponse
    {
        $lang = $request->query->get('lang', 'en');

        try {
            $this->icdApi->setLanguage($lang);
            $children = $this->icdApi->getChildren($releaseId, $code);

            return new JsonResponse($children);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Create error response from exception.
     *
     * @param \RuntimeException $e
     * @return JsonResponse
     */
    private function errorResponse(\RuntimeException $e): JsonResponse
    {
        $message = $e->getMessage();

        // Determine status code from message
        $statusCode = 500;
        if (strpos($message, '(401)') !== false || strpos($message, 'authentication') !== false) {
            $statusCode = 401;
        } elseif (strpos($message, '(404)') !== false) {
            $statusCode = 404;
        } elseif (strpos($message, '(429)') !== false) {
            $statusCode = 429;
        }

        return new JsonResponse([
            'error' => $message,
        ], $statusCode);
    }
}
