<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

include_once $Bbc->mod['root'].'custom/demografi/_function.php';
?>

<table border="0" cellspacing="0" cellpadding="0" class="table table-striped">
	<tr>
		<th style="width: 5px;">1</th>
		<td><?php echo survey_demografi_text('Tahun lahir',1);?></td>
		<td style="width: 5px;">:</td>
		<td><?php echo survey_demografi_input(1);?></td>
	</tr>
	<tr>
		<th>2</th>
		<td><?php echo survey_demografi_text('Jenis kelamin',2);?></td>
		<td>:</td>
		<td><?php echo survey_demografi_select('Laki Laki;Perempuan', 2);?></td>
	</tr>
	<tr>
		<th>3</th>
		<td><?php echo survey_demografi_text('Asal kota', 3);?></td>
		<td>:</td>
		<td> <?php echo survey_demografi_input(3);?> </td>
	</tr>
	<tr>
		<th>4</th>
		<td><?php echo survey_demografi_text('Status',4);?></td>
		<td>:</td>
		<td><?php echo survey_demografi_select('Belum/tidak menikah;Menikah', 4);?></td>
	</tr>
	<tr>
		<th>5</th>
		<td><?php echo survey_demografi_text('Pendidikan terakhir',5);?></td>
		<td>:</td>
		<td><?php echo survey_demografi_select('SD/Sederajat;SMP/Sederajat;SMA/Sederajat;Diploma D1/D2/D3;Sarjana S1;Sarjana S2;Sarjana S3;Program Profesi', 5);?></td>
	</tr>
	<tr>
		<th>6</th>
		<td><?php echo survey_demografi_text('Pekerjaan',6);?></td>
		<td>:</td>
		<td><?php echo survey_demografi_select('Pegawai Negeri Sipil;Pegawai Swasta;TNI/Polri;Mahasiswa D3/S1;Mahasiswa S2/S3;Siswa SLTP/SLTA;Wirausahawan;Lainnya', 6);?></td>
	</tr>
	<tr>
		<th>7</th>
		<td><?php echo survey_demografi_text('Pengeluaran per bulan',7);?></td>
		<td>:</td>
		<td><?php echo survey_demografi_input(7, 'Rp.', 1);?></td>
	</tr>
	<tr>
		<th>8</th>
		<td><?php echo survey_demografi_text('Berapa pendapatan',8);?></td>
		<td>:</td>
		<td><?php echo survey_demografi_input(8, 'Rp.', 1);?></td>
	</tr>
	<tr>
		<th>9</th>
		<td><?php echo survey_demografi_text('Mengakses situs berita online pertama kali (tahun)',9);?></td>
		<td>:</td>
		<td><?php echo survey_demografi_input(9);?></td>
	</tr>
	<tr>
		<th>10</th>
		<td><?php echo survey_demografi_text('Tempat mengakses situs berita online tersering',10);?></td>
		<td>:</td>
		<td><?php echo survey_demografi_select('Warung Internet;Rumah;Kampus;Kantor;Lainnya', 10);?></td>
	</tr>
	<tr>
		<th>11</th>
		<td colspan=3><?php echo survey_demografi_text('Rata-rata, berapa jam dalam seminggu Anda mengakses situs berita online',11);?></td>
	</tr>
	<tr>
		<th></th>
		<td colspan=3><?php echo survey_demografi_input(11, 'Jam');?></td>
	</tr>
	<tr>
		<th>12</th>
		<td colspan=3><?php echo survey_demografi_text('Rata-rata, berapa berita yang Anda baca setiap membuka sebuah situs berita online',12);?></td>
	</tr>
	<tr>
		<th></th>
		<td colspan=3><?php echo survey_demografi_input(12, 'Berita');?></td>
	</tr>
	<tr>
		<th>13</th>
		<td colspan=3><?php echo survey_demografi_text('Rata-rata, berapa lama Anda membaca satu berita di sebuah situs berita online',13);?></td>
	</tr>
	<tr>
		<th></th>
		<td colspan=3><?php echo survey_demografi_input(13, 'Menit');?></td>
	</tr>
	<tr>
		<th>14</th>
		<td colspan=3><?php echo survey_demografi_text('Walaupun Anda mengakses situs berita online, apakah Anda tetap berlangganan koran, majalah, tabloid, dll, tetap menonton televisi, dan mendengarkan radio untuk mendapatkan sebuah berita/informasi?',14);?></td>
	</tr>
	<tr>
		<th></th>
		<td colspan=3><?php echo survey_demografi_option('Ya, alasanya_text;Tidak, alasanya_text', 14);?></td>
	</tr>
	<tr>
		<th>15</th>
		<td colspan=3><?php echo survey_demografi_text('Selama mengakses situs berita online, tema rubrik/berita apa yang paling sering Anda baca ?',15);?></td>
	</tr>
	<tr>
		<th></th>
		<td colspan=3><?php echo survey_demografi_checkbox('terkait dengan pendidikan;terkait dengan politik;terkait dengan ekonomi/bisnis;hiburan/hobi (musik, film, fotografi);entertainment (berita/gosip artis);terkait dengan kuliner;terkait dengan otomotif;terkait dengan olah raga;terkait dengan lifestyle (fashion, keluarga, seksualitas);terkait dengan teknologi informasi dan komunikasi (trend teknologi, gadget);terkait dengan wisata;terkait dengan pekerjaan (lowongan/informasi pekerjaan);text', 15);?></td>
	</tr>
	<tr>
		<th>16</th>
		<td colspan=3><?php echo survey_demografi_text('Sebutkan 5 situs berita (media massa) online yang paling sering Anda kunjungi, urutkan dari yang paling sering, tidak harus berbahasa Indonesia',16);?></td>
	</tr>
	<tr>
		<th></th>
		<td colspan=3><?php echo survey_demografi_texts(5, 16);?></td>
	</tr>
	<tr>
		<th>17</th>
		<td colspan=3><b><?php echo survey_demografi_text('Alasan mengapa Anda mengakses situs tersebut',17);?></b><br /><?php echo lang('Urutkan lima dari faktor-faktor berikut yang menurut Anda mempengaruhi Anda mengakses situs media massa online dengan memberi nomor 1 pada kolom ranking untuk faktor yang paling menghambat dan seterusnya sampai nomor 5');?></td>
	</tr>
	<tr>
		<th></th>
		<td colspan=3><?php echo survey_demografi_checkbox('Beritanya selalu ada yang baru (update);Beritanya dapat dipercaya, tidak memihak, dan komprehensif ;Beritanya memenuhi unsur 5W+1H (What, Why, When, Where, Who, How);Lembaga/pengelola situsnya kredibel;Beritanya mudah/enak dibaca;Bahasa yang digunakan mudah/enak dibaca;Situsnya mudah diakses;Nama atau alamat situs (URL)-nya mudah diingat;Tata letak atau desain situsnya tidak membosankan;Akses ke situs tersebut cepat dibanding situs lainnya;Beritanya selalu dilengkapi dengan foto/gambar/ilustrasi;Berita dan rubriknya lengkap;Penulisan judul beritanya menarik saya untuk membaca beritanya secara lengkap;text',17 , 'Faktor');?></td>
	</tr>
	<tr>
		<th>18</th>
		<td colspan=3><b><?php echo survey_demografi_text('Alat atau gadget yang paling sering Anda gunakan untuk akses situs tersebut',18);?></b><br /><?php echo lang('Urutkan dari alat berikut yang menurut Anda paling sering Anda gunakan untuk mengakses situs media massa online dengan memberi nomor 1 pada kolom ranking untuk faktor yang paling menghambat dan seterusnya');?></td>
	</tr>
	<tr>
		<th></th>
		<th colspan=3><?php echo survey_demografi_checkbox('Komputer Personal (dengan CPU);Laptop (notebook);Laptop (netbook);Blackberry;Handphone (dengan fasilitas GPRS, 3G, dll);text', 18, 'Alat');?></th>
	</tr>
</table>
