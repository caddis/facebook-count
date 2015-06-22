<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array (
	'pi_name' => 'Facebook Count',
	'pi_version' => '1.1.1',
	'pi_author' => 'Caddis',
	'pi_author_url' => 'https://www.caddis.co',
	'pi_description' => 'Return Facebook page like or share count.',
	'pi_usage' => Facebook_count::usage()
);

class Facebook_count {

	private $graph_url = 'https://api.facebook.com/method/links.getStats?format=json&urls=';

	public function __construct()
	{
		$this->page = ee()->TMPL->fetch_param('page');

		if (! $this->page) {
			return 0;
		}
	}

	public function likes()
	{
		$likes = 0;

		$data = $this->getData();

		if (isset($data->like_count)) {
			$likes = number_format($data->like_count);
		}

		return $likes;
	}

	public function shares()
	{
		$shares = 0;

		$data = $this->getData();

		if (isset($data->share_count)) {
			$shares = number_format($data->share_count);
		}

		return $shares;
	}

	private function getData()
	{
		if (! preg_match("#https?://#i", $this->page)) {
			$this->page = 'https://www.facebook.com/' . $this->page;
		} 
		 
		$url = $this->graph_url . urlencode($this->page);
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$raw = curl_exec($ch);

		curl_close($ch);

		$response = json_decode($raw);

		if (is_array($response)) {
			return $response[0];
		}

		return array();
	}

	public static function usage()
	{
		ob_start();
?>
Parameters:

page = 'url' // page URL you wish to get the count for

Usage:

{exp:facebook_count:likes page="url"} outputs 4,234
{exp:facebook_count:shares page="url"} outputs 164
<?php
		$buffer = ob_get_contents();

		ob_end_clean();

		return $buffer;
	}
}
?>