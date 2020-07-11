<?php
$curl = curl_init('http://www.flipkart.com/apple-iphone-5s/p/itmdv6f75dyxhmt4?pid=MOBDPPZZDX8WSPAT');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
// curl_setopt($curl,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

$page = curl_exec($curl);

if(!empty($curl)) { //if any html is actually returned

    $pokemon_doc = new DOMDocument;
    libxml_use_internal_errors(true);
    $pokemon_doc->loadHTML($page);
    libxml_clear_errors();

    $pokemon_xpath = new DOMXPath($pokemon_doc);
    // echo "TEs";

    $price = $pokemon_xpath->evaluate('string(//div[@class="prices"]/meta[@itemprop="price"]/@content)');
    echo $price;

    $rupees = $pokemon_xpath->evaluate('string(//div[@class="prices"]/div/span)');
    echo $rupees;
}
else {
    print "Not found";
}
?>