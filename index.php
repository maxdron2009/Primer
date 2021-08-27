<? if ( !isset($_SESSION) ) session_start();

include_once("inc.php");

$Action="";
if ( isset($_GET["Action"]) )  $Action=trim($_GET["Action"]);

$ClassName="list_tovar";
$ActionIndex="Action";

switch($Action)
 {
	case 'ShowTovar':
		 $ClassName="show_tovar";
		 $ActionIndex="Action";
	     break;  


	case 'Info':
		 $ClassName="show_inf";
		 $ActionIndex="Action";
	     break; 
		 

	case 'Basket':
		 $ClassName="basket";
		 $ActionIndex="Action";
	     break;  

	case 'FormMail':
	
		 $tpl = APP::Template(NULL,'shb/form_mail_mag.php');
		 $tpl->SetValue("TEXT",1);
		 $tpl->SetValue("TITLE",1);
		 $tpl->SetValue("MAG_NAME","Название магазина");
		 $tpl->SetValue("MAG_ADRES","Тут идет адрес");
		 $tpl->Show();
		 return ;
		 
	case 'Exit':
	     unset($_SESSION["MUSER"]);
	     unset($_SESSION["CLIENT"]);
		 header('Location:?'); 
	     break;
 }
 
include $ClassName.".php";
APPLICATION::Index($ClassName,$ActionIndex);
?>
