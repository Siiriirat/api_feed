<?php

class Rss extends CI_Model{

  var $table = 'rss';

  public function insert($rss)
  {
      $this->db->where('guid', $rss['guid']);
      $query = $this->db->get($this->table);
      $rs = $query->result();
      if(empty($rs[0]->id)){
        $this->db->insert($this->table, $rss);
        return $this->db->insert_id();
      }
      else{
        return false;
      }
  }
}
