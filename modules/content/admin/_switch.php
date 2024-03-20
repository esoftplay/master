<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

// Module ini digunakan untuk mengatur semua content anda, dimana content itu sendiri terdiri dari artikel, galeri, file download, video maupun audio untuk pengunjung situs
switch( $Bbc->mod['task'] )
{
	case 'main' : // daftar semua content yang tersedia
	case 'content': // task alias untuk "main"
		include 'content.php';
		break;
	case 'content_add': // menu untuk menambah content baru
		if(@$_GET['type_id'] > 0)	include 'content_edit.php';
		else include 'content_add.php';
		break;
	case 'content_edit':
		include 'content_edit.php';
		break;
	case 'content_edit_menu_create':
		include 'content_edit_menu_create.php';
		break;
	case 'content_edit_menu_delete':
		include 'content_edit_menu_delete.php';
		break;
	case 'content_edit_check':
		include 'content_edit_check.php';
		break;
	case 'content_edit_related':
		include 'content_edit_related.php';
		break;
	case 'content_downloader':
		include 'content_downloader.php';
		break;

	case 'tag':  // Jika type website anda adalah "News Article" dan bukan "Coorperate" maka menu ini untuk menampilkan daftar semua tags yang tersedia untuk content anda
		include 'tag.php';
		break;
	case 'tag_detail':
		include 'tag_detail.php';
		break;

	case 'ads': // Untuk mengatur iklan yang akan ditampilkan jika content diakses dari Aplikasi lain melalui API (Content Ads harus diaktifkan terlebih dahulu di menu "Control Panel / Third Party App" pada Tombol besar paling bawah
		include 'ads.php';
		break;
	case 'ads_edit':
		include 'ads_edit.php';
		break;

	case 'send_notification': // Menu untuk mengirimkan notifikasi ke para pengguna aplikasi jika ada mobile app nya
		include 'send_notification.php';
		break;
	case 'send_notification_content':
		include 'send_notification_content.php';
		break;

	case 'type': // menampilkan daftar "Content Type" untuk membagi semua content kedalam configurasi yang berbeda2 di tiap content type
		include 'type.php';
		break;
	case 'type_edit':
		include 'type_edit.php';
		echo $type_form;
		break;
	case 'type_add':
		include 'type_edit.php';
		echo $type_form;
		break;
	case 'type_edit_menu_create':
		include 'type_edit-menu-available.php';
		die;
		break;

	case 'content_sub':
	case 'content_sub_list':
		$Bbc->mod['task'] = 'content_sub';
		include 'content.php';
		break;
	case 'content_sub_add':
		include 'content_edit.php';
		break;
	case 'content_sub_edit':
		include 'content_edit.php';
		break;
	case 'category_sub':
		include 'category.php';
		break;

	case 'category': // menampilkan daftar category di berbagai content type (jika anda memiliki content type lebih dari satu)
		include 'category.php';
		break;
	case 'category_showtree':
		$bool = (@$_GET['id']=='true') ? 1 : 0;
		$_SESSION['content_category_showtree'] = $bool;
		redirect();
		break;
	case 'category_menu_create':
		include 'category_menu_create.php';
		break;
	case 'category_menu_delete':
		include 'category_menu_delete.php';
		break;

	/*
	UNTUK PEMGGUNAAN DI MODULE LAIN, SILAHKAN INCLUDE comment.php dan comment_edit.php
	dengan sebelumnya menentukan variable:
	$table   = 'bbc_content_comment'; // nama table yang ingin diarahkan
	$i_field = 'content';							// prefix field untuk comment
	$i_func  = 'content_link';				// function yang di panggil untuk mengarah ke link detail data (misal content detail di public)
	 */
	case 'comment': // menampilkan daftar comment yang telah dimasukkan oleh user
		include 'comment.php';
		break;
	case 'comment_edit':
		include 'comment_edit.php';
		break;

	case 'config': // Konfigurasi untuk pengaturan semua content yang akan dan telah di upload
		include 'config.php';
		break;
	case 'config_default':
		include 'config_default.php';
		break;

	case 'delete_pruned':
		include 'delete_pruned.php';
		break;

	default:
		echo 'Invalid action <b>'.$Bbc->mod['task'].'</b> has been received...';
		break;
}