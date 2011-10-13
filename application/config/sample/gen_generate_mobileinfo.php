<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['loadfiles'] = array('UserAgent', 'DisplayInfo', 'ProfileData');

$config['usedata']['UserAgent']['デバイス名'] = 'device';
$config['usedata']['UserAgent']['最終更新日'] = 'update_useragent';
$config['usedata']['DisplayInfo']['キャリア'] = 'carrier';
$config['usedata']['DisplayInfo']['機種名'] = 'type';
$config['usedata']['DisplayInfo']['横'] = 'width';
$config['usedata']['DisplayInfo']['縦'] = 'height';
$config['usedata']['DisplayInfo']['最終更新日'] = 'update_displayinfo';
$config['usedata']['ProfileData']['メーカー'] = 'maker';
$config['usedata']['ProfileData']['発売日'] = 'release_ymd';
$config['usedata']['ProfileData']['Flashバージョン'] = 'is_flash';
$config['usedata']['ProfileData']['最終更新日'] = 'update_profiledata';

$config['header']['UserAgent'][] = 'キャリア';
$config['header']['UserAgent'][] = '機種名';
$config['header']['UserAgent'][] = 'デバイス名';
$config['header']['UserAgent'][] = 'USER_AGENT';
$config['header']['UserAgent'][] = 'USER_AGENT_SCRAP';
$config['header']['UserAgent'][] = '最終更新日';

$config['header']['DisplayInfo'][] = 'キャリア';
$config['header']['DisplayInfo'][] = '機種名';
$config['header']['DisplayInfo'][] = '表示タイプ';
$config['header']['DisplayInfo'][] = '横';
$config['header']['DisplayInfo'][] = '縦';
$config['header']['DisplayInfo'][] = '最終更新日';

$config['header']['ProfileData'][] = 'キャリア';
$config['header']['ProfileData'][] = '機種名';
$config['header']['ProfileData'][] = 'メーカー';
$config['header']['ProfileData'][] = 'ニックネーム';
$config['header']['ProfileData'][] = '発売日';
$config['header']['ProfileData'][] = 'シリーズ';
$config['header']['ProfileData'][] = '通信方式';
$config['header']['ProfileData'][] = '下り通信速度';
$config['header']['ProfileData'][] = 'マークアップ言語';
$config['header']['ProfileData'][] = 'ブラウザバージョン';
$config['header']['ProfileData'][] = 'キャッシュ容量';
$config['header']['ProfileData'][] = 'QVGA画面';
$config['header']['ProfileData'][] = '画像階調';
$config['header']['ProfileData'][] = 'GIF';
$config['header']['ProfileData'][] = 'JPEG';
$config['header']['ProfileData'][] = 'PNG';
$config['header']['ProfileData'][] = 'BMP2';
$config['header']['ProfileData'][] = 'BMP4';
$config['header']['ProfileData'][] = 'MNG';
$config['header']['ProfileData'][] = 'アニメーションGIF';
$config['header']['ProfileData'][] = '透過GIF';
$config['header']['ProfileData'][] = 'AMC';
$config['header']['ProfileData'][] = 'ASF';
$config['header']['ProfileData'][] = '3G2';
$config['header']['ProfileData'][] = '3GP';
$config['header']['ProfileData'][] = '和音数';
$config['header']['ProfileData'][] = '着うた対応';
$config['header']['ProfileData'][] = '着うたフル対応';
$config['header']['ProfileData'][] = '着ムービー対応';
$config['header']['ProfileData'][] = '大容量動画対応';
$config['header']['ProfileData'][] = 'アプリケーション対応';
$config['header']['ProfileData'][] = 'アプリケーション種類';
$config['header']['ProfileData'][] = 'アプリケーションバージョン';
$config['header']['ProfileData'][] = 'アプリケーション最大容量';
$config['header']['ProfileData'][] = 'FLASH対応';
$config['header']['ProfileData'][] = 'Flashバージョン';
$config['header']['ProfileData'][] = 'QR対応';
$config['header']['ProfileData'][] = 'Felica対応';
$config['header']['ProfileData'][] = 'モバイルSuica';
$config['header']['ProfileData'][] = 'Bluetooth対応';
$config['header']['ProfileData'][] = '赤外線対応';
$config['header']['ProfileData'][] = 'GPS対応';
$config['header']['ProfileData'][] = 'メール件名文字数';
$config['header']['ProfileData'][] = '添付受信サイズ';
$config['header']['ProfileData'][] = '添付送信サイズ';
$config['header']['ProfileData'][] = 'HTMLメール';
$config['header']['ProfileData'][] = 'PDFビューアー対応';
$config['header']['ProfileData'][] = 'Office文書ビューアー対応';
$config['header']['ProfileData'][] = 'office文書WebDL';
$config['header']['ProfileData'][] = 'officeファイル表示可能サイズ';
$config['header']['ProfileData'][] = 'フルブラウザ対応';
$config['header']['ProfileData'][] = 'フルブラウザバージョン';
$config['header']['ProfileData'][] = 'フルブラウザUSER-AGENT';
$config['header']['ProfileData'][] = 'プッシュ型情報配信';
$config['header']['ProfileData'][] = 'きせかえツール';
$config['header']['ProfileData'][] = '待受コンシェルジュ機能対応';
$config['header']['ProfileData'][] = 'SSL対応';
$config['header']['ProfileData'][] = 'ルートCA証明書VeriSign';
$config['header']['ProfileData'][] = 'ルートCA証明書Entrust';
$config['header']['ProfileData'][] = 'ルートCA証明書Cyber Trust';
$config['header']['ProfileData'][] = 'ルートCA証明書Geotrust';
$config['header']['ProfileData'][] = 'ルートCA証明書RSA Security';
$config['header']['ProfileData'][] = 'Cookie';
$config['header']['ProfileData'][] = '機体番号送信';
$config['header']['ProfileData'][] = 'メールURL長';
$config['header']['ProfileData'][] = 'BookMarkURL長';
$config['header']['ProfileData'][] = 'ブラウザURL長';
$config['header']['ProfileData'][] = '横画面用待受設定';
$config['header']['ProfileData'][] = '横画面ブラウザ対応';
$config['header']['ProfileData'][] = 'タッチパネル';
$config['header']['ProfileData'][] = 'カメラの画素数';
$config['header']['ProfileData'][] = '第二カメラの画素数';
$config['header']['ProfileData'][] = 'テレビ電話対応';
$config['header']['ProfileData'][] = '外部メモリースロットとメディアの種類';
$config['header']['ProfileData'][] = 'ワンセグ対応';
$config['header']['ProfileData'][] = 'サービス提供中';
$config['header']['ProfileData'][] = '最終更新日';

