<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\User;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UsersController
{
    /**
     * UsersController constructor.
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
     * @throws \Exception
     */
    public function store(Request $request, Response $response, array $args): Response
    {
        $data = [
            'data' => [ ],
            'status' => ResponseHelper::STATUS_SUCCESS,
            'message' => null,
        ];

        try {
            // @TODO Create an http validator (with rules, messages, etc)
            if (!($params = $request->getParsedBody()) || !isset($params['name']) || empty($params['name'])
                || !isset($params['email']) || empty($params['email'])
                || !isset($params['password']) || empty($params['password'])) {
                throw new \Exception('Bad parameters',
                    ResponseHelper::BAD_REQUEST
                );
            }

            // -- normalize
            $params['email'] = trim(strtolower($params['email']));
            $params['password'] = trim($params['password']);

            // -- simple validation
            if (User::whereEmail($params['email'])->first(['id'])) {
                throw new \Exception('Sorry, user already exists!',
                    ResponseHelper::BAD_REQUEST
                );
            }

            // find or create the stock, basically
            $user = User::create([
                'name'          => $params['name'],
                'email'         => $params['email'],
                'password'      => $params['password'],
            ]);

            $payload = [ 'user_id' => $user->id ]; // JWT Payload
            $access_token = JWT::encode( $payload, env('JWT_SECRET') ); // JWT Payload

            $user->update(compact('access_token'));

            $data['data'] = [ 'email' => $params['email'], 'access_token' => $access_token ];

        } catch (\Exception $ex) {

            $data['status'] = ResponseHelper::STATUS_ERROR;

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
