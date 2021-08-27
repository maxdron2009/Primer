<? 
class show_tovar
{
  static public function Action()
  {
       $ID=APP::val_get("ID");
       $TOVAR=APP::GetFullDataTovar($ID);
	   if ( is_null($TOVAR) ) return ;
		  
	   $Ackia="";
	   
       if ( $TOVAR["proc_skidki"]>0 ) 
          {
  	        $old_cena=$TOVAR["tovar_cena"].' т.';	
  	        $r=round($TOVAR["tovar_cena"]*$TOVAR["proc_skidki"]/100,2); 
  	        $tovar_cena=$TOVAR["tovar_cena"]-$r;
	   
  	        $Ackia='<div class="round red_akcia">'.$TOVAR["ts_name"].' '.$TOVAR["proc_skidki"].'% </div>';
  	        $show_ts=1;
      	 }
      else
         {
	       $old_cena='';	
	       $tovar_cena=$TOVAR["tovar_cena"];
		   $show_ts=0;
	     }	   
		 
	   if ($TOVAR["tovar_novinka"]==1) $Novinka='<div class="round green_nov">НОВИНКА</div>';
	   $Ackia=$Ackia." ".$Novinka;	 
       $TITLE_AKCIA="";
	   
       if ( strlen(trim($Ackia))>0 ) $TITLE_AKCIA='<div class="title_akcia">'.$Ackia.'</div>';
	   
	   
	   $tpl = APP::Template('shb/show_tovar.php');
	   
       if ( APPLICATION::IsCheckBasket($TOVAR["tovar_id"]) ) $tpl->SetValue("TITLE_BASKET","Убрать из корзины");
	   else $tpl->SetValue("TITLE_BASKET","Добавить в корзину");
	   
       $tpl->SetValue("value_kol",APPLICATION::GetKolBasket($TOVAR["tovar_id"]));
	   $tpl->SetValue("TITLE_AKCIA",$TITLE_AKCIA);
	   $tpl->SetValue("tovar_name",$TOVAR["tovar_name"]);
	   $tpl->SetValue("tovar_art",$TOVAR["tovar_art"]);
	   $tpl->SetValue("tovar_id",$TOVAR["tovar_id"]);
	   
	   if ( $show_ts==0 ) $tpl->SetValue("tovar_cena",$tovar_cena.'</b> тенге');		  
	   else $tpl->SetValue("tovar_cena",$tovar_cena.'<small class="OldCena">'.$old_cena.'</small>');		  
	   
	   $tpl->SetValue("WIDTH",$TOVAR["tovar_width"]==0 ?0:1);
	   $tpl->SetValue("tovar_width",$TOVAR["tovar_width"]);

	   $tpl->SetValue("HEIGHT",$TOVAR["tovar_height"]==0 ?0:1);
	   $tpl->SetValue("tovar_height",$TOVAR["tovar_height"]);

	   $tpl->SetValue("LEN",$TOVAR["tovar_len"]==0 ?0:1);
	   $tpl->SetValue("tovar_len",$TOVAR["tovar_len"]);
	   
	   $tpl->SetValue("tovar_opis",$TOVAR["tovar_opis"]);
	   $tpl->SetValue("AKCIA",$show_ts);
	   
	   
	   $NameDir=FILES."/tphoto/";

       $d=APP::ExecuteSQL("select * from photo where tovar_id=$ID order by photo_id");
       for($i=0;$i<$d->Count;$i++)
        {
          $DATA=$d->GetRow();
          $BigPhoto=$NameDir.$DATA["photo_big"];
          $SmallPhoto=$NameDir.$DATA["photo_small"];
          if ($i==0) $StartPhoto=$BigPhoto;
          $photos_mobile.='<div>
                              <img src="'.$BigPhoto.'" alt="">
                          </div>';								
				  
				  
          $photos_comp.='<div class="image-one-product">
                           <a href="'.$BigPhoto.'">
                              <img src="'.$SmallPhoto.'" alt="">
                           </a>
                       </div>'; 				  
          
        }
	   
	   $tpl->SetValue("photos_mobile",$photos_mobile);
	   $tpl->SetValue("photos_comp",$photos_comp);
	   $tpl->SetValue("StartPhoto",'<img src="'.$StartPhoto.'" alt="">');
       $tpl->Show();  
   }
}
