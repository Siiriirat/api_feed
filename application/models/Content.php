<?php

class Content extends CI_Model{

  var $table = 'site_content';

  function insert($arr)
	{
		$arr['title'] = stripslashes($arr['title']);
		$this->db->insert($this->table, $arr);
		return $this->db->insert_id();
	}

	function update($news_id, $arr)
	{
		$this->db->where('id', $news_id);
		$this->db->update($this->table, $arr);

	}

  function slug($str)
	{
    $this->db->where('title', $str);
    $query = $this->db->get($this->table);
    $rs = $query->result();
		$text = $rs[0]->id;
		return $text;
	}

}
