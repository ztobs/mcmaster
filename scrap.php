<?php


include('functions.php');



function processProdQueue($ProdListUrl, $cat_db_id)
{
	
}


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
	$cat_name = $row['name'];
	$cat_code = $row['cat_code'];

	$dom1 = getDomSelenium("https://www.mcmaster.com/#".$cat_code, chooseProxy());

	// If its a category page with link <Level 1>
	$innerCats = getInnerCat($dom1);
	if($innerCats)
	{
		foreach ($innerCats as $innerCat) {
			$innerCatName = $innerCat['name'];
			$innerCatCode = str_replace("#", "", $innerCat['url']); 
			$cat_db_id0 = insertCat_($innerCatName, $innerCatCode, $cat_code, "yes");

			$dom1a = getDomSelenium("https://www.mcmaster.com/#".$innerCatCode, chooseProxy());
			

			// If its a category page without link <Level 2>
			$catNoLinks = getCatNoLink($dom1a, $innerCatCode);
			if($catNoLinks)
			{
				foreach ($catNoLinks as $catNoLink) {
					$catNoLinkId = $catNoLink['id'];
					$catNoLinkName = $catNoLink['name'];
					$catNoLinkParent = $catNoLink['parent'];
					$cat_db_id = insertCat_($catNoLinkName, str_replace(" ", "-", $catNoLinkName), $catNoLinkParent, "yes");
					
					$dom1b = getCatN($innerCatCode, $catNoLinkId);

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
						}
						

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
			$catNoLinkParent = $catNoLink['parent'];
			$cat_db_id = insertCat_($catNoLinkName, str_replace(" ", "-", $catNoLinkName), $catNoLinkParent, "yes");
			
			$dom2a = getCatN($cat_code, $catNoLinkId);

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
				}
			}
			
		}
	}
}




