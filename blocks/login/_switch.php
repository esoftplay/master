<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

// Menampilkan login form, atau nama pengunjung jika posisi sudah login. Jangan lupa untuk menentukan User Privilege di "Advance Panel" nanti, agar hanya user yg belum login saja block ini ditampilkan
$user_url = seo_url();
if (!empty($_POST['url']))
{
  $user_url = $_POST['url'];
}else
if (!empty($_GET['return']))
{
  $user_url = $_GET['return'];
}else
if (!empty($_GET['url']))
{
  $user_url = $_GET['url'];
// }else
// if (!empty($_SERVER['HTTP_REFERER']))
// {
//   // $user_url = $_SERVER['HTTP_REFERER'];
}
include tpl(@$config['template'].'.html.php', 'default.html.php');