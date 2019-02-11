<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

_func('date');
$id = @intval($_GET['id']);
if (!empty($id))
{
	$output = "Data not found";
	$data = $db->getRow("SELECT * FROM bbc_content WHERE id={$id}");
	if (!empty($data))
	{
		if ($data['kind_id']=='2')
		{
			if (!empty($_GET['act']) && $_GET['act']=='file')
			{
				$path = _ROOT.'images/modules/content/'.$id.'/';
				if (!empty($data['file']) && is_file($path.$data['file']))
				{
					_func('download');
					download_file($data['file'], $path.$data['file']);
				}else{
					$output = 'Sorry, file not found';
				}
			}else
			if (!empty($data['file_register']))
			{
				$output = '';
				if (!empty($_GET['is_ajax']))
				{
					$page  = @intval($_GET['page']);
					$limit = 5;
					$start = $page*$limit;
					$users = array();
					$r     = $db->getAll("SELECT * FROM `bbc_content_registrant` WHERE `content_id`={$id} ORDER BY id DESC LIMIT {$start}, {$limit}");
					foreach ($r as $d)
					{
						$users[] = array(
							'name'    => $d['name'],
							'email'   => '<a href="mailto:'.$d['email'].'">'.$d['email'].'</a>',
							'phone'   => $d['phone'],
							'address' => $d['address'],
							'created' => timespan($d['created']).' ago'
							);
					}
					output_json(array('users' => $users));
				}else{
					$title = $db->getOne("SELECT title FROM `bbc_content_text` WHERE `content_id`={$id} AND `lang_id`=".lang_id());
					$total = $db->getOne("SELECT COUNT(*) FROM `bbc_content_registrant` WHERE `content_id`={$id}");
					$file  = $data['file_type'] ? $data['file_url'] : $data['file'];
					$excel = array(
						'Registrant' => array(
							array('NO', 'Name', 'Email', 'Phone', 'Address', 'Date')
							),
						'File Data' => array(
							array('Content ID', $id),
							array('Title', $title),
							array('File Name', $file),
							array('File Format', $data['file_format']),
							array('Uploaded On', content_date($data['created'])),
							array('Total Download', items($data['file_hit'], 'time')),
							array('Last Download', content_date($data['file_hit_time'], 'Never')),
							array('Last Downloader IP', $data['file_hit_ip']),
							)
						);
					$limit   = 100;
					$maxpage = ceil($total/$limit);
					for ($i = 0; $i < $maxpage; $i++)
					{
						$start = $i*$limit;
						$r = $db->getAll("SELECT * FROM `bbc_content_registrant` WHERE `content_id`={$id} ORDER BY id DESC LIMIT {$start}, {$limit}");
						foreach ($r as $d)
						{
							$excel['Registrant'][] = array(++$start, $d['name'], $d['email'], $d['phone'], $d['address'], content_date($d['created']));
						}
					}
					_lib('excel')->create($excel)->download(menu_save($title).'.xlsx');
					die();
				}
			}else{
				$output = "You have to check 'User must register to download' option to download registrant";
			}
		}else{
			$output = "This data is not file for user download";
		}
	}
	if (!empty($output))
	{
		echo msg($output, 'danger');
	}
}