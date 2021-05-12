<?php namespace figure\sdk;

use BadMethodCallException;
use magic3w\http\url\reflection\URLReflection;
use magic3w\phpauth\sdk\SSO;
use magic3w\phpauth\sdk\Token;
use spitfire\io\request\Request;

/* 
 * The MIT License
 *
 * Copyright 2021 César de la Cal Bretschneider <cesar@magic3w.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class Client
{
	
	/**
	 * The url where the PHPAuth Server is residing and handling requests.
	 * 
	 * @var string
	 */
	private $endpoint;
	
	/**
	 *
	 * @var Token
	 */
	private $token;
	
	/**
	 * 
	 * @param string $endpoint
	 * @param SSO|Token $token
	 */
	public function __construct(string $endpoint, $token) 
	{
		$reflection = URLReflection::fromURL($endpoint);
		$this->endpoint = (string)($reflection->stripCredentials());
		
		if ($token instanceof Token) {
			$this->token = $token;
		}
		
		elseif ($token instanceof SSO) {
			$appid = $reflection->getUser();
			$this->token = $token->credentials($appid);
		}
		
		else {
			throw new BadMethodCallException('Figure client cannot be invoked without a valid token or SSO instance', 2102021403);
		}
	}
	
	/**
	 * Get the base URL for figure. This allows the application to generate the 
	 * URLs that the client SDK requires to function.
	 * 
	 * @return string
	 */
	public function url() : string
	{
		return rtrim($this->endpoint, '\/');
	}
	
	/**
	 * When the user creates an upload, it's left in an unclaimed state. This means
	 * that the upload will expire after thirty days if no client claimed it.
	 * 
	 * Up to this point, the resource can be claimed by any client that has received 
	 * the secret from the resource owner. Once a client claimed it, the upload is
	 * transferred from the resource owner (the user) to the client (the application
	 * receiving the upload) and the expiration is revoked.
	 * 
	 * @param int $id
	 * @param string $secret
	 * @return bool
	 */
	public function claim(int $id, string $secret) : bool
	{
		$request = new Request(sprintf('%s/upload/claim/%s/%s.json', $this->endpoint, $id, $secret));
		$request->get('token', (string)$this->token->getId());
		$request->send();
		
		return true;
	}
	
	/**
	 * Deletes an upload. Once the client has claimed the upload, it is maintained
	 * forever by figure, unless the client explicitly requests deletion of the
	 * resource.
	 * 
	 * @param int $id
	 * @return bool Will be false on failure.
	 */
	public function delete(int $id) : bool
	{
		$request = new Request(sprintf('%s/upload/delete/%s.json', $this->endpoint, $id));
		$request->get('token', (string)$this->token->getId());
		$request->send();
		
		return true;
	}
	
	/**
	 * Retrieves the information for an upload, this contains the locations of the
	 * different scaled versions that figure hosts for this client.
	 * 
	 * @param int $id
	 * @return Upload
	 */
	public function retrieve(int $id) : Upload
	{
		$request = new Request(sprintf('%s/upload/retrieve/%s.json', $this->endpoint, $id));
		$request->get('token', (string)$this->token->getId());
		$response = $request->send()->expect(200)->json();
		
		return new Upload($response->payload);
	}
	
}
