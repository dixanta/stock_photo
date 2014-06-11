<?php
Class Search_model extends MY_Model
{
	function __construct()
	{
		parent::__construct();
		$this->prefix='gc_';
        $this->_TABLES=array('SEARCH'=>$this->prefix.'search',
							
							);
		
		$this->_JOINS=array('KEY'=>array('join_type'=>'LEFT','join_field'=>'join1.id=join2.id',
                                           'select'=>'field_names','alias'=>'alias_name'),
                           
                            );        
	}
	
	/********************************************************************

	********************************************************************/
	
	function record_term($term)
	{
		$code	= md5($term);
		$this->db->where('code', $code);
		$this->db->from($this->_TABLES['SEARCH'].' search');
		$exists	= $this->db->count_all_results();
		if ($exists < 1)
		{
			$this->db->insert($this->_TABLES['SEARCH'], array('code'=>$code, 'term'=>$term));
		}
		return $code;
	}
	
	function get_term($code)
	{
		$this->db->select('term');
		$this->db->from($this->_TABLES['SEARCH'].' search');
		$result	= $this->db->get_where(array('code'=>$code));
		$result	= $result->row();
		return $result->term;
	}
}

?>