<?php
class Box_model extends MY_Model
{
	
	public function __construct()
    {
    	parent::__construct();
		
        $this->prefix='gc_';
		
        $this->_TABLES=array('CATEGORIES'=>$this->prefix.'categories',
							'BOXES'=>$this->prefix.'boxes'
						);
		
		$this->_JOINS=array('KEY'=>array('join_type'=>'LEFT','join_field'=>'join1.id=join2.id',
                                           'select'=>'field_names','alias'=>'alias_name'),
                           
                            );        
    }
	function get_boxes($limit = false)
	{
		if($limit)
		{
			$this->db->limit($limit);
		}
		$this->db->order_by('sequence ASC');
		$this->db->from($this->_TABLES['BOXES'].' boxes');
		return $this->db->get()->result();
	}
	
	function get_homepage_box($limit = false)
	{
		$this->db->order_by('sequence ASC');
		$this->db->from($this->_TABLES['BOXES'].' boxes');
		$box=$this->db->get()->result();
		
		$return	= array();
		foreach ($box as $box)
		{
			if ($box->enable_on == '0000-00-00')
			{
				$enable_test	= false;
				$enable			= '';
			}
			else
			{
				$eo			 	= explode('-', $box->enable_on);
				$enable_test	= $eo[0].$eo[1].$eo[2];
				$enable			= $eo[1].'-'.$eo[2].'-'.$eo[0];
			}

			if ($box->disable_on == '0000-00-00')
			{
				$disable_test	= false;
				$disable		= '';
			}
			else
			{
				$do			 	= explode('-', $box->disable_on);
				$disable_test	= $do[0].$do[1].$do[2];
				$disable		= $do[1].'-'.$do[2].'-'.$do[0];
			}

			$curDate		= date('Ymd');

			if (($enable_test && $enable_test > $curDate) || ($disable_test && $disable_test <= $curDate))
			{
				//fails to make it. rewrite this if statement one day to work opposite of how it does.
			}
			else
			{
				$return[]	= $box;
			}
			
			if($limit && $limit <= count($return))
			{
				break;
			}
		}
		return $return;
	}
	
	function get_box($id)
	{
		$this->db->where('id', $id);
		$this->db->from($this->_TABLES['BOXES'].' boxes');
		$result = $this->db->get();
		$result = $result->row();
		
		if ($result)
		{
			if ($result->enable_on == '0000-00-00')
			{
				$result->enable_on = '';
			}
			
			if ($result->disable_on == '0000-00-00')
			{
				$result->disable_on = '';
			}
		
			return $result;
		}
		else
		{ 
			return array();
		}
	}
	
	function delete($id)
	{
		
		$box	= $this->get_box($id);
		if ($box)
		{
			$this->db->where('id', $id);
			$this->db->delete($this->_TABLES['BOXES']);
			
			return 'The "'.$box->title.'" box has been removed.';
		}
		else
		{
			return 'The box could not be found.';
		}
	}
	
	function get_next_sequence()
	{
		$this->db->select('sequence');
		$this->db->order_by('sequence DESC');
		$this->db->limit(1);
		$this->db->from($this->_TABLES['BOXES'].' boxes');
		$result = $this->db->get()->result();
		$result = $result->row();
		if ($result)
		{
			return $result->sequence + 1;
		}
		else
		{
			return 0;
		}
	}
	
	function save($data)
	{
		if(isset($data['id']))
		{
			$this->db->where('id', $data['id']);
			$this->db->update($this->_TABLES['BOXES'], $data);
		}
		else
		{
			$data['sequence'] = $this->get_next_sequence();
			$this->db->insert($this->_TABLES['BOXES'], $data);
		}
	}
	
	function organize($box)
	{
		foreach ($box as $sequence => $id)
		{
			$data = array('sequence' => $sequence);
			$this->db->where('id', $id);
			$this->db->update($this->_TABLES['BOXES'], $data);
		}
	}
}

?>