<?php

ini_set('memory_limit', '2048M'); 
use Facebook\WebDriver\Remote\DesiredCapabilities as DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver as RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
require_once('vendor/autoload.php');

//error_reporting(E_ALL & ~E_NOTICE);
error_reporting(0);

include('lib/simple_html_dom.php');

//$input = array("mv1487192648", "Morpheus", "Trinity", "Cypher", "Tank");
//$rand_keys = array_rand($input, 2);

$session = "mv1490296384";

$host = "127.0.0.1";
$user = "root";
$pass = "";
$db = "mcmastercarr";
$conn = new mysqli($host, $user, $pass, $db);

if (!$conn->connect_errno) {
    echo "Database Connected\n";
}

$output_filename = "products.csv";
$proxy_file = "proxies.csv";
$urls2scrap = 'urls.csv';
$written_dom = 0;
$silent = false;


$timeouts = 50;
$proxyauth = "73290:s6yk2BpCu";
//$userauth = "";
$removeBadProxy = false;
$useProxy = true;
$seleProxy = false;
$solverCaptcha = false;


// start chrome with 5 second timeout
$host = 'http://localhost:4444/wd/hub'; // this is the default



function getDomSelenium($url, $proxy=null)
{
	global $host;

	$capabilities = DesiredCapabilities::chrome();
	//$capabilities->setCapability('acceptSslCerts', true);
	if($proxy)
	{
		$capabilities->setCapability('proxy', [
											    'proxyType' => 'manual',
											    'httpProxy' => $proxy,
											    'sslProxy' => $proxy
											  ]);
	}
	$driver = RemoteWebDriver::create($host, $capabilities, 1000 * 1000, 1000 * 1000);
	//$driver->get("https://www.mcmaster.com/");
	$driver->get($url);
	$page = $driver->getPageSource();
	$dom = str_get_html($page);
	$driver->close();
	return $dom;
}



////////////////////////////////////////////////////////////////////////////////


$file = fopen($proxy_file, 'r');
while (($line = fgetcsv($file)) !== FALSE) {
  //$line is an array of the csv elements
  $proxies[] = $line[0];
}
fclose($file);


/////////////////////////////////////////////////////////////////////////////


function getDom($url, $hello=null)
{
	global $useProxy;

	if($useProxy)
	{
		return getDom9($url, false); 
	}
	else
	{
		return getDom9($url, true);
	}
}

function getDom_($url, $hello=null)
{
	global $useProxy;

	if($useProxy)
	{
		return getDom9_($url, false);
	}
	else
	{
		return getDom9_($url, true);
	}
}


function getDom9($link, $noProxy=false, $type="curl")
 {
 	global $proxies;
 	global $solverCaptcha;
 	global $removeBadProxy;
 	$dom = null;
    
    	
    if($type=="curl")
    {
    	//while($dom[1]['content_type'] == null)
    	while($dom[0] == null)
    	{
    		if($noProxy==false) $proxy = chooseProxy();
    		else $proxy = null;
   			$dom = getDomCurl($link, $proxy);

   			if($dom[0] == null || strpos($dom[0], "cannot be displayed") != false || strpos($dom[0], 'roXm{+sWhit-ZI2i<Z5aOk$qnA;1hQUsfj&n)e2') != false || strpos($dom[0], "webmaster") != false )
   			{
   				echo "retrying...\r\n";
   				if(strpos($dom[0],"Robot Check")!=false)
   				{
   					echo "The proxy has been block\r\n";
   					//if($solverCaptcha == true) captchaSolver($dom[0], $proxy);
   				} 
   				$dom[0] = null;
   				if($removeBadProxy == true)$proxies = array_diff($proxies, array($proxy)); // removing proxy that didnt work
   			}
    	}
   		
   	}
   	elseif ($type=="file_get")
   	{
   		while($dom[0] == null)
   		{
   			if($noProxy==false) $proxy = chooseProxy();
    		else $proxy = null;
    		$context = setContext($proxy);
	   		$content = file_get_html($link, false, $context);
	   		$dom = array( $content, null ); // Making it look like curls output, make make it an array with null headers
	   		if($dom[0] == null || strpos($dom[0], "cannot be displayed") != false || strpos($dom[0], 'roXm{+sWhit-ZI2i<Z5aOk$qnA;1hQUsfj&n)e2') != false || strpos($dom[0], "webmaster") != false )
	   		{
   				echo "retrying...\r\n";
   				if(strpos($dom[0],"Robot Check")!=false)
   				{
   					echo "The proxy has been block\r\n";
   					//if($solverCaptcha == true) captchaSolver($dom[0], $proxy);
   				} 
   				$dom[0] = null;
   				if($removeBadProxy == true)$proxies = array_diff($proxies, array($proxy)); // removing proxy that didnt work
   			}
	   		
	   		
   		}
   	}
    return $dom[0];
 }

 function getDom9_($link, $noProxy=false, $type="curl")
 {
 	global $proxies;
 	global $solverCaptcha;
 	global $removeBadProxy;
 	global $session;
 	$dom = null;
    
    	
    if($type=="curl")
    {
    	//while($dom[1]['content_type'] == null)
    	while($dom[0] == null)
    	{
    		if($noProxy==false) $proxy = chooseProxy();
    		//if($noProxy==false) $proxy = "104.83.11.159:443";//chooseProxy();
    		else $proxy = null;
   			$dom = getDomCurl($link, $proxy);

   			 
   			if($dom[0] == null || strpos($dom[0], "ProdDatProtectionWebPart_Redirect.js") != false || strpos($dom[0], "cannot be displayed") != false || strpos($dom[0], 'roXm{+sWhit-ZI2i<Z5aOk$qnA;1hQUsfj&n)e2') != false || strpos($dom[0], "webmaster") != false )
   			{
   				//writeDom2File($dom[0], "sss");
   				echo "retrying...\r\n";
   				if(strpos($dom[0],"Robot Check")!=false)
   				{
   					echo "The proxy has been block\r\n";
   					//if($solverCaptcha == true) captchaSolver($dom[0], $proxy);
   				} 
   				$dom[0] = null;
   				//getDomCurl("https://www.mcmaster.com/mv1487192649/WebParts/LoginWebPart/LoginHTTPHandler.aspx", $proxy);
   				if($removeBadProxy == true)$proxies = array_diff($proxies, array($proxy)); // removing proxy that didnt work
   			}
   			
    	}
   		
   	}
   	return $dom[0];
}


function writeDom2File($dom, $output_filename)
{
	global $written_dom;
	$written_dom++;
	$myfile = fopen($output_filename."_".$written_dom.".html", "a") or die("Unable to open file!");
	fwrite($myfile, $dom."\r\n");
	fclose($myfile);
}

 function getDomCurl($link, $proxy)
 {
 	global $proxyauth;
 	global $userauth;
 	global $timeouts;
 	$pr = explode(":", $proxy);
 	$ch = curl_init();
 	$headers = array();
 	
 	$cookie = 'PAGEPREF=HTML;FAFSTRKPN=TRUE;volver=mv1487192648;stbver=mvB;rs=10;MAT=dVl2VU5NVmFRRFJLOUo5eklMMDdERzNsdDZiMQ==;sesnextrep=105081140418962;clntextrep=5488980759126947;vstrextrep=9021813910054964;contentver=1486118082;trkrvisit=7835505aef584357a263a7338efa2a22';

	$headers[] = 'Accept: */*';
	$headers[] = 'Connection: keep-alive';
	$headers[] = 'Host: www.mcmaster.com';
	$headers[] = 'Referer: https://www.mcmaster.com/';
	$headers[] = 'X-Requested-With: XMLHttpRequest';
	//$headers[] = 'cache-control: public';
	//$headers[] = 'x-forwarded-proto: https';
	//$headers[] = 'x-forwarded-port: 443';

	//$headers[] = 'Cookie: '.$cookie;

	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
 	curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36" );
	if($proxy) curl_setopt($ch, CURLOPT_PROXY, $proxy);
	curl_setopt($ch, CURLOPT_URL,$link);
	//curl_setopt( $ch, CURLOPT_COOKIEJAR, "cookies.txt" );
	//curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
	//curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	//curl_setopt($ch, CURLOPT_USERPWD, $userauth);
	//curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
	//curl_setopt($ch, CURLOPT_ENCODING,  ''); //gzip
	curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeouts );
    curl_setopt( $ch, CURLOPT_TIMEOUT, $timeouts );
    curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	//curl_setopt($ch, CURLOPT_FAILONERROR, true); 
	//curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
	curl_setopt($ch, CURLOPT_HEADER, 0);
	//curl_setopt($ch, CURLOPT_POST, 1);
	$content = curl_exec( $ch );
	$content = str_get_html($content);
    $response = curl_getinfo( $ch );
	curl_close($ch);

	if ($response['http_code'] == 301 || $response['http_code'] == 302) {
        ini_set("user_agent", "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");

        if ( $headers = get_headers($response['url']) ) {
            foreach( $headers as $value ) {
                if ( substr( strtolower($value), 0, 9 ) == "location:" )
                    return get_url( trim( substr( $value, 9, strlen($value) ) ) );
            }
        }
    }

    if (    ( preg_match("/>[[:space:]]+window\.location\.replace\('(.*)'\)/i", $content, $value) || preg_match("/>[[:space:]]+window\.location\=\"(.*)\"/i", $content, $value) ) && $javascript_loop < 5) {
        return get_url( $value[1], $javascript_loop+1 );
    } else {
        return array( $content, $response );
        //return $response;
    }
 }


 function getDomMink($url, $proxy)
 {
 	global $client;

 	$client->setClient(
	    new GuzzleClient(
	        [
	                'allow_redirects' => true,
	                'cookies'         => true,
	                'verify' => false,
	                'proxy' => $proxy
	        ]
	    )
	);

	$session = new Session(new GoutteDriver($client));

	// start the session
	$session->start();

	$session->visit($url);

	//$session->getStatusCode(); 

	$page = $session->getPage()->getContent();

	return $page;

 }





 function chooseProxy()
 {
 	global $proxy_file;
 	global $proxies;
 	if(count($proxies) < 1)
 	{
 		echo "All the proxies are dead, fetching new ones\r\n";
 		$proxies = fetchProxies();
 		var_dump($proxies);
 		$to_proxy_file = implode("\r\n", $proxies);
 		// writing new proxies to file
 		$myfile = fopen($proxy_file, "w") or die("Unable to open file! $proxy_file");
		fwrite($myfile, "\r\n".$to_proxy_file);
		fclose($myfile);
 	}
	$proxy = $proxies[array_rand($proxies)];
	echo "Using: $proxy\r\n";
	return $proxy;
 }


 function setContext($proxy=null, $username=null, $pass=null)
 {
 	$auth = base64_encode('$username:$pass');

	$aContext = array(
	    'http' => array(
	        'proxy' => $proxy,
	        'request_fulluri' => true,
	        'header' => "Proxy-Authorization: Basic $auth\r\n".
	        			"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8\r\n".
	        			"User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36"
	    )
	);
	$cxContext = stream_context_create($aContext);
	return $cxContext;
 }




function save_image($inPath,$outPath)
{ //Download images from remote server


    $in=    fopen($inPath, "rb");
    $out=   fopen($outPath, "wb");
    while ($chunk = fread($in,8192))
    {
        fwrite($out, $chunk, 8192);
    }
    fclose($in);
    fclose($out);
    return true;
}


function base64_to_jpeg($base64_string, $output_file) {
    $ifp = fopen($output_file, "wb"); 

    $data = explode(',', $base64_string);

    fwrite($ifp, base64_decode($data[1])); 
    fclose($ifp); 

    return $output_file; 
}

function fetchProxies()
{
	$dom = getDom("https://free-proxy-list.net/", true);
	
	foreach($dom->find('#proxylisttable > tbody > tr') as $e)
	{
			$ips[] = $e->childNodes(0)->innertext;
	}
	foreach($dom->find('#proxylisttable > tbody > tr') as $e)
	{
			$ports[] = $e->childNodes(1)->innertext;
	}

	for ($k=0; $k<count($ips); $k++)
	{
		$prox[] = $ips[$k].":".$ports[$k];
	}

	return array_slice($prox, 1, -1);;
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////





function catSearchById($id)
{
	global $session;
	$url = "https://www.mcmaster.com/".$session."/webparts/content/ProdPageWebPart/ProdPageWebPart.aspx?cntnridtxt=MainContent&srchidtxt=".$id."&cntnrwdth=1361&srchrsltdisplovrdind=false&specsrchhexnutsovrdind=false&landingpagesuppressedind=false&srchrslttxt=standard%20filtration%20strainers&expandedprsnttns=&viewporthgt=315&envrmgrcharsetind=2";

	$dom = getDom($url, false);

	return $dom;
}


function catSearchByString($query)
{
	global $session;
	$query = str_replace(" ", "%20", $query);
	$url = "https://www.mcmaster.com/".$session."/WebParts/Content/IntermediatePageWebPart/IntermediatePageWebPart.aspx?cntnrid=MainContent&pageid=".$query."&cntnrwdth=1360&envrmgrcharsetind=2";

	$dom = getDom($url, false);

	return $dom;
}


function getProdPage($part_number)
{
	global $session;
	$url = "https://www.mcmaster.com/".$session."/WebParts/Content/ItmPrsnttnWebPart.aspx?partnbrtxt=".$part_number."&attrcompitmids=&attrnm=&attrval=&cntnridtxt=MainContent&proddtllnkclickedInd=true&cntnrWdth=900&cntnrHght=258&printprsnttnInd=true&screenDensity=1&envrmgrcharsetind=2";

	$dom = getDom($url, false);

	return $dom;
}


///////////////////////////////////////////////////////////////////////////////////////
////////////////////////////  House Keeping  //////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////

function removeCarriage($string)
{
	$string = str_replace('\r', "", $string);
	$string = str_replace('\n', " ", $string);
	return $string;
}


///////////////////////////////////////////////////////////////////////////////////////
/////////////////////// Database Operations  //////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////

function existCat($cat_code)
{
	global $conn;
	$query = "SELECT * FROM categories WHERE cat_code='$cat_code'";
	$rs = $conn->query($query);
	if($rs->num_rows > 0)
	{
		$row = $rs->fetch_assoc();
		return $row['id'];
	}
}

function existFilter($value, $title)
{
	global $conn;
	$query = "SELECT * FROM filters WHERE value='$value' AND title='$title'";
	$rs = $conn->query($query);
	if($rs->num_rows > 0)
	{
		$row = $rs->fetch_assoc();
		return $row['id'];
	}
}

function existProd($name)
{
	global $conn;
	$query = "SELECT * FROM products WHERE name='$name'";
	$rs = $conn->query($query);
	if($rs->num_rows > 0)
	{
		$row = $rs->fetch_assoc();
		return $row['id'];
	}
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function insertCat($name, $code, $parent=null, $short_desc=null, $desc=null, $about=null)
{
	global $conn;
	global $silent;
	$query = "INSERT INTO categories (name, parent, cat_code, short_description, description, about) VALUES ('$name', '$parent', '$code', '$short_desc', '$desc', '$about' )";
	$conn->query($query);
	if($conn->affected_rows > 0)
		{
			if(!$silent) echo "Category ($name) inserted to db\n";
			return existCat($code);
		}
	else
	{
		return existCat($code);
	}
}

function updateCatAbout($code, $about)
{
	global $conn;
	global $silent;
	
	$query = "SELECT * FROM categories WHERE about LIKE '%$about%' AND cat_code = '$code' ";
	$rs = $conn->query($query);
	if($rs->num_rows > 0)
	{
		$row = $rs->fetch_assoc();
		$about_db = $row['$about'];
		if(strrpos($about_db, $about) !== false) // it its contains
		{
			$about = $about_db;
		}
		else
		{
			$about = $about_db." ".$about;
		}
	}
	$query = "UPDATE categories SET about = '$about' WHERE cat_code = '$code' ";
	$conn->query($query);
	if($conn->affected_rows > 0) return true;
	
}


function updateParentCatDesc($code, $description)
{
	global $conn;
	global $silent;
	
	$query = "SELECT * FROM categories WHERE description LIKE '%$description%' AND cat_code = '$code' ";
	$rs = $conn->query($query);
	if($rs->num_rows > 0)
	{
		$row = $rs->fetch_assoc();
		$description_db = $row['$description'];
		if(strrpos($about_db, $description) !== false) // it its contains
		{
			$description = $description_db;
		}
		else
		{
			$description = $description_db." ".$description;
		}
	}
	$query = "UPDATE categories SET description = '$description' WHERE cat_code = '$code' ";
	$conn->query($query);
	if($conn->affected_rows > 0) return true;
	
}


function insertFilter($value, $title)
{
	global $conn;
	global $silent;
	if(!existFilter($value, $title))
	{
		$query = "INSERT INTO filters (value, title) VALUES ('$value', '$title')";
		$conn->query($query);
		if($conn->affected_rows > 0) 
			{
				if(!$silent) echo "Filter ($value) inserted to db\n";
				//return true;
				return existFilter($value, $title);
			}
	}
	else
	{
		return existFilter($value, $title);
	}
	
}

/************/
function insertProduct($name, $description, $categories, $filters, $image)
{
	global $conn;
	global $silent;
	$name = str_replace("'", "", $name);
	$description = str_replace("'", "", $description);
	$query = "INSERT INTO products (name, description, categories, filters, image) VALUES ('$name', '$description', '$categories', '$filters', '$image' )";
	$conn->query($query);
	if($conn->affected_rows > 0) 
	{
		if(!$silent) echo "Product ($name) inserted to db\n";
		return true;
	}
	else
	{
		insertProdCat($name, $categories);
		insertProdFilter($name, $filters);
	}
}

function insertProdCat($prod_name, $cat_id)
{
	global $conn;
	global $silent;
	$query = "SELECT categories FROM products WHERE name='$prod_name'";
	$rs = $conn->query($query);
	if($rs->num_rows > 0)
	{
		$row = $rs->fetch_assoc();
		$categories_array_db = explode(",", $row['categories']);

		if(!in_array($cat_id, $categories_array_db))
		{
			$query = "UPDATE products SET categories = CONCAT(categories, ',$cat_id') WHERE name = '$prod_name'";
			$conn->query($query);
			if($conn->affected_rows > 0) 
				{
					if(!$silent) echo "Update! Product Category ($prod_name ==> $cat_id) inserted to db\n";
					return true;
				}
		}
	}
	
}


function insertProdFilter($prod_name, $filters_str)
{
	global $conn;
	global $silent;
	$filter_id_arr = explode(",", $filters_str);
	$query = "SELECT filters FROM products WHERE name='$prod_name'";
	$rs = $conn->query($query);
	if($rs->num_rows > 0)
	{
		$row = $rs->fetch_assoc();
		$filter_array_db = explode(",", $row['filters']);

		$filter_togo = array_unique(array_merge($filter_array_db, $filter_id_arr));
		
		$ff = implode(",", $filter_togo);
		$query = "UPDATE products SET filters = '$ff' WHERE name = '$prod_name'";
		$conn->query($query);
		if($conn->affected_rows > 0) 
		{
			if(!$silent) echo "Update! Product Filter ($prod_name ==> $filters_str) inserted to db\n";
			return true;
		}
		
		
	}
}


//////////////////////////////////////////////// Special /////////////////////////////////////////////////////////////////
function insertCat_($name, $code, $parent, $child=null, $process=null, $short_desc=null)  
{
	global $conn;
	global $silent;
	$query = "INSERT INTO categories (name, parent, cat_code, child, process, short_description ) SELECT '$name', id, '$code', '$child', '$process', '$short_desc' FROM categories WHERE cat_code='$parent'";
	$conn->query($query);
	if($conn->affected_rows > 0)
		{
			if(!$silent) echo "Category ($name) inserted to db **\n";
			return existCat($code);
		}
	else
	{
		return existCat($code);
	}
}



function isProcessed($name)
{
	global $conn;
	global $silent;
	$query = "SELECT * FROM products WHERE name = '$name'";
	$rs = $conn->query($query);
	if($rs->num_rows > 0) return true;
}



///////////////////////////////////////////////////////////////////////////////////////
/////////////////////// Traversing Operations  ///////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////

function getCatList()
{
	global $session;
	$url = "https://www.mcmaster.com/".$session."/VersionedWebParts/HomePageWebPart/HomePageWebPartHTTPHandler.aspx?cntnrIDtxt=MainContent&ver=572c881b6613b0fcc882e182c4";

	$dom = getDom($url, false);
	$counter = 0;
	foreach ($dom->find('div .catg') as $e) {
		if($counter > 0)
		{
			$superCat = trim($e->childNodes(0)->plaintext);
			$superCat_code = str_replace("catg ", "", $e->class)."\n"; 
			insertCat($superCat, $superCat_code);
			// decending to inner categories 
			foreach ($e->find('.subcat') as $e2) {
				$cat = trim($e2->childNodes(0)->plaintext);
				if($cat)
				{
					$cat_code = str_replace(" ", "-", $cat);
					$cat_code = preg_replace('/[^A-Za-z0-9\-]/', '', $cat_code);
					$parent = $superCat_code;
					insertCat_($cat, $cat_code, $parent, null, null);
				}
				
				// decending much more inner
				foreach ($e2->find('li a') as $dd) {
					if($cat) $parent2 = $cat_code;
					else $parent2 = $superCat_code;
					$sub_cat = trim($dd->plaintext);
					$subcat_code = str_replace("#", "", $dd->href);
					insertCat_($sub_cat, $subcat_code, $parent2, null, "yes");
					
					//$cats[] =  array("cat"=>$sub_cat, "cat_code"=>$subcat_code);
					$cats[] = $subcat_code;

				}
			}
		}
		$counter++;
	}

	return $cats;
}





function searchType1($id, $query)
{
	global $session;
	$query = str_replace(" ", "%20", $query);
	$url = "https://www.mcmaster.com/".$session."/webparts/content/ProdPageWebPart/ProdPageWebPart.aspx?cntnridtxt=MainContent&srchidtxt=".$id."&cntnrwdth=1361&srchrsltdisplovrdind=false&specsrchhexnutsovrdind=false&landingpagesuppressedind=false&srchrslttxt=screw&expandedprsnttns=&viewporthgt=315&envrmgrcharsetind=2";

	$dom = getDom($url, false);
	return $dom;
}


function getProdList($dom)
{
	foreach ($dom->find('.PartNbrLnk') as $e) {
		$prodnum[] = str_replace("/#", "", $e->href);
	}
	return $prodnum;
}


function processProd($id)
{
	global $session;
	echo $url = "https://www1.mcmaster.com/".$session."/WebParts/Content/ItmPrsnttnWebPart.aspx?partnbrtxt=".$id."&attrcompitmids=&attrnm=&attrval=&cntnridtxt=MainContent&proddtllnkclickedInd=true&cntnrWdth=900&cntnrHght=263&printprsnttnInd=true&screenDensity=1&envrmgrcharsetind=2";

	//echo $url = "https://www.mcmaster.com/#".$id;

	if ($seleProxy == true) $dom = getDomSelenium($url, chooseProxy());
	else $dom = getDomSelenium($url);
	
	return $dom;
}

function getAbbrIds($dom)
{
	foreach ($dom->find('.AbbrPrsnttn') as $e) {
		$dd = str_replace("Abbr_", "", $e->id);
		$abbr[] = str_replace("-", "", $dd);
	}
	return $abbr;
}


function getProdDet($dom, $cat)
{
	$pname = $dom->find('.PrsnttnHdrCntnr', 0)->plaintext;

	foreach ($dom->find('.spec-table--pd tr') as $e) {
		$filter_title = $e->childNodes(0)->plaintext;
		$filter_value = $e->childNodes(1)->plaintext;

		$filters[] = array($filter_title=>$filter_value);
	}

	$desc = "";
	foreach ($dom->find('.copy') as $e) {
		$desc .= $e->plaintext."<br>";
	}

	return array("name"=>$pname, "filters"=>$filters, "description"=>$desc);
}


function getInnerCat($dom)
{
	foreach ($dom->find('.IntermediatePrsnttn') as $e) {
		$url = $e->find('a.IntermediatePrsnttn_Anchor', 0)->href;
		$name = $e->find('div', 0)->plaintext;
		$cats[] = array('url'=>$url, 'name'=>$name);
	}
	return $cats;
}

function withAbbrNext($id, $abbr)
{
	global $session;
	$url = "https://www.mcmaster.com/".$session."/WebParts/Content/ContentWebPart/ContentWebPart.aspx?cntnrIDtxt=ProdPageContent&srchidtxt=".$id."&cntnrwdth=1111&srchrslttxt=Thermal%20Insulation&PrsnttnUsrInps=[{%22PrsnttnId%22:%22".$abbr."%22}]";

	$dom = getDomSeleniumPre($url);
	return $dom;
}

function getSearchId($query)
{
	global $session;
	$query = str_replace(" ", "%20", $query);
	//$url = "https://www.mcmaster.com/".$session."/WebParts/SrchRsltWebPart/WebSrchEng.aspx?inpArgTxt=".$query;
	$url = "https://www.mcmaster.com/".$session."/WebParts/SrchRsltWebPart/WebSrchEng.aspx?inpArgTxt=".$query."&abtests=Search-NNPF~~2";

	$dom = getDom_($url, false);
	$data = json_decode($dom, true);
	
	return $data[0]['WebSrchEngMetaDat']['FastTrackSrchRsltId'];
}

function getCatNoLink($dom, $parent)
{
	foreach ($dom->find('.GroupPrsnttn') as $e_) {
		$grp_cat_name = $e_->find('.PrsnttnHdrCntnr ',0)->plaintext;
		$grp_cat_code = str_replace(" ", "-", $grp_cat_name);
		insertCat_($grp_cat_name, $grp_cat_code, $parent, 'grpcat');
		foreach ($e_->find('.AbbrPrsnttn') as $e) {
			$id = $e->id;
			$name = trim($e->find('.PrsnttnHdrCntnr', 0)->plaintext);
			$short_desc = $e->find('.PrsnttnCpy', 0)->plaintext;
			$nolinkcat[] = array('id'=>$id, 'name'=>$name, 'short_desc'=>$short_desc, 'parent'=>$grp_cat_code);
		}
	}
		
	return $nolinkcat;
}




function getCatNoLinkDom($id, $parent)
{
	$psid = getSearchId($parent);
	$dom = withAbbrNext($id, $psid);
	return $dom;
}


function getCatN($parent, $id, $proxy=null)
{
	global $host;
	
	$capabilities = DesiredCapabilities::chrome();
	//$capabilities->setCapability('acceptSslCerts', true);
	if($proxy)
	{
		$capabilities->setCapability('proxy', [
											    'proxyType' => 'manual',
											    'httpProxy' => $proxy,
											    'sslProxy' => $proxy
											  ]);
	}
	
											        
	$driver = RemoteWebDriver::create($host, $capabilities, 1000 * 1000, 1000 * 1000);
	$driver->get("https://www.mcmaster.com/#".$parent);
	$driver->findElement(WebDriverBy::id($id))->click();
	sleep(7);
	$page = $driver->getPageSource();
	$dom = str_get_html($page);
	$driver->close();
	return $dom;
}

function getProductListPage($dom)
{
	foreach ($dom->find('.FullPrsnttn') as $e) {
		$prod_name = trim($e->find('.PrsnttnNm',0)->plaintext);
		$prod_desc = $e->find('.CpyCntnr',0)->outertext;
		foreach ($e->find('.ImgCaptionCntnrHover img') as $im) {
			$w_im[] = $im->src;
		}
		if($w_im) $prod_img = implode(";", $w_im);
		$prod[] = array("name"=>$prod_name, "desc"=>$prod_desc, "img"=>$prod_img);
	}

	return $prod;
}

function getFilters($dom)
{
	foreach ($dom->find('.SpecSrch_Attribute') as $e) {
		$title = trim($e->find('.SpecSrch_AttrLabel',0)->plaintext);
		foreach ($e->find('.SpecSrch_Txt') as $e2) {
			$value = trim($e2->plaintext);
			$filter_id = insertFilter($value, $title);
			$filters[] = $filter_id;
		}
	}

	return implode(",", $filters);
}


function getProdParts($dom)
{
	try {
		$name = $dom->find('.PrsnttnHdrCntnr', 0)->plaintext;
		$price = $dom->find('.PrceTxt', 0)->plaintext;
		foreach ($dom->find('.NormalAttrRow') as $e) {
			$f_title = $e->childNodes(0)->plaintext;
			$f_value = $e->childNodes(1)->plaintext;
			$f_id = insertFilter($f_value, $f_title);
			$filters[] = $f_id;
		}
		$desc = $dom->find('.CpyCntnr', 0)->plaintext;
		$img = $dom->find('.ImgCaptionCntnr', 0);
		if($img) $image = $img->childNodes(0)->src;

		$prodparts = array('name'=>$name, 'price'=>$price, 'filters'=>$filters, 'description'=>$desc, 'image'=>$image);
		return $prodparts;
	} catch (Exception $e) {
		echo "An error was caught on childnode \n";
	}
	
}


function hasAbout($dom, $cat_code)
{
	global $session;
	foreach ($dom->find('.AboutBox') as $e) {
		$about_code = str_replace("Abbr_", "", $e->id);
		$url = "https://www.mcmaster.com/".$session."/WebParts/Content/PrsnttnWebPart/PrsnttnWebPart.aspx?srchid=0&prsnttnid=".$about_code."&scrnwdth=1129&displtyp=full&listind=false&cpyind=true";
		$dom = explode("}", getDom9($url, false));
		$about .= $dom[1];
	}
	
	if($about) updateCatAbout($cat_code, $about);
	
}

function hasParentCatDesc($dom, $cat_code)
{
	global $session;
	$desc = $dom->find('.FullPrsnttn .CpyCntnr',0)->outertext;
	if($desc) updateParentCatDesc($cat_code, $desc);
}