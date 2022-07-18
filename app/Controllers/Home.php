<?php

/**
 * Root controller file
 */

namespace App\Controllers;

use App\Models\UserMeta;
use CodeIgniter\API\ResponseTrait;
use Config\Services;
use Exception;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Root Controller
 *
 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since   1.0.0
 * @version 1.0.0
 */
class Home extends BaseController
{

	use ResponseTrait;

	/**
	 * Site home
	 *
	 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return mixed
	 */
	public function index()
	{
		return view('home');
	}

	/**
	 * Scrappy api root
	 *
	 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return mixed
	 */
	public function scrape()
	{
		$response = [];

		$rules = [
			'url'    => 'required|valid_url_strict[https,http]',
			'css'    => 'required_without[xpath]',
			'xpath'  => 'required_without[css]',
			'format' => 'if_exist|in_list[text,html]',
		];

		if ($this->validate($rules))
		{
			$client = Services::curlrequest();

			$url = $this->request->getPostGet('url');

			try
			{
				$timer = Services::timer();
				$timer->start('scrappy');

				$headers = [
					'Accept'         => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
					'Accept-Charset' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
					'Cache-Control'  => 'max-age=0',
				];

				$html = $client->get($url, [
					'headers'    => $headers,
					'user_agent' => $this->request->getUserAgent() ?: 'Scrappy/1.0',
					'verify'     => false,
					'timeout'    => MINUTE,
				]);

				$crawler = new Crawler($html->getBody(), $url);

				$format = $this->request->getPostGet('format') ?: 'text';

				//Selector
				$selector = $this->request->getPostGet('css');
				if (is_null($selector))
				{
					$crawler = $crawler->filterXPath($this->request->getPostGet('xpath'));
				}
				else
				{
					$crawler = $crawler->filter($selector);
				}

				//Extract
				$response['data'] = $crawler->first()->{$format}();

				$timer->stop('scrappy');

				$user = current_user_id();
				$meta = get_user_meta('timer', $user) ?: 0;
				update_user_meta($user, 'timer', $timer->getElapsedTime('scrappy') + $meta->value);

				return $this->respond($response);
			}
			catch (Exception $e)
			{
				return $this->fail($e->getMessage());
			}
		}
		else
		{
			$response = $this->validator->getErrors();

			return $this->failValidationErrors($response);
		}
	}

	/**
	 * Create a token to access the api
	 *
	 * @author  Richard Muvirimi <rich4rdmuvirimi@gmail.com>
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return mixed
	 */
	public function auth()
	{
		helper('user');
		$rules = [
			'email' => 'required|valid_email',
		];

		if ($this->validate($rules))
		{
			$email = $this->request->getPostGet('email');

			$user = update_user($email, ['email' => $email, 'password' => $email]);
			$user = get_user($user);

			$encrypter     = \Config\Services::encrypter();
			$token         = $encrypter->encrypt(sprintf('%s:%s', $user->email, $user->password));
			$token         = base64_encode($token);
			$documentation = base_url('documentation');

			$message['token']   = $token;
			$message['message'] = 'Token generated';
		}

		return $this->respondCreated(compact('message'));
	}
}
