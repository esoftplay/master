<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

include_once $Bbc->mod['root'].'custom/demografi/_function.php';
?>

<table border="0" cellspacing="0" cellpadding="0" class="questionary">
	<tr>
		<td style="width: 5px;">1</td>
		<td><?php echo survey_demografi_text('Tahun lahir',1);?></td>
		<td style="width: 5px;">:</td>
		<td><?php echo survey_demografi_input(1);?></td>
	</tr>
	<tr>
		<td>2</td>
		<td><?php echo survey_demografi_text('Jenis kelamin',2);?></td>
		<td>:</td>
		<td><?php echo survey_demografi_select('Laki Laki;Perempuan', 2);?></td>
	</tr>
	<tr>
		<td>3</td>
		<td><?php echo survey_demografi_text('Asal kota', 3);?></td>
		<td>:</td>
		<td>
			<?php echo survey_demografi_input(3);?><br />
			<i><?php echo lang('sebutkan kota asal di Indonesia, jika saat ini Anda sedang di luar negeri');?></i>
		</td>
	</tr>
	<tr>
		<td>4</td>
		<td><?php echo survey_demografi_text('Status',4);?></td>
		<td>:</td>
		<td><?php echo survey_demografi_select('Belum/tidak menikah;Menikah', 4);?></td>
	</tr>
	<tr>
		<td>5</td>
		<td><?php echo survey_demografi_text('Pendidikan terakhir',5);?></td>
		<td>:</td>
		<td><?php echo survey_demografi_select('SD/Sederajat;SMP/Sederajat;SMA/Sederajat;Diploma D1/D2/D3;Sarjana S1;Sarjana S2;Sarjana S3;Program Profesi', 5);?></td>
	</tr>
	<tr>
		<td>6</td>
		<td><?php echo survey_demografi_text('Pekerjaan',6);?></td>
		<td>:</td>
		<td><?php echo survey_demografi_select('Pegawai Negeri Sipil;Pegawai Swasta;TNI/Polri;Mahasiswa D3/S1;Mahasiswa S2/S3;Siswa SLTP/SLTA;Wirausahawan;Lainnya', 6);?></td>
	</tr>
	<tr>
		<td>7</td>
		<td><?php echo survey_demografi_text('Pengeluaran per bulan',7);?></td>
		<td>:</td>
		<td><?php echo lang('Rp.').survey_demografi_input(7);?></td>
	</tr>
	<tr>
		<td>8</td>
		<td><?php echo survey_demografi_text('Berapa uang saku',8);?></td>
		<td>:</td>
		<td><?php echo lang('Rp.').survey_demografi_input(8);?><br /><i><?php echo lang('(jika masih menjadi tanggungan orang tua) atau penghasilan Anda per bulan');?></i></td>
	</tr>
	<tr>
		<td>9</td>
		<td><?php echo survey_demografi_text('Mengakses situs berita online pertama kali (tahun)',9);?></td>
		<td>:</td>
		<td><?php echo survey_demografi_input(9, ' size="5"');?></td>
	</tr>
	<tr>
		<td>10</td>
		<td><?php echo survey_demografi_text('Tempat mengakses situs berita online tersering',10);?></td>
		<td>:</td>
		<td><?php echo survey_demografi_select('Warung Internet;Rumah;Kampus;Kantor;Lainnya', 10);?></td>
	</tr>
	<tr>
		<td>11</td>
		<td colspan=3><?php echo survey_demografi_text('Selain tempat tersebut pada nomor 10. dimana lagi?',11);?></td>
	</tr>
	<tr>
		<td></td>
		<td colspan=3><?php echo survey_demografi_checkbox('Warung Internet;Rumah;Kampus;Kantor;text', 11);?></td>
	</tr>
	<tr>
		<td>12</td>
		<td colspan=3><?php echo survey_demografi_text('Rata-rata, berapa jam dalam seminggu Anda mengakses situs berita online',12);?></td>
	</tr>
	<tr>
		<td></td>
		<td colspan=3><?php echo survey_demografi_input(12, ' size="5"');?> <?php echo lang('Jam');?></td>
	</tr>
	<tr>
		<td>13</td>
		<td colspan=3><?php echo survey_demografi_text('Rata-rata, berapa berita yang Anda baca setiap membuka sebuah situs berita online',13);?></td>
	</tr>
	<tr>
		<td></td>
		<td colspan=3><?php echo survey_demografi_input(13, ' size="5"');?> <?php echo lang('Berita');?></td>
	</tr>
	<tr>
		<td>14</td>
		<td colspan=3><?php echo survey_demografi_text('Rata-rata, berapa lama Anda membaca satu berita di sebuah situs berita online',14);?></td>
	</tr>
	<tr>
		<td></td>
		<td colspan=3><?php echo survey_demografi_input(14, ' size="5"');?> <?php echo lang('Menit');?></td>
	</tr>
	<tr>
		<td>15</td>
		<td colspan=3><?php echo survey_demografi_text('Rata-rata, selama mengakses situs berita online, jika dihitung dengan uang, berapa banyak uang yang harus Anda keluarkan untuk akses situs berita online dalam satu bulan?',15);?></td>
	</tr>
	<tr>
		<td></td>
		<td colspan=3><?php echo lang('Rp.');?> <?php echo survey_demografi_input(15, ' size="5"');?></td>
	</tr>
	<tr>
		<td>16</td>
		<td colspan=3><?php echo survey_demografi_text('Walaupun Anda mengakses situs berita online, apakah Anda tetap berlangganan koran, majalah, tabloid, dll, tetap menonton televisi, dan mendengarkan radio untuk mendapatkan sebuah berita/informasi?',16);?></td>
	</tr>
	<tr>
		<td></td>
		<td colspan=3><?php echo survey_demografi_option('Ya, alasanya_text;Tidak, alasanya_text', 16);?></td>
	</tr>
	<tr>
		<td>17</td>
		<td colspan=3><?php echo survey_demografi_text('Selama mengakses situs berita online, tema rubrik/berita apa yang paling sering Anda baca ?',17);?></td>
	</tr>
	<tr>
		<td></td>
		<td colspan=3><?php echo survey_demografi_checkbox('terkait dengan pendidikan;terkait dengan politik;terkait dengan ekonomi/bisnis;hiburan/hobi (musik, film, fotografi);entertainment (berita/gosip artis);terkait dengan kuliner;terkait dengan otomotif;terkait dengan olah raga;terkait dengan lifestyle (fashion, keluarga, seksualitas);terkait dengan teknologi informasi dan komunikasi (trend teknologi, gadget);terkait dengan wisata;terkait dengan pekerjaan (lowongan/informasi pekerjaan);text', 17);?></td>
	</tr>
	<tr>
		<td>18</td>
		<td colspan=3><?php echo survey_demografi_text('Sebutkan 3 rubrik atau tema berita yang paling sering Anda baca (urutkan dari yang paling sering, contoh: politik, kuliner dsb.)',18);?></td>
	</tr>
	<tr>
		<td></td>
		<td colspan=3><?php echo survey_demografi_texts(3, 18);?></td>
	</tr>
	<tr>
		<td>19</td>
		<td colspan=3><?php echo survey_demografi_text('Sebutkan 5 situs berita (media massa) online yang paling sering Anda kunjungi, urutkan dari yang paling sering, tidak harus berbahasa Indonesia',19);?></td>
	</tr>
	<tr>
		<td></td>
		<td colspan=3><?php echo survey_demografi_texts(5, 19);?></td>
	</tr>
	<tr>
		<td>20</td>
		<td colspan=3><b><?php echo survey_demografi_text('Alasan mengapa Anda mengakses situs tersebut',20);?></b><br /><?php echo lang('Urutkan lima dari faktor-faktor berikut yang menurut Anda mempengaruhi Anda mengakses situs media massa online dengan memberi nomor 1 pada kolom ranking untuk faktor yang paling menghambat dan seterusnya sampai nomor 5');?></td>
	</tr>
	<tr>
		<td></td>
		<td colspan=3><?php echo survey_demografi_rangking('Beritanya selalu ada yang baru (update);Beritanya dapat dipercaya, tidak memihak, dan komprehensif ;Beritanya memenuhi unsur 5W+1H (What, Why, When, Where, Who, How);Lembaga/pengelola situsnya kredibel;Beritanya mudah/enak dibaca;Bahasa yang digunakan mudah/enak dibaca;Situsnya mudah diakses;Nama atau alamat situs (URL)-nya mudah diingat;Tata letak atau desain situsnya tidak membosankan;Akses ke situs tersebut cepat dibanding situs lainnya;Beritanya selalu dilengkapi dengan foto/gambar/ilustrasi;Berita dan rubriknya lengkap;Penulisan judul beritanya menarik saya untuk membaca beritanya secara lengkap;text;text;text',20 , 'Faktor');?></td>
	</tr>
	<tr>
		<td>21</td>
		<td colspan=3><b><?php echo survey_demografi_text('Alat atau gadget yang paling sering Anda gunakan untuk akses situs tersebut',21);?></b><br /><?php echo lang('Urutkan dari alat berikut yang menurut Anda paling sering Anda gunakan untuk mengakses situs media massa online dengan memberi nomor 1 pada kolom ranking untuk faktor yang paling menghambat dan seterusnya');?></td>
	</tr>
	<tr>
		<td></td>
		<td colspan=3><?php echo survey_demografi_rangking('Komputer Personal (dengan CPU);Laptop (notebook);Laptop (netbook);Blackberry;Handphone (dengan fasilitas GPRS, 3G, dll);text;text', 21, 'Alat');?></td>
	</tr>
</table>
