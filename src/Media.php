<?php namespace figure\sdk;

use Exception;

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
 * Media is a single file that was scaled from the original user upload. These
 * can be served by the client as image or video tags.
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class Media
{
	
	private $type;
	private $mime;
	private $width;
	private $height;
	private $url;
	
	public function __construct($type, $mime, $width, $height, $url) 
	{
		$this->type = $type;
		$this->mime = $mime;
		$this->width = $width;
		$this->height = $height;
		$this->url = $url;
	}
	
	/**
	 * The type of media is determined by the server when a file is uploaded. It provides
	 * different data than the mime type, because it's sometimes more specific than the 
	 * mime and sometimes way less specific.
	 * 
	 * For example, png, jpg, etc are all treated as image, while an mp4 file may contain
	 * an animation (silent and looping) or a video (with sound, no autoplay, no loop).
	 * 
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}
	
	public function getMime() {
		return $this->mime;
	}
	
	public function getWidth() {
		return $this->width;
	}
	
	public function getHeight() {
		return $this->height;
	}
	
	public function getUrl() {
		return $this->url;
	}
	
	/**
	 * Exports the current object to JSON. This mechanism makes it easy for applications
	 * using figure to cache the result of an upload.
	 * 
	 * @return string
	 */
	public function toJSON() 
	{
		return json_encode([
			'version' => 1,
			'type' => $this->type,
			'mime' => $this->mime,
			'width' => $this->width,
			'height' => $this->height,
			'url' => $this->url
		]);
	}
	
	/**
	 * Create an instance of a Media object from the JSON representation generated
	 * by toJSON(). 
	 */
	public static function fromJSON($string) {
		$data = json_decode($string);
		
		if ($data->version !== 1) {
			throw new Exception('Version missmatch when loading figure media from JSON');
		}
		
		return new self($data->type, $data->mime, $data->width, $data->height, $data->url);
	}
}
