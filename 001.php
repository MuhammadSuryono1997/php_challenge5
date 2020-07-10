<?php 
/**
 * 
 */
require_once "simple_dom/simple_html_dom.php";
class ChallengePHP
{
	protected $api_key,$url_movie;
	function __construct()
	{
		$this->api_key = 'ba2e0ba26bf3f9d93dca9b2624bc46df';
		$this->url_movie = 'https://api.themoviedb.org/3/';
	}

	function get_headlines($url)
	{
		// $ch = curl_init($url);
		// $response = curl_exec($ch);
		// foreach ($response as $data) 
		// {
		// 	echo $data."\n";
		// }

		$html = file_get_html($url, false);
		$answer = array();
		if (!empty($html)) 
		{
			$divClass = $title = $i = 0;
			foreach ($html->find('header__title') as $desc) 
			{
				$text = html_entity_decode($desc->plaintext);
				$text = preg_replace('/\&#39;/', "", $text);
				$answer[$i]['header__title'] = html_entity_decode($text);
			}
		}
	}

	function get_movie_pop_indo()
	{
		$ch = file_get_contents($this->url_movie."discover/movie?api_key=".$this->api_key."&language=id-ID&region=ID&sort_by=popularity.asc&page=2&include_adult=true&include_video=false");
		$response = json_decode($ch,true)['results'];
		$urutan = 1;
		echo "=========== FILM INDONESIA ============\n";
		for ($i=0; $i < 10 ; $i++) 
		{ 
			echo $urutan++.". ".$response[$i]['title'].".\n";
		}
		return $response;
	}

	function get_movie_by_year($year,$vote)
	{
		$ch = file_get_contents($this->url_movie."discover/movie?api_key=".$this->api_key."&language=id-ID&region=ID&sort_by=popularity.asc&include_adult=true&include_video=false&page=1&primary_release_year=".$year."&vote_count.gte=".$vote);
		$response = json_decode($ch,true)['results'];
		$urutan = 1;
		echo "=========== DAFTAR FILM RELEASE IN ".$year." ============\n";
		for ($i=0; $i < count($response) ; $i++) 
		{ 
			echo $urutan++.". ".$response[$i]['title'].".\n";
		}
		return $response;
	}

	function get_movie_by_person($id_person)
	{
		$ch = file_get_contents($this->url_movie."person/".$id_person."/movie_credits?api_key=".$this->api_key."&language=id-ID");
		$response = json_decode($ch,true)['cast'];
		$urutan = 1;
		echo "=========== FILM BY PERSON ============\n";
		for ($i=0; $i < 15 ; $i++) 
		{ 
			echo $urutan++.". ".$response[$i]['title'].".\n";
		}

		return $response;
	}

	function get_movie_by_more_person($id1,$id2)
	{
		$data1 = $this->get_movie_by_person($id1);
		$data2 = $this->get_movie_by_person($id2);

		$data1 = array_map(function($v){return $v['title'];}, $data1);
		$data2 = array_map(function($v){return $v['title'];}, $data2);

		echo "============== FILM YANG DIMAINKAN OLEH TOM HOLLAND DAN ROBERT DOWNEY JR ====================\n";
		$urutan = 1;
		foreach ($data1 as $value) 
		{
			foreach ($data2 as $value2) 
			{
				if ($value == $value2) 
				{
					echo $urutan++.". ".$value."\n";
				}
			}
		}
	}
}

$challenge = new ChallengePHP();
// $challenge->get_headlines("https://regional.kompas.com/read/2018/03/29/07265661/cerita-sripun-dara-asal-semarang-yang-taklukkan-hati-david-beckham-1");
$challenge->get_movie_pop_indo();
$challenge->get_movie_by_person(6384);
$challenge->get_movie_by_more_person(3223,1136406);
$challenge->get_movie_by_year(2016,7.5);



 ?>