<?
  session_start();  
  
/*  if ( !isset( $_SESSION["PRT_KOD"]  )
     {
	   echo "SYSTEM ERROR BAD !!! NOT KODE!!!<BR>";
	   return ;
	 }*/
 
  $PRT_KOD=$_SESSION["PRT_KOD"];

  $width  = 35; //чЩУПФБ ЛБТФЙОЛЙ
  $height = 18; //ыЙТЙОБ ЛБТФЙОЛЙ

  $transparent = 1;	//рТПЪТБЮОПУФШ
  $interlace = false;

  $msg = $PRT_KOD; 	//пФПВТБЦБЕНЩК ФЕЛУФ

  //оБУФТПКЛБ ЫТЙЖФБ
  $fonttype = ''; //чЙД ЫТЙЖФБ, ЕУМЙ ОЕ ЪБДБО, ФП ЙУРПМШЪХЕФУС УЙУФЕНОЩК
  $font = 5; // full path to your font
  $tuningfount=5;
  $size = 16;
  $rotation = 45;

   //пФУФХР ФЕЛУФБ ПФ ЛТБС
   $pad_x = 0;
   $pad_y = 0;

   // RGB ГЧЕФ ФЕЛУФБ
   $fg_r = 0;
   $fg_g = 0;
   $fg_b = 0;
   
   // RGB ГЧЕФ ЖПОБ
   $bg_r = 255;
   $bg_g = 255;
   $bg_b = 255;

   //рТЙЪОБЛЙ ЧЩЧПДБ РПНЕИ
   $ShowDot = true;
   $ShowFig = false;

   //дМС ИЕЫБ
   $hashcode=1245;  //УБН ЛПД
   $hashvalue=1245; //ЕЗП ЪБЫЙЖТПЧБООПЕ ЪОБЮЕОЙЕ
   $hashfield=1234; //ФЕЛУФ РПМС, ДМС ХДПВУФЧБ РТЙ ЮБУФПН ЙУРПМШЪПЧБОЙЙ

 $image = ImageCreate($width+($pad_x*2),$height+($pad_y*2));
 // гЧЕФ ЖПОБ
 $bg = ImageColorAllocate($image, $bg_r, $bg_g, $bg_b); 
 // гЧЕФ ФЕЛУФБ
 $fg = ImageColorAllocate($image, $fg_r, $fg_g, $fg_b); 

 if ($transparent)  ImageColorTransparent($image, $bg);

 ImageInterlace($image, $interlace);

 if ( $fonttype == 'ttf' )
    {  //ДМС TrueType ЫТЙЖФПЧ
      ImageTTFText($image, $size, $rotation,$pad_x, $pad_y, $fg, $font, $msg);
    } 
 else 
    { //уЙУФЕНОЩК ЫТЙЖФ
      ImageString($image, $tuningfount, $pad_x, $pad_y, $msg, $fg);
    }

 //чОПУЙН Ч ЙЪПВТБЦЕОЙЕ ЫХН
 if ( $ShowFig )
    { 
      $dc = ImageColorAllocate($image, rand(0,255), rand(0,255), rand(0,255));
      ImageRectangle($image, rand(0, $width/2 ), rand(0, $height/2 ),
                             rand($width / 2, $width) ,rand($height / 2, $height), $dc);
      $dc = ImageColorAllocate($image, rand(0,255), rand(0,255), rand(0,255));
      ImageRectangle($image, rand(0, $width/2 ), rand(0, $height/2 ),
                             rand($width / 2, $width) ,rand($height / 2, $height), $dc);
    }

 //ыХНЩ Ч ЧЙДЕ ФПЮЕЛ
 if ( $ShowDot )
    {
      for($i = $width * $height / 10; $i >= 0;$i--)
       {
          ImageSetPixel($image, rand(0,$width), rand(0,$height),
          ImageColorAllocate($image, rand(0,255), rand(0,255), rand(0,255)));
       }
     }

  Header("Content-type: image/png");
  ImagePNG($image);
  ImageDestroy($image);

?>