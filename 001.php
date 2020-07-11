<?php 
/**
 * 
 */
require_once "simple_dom/simple_html_dom.php";
require_once "dompdf/autoload.inc.php";

use Dompdf\Dompdf;

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

		$berita = new DOMDocument();

		libxml_use_internal_errors(TRUE);

		if(!empty($html)){

			$berita->loadHTML($html);
			libxml_clear_errors();
			
			$berita_xpath = new DOMXPath($berita);

			$berita_row = $berita_xpath->query('//h3[contains(@class, "article__title article__title--medium")]');

			if($berita_row->length > 0){
				foreach($berita_row as $row){
					echo "[TITLE]: ".$row->nodeValue . "\n";
					// echo "[URL]: ".$row->firstChild->attributes."\n";
					foreach ($row->firstChild->attributes as $op) 
					{
						if ($op->name == 'href') 
						{
							echo "[URL]: ".$op->value."\n";
						}
					}

				}
			}

		}
	}

	function get_movie_pop_indo()
	{
		$ch = file_get_contents($this->url_movie."discover/movie?api_key=".$this->api_key."&language=id-ID&region=ID&sort_by=popularity.asc&page=2&include_adult=true&include_video=false");
		$response = json_decode($ch,true)['results'];
		$urutan = 1;
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

		// for ($i=0; $i < count($data1); $i++) 
		// { 
			foreach ($data1 as $post) 
			{
				foreach ($data2 as $users) 
				{
					if ($post["userId"] == $users["id"]) 
					{
						$post['user'] = $users;
					}
				}
				
				array_push($new_data, $post);
			}
		// }
		$update = json_encode($new_data, JSON_PRETTY_PRINT);
		if(file_put_contents("data_gabungan.json", $update))
		{
			echo "Data berhasil diunggah!\n";
		}
	}

	function get_film_comingsoon($url)
	{
		$html = file_get_html("https://www.cgv.id/en/loader/home_movie_list");
		$html = $html->find('a');
		$link = [];
		echo "Sedang get data from url ....\n";
		for ($i=0; $i < count($html); $i++) 
		{ 
			$get = str_replace('\\','',$html[$i]->attr['href']);
			$get = str_replace('"','',$get);
			$new = "https://www.cgv.id".$get;
			array_push($link,$new);
		}
		// var_dump($link[0]);
		$kumpulan = [];
		$satu = array();
		echo "Sedang generate data to pdf..... \n";
		for($i=0; $i < count($link); $i++)
		{
			$awal = file_get_html($link[$i]);
			$kumpulan['title'] = $awal->find('div.movie-info-title', 0)->plaintext;
			$kumpulan['sinopsis'] = $awal->find('div.movie-synopsis', 0)->plaintext;
			$kumpulan['info'] = $awal->find('div.movie-add-info', 0)->plaintext;
			array_push($satu,$kumpulan);
		}

		
		echo "Sedang mengkonversi ke pdf....\n";
		$this->convert_pdf($satu);
	}

	public function convert_pdf($data)
	{
		$content = '<h3><b>REVIEW FILM COMINGSOON</b></h3><br><br>';
		foreach ($data as $value) 
		{
			$content .= $value['title']."<br>";
			$content .= "<br>";
			$content .= $value['info']."<br>";
			$content .= "<br>";
			$content .= "<b>Sinopsis</b><br>";
			$content .= $value['sinopsis']."<br>";
			$content .= "-----------------------------------------------------------------------------------------------------------------------------<br>";
			$content .= "<br>";
		}
		$dompdf = new Dompdf();
		$dompdf->load_html($content);
		$dompdf->setPaper('A4','portrait');
		$dompdf->render();
		// $dompdf->stream("film.pdf",0);
		$output = $dompdf->output();
		if(file_put_contents('film.pdf',$output))
		{
			echo "Data berhasil dikonversi ke PDF dengan nama file film.pdf";
		}
	}
}

$challenge = new ChallengePHP();
echo "==============================JUDUL BERITADARI KOMPAS.COM=================================\n";
echo "Sedang memuat..........\n";
$challenge->get_headlines("https://www.kompas.com/");
echo "=========== FILM INDONESIA ============\n";
echo "Sedang memuat..........\n";
$challenge->get_movie_pop_indo();
echo "Sedang memuat..........\n";
$challenge->get_movie_by_person(6384);
echo "Sedang memuat..........\n";
$challenge->get_movie_by_more_person(3223,1136406);
echo "Sedang memuat..........\n";
$challenge->get_movie_by_year(2016,7.5);
echo "Sedang mengunggah ke file json...........\n";
$challenge->get_data_gabungkan();
$challenge->get_film_comingsoon("https://www.cgv.id/en/movies/now_playing");



 ?>