<?php
ini_set( 'display_errors', 1 );
mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");
date_default_timezone_set('Asia/Tokyo');

main( $argv );

//***************************************************************************
//!		usage
//***************************************************************************
function usage(){
  printf( "%s  ver0.01 - 2013.07.14 M.Hibara".PHP_EOL, basename( $_SERVER[ 'SCRIPT_NAME' ] ) );
  printf( "usage: %s [-i genko.txt]".PHP_EOL, basename( $_SERVER[ 'SCRIPT_NAME' ] ) );
  printf( "     : UTF-8エンコーディングのテキストファイルを読み込み400詰原稿用紙換算枚数を計算する。");
  exit( 1 );
}
//***************************************************************************
//!		main
//***************************************************************************
function main( $argv ){

  if( count($argv) < 3 ) usage();
  array_shift( $argv );

  $input_filename = null;
  for ( $i = 0; $i < count($argv); $i++ ){
    if( $argv[ 0 ] == '-i' ){
      array_shift( $argv );
      $input_filename = array_shift( $argv );
    }
  }

  if( $input_filename != null ){

    if( !file_exists( $input_filename ) ){
      pr("error: ファイル '$input_filename' がありません。");
      exit(1);
    }
    $alltext = file_get_contents($input_filename);
    if( $alltext === false ){
      die( $input_filename );
    }

    //原稿用紙換算枚数を計算（返値：行数）
    $linesNum = CountGenkoCharacter($alltext);
    $pageNum = intval($linesNum/20);  //ページ
    $linesNum = $linesNum%20;         //行（剰余）
    printf("%s pages, %s lines\n\n ", $pageNum, $linesNum);

  }
  exit(0);

}
//***************************************************************************
//	原稿用紙換算枚数カウント
//  return: 行数
//***************************************************************************
function CountGenkoCharacter($all_text){

//一行の文字数
$wordCount = 20;
//ぶら下がり文字数制限
$hangdownNum = 2;
//行頭禁則：
$kin_line_top = "、。，．・？！゛゜ヽヾゝゞ々ー）］｝」』ぁぃぅぇぉっゃゅょァィゥェォッャュョ!),.:;?]}｡｣､･ｰﾞﾟ";
//行末禁則：
$kin_line_end = "（［｛「『#([{｢";
//カウントする行数
$linesNum = 0;
//一行ごとに処理
$array_text = explode( "\n", $all_text );
for ( $i = 0; $i < count($array_text); $i++ ){
  $charNum = 0;
  $array_char = mb_str_split($array_text[$i]);
  while ( count($array_char) > 0 ){
    if ($array_char[0] === "" || $array_char[0] === null){
      break;
    }
    if ( $charNum == 0 ){                //行頭禁則のチェック
      if ( mb_strpos($kin_line_top, $array_char[0]) !== false){
        $charNum = 0;
      }
      else{
        $charNum++;
      }
    }
    elseif ( $charNum > $wordCount-1 ){  //行末禁則のチェック
      if ( mb_strpos($kin_line_end, $array_char[0]) !== false){
        if ( $charNum > $wordCount+$hangdownNum-1){ //ぶら下がり文字数を超えた
          $charNum = 0;
          $linesNum++;
        }
        else{
          $charNum++;
        }
      }
      else{
        $charNum = 0;
        $linesNum++;
      }
    }
    else{
      $charNum++;
    }
    array_shift($array_char);
  }
  //一行文字数ピッタリの場合は行数をカウントしない
  if ( $charNum < $wordCount+1){
    $linesNum++;
  }
}

return($linesNum);

}
//***************************************************************************
function mb_str_split( $string ) {
  # http://www.php.net/manual/ja/function.mb-split.php
  # Split at all position not after the start: ^
  # and not before the end: $
  return preg_split('/(?<!^)(?!$)/um', $string );
}
//***************************************************************************
?>
