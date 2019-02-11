<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

class agenda_class {

	function __construct()
	{
		global $db, $sys;
		$this->r_cat		= agenda_cat();
		$this->limit		= 12;
		$this->no_empty	= 1;
		$this->return		= '';
		$this->db				=& $db;
		$this->sys			=& $sys;
	}

	function main($id = 0)
	{
		$this->is_return = 1;
		$output = $this->events();
		$out		= $this->routine();
		if(!empty($out))
		{
			$output .= $out;
		}
		echo $output;
	}
	function calendar($year = 'none', $month = 'none', $page=0)
	{
		$this->is_return= 1;
		$this->no_empty	= 0;
		echo agenda_calendar($year, $month).'<br />';
		echo $this->events($year, $month, 'none', $page);
	}
	function routine($cat = 0)
	{
		$output = '';
		$r_cat = agenda_cat();
		unset($r_cat[1]);
		if(in_array(ucwords($cat), $r_cat)) {
			$output .= $this->$cat();
		}else{
			$this->no_empty= '0';
			foreach($r_cat AS $cat)
				$output .= $this->$cat();
		}
		return $output;
	}
	function events($year = 'none', $month = 'none', $date='none', $page = 0)
	{
		$this->time_format = $this->get_time_format(1);
		if($year == 'none' && $month == 'none' && $date == 'none')
		{
			return $this->_table(1, $page, " AND DATE(end_date) >= CURDATE()");
		}
		$year = $this->_setnumber($year, date('Y'));
		$month= $this->_setnumber($month, date('m'));
		if($date != 'none'){
			$date = $this->_setnumber($date, date('d'));
			$add_sql = "AND DATE_FORMAT(start_date,'%d')='$date'";
		}else $add_sql = '';
		return $this->_table(1, $page, " AND DATE(`end_date`) >= CURDATE() $add_sql");
	}
	function daily($page = 0)
	{
		$this->time_format = $this->get_time_format(2);
		return $this->_table(2, $page);
	}
	function weekly($page = 0)
	{
		$this->time_format = $this->get_time_format(3);
		return $this->_table(3, $page);
	}
	function monthly($page = 0)
	{
		$this->time_format = $this->get_time_format(4);
		return $this->_table(4, $page);
	}
	function yearly($page = 0)
	{
		$this->time_format = $this->get_time_format(5);
		return $this->_table(5, $page);
	}
	function _table($cat_id, $page = 0, $where = '')
	{
		$start = intval($page) * $this->limit;
		ob_start();
		$q = "SELECT SQL_CALC_FOUND_ROWS a.*, t.title, t.intro FROM agenda AS a LEFT JOIN bbc_content_text AS t ON (t.content_id=a.content_id
		AND t.lang_id=".lang_id().")	WHERE publish=1 AND cat_id=$cat_id $where ORDER BY `start_date` DESC LIMIT $start, $this->limit";
		$r = $this->db->getAll($q);
		if($this->db->Affected_rows())
		{
			$total = $this->db->GetOne("SELECT FOUND_ROWS()");
		?>
		<h1><?php echo lang(agenda_cat($cat_id).' Agenda');?></h1>
		<ul class="comment_list">
<?php foreach((array)$r AS $data)
		{	?>
			<li>
				<div class="comment_list-content">
					<div class="brief">
						<a href="<?php echo content_link($data['content_id'], $data['title']);?>"><b><?php echo $data['title'];?></b></a>
						<span>
							<?php							$i_start = strtotime($data['start_date'].' '.$data['start_hour'].':'.$data['start_minute']);
							$i_end	 = strtotime($data['end_date'].' '.$data['end_hour'].':'.$data['end_minute']);
							echo date($this->time_format, $i_start);
							if($i_end > $i_start)	echo ' - '.date($this->time_format, $i_end);
							?>
						</span>
						<p><?php echo $data['intro'];?></p>
					</div>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
			</li>
			<?php
		}	?>
			<div class="clear"></div>
		</ul>
		<?php		echo page_list($total, $this->limit, $page );
		}elseif($this->no_empty) echo msg(lang('No Schedule'));
		$output = ob_get_contents();
		ob_end_clean();
		if(isset($this->is_return) && $this->is_return == 1) {
			return $output;
		}else{
			echo $output;
		}
	}
	function get_time_format($i)
	{
		$r = array(
			'd M Y | H:i', 'd M Y | H:i', 'H:i', 'D | H:i', 'jS | H:i', 'M jS | H:i'
		);
		$out = isset($r[$i]) ? $r[$i] : $r[0];
		return $out;
	}
	function _setnumber($input, $default)
	{
		$strlen = strlen($default);
		if(is_numeric($input) && strlen($input)==$strlen) {
			return $input;
		}else	return $default;
	}
}