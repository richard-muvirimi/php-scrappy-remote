<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Exception;

class Auth implements FilterInterface
{
	/**
	 * Do whatever processing this filter needs to do.
	 * By default it should not return anything during
	 * normal execution. However, when an abnormal state
	 * is found, it should return an instance of
	 * CodeIgniter\HTTP\Response. If it does, script
	 * execution will end and that Response will be
	 * sent back to the client, allowing for error pages,
	 * redirects, etc.
	 *
	 * @param RequestInterface $request
	 * @param array|null       $arguments
	 *
	 * @return mixed
	 */
	public function before(RequestInterface $request, $arguments = null)
	{
		helper('user');

		$token = $request->header('authorization');
		try
		{
			if (empty($token))
			{
					throw new Exception('A Bearer token is required to use this service');
			}

			preg_match('/bearer\s*(.*)/i', $token, $match);

			$token = $match[1];

			$token     = base64_decode($token);
			$encrypter = Services::encrypter();
			try
			{
				$token = $encrypter->decrypt($token);
			}
			catch (Exception $e)
			{
				throw new Exception('A valid Bearer token is required to use this service');
			}

			if (! str_contains($token, ':'))
			{
				throw new Exception('A valid Bearer token is required to use this service');
			}

			$email = strtok($token, ':');
			$token = strtok(':');

			$user = get_user($email);

			if (! $user->verifyPassword($email) && $user->password == $token)
			{
				throw new Exception('A valid Bearer token is required to use this service');
			}

			$session = Services::session();
			$session->set('user_id', $user->id);
		}
		catch (Exception $e)
		{
			$response = Services::response();
			return $response->setBody($e->getMessage())->setStatusCode(401);
		}
	}

	/**
	 * Allows After filters to inspect and modify the response
	 * object as needed. This method does not allow any way
	 * to stop execution of other after filters, short of
	 * throwing an Exception or Error.
	 *
	 * @param RequestInterface  $request
	 * @param ResponseInterface $response
	 * @param array|null        $arguments
	 *
	 * @return mixed
	 */
	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
	{
		//
	}
}
