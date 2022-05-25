<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Quote;
use App\Models\Stock;
use App\Models\User;
use App\Services\EmailQuoteService;
use Firebase\JWT\JWT;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Swift_Mailer;
use Swift_Message;

class StocksController
{
    /**
     * @var EmailQuoteService
     */
    private EmailQuoteService $emailQuoteService;

    /**
     * @var Swift_Mailer
     */
    private Swift_Mailer $mailer;

    /**
     * @param EmailQuoteService $emailQuoteService
     */
    public function __construct(EmailQuoteService $emailQuoteService, Swift_Mailer $mailer)
    {
        $this->emailQuoteService = $emailQuoteService;
        $this->mailer = $mailer;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     *
     * @throws \Exception
     */
    public function index(Request $request, Response $response, array $args): Response
    {
        $data = [
            'data' => [ ],
            'status' => ResponseHelper::STATUS_SUCCESS,
            'message' => null,
            'live_data' => true,
        ];

        try
        {
            if( !($params = $request->getQueryParams()) || !isset($params['symbol']) || empty($params['symbol']) ) {
                throw new \Exception('Bad parameters',
                    ResponseHelper::BAD_REQUEST
                );
            }

            $payload_token = $request->getAttribute('token'); // JWT Payload Token

            // -- normalize
            $params['symbol'] = trim(strtolower($params['symbol']));

            // -- create the request
            $client = new \GuzzleHttp\Client([ 'timeout' => 5 ]); // we need short timeout
            $http_response = $client->request('GET', "https://stooq.com/q/l/?s={$params['symbol']}&f=d2t2ohlcn&h&e=json");

            if( $http_response->getStatusCode() !== 200 ) {
                throw new \Exception('External service (Stooq) unavailable',
                    ResponseHelper::EXTERNAL_SERVICE_UNAVAILABLE
                );
            }

            $http_response = json_decode( $http_response->getBody()->getContents() );

            if( !$http_response || !property_exists($http_response, "symbols") ||
                !is_array($http_response->symbols) ) {
                throw new \Exception('Error when trying to decode the JSON response from the external service',
                    ResponseHelper::EXTERNAL_SERVICE_BAD_RESPONSE
                );
            }

            if( empty($http_response->symbols) ) {
                throw new \Exception('Symbol not found',
                    ResponseHelper::NOT_FOUND
                );
            }

            // find or create the stock, basically
            $stock = Stock::firstOrCreate([ 'symbol' => $params['symbol'] ], [
                'name'      => $http_response->symbols[0]->name,
                'symbol'    => $params['symbol'],
            ]);

            // set values
            $values = [
                'open'      => $http_response->symbols[0]->open,
                'high'      => $http_response->symbols[0]->high,
                'low'       => $http_response->symbols[0]->low,
                'close'     => $http_response->symbols[0]->close,
            ];

            // store the quote
            $quote = Quote::create(array_merge([
                'user_id'   => $payload_token['user_id'],
                'stock_id'  => $stock->id,
                'date'      => "{$http_response->symbols[0]->date} {$http_response->symbols[0]->time}",
            ], $values));

            $data['data'] = array_merge([
                'name'      => $http_response->symbols[0]->name,
                'symbol'    => $params['symbol'],
            ], $values);

            ################################################
            // -- E-mail
            if(env('RMQ_ENABLED', 1)) {
                $this->emailQuoteService->send( $quote );
            } else {
                $user = User::find($payload_token['user_id']);

                $body = $data['data'];

                // -- prepares the email body string
                array_walk($body, fn (&$i, $k) => $i = "$k => {$i}\n" );

                // Then we can just call the Mailer from any Controller method.
                $swift_message = (new Swift_Message('Hello from PHP Challenge'))
                    ->setFrom([ "{$user->email}" => 'PHP Challenge'])
                    ->setTo([ $user->email ])
                    ->setBody( $body );

                // Later just do the actual email sending.
                $response = $this->mailer->send($swift_message);
            }


        } catch ( ClientException ) {

            $data['status'] = ResponseHelper::STATUS_ERROR;
            $data['message'] = 'Internal server error';
            $response = $response->withStatus( ResponseHelper::EXTERNAL_SERVICE_UNAVAILABLE );
            $data['live_data'] = false;

        } catch (\Exception $ex) {

            $data['status'] = ResponseHelper::STATUS_ERROR;
            $data['live_data'] = false;

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
