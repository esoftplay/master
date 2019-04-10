<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

// Module tambahan untuk keperluan pengaturan user atau pengunjung yang telah login
switch($Bbc->mod['task'])
{
	case 'main':
	case 'notfound':
		include 'not_found.php';
		break;

	case 'notAllowed':
		include 'not_allowed.php';
		break;

	case 'lang':
		include 'lang.php';
		break;

	case 'location':
		include 'location.php';
		break;

	case 'register': // Halaman registrasi user
		$is_register_only = true;
		include 'register-form.php';
		break;
	case 'register-validate':
		include 'register-validate.php';
		break;
	case 'register-finish':
		include 'register-finish.php';
		break;

	case 'forget-password': // halaman untuk mengingat kan user mengenai username dan password untuk login melalui email
		include 'forget-password.php';
		break;
	case 'forget-finished':
		echo msg(lang('forget password success'));
		break;

	case 'login': // Halaman login untuk user / pengujung yang belu login
		include 'login-form.php';
		break;
	case 'force2Login':
		include 'force2Login.php';
		break;

	case 'alert':
		include 'alert.php';
		break;
	case 'alert_list':
		include 'alert_list.php';
		break;
	case 'alert_list_detail':
		include 'alert_list_detail.php';
		break;
	case 'alert_click':
		include 'alert_click.php';
		break;
	case 'alert_remove':
		include 'alert_remove.php';
		break;

	case 'auth':
		include 'auth.php'; // untuk mengambil foto user yang ingin men-submit gambar
		break;

	case 'logout': // Halaman untuk logout bagi user yang sudah login
		user_logout($user->id);
		redirect(_URL);
		break;
	case 'password': // Untuk mengganti password dari user yg saat itu login, Jika anda mensetting login menggunakan thirdparty semisal google/facebook/yahoo dll untuk login, maka user hanya bisa merubah password mereka di thirdparty tersebut
		include 'admin/user.password.php';
		break;
	case 'account': // Data informasi profil dari user yang sudah login. Jadi pastikan halaman ini hanya bisa diakses oleh user yang telah login dengan menggunakan checkbox Menu Protection di 'Control Panel / Menu Manager'
		include 'account.php';
		break;
	case 'account_image':
		include 'account_image.php';
		break;
	case 'option':
		include 'option.php';
		break;
	case 'help':
		include 'help.php';
		break;
	case 'async':
		include 'async.php';
		break;
	case 'repair':
		include 'repair.php';
		break;
	case 'files':
		include 'files.php';
		break;
	case 'editor_css':
		include 'editor_css.php';
		break;
	case 'push-token':
		include 'push-token.php';
		break;
	case 'push-notif':
		include 'push-notif.php';
		break;
	case 'push-read':
		include 'push-read.php';
		break;
	/* start peaform */
	case 'orderby':
		include 'orderby.php';
		break;
	case 'multifile':
		include 'multifile.php';
		break;
	case 'multiid':
		include 'multiid.php';
		break;
	case 'tags':
	case 'selecttable':
		include 'tags.php';
		break;
	/* end peaform */
	case 'rating':
		include 'rating.php';
		break;
	case 'comment_list':
		include 'comment_list.php';
		break;
	case 'comment_post':
		include 'comment_post.php';
		break;
	case 'comment_publish':
		include 'comment_publish.php';
		break;
	case 'comment_delete':
		include 'comment_delete.php';
		break;
	case 'comment_login':
		include 'comment_login.php';
		break;

	default:
		echo 'Invalid action <b>'.$Bbc->mod['task'].'</b> has been received...';
		break;
}