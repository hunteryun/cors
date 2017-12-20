<?php

namespace Hunter\cors;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Neomerx\Cors\Analyzer;
use Neomerx\Cors\Contracts\AnalysisResultInterface;
use Neomerx\Cors\Strategies\Settings;

/**
 * Provides cors module permission auth.
 */
class CorsPermission {

  protected $allowed_origins;

  /**
   * Constructs a cors config.
   */
  public function __construct() {
    $this->allowed_origins = config('cors')->get('allowed_origins');
  }

  /**
   * Returns bool value of cors permission.
   *
   * @return bool
   */
  public function handle(ServerRequestInterface $request, ResponseInterface $response, callable $next) {
    $settings = new Settings();
    $settings->setRequestAllowedOrigins($this->allowed_origins);

    $cors = Analyzer::instance($settings)->analyze($request);
    switch ($cors->getRequestType()) {
        case AnalysisResultInterface::ERR_NO_HOST_HEADER:
        case AnalysisResultInterface::ERR_ORIGIN_NOT_ALLOWED:
        case AnalysisResultInterface::ERR_METHOD_NOT_SUPPORTED:
        case AnalysisResultInterface::ERR_HEADERS_NOT_SUPPORTED:
          return $response->withStatus(403);
        case AnalysisResultInterface::TYPE_REQUEST_OUT_OF_CORS_SCOPE:
          return $next($request, $response);
        case AnalysisResultInterface::TYPE_PRE_FLIGHT_REQUEST:
          $corsHeaders = $cors->getResponseHeaders();
          foreach ($corsHeaders as $header => $value) {
              /* Diactoros errors on integer values. */
              if (!is_array($value)) {
                  $value = (string)$value;
              }
              $response = $response->withHeader($header, $value);
          }
          return $response->withStatus(200);
        default:
          $response = $next($request, $response);
          $corsHeaders = $cors->getResponseHeaders();
          foreach ($corsHeaders as $header => $value) {
              /* Diactoros errors on integer values. */
              if (!is_array($value)) {
                  $value = (string)$value;
              }
              $response = $response->withHeader($header, $value);
          }
          return $response;
    }
  }

}
