<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    private $httpClient;
    private $serviceUrls;

    public function __construct()
    {
        $this->httpClient = new Client([
            'timeout' => 30,
            'connect_timeout' => 5
        ]);

        $this->serviceUrls = [
            'auth' => env('AUTH_SERVICE_URL', 'http://localhost:8001'),
            'pets' => env('PETS_SERVICE_URL', 'http://localhost:8002'),
            'appointments' => env('APPOINTMENTS_SERVICE_URL', 'http://localhost:8003'),
            'medical' => env('MEDICAL_SERVICE_URL', 'http://localhost:8004'),
            'inventory' => env('INVENTORY_SERVICE_URL', 'http://localhost:8005'),
            'billing' => env('BILLING_SERVICE_URL', 'http://localhost:8006'),
        ];
    }

    /**
     * Health check del gateway
     */
    public function health()
    {
        $services = [];
        
        foreach ($this->serviceUrls as $name => $url) {
            try {
                $response = $this->httpClient->get($url, ['timeout' => 5]);
                $services[$name] = [
                    'status' => 'online',
                    'url' => $url,
                    'response_time' => 'OK'
                ];
            } catch (\Exception $e) {
                $services[$name] = [
                    'status' => 'offline',
                    'url' => $url,
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'gateway' => 'API Gateway Veterinaria',
            'version' => '1.0.0',
            'status' => 'running',
            'services' => $services
        ]);
    }

    /**
     * Proxy específico para autenticación
     */
    public function authProxy(Request $request)
    {
        $currentPath = $request->path();
        $path = str_replace('api/auth/', '', $currentPath);
        
        $serviceUrl = $this->serviceUrls['auth'];
        $fullUrl = $serviceUrl . '/api/auth/' . $path;

        try {
            $response = $this->httpClient->request(
                $request->method(),
                $fullUrl,
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ],
                    'query' => $request->query->all(),
                    'json' => $request->method() !== 'GET' ? $request->all() : null
                ]
            );

            return response($response->getBody()->getContents())
                ->header('Content-Type', 'application/json')
                ->setStatusCode($response->getStatusCode());

        } catch (RequestException $e) {
            $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 500;
            $message = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();

            return response($message)
                ->header('Content-Type', 'application/json')
                ->setStatusCode($statusCode);
        }
    }

    /**
     * Proxy general para microservicios
     */
    public function proxy(Request $request, $service, $path = '')
    {
        if (!isset($this->serviceUrls[$service])) {
            return response()->json([
                'success' => false,
                'message' => 'Microservicio no encontrado'
            ], 404);
        }

        $serviceUrl = $this->serviceUrls[$service];
        $fullUrl = $serviceUrl . '/api/' . $service . '/' . $path;

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        if ($request->hasHeader('Authorization')) {
            $headers['Authorization'] = $request->header('Authorization');
        }

        if ($request->attributes->has('user_id')) {
            $headers['X-User-ID'] = $request->attributes->get('user_id');
            $headers['X-User-Email'] = $request->attributes->get('user_email');
            $headers['X-User-Role'] = $request->attributes->get('user_role');
        }

        try {
            $response = $this->httpClient->request(
                $request->method(),
                $fullUrl,
                [
                    'headers' => $headers,
                    'query' => $request->query->all(),
                    'json' => $request->method() !== 'GET' ? $request->all() : null
                ]
            );

            return response($response->getBody()->getContents())
                ->header('Content-Type', 'application/json')
                ->setStatusCode($response->getStatusCode());

        } catch (RequestException $e) {
            $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 500;
            $message = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();

            return response($message)
                ->header('Content-Type', 'application/json')
                ->setStatusCode($statusCode);
        }
    }
}