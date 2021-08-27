<? 

class list_tovar
{
  static $PHONES=array();

  static public function CellTovars($Sender, $LoopBlock, $Data, $n)
    {
	  $LoopBlock->start_html("LIST");	
      APPLICATION::ShowCellTovar($Data);		  
	  $LoopBlock->end_html();	
	  $LoopBlock->Show();
	}
  
  static public function Action()
   {
	   
      $KategID=APP::val_get('KatID');
      $FindText=trim(APP::string_get('FindText'));
      $Action=trim($_GET["Action"]);
      $Mode=trim($_GET["Mode"]);
      $TsID=APP::val_get('TsID');
      $HREF_SELECT=APP::HBQ($_GET);
	  
      $d=APP::DBC();
  
      $Sort=APP::val($_SESSION["SORT_KATALOG"]);
  
      $kat_key='';
      if ( $KategID>0 )
         {
           $KATEG=APP::GetRecordTable("kat","kat_id",$KategID);	 
           $kat_key=$KATEG["kat_key"];
         }

      $ListNews=new TDBGridClass(30,'list_tovar','CellTovars','IndV3343334op');
	  
      switch($Action)
         {
	       case 'Nov':
                $SQLCount="select count(*) as count  from tovar where  tovar_delete=0 and  tovar_novinka=1";										 
                $SQL="select *, (select ts_name from ts where ts.ts_id=tovar.ts_id) as ts_name  
                       from tovar where tovar_delete=0 and  tovar_novinka=1 order by tovar_name";
                break; 
	       case 'Catalog':
           
                $Where="";
                for($i=0;$i<$_SESSION["FILTER"]["COUNT"];$i++)
                 {
                     if ( $i==0 )  
					    $Where.="( frms_id=".$_SESSION["FILTER"]["LIST"][$i]["frms_id"]." and tovs_value=".$_SESSION["FILTER"]["LIST"][$i]["value"].")";
                   else 
				        $Where.=" and ( frms_id=".$_SESSION["FILTER"]["LIST"][$i]["frms_id"]." and tovs_value=".$_SESSION["FILTER"]["LIST"][$i]["value"].")";
                 }
	  
                if ( strlen($Where)>0 ) 
		           {
			          $Where=" (".$Where.") ";	 
                      $SQLCount="select count(*) as count from tovar,tovs  
		                          where  tovar_delete=0 and  
				                         kat_key like '%$kat_key%' and 
				                         tovar.tovar_id=tovs.tovar_id and ".$Where;										 
					   
                      $SQL="select *, (select ts_name from ts where ts.ts_id=tovar.ts_id) as ts_name  
                               from tovar,tovs 
     			             where tovar_delete=0 and  
	     			               kat_key like '%$kat_key%' and 
		     		               tovar.tovar_id=tovs.tovar_id and "
     				               .$Where .
	     	               "order by tovar_name";
		           }
                else
	     	      {
                     $SQLCount="select count(*) as count from tovar  
					            where tovar_delete=0 and kat_key like '%$kat_key%'";										                     
					$SQL="select *,
                             (select ts_name from ts where ts.ts_id=tovar.ts_id) as ts_name  
                           from tovar 
					      where  tovar_delete=0 and kat_key like '%$kat_key%' order by tovar_name";
     			 }
		        break; 
	       case 'Find':
                 $SQLCount="select count(*) as count from tovar  
				             where tovar_delete=0 and 
								    tovar_name like '$FindText%'";
	             $SQL="select *,
                      (select ts_name from ts where ts.ts_id=tovar.ts_id) as ts_name  
                 from tovar 
				where tovar_delete=0 and  tovar_name like '%$FindText%' order by tovar_name";
                break;				 
	       case 'Ts':
                $SQLCount="select count(*) as count 
				              from tovar  
						     where tovar_delete=0 and ts_id=$TsID";										 
                 $SQL="select *,
                             (select ts_name from ts where ts.ts_id=tovar.ts_id) as ts_name  
                        from tovar 
					   where tovar_delete=0 and 
							 ts_id=$TsID 
				    order by tovar_name";
		        break; 
	       default:	 
                $SQLCount="select count(*) as count from tovar  
             				 where tovar_delete=0";
	             $SQL="select *,
                              (select ts_name from ts where ts.ts_id=tovar.ts_id) as ts_name  
                         from tovar 
					    where tovar_delete=0 order by tovar_name";
           break;				 
         }
  
      $ListNews->SqlCount=$SQLCount;
      $ListNews->Sql=$SQL;
      $ListNews->TextNotData="<tr><td colspan='4'><BR>Товаров по данным параметрам нет<BR><BR></td></tr>";      // вызываетс¤ если таблица пуста¤
      $ListNews->Navi_Label='NAVI';                                           // в место данной метки буде выходит блок
      $ListNews->Navi_Block='<div class="navigation">%NAVI%</div>';   // данной навигации по страницам
      $ListNews->FindKeyField="tovar_id";             // ключеваое поле по которому осущевствлает¤ поиск
      $ListNews->FindID=$FindSnu;   
      $ListNews->NowTime=time();
      $ListNews->HREF_SELECT=$HREF_SELECT;

      $tpl = APP::Template('shb/start_tovars.php');
      $tpl->SetValue("CELL",$ListNews);
      $tpl->Show();  

   }
  }


