<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Module ini akan menampilkan semua data content yang telah dibuat oleh admin, dimana content itu sendiri terdiri dari artikel, galeri, file download, video maupun audio untuk dapat diakses pengunjung situs
switch( $Bbc->mod['task'] )
{
	case 'main':
	case 'list':
		include 'list.php';
		break;
	case 'tag':
		include 'tag.php';
		break;
	case 'type':
		include 'type.php';
		break;
	case 'latest': // Daftar content berdasarkan content terbaru ke yang lama
	case 'popular': // Daftar content berdasarkan popularitas, adapun untuk membatasi lama rentang popular bisa anda tambahkan lama waktu (contoh: 1week, 2months, 1year). Semisal ingin menampilkan content yang popular dalam 2 pekan terakhir maka urlnya adalah "index.php?mod=content.popular&id=2weeks"
		include 'popular.php';
		break;
	case 'category':
		include 'category.php';
		break;
	case 'rss':
		include 'rss.php';
		break;
	case 'home':
		include 'home.php';
		break;
	case 'detail':
		include 'detail.php';
		break;
	case 'detail_download':
		include 'detail_download.php';
		break;
	case 'detail_print':
		include 'detail_print.php';
		break;
	case 'detail_mail':
		include 'detail_mail.php';
		break;
	case 'detail_mail_post':
		include 'detail_mail_post.php';
		break;
	case 'detail_pdf':
		include 'detail_pdf.php';
		break;
	case 'rss_content':
		include 'rss_content.php';
		break;
	case 'search': // menampilkan hasil pencarian beserta form pencarian, adapun lokasi pencarian adalah semua data yang ada di module content ini. module lain tidak ikut dalam pencarian
		include 'search.php';
		break;

	case 'article': // menampilkan semua content artikel
	case 'gallery': // menampilkan semua content gallery
	case 'download': // menampilkan semua content download
	case 'video': // menampilkan semua content video
	case 'audio': // menampilkan semua content audio
		include 'article.php';
		break;

	case 'posted': // menampilkan daftar content yang telah di posting oleh user jika user tersbut telah login, Jadi pastikan halaman ini hanya bisa diakses oleh user yang telah login dengan menggunakan checkbox Menu Protection di 'Control Panel / Menu Manager'
		include 'posted.php';
		break;
	case 'posted_form': // halaman form untuk mem-posting content baru khusus untuk user yang telah login. pastikan halaman ini hanya bisa diakses oleh user yang telah login dengan menggunakan checkbox Menu Protection di 'Control Panel / Menu Manager'
		include 'posted_form.php';
		break;
	case 'posted_form_related':
		include 'admin/content_edit_related.php';
		break;
	case 'posted_downloader':
		include 'admin/content_downloader.php';
		break;

	case 'sitemap':
		include 'sitemap.php';
		break;
	case 'mobilesitemap':
		include 'mobilesitemap.php';
		break;
	case 'cron':
		include 'cron.php';
		break;

	case 'ads':
		include 'ads.php';
		break;
	case 'id':
		include 'id.php';
		break;

	default:
		echo "Invalid action <b>".$Bbc->mod['task']."</b> has been received...";
		break;
}