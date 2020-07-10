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
		$html = file_get_contents($url);

		$pokemon_doc = new DOMDocument();

		libxml_use_internal_errors(TRUE);

		if(!empty($html)){

			$pokemon_doc->loadHTML($html);
			libxml_clear_errors();
			
			$pokemon_xpath = new DOMXPath($pokemon_doc);
			
			$pokemon_row = $pokemon_xpath->query('//h1[@class]');

			if($pokemon_row->length > 0){
				foreach($pokemon_row as $row){
					echo $row->nodeValue . "\n";
				}
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

	function get_data_gabungkan()
	{
		$data1 = file_get_contents("https://jsonplaceholder.typicode.com/posts");
		$data1 = json_decode($data1, true);
		$data2 = file_get_contents("https://jsonplaceholder.typicode.com/users");
		$data2 = json_decode($data2, true);
		$new_data = array();

		for ($i=0; $i < count($data1); $i++) 
		{ 
			foreach ($data1 as $post) 
			{
				foreach ($data2 as $users) 
				{
					if ($post["userId"] == $users["id"]) 
					{
						$post['user'] = $users;
					}
				}
				// print_r($post);
			}
			array_push($new_data, $post);
		}
		$update = json_encode($new_data, JSON_PRETTY_PRINT);
		file_put_contents("data_gabungan.json", $update);
	}
}

$challenge = new ChallengePHP();
echo "==============================JUDUL BERITADARI KOMPAS.COM=================================\n";
$challenge->get_headlines("https://regional.kompas.com/read/2018/03/29/07265661/cerita-sripun-dara-asal-semarang-yang-taklukkan-hati-david-beckham-1");
$challenge->get_headlines("https://nasional.kompas.com/read/2018/03/29/08514041/aplikator-sepakat-tingkatkan-pendapatan-ojek-online-pengemudi-ngotot-di");
$challenge->get_headlines("https://lifestyle.kompas.com/read/2018/03/29/063700020/penampilan-modis-istri-kim-jong-un-saat-berkunjung-ke-china");
$challenge->get_headlines("https://internasional.kompas.com/read/2018/03/29/10534231/rusia-tantang-balik-inggris-untuk-buktikan-tak-terlibat-racuni-skripal");
$challenge->get_movie_pop_indo();
$challenge->get_movie_by_person(6384);
$challenge->get_movie_by_more_person(3223,1136406);
$challenge->get_movie_by_year(2016,7.5);
$challenge->get_data_gabungkan();



 ?>