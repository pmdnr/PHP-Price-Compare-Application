<?php
//Licence-This Code only for Learning Purposs. Do not use this code in Real Applications, I am Not responsible for anything.
?>
<!DOCTYPE html>
<html>
<title>Price Compare Site</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://www.w3schools.com/lib/w3.css">
<body>

<header class="w3-container w3-teal">
  <h1>Price Compare Site</h1>
</header>
<div class="w3-container">

<form class="w3-container w3-card-4" action="main.php" method="get">
 <p>
 <input class="w3-input" name="searchdata" type="text" required>
 <label class="w3-label w3-validate">Search Product</label></p>
 <p>
 <input id="search" class="w3-input" type="submit" value="Search">
</form>
<div class="w3-row-padding w3-margin-top">

<?php
//Get web page data by using URL ie web page link
error_reporting(E_ALL & ~E_NOTICE);
if(isset($_GET['searchdata']))
{
//if user enter searchdata then only show this
//pass searchdata to below link as search
$search = $_GET['searchdata'];
$search = strtolower($search);
//space to plus replace
$search = str_replace(" ","+",$search);
  $web_page_data = file_get_contents("http://www.pricetree.com/search.aspx?q=".$search);
  //we need particular data from page not entire page. echo $web_page_data;

  $item_list = explode('<div class="items-wrap">', $web_page_data); //from entire page it will split based on word <div class="items-wrap">
  //$item_list is arrat so print_r
  //print_r($item_list);
  $i=1;
  if(sizeof($item_list)<2){
    echo '<p><b>No results, enter proper product name Ex: Moto G</b></p>';
    $i=5;
  }
//variable to check no data
$count = 4;
  //avoid array[0] and loop for 4 items-wrap items and print them
  for($i;$i<5;$i++){

    //echo $item_list[$i]; //this is array saperated based on split string <div class="items-wrap">
    //I want title and another information
    //it is printing on 4 items
    //for those items i want item image url and item link
    //from list item split based on href=" and then " because we want url between them

    $url_link1 = explode('href="',$item_list[$i]);
    $url_link2 = explode('"', $url_link1[1]); //$url_link1[0] will be before http=" data
    //echo $url_link2[0]."</br>"; //split by " and before that

    //now image link, same as above but split with data-original="

    $image_link1 = explode('data-original="',$item_list[$i]);
    $image_link2 = explode('"', $image_link1[1]); //$image_link1[0] will be before data-original=" data
    //echo $image_link2[0]."</br>"; //split by " and before that

    //I want title and only avaliable
    //getting title split between title=" and "
    $title1 = explode('title="', $item_list[$i]);
    $title2 = explode('"', $title1[1]);

    //get only avaliable items
    //split between avail-stores"> and </div>
    $avaliavle1 = explode('avail-stores">', $item_list[$i]);
    $avaliable = explode('</div>', $avaliavle1[1]);
    if(strcmp($avaliable[0],"Not available") == 0) {
      //means not avaliable
      $count = $count-1;
      continue;
      //goto next item in for loop
    }

    $item_title = $title2[0];
    if(strlen($item_title)<2){
      continue;
    }
    $item_link = $url_link2[0];
    $item_image_link = $image_link2[0];
    $item_id1 = explode("-", $item_link);
    $item_id = end($item_id1); //split with "-" and print last one after split that is id
    //show image and product title
    echo '
    <br>
    <div class="w3-row">
    <div class="w3-col l2 w3-row-padding">
    <div class="w3-card-2" style="background-color:teal;color:white;">
    <img src="'.$item_image_link.'" style="width:100%">
    <div class="w3-container">
    <h5>'.$item_title.'</h5>
    </div>
    </div>
    </div>
  ';


    //echo ."</br>";
    //echo $item_link."</br>";
    //echo $item_image_link."</br>";
    //echo $item_id."</br>";

    //goto pricetree access api to get price list
    //price list will be accessable based on $item_id

    $request = "http://www.pricetree.com/dev/api.ashx?pricetreeId=".$item_id."&apikey=7770AD31-382F-4D32-8C36-3743C0271699";
    $response = file_get_contents($request);
    $results = json_decode($response, TRUE);
    //print_r($results);
    //echo "-------------------------";
    //echo $results['count'];
    //table need to be open before for each
    //3 parts image and 9 parts table in a web page width
    echo '
    <div class="w3-col l8">
    <div class="w3-card-2">
      <table class="w3-table w3-striped w3-bordered w3-card-4">
      <thead>
      <tr class="w3-blue">
        <th>Seller_Name</th>
        <th>Price</th>
        <th>Buy Here</th>
      </tr>
      </thead>
    ';
    foreach ($results['data'] as $itemdata) {
      $seller = $itemdata['Seller_Name'];
      $price = $itemdata['Best_Price'];
      $product_link = $itemdata['Uri'];
      //echo $seller.",".$price.",".$product_link."</br>";
  echo '

      <tr>
        <td>'.$seller.'</td>
        <td>'.$price.'</td>
        <td><a href="'.$product_link.'">Buy</a></td>
      </tr>

      ';
    }
    //close table after for each
    echo '
      </table>
      </div>
      </div>
      </div>
    ';
  }
  if($count == 0){
    echo '<p><b>No Products avaliable, Enter Proper Product Ex: Moto G</b></p>';
  }
}
else {
  echo '<p>Use this to get Best Price from all Sites. <b>Search Product to Know Price from All Online Shops</b></p>';
}
?>

</div>
</div>
</div>

<footer class="w3-container w3-teal w3-opacity">
<p>Copyright @ Me</p>
</footer>
</body>
</html>
