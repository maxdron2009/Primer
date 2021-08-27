<? 
class basket
{
  static public function Action()
  {
	   $tpl = APP::Template(NULL,'shb/basket.php');
	   $PAGE=APPLICATION::DrawBasket();
	   $tpl->SetValue("jk334455",$PAGE["PAGE"]);
       $tpl->Show();  
   }
}
