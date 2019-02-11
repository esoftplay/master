<?php
$denied_module = array('admin', 'blocks', 'images', 'includes', 'modules', 'repair', 'templates');
/*
CARA MEMBUAT MODULE :

1. JIKA MODULE BISA DIAKSES DARI URL
	- {namamodule}/_switch.php = inisialisasi nama task untuk halaman public
		- tambahkan tanda '//' diikuti keterangan di baris sebelum switch($Bbc->mod['task']) untuk keterangan singkat fungsi module (baik di admin ato public nya)
		- setiap `case` (task) yang bisa diakses langsung melalui URL tanpa parameter _GET apapun bisa di kasih keterangan dgn memberi tanda '//' setelah case 'NamaTask': CONTOH ==> case 'list': // menampilkan semua data
		- semua keterangan tersebut akan ditampilkan di Control Panel / Menu Manager ketika akan membuat menu (baik add maupun edit) ketika module dipilih

2. JIKA MODULE HANYA DIPAKAI UNTUK MENAMBAH LIBRARY
	- {namamodule}/_function.php = maka semua module lain bisa memanggil semua function dengan cara: _func({namamodule}, {namafungsi} [, {param1} [, {param2} [...]]]) atau cukup _func({namamodule}) jika hanya ingin include saja
	- {namamodule}/_class.php    = maka semua module lain bisa memanggil class ini dengan cara: $object = _class({namamodule}, [, {param1} [, {param2} [...]]])

3. FILE BANTUAN
	- {namamodule}/_function.php       = semua function yang dibuat harus mempunyai nama dengan awalan (prefix) nama module tersebut. contoh {namamodule}_{namafungsi}
	- {namamodule}/_class.php          = nama class yang dibuat harus dengan struktur {namamodule}_class dan file ini tidak terload otomatis
	- {namamodule}/_setting.php        = ini adalah file yang akan diinclude sebelum _switch di include
	- {namamodule}/_config.php         = file ini akan diinclude di semua module yang ada di project dengan ketentuan tabel `bbc_module` dengan field `is_config`=1 AND `name`={namamodule}
	- {namamodule}/admin/_switch.php   = inisialisasi nama task pada admin area nya
	- {namamodule}/admin/_function.php = file ini berisi fungsi yang hanya bisa diakses oleh admin. (penulisan nama fungsi harus dengan struktur {namamodule}_{namafungsi})
	- {namamodule}/admin/_class.php    = file ini berisi class yang hanya bisa diakses oleh admin. (penulisan nama class harus dengan struktur {namamodule}_class)
	- {namamodule}/admin/_setting.php  = ini adalah file yang akan diinclude sebelum _switch di include

4. HOOK FUNCTIONS
	Function yang akan dipanggil ketika function utama pada framework di panggil, ini berlaku untuk semua module meskipun module tersebut tidak ada di table database (`bbc_module`).
	Digunakan untuk menambahkan/mengganti action default pada framework
	Function tersebut antara lain:
		- alert_add($data)                    = dipanggil ketika ada penambahan notifikasi, sedangkan $data adalah row yang ada di table bbc_alert
		- alert_view($data)                   = dipanggil ketika ada notifikasi untuk module tertentu yang di klik $data adalah row yang ada di table bbc_alert harus return URL di field params, eg: params = '{"url":"public_url", "url_admin":"admin_url"}'
		- site_url($string='', $add_URL=true) = dipanggil ketika ingin mengconvert url misal index.php?mod=modulename.taskname menjadi _URL+'modulename/taskname'
		- seo_uri($id='none')                 = dipanggil ketika ingin mengetahui path url saat itu, jika input ke1 di isi angka (contoh: 2) maka akan mengambil value dari path tsb (contoh hasil: taskname)
		- url_parse($txt_url)                 = dipanggil ketika menambah menu di "Control Panel / Menu Manager" $txt_url sendiri adalah apa yg diinputkan di field "Real Link"
		- user_create_validate($data)         = dipanggil sebelum user dibuat untuk me-validasi apakah data params sudah sesuai kebutuhan ataukah tidak dan return HARUS boolean jika false maka user tidak jadi dibuat, gunakan function user_create_validate_msg($msg); untuk menulis pesan error, dan user_create_validate_msg() untuk menampilkan
		- user_create($user_id)               = dipanggil ketika user dibuat / ketika user konfirmasi email / admin menyetujui pendaftaran
		- user_change($user_id)               = dipanggil ketika user melakukan perubahan data diri di user/account
		- user_delete($user_ids)              = dipanggil ketika user dihapus. input1 dalam fungsi tersebut berisi kumpulan user_id yang akan dihapus (bisa array atau beberapa id yg dipisahkan dengan koma)
		- user_login($user_id)                = dipanggil ketika user login.
		- user_logout($user_id)               = dipanggil ketika user berstatus logout (dengan ketentuan selama batas kemampuan server menyimpan session).
	Contoh penggunaan function hook, semisal anda ingin menghapus semua data dari user di dalam module anda ketika user tersebut dihapus:
	function {namamodule}_user_delete($user_ids)
	{
		ids($user_ids); // ini akan meng implode dengan koma "," hanya jika variable adalah array
		if (!empty($user_ids))
		{
			global $db;
			$db->Execute("DELETE FROM `{namamodule}` WHERE `user_id` IN ($user_ids)");
		}
	}
*/