<?php
	
	// Read the urlDetail from the POST request
	if(isset($_POST)){
		$urlDetail = $_POST;
	}
	
	// For testing/debugging
	if(isset($_GET['debug']) && $_GET['debug'] == 1){

		$testJson = [];
		$testJson["title"] = "BBC Nepali";
		$testJson["url"] = "https://www.bbc.com/nepali/";
		$testJson["urlBase"] = "https://www.bbc.com/";
		$testJson["skipKeywordCheck"] = "true";
		$testJson["category"] = "Page";
		$testJson["maxPosts"] = 20;
		$testJson["titleAttribute"] = "title";
		$urlDetail = $testJson;
	}
	
	global $array;
	global $keywords;
	
	$array = [];
	$keywords = file_get_contents("https://raw.githubusercontent.com/padamdahal/media-scanning-for-health-events/main/keywords.txt");        
	$keywords = explode(',', $keywords);
	
	init($urlDetail);
	
	function init($urlDetail){
		$skipKeywordCheck = $urlDetail['skipKeywordCheck'];
        
		$rssContents;
		if($urlDetail['category'] == 'Feed'){
			$rssContents = feedScanner($urlDetail);
		}else{
			$rssContents = pageScanner($urlDetail);
		}
		
		echo(json_encode($GLOBALS['array']));
	}
	
	function feedScanner($urlDetail){
		
        $rssContent = @simplexml_load_file($urlDetail['url']);
		if(false === $rssContent){
			$rssContent = false;
		}
		
		$keywords = $GLOBALS['keywords'];
        $items;
        if(isset($rssContent->channel->item)){
            $items = $rssContent->channel->item;
        }else if(isset($rssContent->entry)){
            $items = $rssContent->entry;
        }

        $itemArray = [];
        foreach ($items as $item) {
			$keywordFound = false;
            $namespaces = $item->getNamespaces(true);
            $title = (string)$item->title;
			$titleWords = explode(' ', $title);
			if($urlDetail['skipKeywordCheck'] == 'true'){
				$keywordFound == true;
			}else{
				
				foreach ($keywords as $keyword){
					$keyword = rtrim(ltrim($keyword));
					if(strpos($i->nodeValue, $keyword)){
						$keywordFound = true;
					}
						
					//if(in_array($keyword, $titleWords)){
					//	$keywordFound = true;
					//}
				}
			}
			
			if($keywordFound == true){
				$link = (string)$item->link;
				//if(substr($link,0,4) != 'http'){
				//	$link = $urlDetail['urlBase']+$link;
				//}  
				if(isset($item->description)){
					$description = (string)$item->description;
				}else if(isset($item->content)){
					$description = (string)$item->content;
				}
							
				$pubDate = (string)$item->pubDate;

				$tempItem = [];
				$tempItem["title"] = $title; 
				$tempItem["link"] = $link;
				$tempItem["description"] = $description;
				$tempItem["source"] = $urlDetail['title'];
				$tempItem["pubDate"] = date("Y-m-d", strtotime($pubDate));
				$tempItem['sourceType'] = $urlDetail['category'];
				
				array_push($GLOBALS['array'], $tempItem);
			}
        }
	}
	
	function pageScanner($urlDetail){
		$keywords = $GLOBALS['keywords'];
		$crawledLinks = [];
		
		$crawling = array();
		$options = array(
			'http' => array(
				'method' => "GET",
				'user-agent' => "gfgBot/0.1\n"
			)
		);
		
		$context = stream_context_create($options);
		$doc = new DomDocument();
		@$doc->loadHTML(file_get_contents($urlDetail['url'], false, $context));
		$links = $doc->getElementsByTagName('a');
		
		foreach ($links as $i){
			$keywordFound = false;
			$titleWords = explode(' ', $i->nodeValue);
			if(count($titleWords) >= 5){
				if($urlDetail['skipKeywordCheck'] == 'true'){
					$keywordFound = true;
				}else{
					foreach ($keywords as $keyword){
						$keyword = rtrim(ltrim($keyword));
						if(strpos($i->nodeValue, $keyword)){
							$keywordFound = true;
						}
						
						if(in_array($keyword, $titleWords)){
							//$keywordFound = true;
						}
					}
				}
			}
			
			if($keywordFound == true){
				$link = $i->getAttribute('href');
				
				if(substr($link,0,4) != 'http' && $urlDetail['urlBase'] != ""){
					$link = $urlDetail['urlBase']+$link;
				}
				
				if (ignoreLink($link)) continue;
				
				if (!in_array($link, $crawledLinks)){
					array_push($crawledLinks, $link);
					$crawling[] = $link;
					$tempItem = [];
					$tempItem["title"] = $i->nodeValue; 
					$tempItem["link"] = $link;
					$tempItem["description"] = $i->nodeValue;
					$tempItem["source"] = $urlDetail['title'];
					$tempItem["pubDate"] = date("Y-m-d");
					$tempItem['sourceType'] = $urlDetail['category'];
					array_push($GLOBALS['array'], $tempItem);
				}
			}
		}
	}
	
	function convertLink($site, $path){
		if (substr_compare($path, "//", 0, 2)==0)
			return parse_url($site)['scheme'].$path;
		elseif (substr_compare($path, "http://", 0, 7)==0 or
			substr_compare($path, "https://", 0, 8)==0 or
			substr_compare($path, "www.", 0, 4)==0)
			return $path;
		else
			return $site.'/'.$path;
	}

	function ignoreLink($url){
		return $url=="#" or substr($url, 0, 11) == "javascript:";
	}

	function insertIntoDatabase($link, $title, &$metaData, $depth){
		echo (
			"Inserting new record {URL= $link".
			", Title = '$title'".
			", Description = '".$metaData['description'].
			"', Keywords = ' ".$metaData['keywords'].
			"'}<br/><br/><br/>"
		);
		$crawledLinks[]=$link;
	}

	function getDocTitle(&$doc, $url){
		$titleNodes=$doc->getElementsByTagName('title');
		if (count($titleNodes)==0 or !isset($titleNodes[0]->nodeValue))
			return $url;
		$title=str_replace('', '\n', $titleNodes[0]->nodeValue);
		return (strlen($title)<1)?$url:$title;
	}

	function getDocMetaData(&$doc){
		$metaData=array();
		$metaNodes=$doc->getElementsByTagName('meta');
		foreach ($metaNodes as $node)
			$metaData[$node->getAttribute("name")]
					= $node->getAttribute("content");
		if (!isset($metaData['description']))
			$metaData['description']='No Description Available';
		if (!isset($metaData['keywords'])) $metaData['keywords']='';
		return array(
			'keywords'=>str_replace('', '\n', $metaData['keywords']),
			'description'=>str_replace('', '\n', $metaData['description'])
		);
	}
	
	function getHtml($newsTitle,$source,$link){
		$html = '<div class="block" style="width:400px;padding:10px;border:1px solid #aaa;margin:10px;background:#efefef"><h3>'.$newsTitle.'</h3>';
		$html .= '<a href="'.$link.'">Detail</a></div>';
		return $html;
	}
	
	


?>