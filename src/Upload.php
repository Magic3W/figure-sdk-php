<?php namespace figure\sdk;

use spitfire\collection\Collection;

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
 * The upload represents a single file that a user uploaded. An upload contains
 * multiple files representing the different scaled versions that figure will
 * be hosting for this client.
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class Upload
{
	
	/**
	 * The raw data that the SDK received from Figure. This contains all the information
	 * that is needed for the application to generate output.
	 *
	 * @var mixed
	 */
	private $payload;
	
	/**
	 * Receives the data from the server. This can then be used to generate output
	 * that a user can understand.
	 * 
	 * @param mixed $payload
	 */
	public function __construct($payload)
	{
		$this->payload = $payload;
	}
	
	/**
	 * Retrieves a scaled version of the media. Please note that this will be the
	 * original format that the media was sent as (if the media was a video, the 
	 * media will be a video)
	 * 
	 * @param string $size
	 * @return Media
	 */
	public function media($size)
	{
		$media = $this->payload->media->{$size};
		return new Media($this->payload->type, $media->mime, $media->width, $media->height, $media->url);
	}
	
	/**
	 * Returns the poster versions of the media. Upon upload, the media is converted
	 * to webp and jpeg to generate an output that can be viewed without downloading
	 * a large video, and to provide a smaller image that can be easily transmitted
	 * to devices with lower network capabilities.
	 * 
	 * @param string $size
	 * @return Collection
	 */
	public function poster($size) : Collection
	{
		$_ret = new Collection;
		
		foreach ($this->payload->media->{$size}->poster as $poster) {
			#TODO: Introduce a class that wraps around the image and generates the 
			# appropriate renderer for it.
			$_ret->push(new Media('image', $poster->mime, $poster->width, $poster->height, $poster->url)); 
		}
		
		return $_ret;
	}
	
	/**
	 * Returns the type of the media. (image, animation or video)
	 * 
	 * @return string
	 */
	public function type() 
	{
		return $this->payload->type;
	}
}
