<?

class APP
{
  static $SID="";
  static $DB=NULL;
  static $TestID=0;
  static $LastTemplate;

  static public function val($ddd)  {	return doubleval($ddd); }
  static public function is_get($Name)  {	return isset($_GET[$Name]);  }
  static public function is_post($Name)  {	return isset($_POST[$Name]);  }
  static public function is_session($Name)  {	return isset($_SESSION[$Name]);  }
  static public function _String($Value) { return addslashes($Value); }
  
  static public function string_post($NameVar) {   return addslashes($_POST[$NameVar]);	 }
  

  static public function random_file()
   {
     $s = md5(microtime().time());
     $ss=preg_replace("[^1-9]","",$s );  
     return substr($ss,1,8);
   }
  
  
  static public function JSON_DATA($Value)
   {
      $DATA=APP::string_post($Value);
      $DATA=APP::CNVJSon($DATA);	
      $DATA=json_decode($DATA);
      return $DATA;
   }
  
  static public function GetDateFromTime($Value)
   {
      return date("d.m.Y G:i",$Value);
   }
  
  
  static public function CNVJSon($StringJSon)   {    return stripslashes($StringJSon);	   }
  static public function GetProtectedCode($hmin=1000, $hmax=9999)    {     return rand($hmin, $hmax);   }
  
  static public function GSA($HTTP,$Var,$Value) {   return APP::GetStrAdres($HTTP,$Var,$Value); }
  
  static public function SystemUser()
   {
      if ( isset($_SESSION["USER"])  && $_SESSION["USER"]["user_status"]==0 ) return 1;
  	  else  return 0;
   }
   
  static public function fGetDateMySql() {  return date("Y-m-d"); }
  static public function fGetTime() {   return date("H:i:s");  }

   
  
  static public function SaveLogs($LogsOper,$SysNom,$logs_rem,$logs_base,$GetUch=0)
   {
    $date=APP::fGetDateMySql();
	$time=APP::fGetTime();  	
	$logs_rem=addslashes($logs_rem);
	$logs_base=$logs_base;
	$logs_ip=APP::GetIP();
	
    APP::AppendSQL("insert into logs (logs_date,logs_timelogs_snom,logs_oper,logs_rem,logs_base,logs_ip) 
	                   values ('$date','$time',$SysNom,$LogsOper,'$logs_rem','$logs_base','$logs_ip') ");
}
  
  static public function SaveLogs_($logs_rem)
   {
     $date=APP::fGetDateMySql();
  	 $time=APP::fGetTime();  	
     $VrachID=0;	
	 $logs_rem=addslashes($logs_rem);
	 $logs_base='';
 	 $logs_ip=APP::GetIP();
	 $SysNom=0;
	 $LogsOper=0;
	
     APP::AppendSQL("insert into logs (logs_date,logs_time,vrach_id,logs_snom,logs_oper,logs_rem,logs_base,logs_ip) 
	                   values ('$date','$time',$VrachID,$SysNom,$LogsOper,'$logs_rem','$logs_base','$logs_ip') ");
  }
  

  static public function GetStrAdres($HTTP,$Var,$Value)
   {
     $Var=trim($Var);
     $lenvar=strlen($Var);
     $found_var=0;
     $str="";
     while( list($p,$s)=each($HTTP) )
      {
    	 if ( $found_var==0 && $lenvar > 0 && $p==$Var )
	        {
		      $s=$Value; 
		      $found_var=1;
  		    }
        if ( strlen($str)>0 ) $str=$str."&";
        $str=$str.$p."=".$s;
       }  
     if ( $found_var==0 && $lenvar > 0 )
        {
	      if  ( strlen($str)>0 )  $str=$str."&".$Var."=".$Value;
   	      else $str=$Var."=".$Value;
     	}
     return $str;
   }  
  
  
  static public function GetCode()
   {
     $PRT_KOD=APP::GetProtectedCode();
     $_SESSION["PRT_KOD"]=$PRT_KOD;
		
     ob_start();
     echo'<img src="showkode.php?i='.time().'" class="prtkod">';
     $Page=ob_get_contents();
     ob_end_clean();
	  
     return $Page;
  }
  
  
  static public function GetIP()
   {
     return $_SERVER['REMOTE_ADDR'];
   }
  

  static public function val_get($Name,$Array=NULL)  
   {	
     if ( is_null($Array) )  return APP::val($_GET[$Name]);  
     else return APP::val($Array[$Name]);  
   }
   
  static public function string_get($NameVar,$Array=NULL)
    {   
      if ( is_null($Array) )  return addslashes($_GET[$NameVar]);	 
      else return addslashes($Array[$NameVar]);	 
    }

  static public function val_post($Name)  
    {	
       $t=$_POST[$Name];
       $t=str_replace(",",'.',$t);
       return APP::val($t); 
    }
	
  static public function val_session($Name)  {	return APP::val($_SESSION[$Name]);  }
  
  static public function HBQ($Get,$Dop="")
   {
     return http_build_query($Get).$Dop;
   }
  

  static public function GetRecordTable($TableName,$KeyField,$SysNom,$String=false)
    {
      $d=APP::DBC();
      if ( !$String )
         {
            $SysNom=(double) $SysNom;
            $d->ExecuteSQL('select * from '.$TableName.' where '.$KeyField.'='.$SysNom.$DopWhere);
         }
      else
         {
            $SysNom="'".$SysNom."'";
            $d->ExecuteSQL('select * from '.$TableName.' where '.$KeyField.'='.$SysNom.$DopWhere);
     	 }
      if ( $d->Count<1 ) return  array();
      $Field=$d->GetRow();  
      return $Field;
   }
  
  

  static public function TemplateAjax($NameFile,$Memo="")
   {
	  self::$LastTemplate = new TemplateAjax(self::$LastTemplate,$NameFile,$Memo); 
	  return self::$LastTemplate;
   }
  
  
  static public function Template($NameFile,$Memo="")
   {
	  self::$LastTemplate = new TemplateCool(self::$LastTemplate,$NameFile,$Memo); 
	  return self::$LastTemplate;
   }
  
  
  static public function DBC()
   {
	  if (self::$DB==NULL) self::$DB=new DBC(); 	 
	  return self::$DB;
   }

  static public function AppendSQL($SQL)
   {
	  if (self::$DB==NULL) self::$DB=new DBC(); 	 
      return self::$DB->AppendSQL($SQL);
   }

  static public function UpdateSQL($SQL)
   {
	  if (self::$DB==NULL) self::$DB=new DBC(); 	 
      self::$DB->UpdateSQL($SQL);
   }

  static public function ExecuteSQL($SQL)
   {
	  if (self::$DB==NULL) self::$DB=new DBC(); 	 
      self::$DB->ExecuteSQL($SQL);
	  return self::$DB;
   }

  static public function GetFullDataTovar($ID)
   {
	  if (self::$DB==NULL) self::$DB=new DBC(); 	 
      self::$DB->ExecuteSQL("select *,(select ts_name from ts where ts.ts_id=tovar.ts_id) as ts_name   
               from tovar where tovar_id=$ID");
      if ( self::$DB->Count==0 ) return array();
	  $DATA=self::$DB->GetRow();
	  return $DATA;
   }
   
   

  static public function CheckTovar($ID)
   {
	 $d=APP::DBC();  
     $d->ExecuteSQL("select * from tovar where kat_id=$ID");   	
     if ($d->Count<1 ) return false;
	 return true;
     return $Page;	
   }

  static public function isLevelEnd($ID)
   {
	 $d=APP::DBC();  
     $d->ExecuteSQL("select * from kat where kat_tip=$ID");   	
     if ($d->Count<1 ) return false;
	 return true;
     return $Page;	
   }
   
   
  static public function Test($ID)
   {
     self::$TestID=$ID;
   }

  static public function GetTest()
   {
     return self::$TestID;
   }
   
  static public function InitSession()
    {
  	  if ( !isset($_SESSION) ) session_start();	
	  self::$SID=session_id();
    }
   
   

  static public function ClearSession()
    {
      $_SESSION = array();
	  $d=APP::DBC();	
	  $d->DeleteSQL("delete from ses where ses_ses='".self::$SID."'");
    }
   
   
  static public function DeleteSession($Name)
    {
  	  if ( isset($_SESSION[$Name]) ) unset($_SESSION[$Name]);
	  $d=APP::DBC();	
	  $d->DeleteSQL("delete from ses where ses_ses='".self::$SID."' and ses_name='".trim($Name)."'");
    }
  
   
  static public function IsSession($Name)
    {
	  $d=APP::DBC();	
	  $d->ExecuteSQL("select * from ses where ses_ses='".self::$SID."' and ses_name='".trim($Name)."'");
	  if ( $d->Count>0 ) return true;
	  else return false;
    }

  static public function GetSession($Name,$FieldName="")
    {
  	  if ( isset($_SESSION) ) 
	     {
		    if ( empty($FieldName) ) return $_SESSION[$Name];
		    else  return $_SESSION[$Name][$FieldName];	 
		 }
	  $d=APP::DBC();	
	  $d->ExecuteSQL("select * from ses where ses_ses='".self::$SID."' and ses_name='".trim($Name)."'");
	  if ( $d->Count>0 ) 
	     {
		   $DATA=$d->GetRow();
		   if ( $DATA["ses_type"]==0 )return $DATA["ses_value_str"];
		   else if ( $DATA["ses_type"]==2 )return $DATA["ses_value_float"];
		   else if ( $DATA["ses_type"]==1 )return $DATA["ses_value_int"];
		   else if ( $DATA["ses_type"]==3 )
		           {
					  if ( empty($FieldName) )	return  unserialize($DATA["ses_value_data"]);
					  $DATA=unserialize($DATA["ses_value_data"]);
					  return $DATA[$FieldName];
				   }
		   else 
		       {
			     echo 'Error GetSession: type not found<BR>';
				 die();
			   }
		 }
	  else 
	      {
		    echo "Error GetSession: session '".$Name."' not found<BR>";
		    die();
		  }
    }
  
	
  static public function SetSession($Name,$Value)
    {
       $ses_name=$ses_value_str=$ses_ses=$ses_value_data="";
   	   $ses_value_int=$ses_value_float=$ses_type=0;		
	 
   	   $ses_name=$Name;
   	   if ( is_string($Value) )
	      {
            $ses_type=0;				 
	        $ses_value_str=$Value;
	      }
	   else if (  is_float($Value) )
	           {
                  $ses_type=2;				 
		          $ses_value_float=$Value;
			   }
	   else if ( is_integer($Value) )	 
	           {
                  $ses_type=1;				 
		          $ses_value_int=$Value;
			   }
	   else if ( is_array($Value) )	 
	           {
                  $ses_type=3;				 
		          $ses_value_data=serialize($Value);
			   }
       else {
		      echo 'error SetSession Type Not Found';
		      return ;
		    }
	  $ses_ses=self::$SID;		
	  
	  $d=APP::DBC();	
	  $d->AppendSQL("insert into ses ( ses_name,
	                                   ses_ses,
									   ses_value_str,
									   ses_value_int,
									   ses_value_float,
									   ses_value_data,
									   ses_type) 
	                         values  ( '$ses_name',
							           '$ses_ses',
									   '$ses_value_str',
									   $ses_value_int,
									   $ses_value_float,
									   '$ses_value_data',
									   $ses_type) ");
	  $_SESSION[$ses_name]=$Value;							 
      return;
    }
	
  static public function DelSpace($_Text)
    {
      return str_replace(" ",'',$_Text);
    }

	static public function Date($dint)
	{
		return date('d.m.y',$dint);
	}

	static public function TestDir($p)
     {
       if (  !is_dir ($p)  )
	      {
             if  ( !mkdir($p) )              // Main papka
                 {  
                   $ErrorMess="do not create dir ".$p;
                   echo $ErrorMess."<BR>";
			       return  false;
                 }
          }		 
       return true;		
    }
	
}


