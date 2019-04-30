<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$r = config('logged');
$type = @intval($r['method_admin']);
if($type == 1 || $type == 2 || $type == 3 || $type == 4)
{
  $arr_auth = array(
    array()
  , array('Google'  , 'https://accounts.google.com/b/0/EditPasswd?hl=en')
  , array('Yahoo'   , 'https://edit.yahoo.com/config/change_pw')
  , array('Facebook', 'https://www.facebook.com/settings?tab=account&section=password')
  , array('Twitter' , 'https://twitter.com/settings/password')
  );
  $type_name = $arr_auth[$type][0];
  switch($Bbc->mod['task'])
  {
    case 'main':
      include 'layout.main.php';
      break;
    case 'login':
      $arr    = $sys->login($type_name);
      $email  = @$arr['email'];
      $user   = $db->getRow("SELECT * FROM `bbc_user` WHERE `username`='{$email}'");
      $MAINURL= _URL._ADMIN;
      if(empty($user))
      {
        $q		= "SELECT `user_id` FROM `bbc_account` WHERE `email`='{$email}'";
        $uid	= intval($db->getOne($q));
        $q		= "SELECT * FROM `bbc_user` WHERE id={$uid}";
        $user	= $db->getRow($q);
      }
      if(empty($user))
      {
        $msg = 'You are not allowed to login !';
      }else{
        _func('password');
        $output		= user_login($user['username'], decode($user['password']), '1');
        switch($output)
        {
          case 'allowed':
            redirect($MAINURL);
            break;
          case 'inactive':
            $msg = "Your account has been disabled.<br />For further information, please contact administrator";
            break;
          case 'notallowed':
            $msg = "Your account is not allowed to access this section.";
            break;
          case 'none':
            $msg = "Invalid Username or Password";
            break;
        }
      }
      ?>
      <div class="container">
        <div class="jumbotron">
          <h1><?php echo $msg; ?></h1>
          <p>Please contact administrator to get privilege to login. Or if you want to try another shot to sign in, you may want to click the button below.</p>
          <p><?php echo $sys->button($MAINURL, 'Relogin', 'repeat'); ?></p>
        </div>
      </div>
      <?php
      $sys->set_layout('blank.php');
      break;

    case 'repair':
      chdir(dirname(dirname(__FILE__)));
      include 'repair.php';
      break;

    case 'clean_cache':
      include 'clean_cache.php';
      break;
    case 'alert':
    case 'alert_list':
    case 'alert_list_detail':
    case 'alert_click':
    case 'alert_remove':
      chdir(dirname(__DIR__));
      include '_switch.php';
      break;

    case 'password':
      $link = $arr_auth[$type][1];
      ?>
      It looks like you use <?php echo $type_name;?> account to login into this admin area, please
      <a href="<?php echo $link;?>" onclick="window.open(this.href,'login','height=480,width=800'); return false;" />Click here</a>
      to change your <?php echo $type_name;?> password';
      <?php
      break;
    case 'link':
      include 'link.php';
      break;
    case 'menu':
      include 'menu.php';
      break;

    case 'logout':
      user_logout($user->id);
      switch ($type)
      {
        case '2':
          $url = 'https://login.yahoo.com/config/login?logout=1&.done=https%3A%2F%2Fwww.yahoo.com';
          break;
        case '3':
          $url = 'https://www.facebook.com';
          break;
        case '1':
        default:
          $url = 'https://accounts.google.com/Logout?hl=en&continue=https%3A%2F%2Fwww.google.com';
          break;
      }
      redirect('http://auth.fisip.net/logout?redirect='.urlencode($url));
      break;

    default:
      echo 'Invalid action <b>'.$Bbc->mod['task'].'</b> has been received...';
      break;
  }
}else{
  // Module untuk mengatur user account yang saat itu login
  switch($Bbc->mod['task'])
  {
    case 'main':
      include 'layout.main.php';
      break;
    case 'login':
      include 'layout.login.php';
      break;
    case 'repair':
      chdir(dirname(dirname(__FILE__)));
      include 'repair.php';
      break;
    case 'clean_cache':
      include 'clean_cache.php';
      break;
    case 'alert':
    case 'alert_list':
    case 'alert_list_detail':
    case 'alert_click':
    case 'alert_remove':
      chdir(dirname(__DIR__));
      include '_switch.php';
      break;

    case 'password': // Untuk mengganti password dari user yg saat itu login, Jika anda mensetting login menggunakan thirdparty semisal google/facebook/yahoo dll untuk login di admin, maka user hanya bisa merubah password mereka di thirdparty tersebut
      include 'user.password.php';
      break;
    case 'link':
      include 'link.php';
      break;
    case 'menu':
      include 'menu.php';
      break;
    case 'logout': // link untuk user logout atau keluar dari mode admin
      user_logout($user->id);
      redirect(_URL._ADMIN);
      break;

    default:
      echo 'Invalid action <b>'.$Bbc->mod['task'].'</b> has been received..2.';
      break;
  }
}