<?  
/*
Описание библиотеки для обраотки изображения

fnc_Image(FromImage,ToImage,SizeBigX,SizeBigY,SizeSmallX,SizeSmallY,TipPreob)

TipPreob
0 - простое уменьшение в фомат X,Y без отрезания (FullZoom)  +
1 - выравниваем по ширине , при этом высоту делаем  резиновую + 
2 - выравниваем по ширине , при этом подрезаем высоту по Y +
3 - выравниаем по высоте при этом ширину делаем резиновую +
4 - выравниаем по высоте при этом ширину делаем по X 
5 - просто вырезаныем данное X,Y без всяких преобразований как есть
6 - подгоняем под указанную область -что бы полностью прикрывала
*/

function __fnc_CnvGrayImage($source,$width, $height)
{
   $image256= imagecreate($width, $height);
   for ($c=0;$c<256;$c++) $palette[$c] = imagecolorallocate($image256,$c,$c,$c);
   for ($y=0;$y<$height;$y++)
   for ($x=0;$x<$width;$x++)
    {
      $rgb = imagecolorat($source,$x,$y);
      $r = ($rgb >> 16) & 0xFF;
      $g = ($rgb >> 8) & 0xFF;
      $b = $rgb & 0xFF;
      $gs = (($r*0.299)+($g*0.587)+($b*0.114));
      imagesetpixel($image256,$x,$y,$palette[$gs]);
    }
  return $image256;
}

function fnc_Image($FromImage,$ToImage,$SizeX,$SizeY,$TipPreob,$BlackColor=false,$ext='')
{
   $Image=false;
   $URL=0;
   
   if ( substr($FromImage,0,1)=='#' )
      {
		  $URL=1;
		  $FromImage=substr($FromImage,1);
	  }

  if ( strlen($ext)<1 ) $ext = strtolower(get_file_ext($FromImage));
  
   if (strlen(trim($ext))==0 ) $ext='jpg';
   
   $TypeJob=0;
   
   if ( file_exists($FromImage) || $URL==1 )
      {
        try
         {
           switch ($ext) 
	        {
              case 'jpg':
              case 'jpeg':
                   $im = @imagecreatefromjpeg($FromImage);
				   $TypeJob=1;
                   break;
              case 'gif':
                   $im = @imagecreatefromgif($FromImage);
				   $TypeJob=2;
                   break;
              case 'png':
                   $im = @imagecreatefrompng($FromImage);
				   $TypeJob=3;
				   //$im = @imagecreatefromstring(file_get_contents($FromImage));
                   break;
              default:
                   break;         
            } 
         }
       catch(Exception $e)
         {
           echo "<BR>error in $FromImage to $thumb_fname<BR>";
		   exit;
         }
	 }
	 
   if ( !@$im ) 
      {
		  echo get_file_ext($FromImage);
		  echo '<BR>';
	     echo "Error system grasphics function fnc_Image ext=$ext  FromImage=$FromImage  TypeJob=$TypeJob";
		 exit;
         return false;
      }	 
  
  /* Получаем размеры изображения */  
  list($width, $height, $type, $attr) = getimagesize($FromImage);
  
 if (  ($width <=$SizeX) and ($height <= $SizeY) ) 
    { 
       copy($FromImage, $ToImage);	 	
	   return true;
	}
  
  
  switch($TipPreob)
   {
     case 0:  //  простое уменьшение в фомат X,Y без отрезания (FullZoom)
          if ( $height>$width )   
             {                             
               $nh=$SizeY;
               $delta=$SizeY/$height;
               $nw = (int) ($width * $delta);
			   if ( $nw>$SizeX ) 
				  {
				    $nw=$SizeX;
		            $delta=$SizeX/$width;
                    $nh = (int) ($height * $delta);
				  }
		     }
          else	  
 	         {
		       $nw=$SizeX;
		       $delta=$SizeX/$width;
               $nh = (int) ($height * $delta);
		       if ( $nh>$SizeY ) 
		          {
         	        $nh=$SizeY;
		            $delta=$SizeY/$height;
                    $nw = (int) ($width * $delta);
				  }
		     }
	     break;
     case 1:  // выравниваем по ширине , при этом высоту делаем  резиновую
          if ( $width <=$SizeX ) 
             { 
               copy($FromImage, $ToImage);	 	
	           return true;
             }
	      $nw=$SizeX;
	      $delta=$SizeX/$width;
          $nh = (int) ($height * $delta);
          break;
     case 2:  // выравниваем по ширине , при этом подрезаем высоту по Y
          if ( $width <=$SizeX ) 
             { 
               copy($FromImage, $ToImage);	 	
	           return true;
           	 }
	      $nw=$SizeX;
	      $delta=$SizeX/$width;
		  $nh = (int) ($height * $delta);
          $thumb = imagecreatetruecolor($nw, $nh);
          imagecopyresampled($thumb, $im, 0, 0, 0, 0, $nw, $nh, $width, $height);
		  $im=$thumb;
		  $width=$nw;
		  $height=$nh;
		  if ( $nh>$SizeY )
		     {
               $nh=$SizeY;
		       $height=$SizeY;			  
		  	 }
          break;
     case 3:  // выравниаем по высоте при этом ширину делаем резиновую
          if ( $height <=$SizeY ) 
             { 
               copy($FromImage, $ToImage);	 	
	           return true;
           	 }
	      $nh=$SizeY;
	      $delta=$SizeY/$height;
          $nw = (int) ($width * $delta);
          break;
     case 4:  // выравниаем по высоте при этом ширину делаем по X 
	      $nh=$SizeY;
	      $delta=$SizeY/$height;
          $nw = (int) ($width * $delta);
		    
          $thumb = imagecreatetruecolor($nw, $nh);
          imagecopyresampled($thumb, $im, 0, 0, 0, 0, $nw, $nh, $width, $height);
		  $im=$thumb;
		  $width=$nw;
		  $height=$nh;
		  if ( $nw>$SizeX )
		     {
               $nw=$SizeX;
		       $width=$SizeX;			  
		     }
          break;		 
     case 5:  // выравниаем по высоте при этом ширину делаем по X 
		  $nw=$width;
          $nh=$height;
		  if ($width>$SizeX) 
		     {
               $nw=$SizeX;
	    	   $width=$SizeX;
			 }
		  if ($height>$SizeY)	 
		     {
               $nh=$SizeY;
               $height=$SizeY;
			 }  
	      break;		 
     case 6:  // выравниаем по высоте при этом ширину делаем по X 
          if ( $width <=$SizeX && $height <=$SizeY )    // если изображение меньше чем X,Y то просто скопировали и все 
             { 
               copy($FromImage, $ToImage);	 	
	           return true;
           	 }

          if ( $height<$SizeY )    // если высота меньше установленного то просто надо уменьшить по X
             { 
			   fnc_Image($FromImage,$ToImage,$SizeX,$SizeY,2,$BlackColor);
	           return true;
           	 }

          if ( $width <=$SizeX )    // если ширина  меньше установленного то просто надо уменьшить по Y
             { 
			    fnc_Image($FromImage,$ToImage,$SizeX,$SizeY,4,$BlackColor);
	            return true;
           	 }
			 
		  if ( $height<$width )	    // Горизонтальное  фото 
		    {
               $nh=$SizeY;
      	       $delta=$SizeY/$height;
               $nw = (int) ($width * $delta);
               $thumb = imagecreatetruecolor($nw, $nh);
               imagecopyresampled($thumb, $im, 0, 0, 0, 0, $nw, $nh, $width, $height);
		       $im=$thumb;
		       $width=$nw;
      		   $height=$nh;
		       if ( $nw>$SizeX )
		         {
                      $nw=$SizeX;
		              $width=$SizeX;			  
		        }
			}
		  else
		    {
	             $nw=$SizeX;
	             $delta=$SizeX/$width;
	             $nh = (int) ($height * $delta);
	             $thumb = imagecreatetruecolor($nw, $nh);
	             imagecopyresampled($thumb, $im, 0, 0, 0, 0, $nw, $nh, $width, $height);
	             $im=$thumb;
	             $width=$nw;
	             $height=$nh;
	             if ( $nh>$SizeY )
		            {
	                   $nh=$SizeY;
	                    $height=$SizeY;			  
	             	 }
				
			}
	      break;		 
   }
  $thumb = imagecreatetruecolor($nw, $nh);
  imagecopyresampled($thumb, $im, 0, 0, 0, 0, $nw, $nh, $width, $height);
  if ($BlackColor) $thumb=__fnc_CnvGrayImage($thumb,$nw,$nh);
  imagejpeg($thumb, $ToImage, 90);
  imagedestroy($thumb); 
  imagedestroy($im);
  return true;
}

function fnc_Images($FromImage,$ToImageBig,$ToImageSmall,$SizeXBig,$SizeYBig,$SizeXSmall,$SizeYSmall,$TipPreob,$BlackColor=false)
{
    fnc_Image($FromImage,$ToImageBig  ,$SizeXBig  ,$SizeYBig  ,$TipPreob,$BlackColor);
    fnc_Image($FromImage,$ToImageSmall,$SizeXSmall,$SizeYSmall,$TipPreob,$BlackColor);
} 

function generate_thumbnail($fname, $thumb_fname, $max_x=99, $max_y=99) 
 {
   $ext = get_file_ext($fname);
   try
      {
        switch ($ext) 
		  {
            case 'jpg':
            case 'jpeg':
                 $im = @imagecreatefromjpeg($fname);
                 break;
            case 'gif':
                 $im = @imagecreatefromgif($fname);
                 break;
            case 'png':
                 $im = @imagecreatefrompng($fname);
                 break;
            default:
                 return false;
                 break;
          } 
    }
  catch(Exception $e)
   {
     echo "<BR>error in $fname to $thumb_fname<BR>";
     return false;
   }
  if (@$im) 
     {
       list($width, $height, $type, $attr) = getimagesize($fname);
       if (($width > $max_x) or ($height > $max_y)) {
          if ($width > $height) {
            $nw = $max_x;
            $nh = ($max_x / $width) * $height;
          }
          else {
            $nw = ($max_y / $height) * $width;
             $nh = $max_y;
          }
         $thumb = imagecreatetruecolor($nw, $nh);
         imagecopyresampled($thumb, $im, 0, 0, 0, 0, $nw, $nh, $width, $height);
         imagejpeg($thumb, $thumb_fname, 90);
         imagedestroy($thumb);
    } 
    else 
	{
      copy($fname, $thumb_fname);
    }
	return true;
  } 
  else 
  {
    return false;
  }
}


function generate_thum_width($fname, $thumb_fname, $max_x=99, $max_y=99) 
 {
   $ext = get_file_ext($fname);
   try
   {
   switch ($ext) 
   {
     case 'jpg':
     case 'jpeg':
          $im = @imagecreatefromjpeg($fname);
          break;
     case 'gif':
          $im = @imagecreatefromgif($fname);
          break;
     case 'png':
          $im = @imagecreatefrompng($fname);
          break;
    default:
	      echo"Error in ext".$ext;
          return false;
          break;
  } 
   }
  catch(Exception $e)
   {
	    echo"Error Exception";
	   return false;
   }
  //echo"Normal ext".$ext;
  if (@$im) 
  {
    list($width, $height, $type, $attr) = getimagesize($fname);
    if (($width > $max_x) or ($height > $max_y)) 
	   {
         $nw = $max_x;
         $nh = ($max_x / $width) * $height;
		 
		 
		 if   ($height>$width) $max_image=$height;
		 else $max_image=$width;
		 
		 if   ($max_x >$max_y) $max_coord=$max_x ;
		 else $max_coord=$max_y;
		 
		 $delta=$max_coord/$max_image;
		 
         $nw = (int) ($width * $delta);
         $nh =  (int ) ($height * $delta);

		 
         $thumb = imagecreatetruecolor($nw, $nh);
         imagecopyresampled($thumb, $im, 0, 0, 0, 0, $nw, $nh, $width, $height);
         imagejpeg($thumb, $thumb_fname, 90);
         imagedestroy($thumb);
      } 
    else {
      copy($fname, $thumb_fname);
    }
  } 
  else {
    return false;
  }
}

?>