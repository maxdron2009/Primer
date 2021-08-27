<? 
class show_inf
{
  static public function Action()
  {
       $ID=APP::val_get("ID");
       $INF=APP::GetRecordTable("inf","inf_id",$ID);
		  
	   $tpl = APP::Template('shb/show_inf.php');
	   $tpl->SetValue("inf_name",$INF["inf_name"]);
	   $tpl->SetValue("inf_text",$INF["inf_text"]);
       $tpl->Show();  
   }
}
