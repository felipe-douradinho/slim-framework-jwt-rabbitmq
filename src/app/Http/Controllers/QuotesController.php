<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Quote;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class QuotesController
{
    /**
     * QuotesController constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     *
     * @throws GuzzleException
     * @throws \Exception
     */
    public function index(Request $request, Response $response, array $args): Response
    {
        $data = [
            'data' => [ ],
            'status' => ResponseHelper::STATUS_SUCCESS,
            'message' => null,
            "paging" => [
                'previous'  => 'http://api.example.com/foo/page/1', // @ TODO: Implement a pagination
                'next'      => 'http://api.example.com/foo/page/3', // @ TODO: Implement a pagination
            ],
        ];

        try
        {
            $payload_token = $request->getAttribute('token'); // JWT Payload Token

            if( !($user_id = $payload_token['user_id']) &&
                !isset($payload_token) || empty($payload_token) || !isset($payload_token['user_id']) ) {
                throw new \Exception('Bad parameters',
                    ResponseHelper::BAD_REQUEST
                );
            }

            // store the quote
            $quotes = Quote::with('stock:id,name,symbol')
                ->whereUserId($user_id)
                ->orderBy('date', 'desc')
                ->limit(env('MAX_QUOTES_PER_PAGE', 20))
                ->get();

            foreach ($quotes as $quote) {
                $data['data'][] = [
                    'date'      => $quote->date,
                    'name'      => $quote->stock->name,
                    'symbol'    => $quote->stock->symbol,
                    'open'      => $quote->open,
                    'high'      => $quote->high,
                    'low'       => $quote->low,
                    'close'     => $quote->close,
                ];
            }

        } catch (\Exception $ex) {

            $data['status'] = ResponseHelper::STATUS_ERROR;
            $data['paging'] = [ 'previous' => null, 'next' => null ];

            if( in_array($ex->getCode(), ResponseHelper::$errors_codes) ) {
                $data['message'] = $ex->getMessage();
                $response = $response->withStatus( $ex->getCode() );
            } else {
                $data['message'] = 'Internal server error';
                $response = $response->withStatus( ResponseHelper::INTERNAL_SERVER_ERROR );
            }

        }

        $response->getBody()->write(
            json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
        );

        return $response->withHeader("Content-Type", "application/json");

    }

}
