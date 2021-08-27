<? 

class APPLICATION
{
  static $NAST=NULL;	
  static $PHONES=array();
  static $EMAIL="";
  
  static public function Index($Class,$ActionIndex)
  {
    $Action=trim($_GET["Action"]);	  
    $FIND_TEXT=trim($_GET["FIND_TEXT"]);	  
	
	if ( $Action=='ShowTovar' )
	   {
         $ID=APP::val_get("ID");
         $TOVAR=APP::GetFullDataTovar($ID);		   
		 $KatID=$TOVAR["kat_id"];
	   }
	else  $KatID=APP::val_get('KatID');
	
	$LOAD_CATALOG=APPLICATION::LoadCatalog($KatID);

    $LIST_TOP_MENU=APPLICATION::LoadINF();
	if ( $KatID==0 ) $KATALOG_LIST='<div class="katalog_in">'.$LOAD_CATALOG["LIST_HTML"].'</div>';  
	else $KATALOG_LIST='<div class="katalog_in nocenter">'.$LOAD_CATALOG["LIST_HTML"].'</div>';  
	
	APPLICATION::LoadNast();

    $youtube=$vk=$insta="";	
	$FoundSOC=0;
	
	if ( strlen(trim(self::$NAST["nast_youtube"]))>0)  
	   { 
	      $youtube='<a href="'.self::$NAST["nast_youtube"].'" class="youtube" target="_blank"></a>'; 
	      $FoundSOC=1; 
       }	 
	   
	if ( strlen(trim(self::$NAST["nast_vk"]))>0) 
	   { 
	     $vk='<a href="'.self::$NAST["nast_vk"].'" class="vk" target="_blank"></a>';	 
		 $FoundSOC=1; 
	   } 
	   
	if ( strlen(trim(self::$NAST["nast_insta"]))>0)  
	   { 
	     $insta='<a href="'.self::$NAST["nast_insta"].'" class="insta" target="_blank"></a>';	
		 $FoundSOC=1; 
	   }
                                   
	
	$Phones='';
	for($i=0;$i<count(self::$PHONES);$i++)
 	   $Phones.='<div>'.self::$PHONES[$i].'</div>';	
	   
 	$Phones.='<div>'.self::$PHONES[$i].'</div>';	

    $tpl = APP::Template('shb/main.php');
    $tpl->SetValue("PHONES",$Phones); 	
    $tpl->SetValue("MAIL",self::$EMAIL); 	
    $tpl->SetValue("SOC",$FoundSOC); 	
    $tpl->SetValue("youtube",$youtube); 	
    $tpl->SetValue("vk",$vk); 	
    $tpl->SetValue("insta",$insta); 	
    $tpl->SetValue("BASKET_SUMMA",APPLICATION::CalcSummaBasket()." т."); 	
    $tpl->SetValue("LIST_TOP_MENU",$LIST_TOP_MENU); 	
    $tpl->SetValue("MOB_MENU",$LOAD_CATALOG["MENU"]); 	
    $tpl->SetValue("KROSHKA",$LOAD_CATALOG["KROSHKA"]); 	
    $tpl->SetValue("FIND_TEXT",$FIND_TEXT); 	
    $tpl->SetValue("KATALOG_LIST",$KATALOG_LIST); 	
    $tpl->start_html("SCREEN"); 	
	$Class::$ActionIndex();
    $tpl->end_html(); 	
    $tpl->Show(); 	  
  }
  
  static public function KRSHKA($KatID)
   {
     if ($KatID==0) return "";

     $LEVELS=array();  
     $d=APP::DBC();
     $KAT=APP::GetRecordTable("kat","kat_id",$KatID);
     $ParentID=$KAT["kat_tip"];
     $LEVELS[]=$KAT;
     while($ParentID)
       {
         $KAT=APP::GetRecordTable("kat","kat_id",$ParentID);
         $LEVELS[]=$KAT;
         $ParentID=$KAT["kat_tip"];
       }
  
      $HTML_TOP_NAVI='<div class="kroshki"><a href="?" class="a">КАТАЛОГ</a>';
      for($i=count($LEVELS)-1;$i>=0;$i--)
       {
         $HTML_TOP_NAVI.=' / ';
         if ( $i==0 ) $HTML_TOP_NAVI.='<span>'.$LEVELS[$i]["kat_name"].'</span>';  
         else $HTML_TOP_NAVI.='<a href="'.$HREF_A.'&KategID='.$LEVELS[$i]["kat_id"].'" class="a">'.$LEVELS[$i]["kat_name"].'</a>';
       }
      $HTML_TOP_NAVI.='</div>';	  
      return $HTML_TOP_NAVI;
    }
  


  static public function LoadCatalog($KatID)
    {
	   $LIST="";
       $d=APP::ExecuteSQL("select * from kat where kat_tip=0");
       for($i=0;$i<$d->Count;$i++)
	    {
		   $DATA=$d->GetRow();    
		   $LIST.='<a href="?Action=Catalog&KatID='.$DATA["kat_id"].'">'.$DATA["kat_name"].'</a>';
	    }
	   
	   if ( $KatID==0 )
	      {
			 $LIST_HTML=$LIST;
			 $HTML_TOP_NAVI='<span>КАТАЛОГ</span>';
		  }
	  else
		 {
		   $HTML_TOP_NAVI=APPLICATION::KRSHKA($KatID);	 
           $LIST_HTML.='<div class="mobil_kat_top">
                   <div class="div_cen">
                        <a href="" class="back_button" onclick="history.go(-1);return false;"></a>                   
                   </div>
                   <div class="div_cen">
                        <div class="kroshki">'.$HTML_TOP_NAVI.'</div>
                  </div> 
              </div>';  			  
		  }
	   return array("LIST_HTML"=>$LIST_HTML,"KROSHKA"=>$HTML_TOP_NAVI,"MENU"=>$LIST);
	}
  
  
  static public function ShowCellTovar($DATA)
  {
    $NameDir=FILES."/tphoto/";
    $photo=$NameDir.$DATA["photo_main_small"];
	
	$HREF_SELECT="?Action=ShowTovar&ID=".$DATA["tovar_id"];

  if ( $DATA["proc_skidki"]>0 ) 
     {
	   $old_cena='<div class="old_cena">'.$DATA["tovar_cena"].' т.</div>';	
	   $r=round($DATA["tovar_cena"]*$DATA["proc_skidki"]/100,2); 
	   $tovar_cena=$DATA["tovar_cena"]-$r;
	   
  	   $akcia='<div class="d sk">'.$DATA["ts_name"].' '.$DATA["proc_skidki"].'% </div>';
	   $show_ts=true;
	 }
  else
     {
	   $old_cena='';	
	   $tovar_cena=$DATA["tovar_cena"];
	 }

   $tovar_len=APP::val($DATA["tovar_len"]);
   $tovar_height=APP::val($DATA["tovar_height"]);
   $tovar_width=APP::val($DATA["tovar_width"]);
   
   if ( $tovar_len!=0 )  $tovar_len='<div><span class="har1">Длина</span>   <span class="har2">'.$tovar_len.'</span></div>';
   else $tovar_len='';

   if ( $tovar_height!=0 )  $tovar_height='<div><span class="har1">Высота</span>   <span class="har2">'.$tovar_height.'</span></div>';
   else $tovar_height='';

   if ( $tovar_width!=0 )  $tovar_width='<div><span class="har1">Ширина</span>   <span class="har2">'.$tovar_width.'</span></div>';
   else $tovar_width='';
	  
    echo'<div class="tovar">
                         <div class="img"><a href="'.$HREF_SELECT.'"><img src="'.$photo.'"></a></div>
                         <div class="title"><a href="'.$HREF_SELECT.'">'.$DATA["tovar_name"].'</a></div>  
                         <div class="art">артикул '.$DATA["tovar_art"].'</div>
                         <div class="har">
                              '.$tovar_len.'
                              '.$tovar_width.'
                              '.$tovar_height.'
                         </div>
                         <div class="cena">
                              '.$tovar_cena.' Т.
                         </div>
         </div>';
	  
  }
  
  static public function LoadINF()
    {
	   $LIST="";
	   $d=APP::ExecuteSQL("select * from inf");
	   for($i=0;$i<$d->Count;$i++)
	   {
		 $DATA=$d->GetRow();    
		 $LIST.='<a href="?Action=Info&ID='.$DATA["inf_id"].'" class="Upper">'.$DATA["inf_name"].'</a>';
	   }
	   return $LIST;
	}


  static public function LoadStartTovar()
    {
	   $LIST="";
	   $d=APP::ExecuteSQL("select * from tovar order by rand() limit 50");
	   for($i=0;$i<$d->Count;$i++)
	   {
		 $DATA=$d->GetRow();    
		 $LIST.='<a href="?Action=Catalog&KatID='.$DATA["kat_id"].'">'.$DATA["kat_name"].'</a>';
	   }
	   return $LIST;
	}

  static public function LoadNast()
    {
       if (is_null(self::$NAST)) self::$NAST=APP::GetRecordTable("nast","nast_id",1);
	   self::$PHONES=explode(',',self::$NAST["nast_phones"]);
	   self::$EMAIL=self::$NAST["nast_mail"];
	   return $LIST;
	}


  static public function CalcBasket()
    {
       if (is_null(self::$NAST)) self::$NAST=APP::GetRecordTable("nast","nast_id",1);
	   self::$PHONES=explode(',',self::$NAST["nast_phones"]);
	   self::$EMAIL=self::$NAST["nast_mail"];
	   return $LIST;
	}
  
  static public function IsCheckBasket($TovarID)
   {
      if  ( isset($_SESSION["BASKET"]["TOVAR_".$TovarID]) )	return true;
      else return false;
   }
   
  static public function DrawBasket()
   { 
  	 
      $tpl = APP::TemplateAjax('jk334455');

      $TMPMAG=array();
      $TOVARS=array();
      $TOVARS_ID='';
  
      if ( isset($_SESSION["BASKET"]) )
         {
           $BASKET=$_SESSION["BASKET"];
           foreach ($BASKET as $key=>$value)
             {
               if ($key=="COUNT") continue;
               $TOVARS[]=$BASKET[$key];
             }
    	 }
  
      if ( count($TOVARS)<1 ) 
         {
	        $tpl->start_loop("CELL");	 
	        echo '<div class="ft_row"><div class="flx_div"><BR>Корзина пуста<BR><BR></div></div>';
	        $tpl->end_loop();
	        $PAGE=$tpl->Show(false);
	        return array("PAGE"=>$PAGE,"BASKET"=>0);
	     }

      $tpl->start_loop("CELL");

      $SummaAll=0;
      $Summa=0;
      ob_start();	
      for($i=0;$i<count($TOVARS);$i++)
        { 
           $TOVAR=$TOVARS[$i];
		
           if ( strlen($TOVARS_ID)<1 ) $TOVARS_ID=$TOVAR["tovar_id"].':'.$TOVAR["kol"];
           else $TOVARS_ID.=';'.$TOVAR["tovar_id"].':'.$TOVAR["kol"];
	 
           $Width=$Height=$Len="";
	 
           if ( $TOVAR["tovar_width"]>0 ) $Width='<div class="hrd"><div class="hh1">Ширина:</div> <div class="hh2">'.$TOVAR["tovar_width"].'</div></div>';    
           if ( $TOVAR["tovar_height"]>0 ) $Height='<div class="hrd"><div class="hh1">Высота:</div> <div class="hh2">'.$TOVAR["tovar_height"].'</div></div>';           if ( $TOVAR["tovar_len"]>0 ) $Len='<div class="hrd"><div class="hh1">Длина:</div> <div class="hh2">'.$TOVAR["tovar_len"].'</div></div>';        
	 
           $NameDir=FILES."/tphoto/";
           $photo=$NameDir.$TOVAR["photo_main_small"];

           if ( $TOVAR["proc_skidki"]>0 ) 
              {
                $old_cena='<div class="old_cena">'.$TOVAR["tovar_cena"].' т.</div>';	
                $r=round($TOVAR["tovar_cena"]*$TOVAR["proc_skidki"]/100,2); 
                $tovar_cena=$TOVAR["tovar_cena"]-$r;
	   
                $akcia='<div class="d sk">'.$TOVAR["ts_name"].' '.$TOVAR["proc_skidki"].'% </div>';
                $show_ts=true;
  	         }
          else
             {
	           $old_cena='';	
               $tovar_cena=$TOVAR["tovar_cena"];
             }
		   
          $cena=round($tovar_cena*$TOVAR["kol"],2);
          $Summa+=$cena;
          $SummaAll+=$cena;

          $Loop=$tpl->Loop();
          $Loop->SetValue("tovar_name",$TOVAR["tovar_name"]);
          $Loop->SetValue("tovar_art",$TOVAR["tovar_art"]);
	 
          $Loop->SetValue("Width",$Width);
          $Loop->SetValue("Height",$Height);
          $Loop->SetValue("Len",$Len);
	 
          $Loop->SetValue("tovar_cena",$tovar_cena);
          $Loop->SetValue("old_cena",$old_cena);
          $Loop->SetValue("photo",$photo);
          $Loop->SetValue("tovar_id",$TOVAR["tovar_id"]);
          $Loop->SetValue("kol",$TOVAR["kol"]);
          $Loop->SetValue("summa",$cena);
	 
          $Loop->Show();  
       }
      $PageBasket=ob_get_contents();
      ob_end_clean();  
      echo $PageBasket;		
      echo '<div class="tovar_bas"><div class="itog">ИТОГО: '.$SummaAll.' тенге</div></div>';
      echo '<div class="zakaz_btn"><button onclick="SERVIS.SendForma();">Сформировать и отправить заявку</button></div>';
      $tpl->end_loop(); 
      $PAGE=$tpl->Show(false);
      return array("PAGE"=>$PAGE,"BASKET"=>1,"TOVARS_ID"=>$TOVARS_ID);
    }   
	
  static public function CalcSummaBasket()
    {
       if (!isset($_SESSION["BASKET"])) return 0;	
       $BASKET=$_SESSION["BASKET"];
       $Summa=0;
       foreach ($BASKET as $key=>$value)
         {
	        if ($key=="COUNT") continue;
	        $TOVAR=$BASKET[$key];

	        if ( $TOVAR["proc_skidki"]>0 ) 
               {
	             $r=round($TOVAR["tovar_cena"]*$TOVAR["proc_skidki"]/100,2); 
	             $tovar_cena=$TOVAR["tovar_cena"]-$r;
   	           }
            else $tovar_cena=$TOVAR["tovar_cena"];
		   
		    $tovar_cena=round($tovar_cena*$TOVAR["kol"],2);
            $Summa+=$tovar_cena;
         }
       return $Summa; 
    }
	

  static public function GetKolBasket($TovarID)
    {
       if  ( isset($_SESSION["BASKET"]["TOVAR_".$TovarID]) )	return $_SESSION["BASKET"]["TOVAR_".$TovarID]["kol"];
       else return 1;
   }
  
}


