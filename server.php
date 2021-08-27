<?  if ( !isset($_SESSION) ) session_start();

 include_once("inc.php");

 $SERVIS_AddKorzina=APP::val_post("SERVIS_AddKorzina");
 $SERVIS_DelFromBasket=APP::val_post("SERVIS_DelFromBasket");
 $SERVIS_ChangeKol=APP::val_post("SERVIS_ChangeKol");
 $SERVIS_SendForma=APP::val_post("SERVIS_SendForma");
 $SERVIS_SaveZai=APP::val_post("SERVIS_SaveZai");
 
 if ( $SERVIS_SaveZai==1 )
    {
      $DATA=APP::string_post("DATA");
	  $DATA=APP::CNVJSon($DATA);	
	  $DATA=json_decode($DATA);
	  
      $zak_fio=addslashes(APP::_String($DATA->zak_fio));		
      $zak_phones=addslashes(APP::_String($DATA->zak_phones));		
      $zak_email=addslashes(APP::_String($DATA->zak_email));		
      $zai_rem=addslashes(APP::_String($DATA->zai_rem));		
      $capcha=APP::val(APP::_String($DATA->capcha));	
      $datalist=unserialize($DATA->datalist);		
	  
	  
      $DATALIST["ERROR"]=0;  
	  if (!isset($datalist["BASKET"]))
	     {
            $DATALIST["ERROR"]=2;  
            echo json_encode($DATALIST);
			return ;		
		 }
	  	
	  
	  if ( $capcha!=$_SESSION["PRT_KOD"] )
 	     {
            $DATALIST["ERROR"]=1;  
            $DATALIST["CODE"]=APP::GetCode();  
			$DATALIST["LIST"]=serialize($_SESSION);  
            echo json_encode($DATALIST);		
            return ;	 		
		 }
		 
	
	  $zakaz_date=time();
	  $zakaz_ip=APP::GetIP();
	  $zakaz_summa=0;
	  
	  
	  $zakaz_id=APP::AppendSQL("insert into zakaz ( zakaz_ip, zakaz_date, zakaz_mail, zakaz_summa, zakaz_fio,zakaz_phone ) 
	                             values ( '$zakaz_ip', $zakaz_date, '$zak_email', $zakaz_summa, '$zak_fio','$zak_phones' ) ");	 
								 
								 

      $TOVARS=array();
      $BASKET=$datalist["BASKET"];
      foreach ($BASKET as $key=>$value)
       {
	     if ($key=="COUNT") continue;
	     $TOVARS[]=$BASKET[$key];
       }								 
	   
       if ( count($TOVARS)<1 ) 
          {
			$zakaz_id=APP::DeleteSQL("delete delete from zakaz where zakaz_id=$zakaz_id");   
            $DATALIST["ERROR"]=2;  
            echo json_encode($DATALIST);
            return ;
	     }

       $Summa=0;
	   $ROW="";
       for($i=0;$i<count($TOVARS);$i++)
         { 
            $TOVAR=$TOVARS[$i];
			$tovar_id=$TOVAR["tovar_id"];
			$zak_kol=$TOVAR["kol"];
			$zak_skidka=$TOVAR["proc_skidki"];
            if ( $zak_skidka>0 ) 
               {
   	              $r=round($TOVAR["tovar_cena"]*$zak_skidka/100,2); 
	              $zak_cena=$TOVAR["tovar_cena"]-$r;
	           }
           else $zak_cena=$TOVAR["tovar_cena"];
           $cena=round($tovar_cena*$zak_kol,2);
           $Summa+=$cena;
	       APP::AppendSQL("insert into zak ( zakaz_id,tovar_id,zak_kol,zak_cena,zak_skidka ) 
	                                values ( $zakaz_id,$tovar_id,$zak_kol,$zak_cena,$zak_skidka ) ");	 
									
           $ROW.='<tr>
                     <td style="padding:5px; border:solid 1px #CCCCCC;">'.$TOVAR["tovar_name"].'</td>
                     <td style="padding:5px; border:solid 1px #CCCCCC;">'.$zak_cena.'</td>
                     <td style="padding:5px; border:solid 1px #CCCCCC;">'.$zak_kol.'</td>
                     <td style="padding:5px; border:solid 1px #CCCCCC;">'.$cena.'</td>
               </tr>'; 									
		 }

	  $zakaz_id=APP::UpdateSQL("update zakaz set zakaz_summa=$Summa where zakaz_id=$zakaz_id");   
	  
	  APPLICATION::LoadNast();
  
  	  $Phones='';
	  for($i=0;$i<count(APPLICATION::$PHONES);$i++)
 	     $Phones.='<div>'.APPLICATION::$PHONES[$i].'</div>';	
	   
     	$Phones.='<div>'.APPLICATION::$PHONES[$i].'</div>';		  
	  
	  
      $tpl = APP::Template('shb/form_mail_mag.php');
      $tpl->SetValue("PHONES",$Phones); 	
      $tpl->SetValue("MAIL",APPLICATION::$EMAIL); 	 
      $tpl->SetValue("zak_fio",$zak_fio); 	 
      $tpl->SetValue("zak_phones",$zak_phones); 	 
      $tpl->SetValue("zak_email",$zak_email); 	 
      $tpl->SetValue("zai_rem",$zai_rem); 	
      $tpl->SetValue("ITOG",$Summa); 	
      $tpl->SetValue("ROW",$ROW); 	
	  $Page=$tpl->Show(false);
	  
	  
	  Mail2016($zak_email,"Заказ",$Page);
	  Mail2016(MAIL_SUPPORT,"Заказ",$Page);
	  Mail2016("andrys2007@mail.ru","Заказ",$Page);
	  
	  unset($_SESSION["BASKET"]);
	  
	  $DATALIST["PAGE"]=APPLICATION::DrawBasket();
	  $DATALIST["SUMMA"]=APPLICATION::CalcSummaBasket()." т.";
		
      $DATALIST["CODE"]=APP::GetCode();  
      echo json_encode($DATALIST);		
	  return ;	 		
	}
 
 
 if ( $SERVIS_SendForma==1 )
    {
      $DATALIST["CODE"]=APP::GetCode();  
      $DATALIST["LIST"]=serialize($_SESSION);  

      echo json_encode($DATALIST);		
	  return ;	 		
	}
 
 
 if ( $SERVIS_ChangeKol==1 )
    {
      $TovarID=APP::val_post("TovarID");
      $Inc=APP::val_post("Inc");
      $Value=APP::val_post("Value");
	  
	  if ( isset($_SESSION["BASKET"]["TOVAR_".$TovarID]) )	
	      {
	         $_SESSION["BASKET"]["TOVAR_".$TovarID]["kol"]=$Value;
			 if ($_SESSION["BASKET"]["TOVAR_".$TovarID]["kol"]<=0) $_SESSION["BASKET"]["TOVAR_".$TovarID]["kol"]=0;
		  }
	  $DATALIST["PAGE"]=APPLICATION::DrawBasket();
	  $DATALIST["SUMMA"]=APPLICATION::CalcSummaBasket()." т.";
      echo json_encode($DATALIST);		
	  return ;	
	}
 

 if ( $SERVIS_DelFromBasket==1 )
    {
      $TovarID=APP::val_post("TovarID");
      $TOVAR=APP::GetRecordTable("tovar","tovar_id",$TovarID);
	  $Count=val($_SESSION["BASKET"]["COUNT"]);	
	  if ( isset($_SESSION["BASKET"]["TOVAR_".$TovarID]) )	
	      {
			  unset($_SESSION["BASKET"]["TOVAR_".$TovarID]) ;
			  $Count--;
			  if ($Count<0) $Count=0;
			  $REZ=0;
		  }
	  $_SESSION["BASKET"]["COUNT"]=$Count;	 
	  $DATALIST["COUNT_BASKET"]=$Count;
	  $DATALIST["REZ"]=$REZ;
	  $DATALIST["PAGE"]=APPLICATION::DrawBasket();
	  $DATALIST["SUMMA"]=APPLICATION::CalcSummaBasket()." т.";
      echo json_encode($DATALIST);		
	  return ;	
	}
 
 if ( $SERVIS_AddKorzina==1 )
    {
      $TovarID=APP::val_post("TovarID");
      $TOVAR=APP::GetRecordTable("tovar","tovar_id",$TovarID);
	  $TOVAR["kol"]=1;
	  $Count=val($_SESSION["BASKET"]["COUNT"]);	
	  if ( isset($_SESSION["BASKET"]["TOVAR_".$TovarID]) )	
	      {
			  unset($_SESSION["BASKET"]["TOVAR_".$TovarID]) ;
			  $Count--;
			  if ($Count<0) $Count=0;
			  $REZ=0;
		  }
	  else 
	     {
			 $_SESSION["BASKET"]["TOVAR_".$TovarID]=$TOVAR; 
			 $Count++;
			 $REZ=1;
		 }
	  $_SESSION["BASKET"]["COUNT"]=$Count;	 
	  $DATALIST["COUNT_BASKET"]=$Count;
	  $DATALIST["REZ"]=$REZ;
	  $DATALIST["SUMMA"]=APPLICATION::CalcSummaBasket()." т.";
      echo json_encode($DATALIST);		
	  return ;	
	}
 
?>