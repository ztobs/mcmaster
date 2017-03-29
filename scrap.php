<?php


include('functions.php');




								///////////////////////////////////////////////////////////////////////////////////////
								//////////////////////////////// MAIN OPERATIONS //////////////////////////////////////
								///////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////





//getCatList();


$query = "SELECT * FROM categories WHERE process = 'yes' AND child=''";
$rs = $conn->query($query);

while($row = $rs->fetch_assoc())
//while ($row = ['name'=>'Machine Keys', 'cat_code'=>'machine-keys'])
{
	$cat_name = $row['name'];		// eg. Screws & Bolts
	$cat_code = $row['cat_code'];

	$dom1 = getDomSelenium("https://www.mcmaster.com/#".$cat_code, chooseProxy());

	// If its a category page with link <Level 1>
	$innerCats = getInnerCat($dom1);
	hasAbout($dom1, $cat_code);
	if($innerCats)
	{
		foreach ($innerCats as $innerCat) {
			$innerCatName = $innerCat['name'];		// eg Standard Socket Head Screws

			$innerCatCode = str_replace("#", "", $innerCat['url']); 
			$cat_db_id0 = insertCat_($innerCatName, $innerCatCode, $cat_code, "yes");

			$dom1a = getDomSelenium("https://www.mcmaster.com/#".$innerCatCode, chooseProxy());
			hasAbout($dom1a, $innerCatCode); 

			// If its a category page without link <Level 2>
			$catNoLinks = getCatNoLink($dom1a, $innerCatCode);
			if($catNoLinks)
			{
				foreach ($catNoLinks as $catNoLink) {
					$catNoLinkId = $catNoLink['id'];
					$catNoLinkName = $catNoLink['name'];		// eg. Alloy Steel Socket Head Screws
					$catNoLinkCode = str_replace(" ", "-", $catNoLinkName);
					$catNoLinkParent = $catNoLink['parent'];
					$catNoLinkShortDesc = $catNoLink['short_desc'];
					$cat_db_id = insertCat_($catNoLinkName, $catNoLinkCode, $catNoLinkParent, "yes", null, $catNoLinkShortDesc );
					hasParentCatDesc($dom1a, $catNoLinkParent);
					
					//if(!isProcessed($catNoLinkName))
					if(true)
					{
						if($seleProxy) $dom1b = getCatN($innerCatCode, $catNoLinkId, chooseProxy());
						else $dom1b = getCatN($innerCatCode, $catNoLinkId);

						hasAbout($dom1b, $catNoLinkCode);

						// If its a product list page <level 3>
						$ProdListing = getProductListPage($dom1b);
						if($ProdListing)
						{
							$filters = getFilters($dom1b);
							foreach ($ProdListing as $prodd) 
							{
								$prodName = $prodd['name'];
								$prodDesc = $prodd['desc'];
								$prodImg = $prodd['img'];
								insertProduct($prodName, $prodDesc, $cat_db_id, $filters, $prodImg);
								hasParentCatDesc($dom1b, $catNoLinkCode);
							}
							

						}
					}
					else
					{
						echo "Skipped!!\n";
						break;
					}
					
					
				}
			}

			// If its a product list page <level 2>
			$ProdListing = getProductListPage($dom1a);
			if($ProdListing)
			{
				$filters = getFilters($dom1a);
				foreach ($ProdListing as $prodd)
				{
					$prodName = $prodd['name'];
					$prodDesc = $prodd['desc'];
					$prodImg = $prodd['img'];
					insertProduct($prodName, $prodDesc, $cat_db_id0, $filters, $prodImg);
					hasParentCatDesc($dom1a, $cat_code);
				}
				
			}
		}
	}

	// If its a category page without link <Level 1>
	$catNoLinks = getCatNoLink($dom1, $cat_code);
	if($catNoLinks)
	{
		foreach ($catNoLinks as $catNoLink) {
			$catNoLinkId = $catNoLink['id'];
			$catNoLinkName = $catNoLink['name'];
			$catNoLinkCode = str_replace(" ", "-", $catNoLinkName);
			$catNoLinkParent = $catNoLink['parent'];
			$catNoLinkShortDesc = $catNoLink['short_desc'];
			$cat_db_id = insertCat_($catNoLinkName, $catNoLinkCode, $catNoLinkParent, "yes", null, $catNoLinkShortDesc);
			hasParentCatDesc($dom1, $cat_code);

			if(!isProcessed($cat_name))
			{
				if($seleProxy) $dom2a = getCatN($cat_code, $catNoLinkId, chooseProxy());
				else  $dom2a = getCatN($cat_code, $catNoLinkId);

				hasAbout($dom2a, $catNoLinkName);

				// If its a product list page <level 3>
				$ProdListing = getProductListPage($dom2a);
				if($ProdListing)
				{
					$filters = getFilters($dom2a);
					foreach ($ProdListing as $prodd)
					{
						$prodName = $prodd['name'];
						$prodDesc = $prodd['desc'];
						$prodImg = $prodd['img'];
						insertProduct($prodName, $prodDesc, $cat_db_id, $filters, $prodImg);
						hasParentCatDesc($dom2a, $catNoLinkCode);
					}
				}
			}
			else
			{
				echo "skipped!!\n";
				break;
			}
			
			
		}
	}
}




