<!-- Samuel Benison Jeyaraj Victor  -->
<!-- sambenison66@gmail.com  -->
<!-- http://omega.uta.edu/~xxx1234/project4/buy.php -->
<html>
<head><title>Buy Products</title></head>
<body>
<?php
// PHP Code starts here
session_start();

// Condition to be executed whenever the Empty Basket button is clicked
if (isset($_GET['clear']))
{ 
   //Reset all the session values
   unset($_SESSION["cart"]);
   unset($_SESSION["querydata"]);
   unset($_SESSION["shoppool"]);
   unset($_SESSION['total']);
   // Declare the Cart Array Session if unavailable
   if (!isset($_SESSION['cart']))
    { 
      $_SESSION['cart'] = array();
    }
}
// Declare the querydata Array Session if unavailable
// This is used to extract the query values and assign it to the shop pool
if(!isset($_SESSION['querydata']))
{
  $_SESSION['querydata'] = array();
}
// Declare the shoppool Array Session if unavailable
// This is used to store the Shopping Cart Product Details
if(!isset($_SESSION['shoppool']))
{
  $_SESSION['shoppool'] = array();
}
// Declare the Total Session if unavailable
// This is used to calculate the total value of the session
if(!isset($_SESSION['total']))
{
  $_SESSION['total'] = 0;
}

//session_start();
?>
<h2>Shopping Basket: (Ebay.com)</h2>
<table border=1>
<?php
// Execute the condition if a product is deleted from the cart
if(isset($_GET["delete"]))
{
  if(strlen($_GET["delete"])!=0)  // Checking the php returns valid product id for deletion
  {
    $product_id = $_GET["delete"];
    $delete_data = $_SESSION['shoppool'];
    $loop = count($_SESSION['shoppool']);
    for($i = 0; $i<$loop;$i++) {  // Check the Deleting Product Id from the Shoppool Session Array
      if($delete_data[$i][0] == $product_id) {  // When the match found
        $calcTotal = floatval($delete_data[$i][3]);
        $_SESSION['total'] = $_SESSION['total'] - $calcTotal;   // Adjust the total amount
        unset($_SESSION['shoppool'][$i][0]);    
        unset($_SESSION['shoppool'][$i][1]);    // Remove the Product from the Shopping Pool
        unset($_SESSION['shoppool'][$i][2]);
        unset($_SESSION['shoppool'][$i][3]);
        unset($_SESSION['shoppool'][$i][4]);
      }
    }
    $print_cart = $_SESSION['shoppool'];  // Display the updated shopping cart
    foreach($print_cart as $temp_shopcart)
    {
      if($temp_shopcart[0] != "") {    // Echo the product list into a table
        echo "<tr><td><a href ='".$temp_shopcart[4]."'><img src='".$temp_shopcart[1]."'></a></td>";
        echo "<td>".$temp_shopcart[2]."</td>";
        echo "<td>".$temp_shopcart[3]."</td>";
        echo "<td><a href='buy.php?delete=".$temp_shopcart[0]."'>Delete</a></td></tr>";
      }
    }
  }
  if(isset($_GET["delete"])) {   // Reset the Delete value once once the delete is done
    unset($_GET["delete"]);
  }
}
// Condition to be executed when a product is added to the shop cart
if(isset($_GET["buy"]))
{
  if(strlen($_GET["buy"])!=0)
  {
    //print_r($_SESSION['querydata']);
    if (!isset($_SESSION['cart']))
    { 
      $_SESSION['cart'] = array();
    }
    $product_id = $_GET["buy"];
    // Store the id in the cart in order to keep the product id in Session Array
    // Cart Session is the base for all buying and removing products
    $_SESSION['cart'][] = $product_id;
    $query_data = $_SESSION['querydata'];
    $loop = count($_SESSION['shoppool']);
    // Move the value from last query result to the shop pool array of session
    foreach($query_data as $temp_output)
    { 
        if($product_id == $temp_output[0])
        {
          $_SESSION['shoppool'][$loop][0] = $temp_output[0];
          $_SESSION['shoppool'][$loop][1] = $temp_output[1];
          $_SESSION['shoppool'][$loop][2] = $temp_output[2];
          $_SESSION['shoppool'][$loop][3] = $temp_output[3];
          $_SESSION['shoppool'][$loop][4] = $temp_output[4];
          $calcTotal = floatval($temp_output[3]);
          $_SESSION['total'] = $_SESSION['total'] + $calcTotal;
        }
    }
    // Print the updated Shop Cart and it's product details
    $print_cart = $_SESSION['shoppool'];
    foreach($print_cart as $temp_shopcart)
    {
      if($temp_shopcart[0] != "") {
      echo "<tr><td><a href ='".$temp_shopcart[4]."'><img src='".$temp_shopcart[1]."'></a></td>";
      echo "<td>".$temp_shopcart[2]."</td>";
      echo "<td>".$temp_shopcart[3]."</td>";
      echo "<td><a href='buy.php?delete=".$temp_shopcart[0]."'>Delete</a></td></tr>";
      }
    }
  }
}

?>
</table>
<p/>
<b>Total: <?php echo $_SESSION['total'] ?>$</b><p/>
<form action="buy.php" method="GET">
<input type="hidden" name="clear" value="1"/>
<input type="submit" value="Empty Basket"/>
</form>



<form action="buy.php" method="GET">
<fieldset><legend>Find products:</legend>
<label>Search for items: <input type="text" name="search"/><label>
<input type="submit" name="submit" value="Search"/>
</fieldset>
</form>

<?php

error_reporting(E_ALL);
ini_set('display_errors','off');

// Condition to be executed when a keyword is searched
if(isset($_GET["submit"])=='Search')
{

$name=urlencode($_GET["search"]);  // Encoding the keyword
// Sending the Keyword to Shopping.com and get the XML result set
$xmlstr = file_get_contents('http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/GeneralSearch?apiKey=YourKey&trackingId=YourId&keyword=' . $name);
$xml = new SimpleXMLElement($xmlstr);  // XML Result Set
header('Content-Type: text/html');
?>
<table border=1>";
  <th>Product Image</th>
  <th>Product Name</th>
  <th>Price</th>
<?php
$query[][] = "";
$count = 0;
// Manipulating the xml result and get the product details of the Search Result XML set
foreach ($xml->categories->category->items->product  as $entry)
{
 $imgURL = $entry->images->image->sourceURL;
 $prodid = $entry['id'];
 // Displaying the Search Result to the front end
 echo "<tr>";
 echo  "<td><a href= 'buy.php?buy=".$prodid."'><img src=".$imgURL."></img></a></td>";
 echo "<td>".$entry->name."</td>";
 echo "<td>"."$".$entry->minPrice."</td></tr>";
 // Storing the Search Result Details to the Session Array in order to further use at Shopping Cart
 $query[$count][0] = (String) $prodid;
 $query[$count][1] = (String) $imgURL;
 $query[$count][2] = (String) $entry->name;
 $query[$count][3] = (String) $entry->minPrice;
 $query[$count][4] = (String) $entry->productOffersURL;
 $count = $count + 1;
}
$_SESSION['querydata'] = $query;
}
?>
</table>
</body>
</html>