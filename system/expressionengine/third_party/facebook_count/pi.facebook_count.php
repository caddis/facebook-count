<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array (
	'pi_name' => 'Facebook Count',
	'pi_version' => '1.0',
	'pi_author' => 'Michael Leigeber',
	'pi_author_url' => 'http://www.caddis.co',
	'pi_description' => 'Return Facebook page like or share count.',
	'pi_usage' => Facebook_count::usage()
);

class Facebook_count {

	private $graph_url = 'http://graph.facebook.com/';

	public function __construct()
	{
		$this->EE =& get_instance();

		// Get target page
		$this->page = $this->EE->TMPL->fetch_param('page');

		if (! $this->page)
		{
			return 0;
		}
	}

	public function likes()
	{
		$likes = 0;

		$data = $this->getData();

		if (isset($data->likes))
		{
			$likes = number_format($data->likes);
		}

		return $likes;
    }

	public function shares()
	{
		$shares = 0;

		$data = $this->getData();

		if (isset($data->shares))
		{
			$shares = number_format($data->shares);
		}

		return $shares;
	}

	private function getData()
	{
		$url = $this->graph_url . urlencode($this->page);

		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		$raw = curl_exec($ch);

		curl_close($ch);

		return json_decode($raw);
	}

	public static function usage()
	{
		ob_start();
?>
Parameters:

page = 'url'		// page URL you wish to get the count for

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
