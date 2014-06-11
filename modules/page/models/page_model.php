<?php
Class Page_model extends MY_Model
{

	var $joins=array();
    public function __construct()
    {
    	parent::__construct();
        $this->prefix='';
        $this->_TABLES=array('PAGES'=>$this->prefix.'pages');
		$this->_JOINS=array('KEY'=>array('join_type'=>'LEFT','join_field'=>'join1.id=join2.id',
                                           'select'=>'field_names','alias'=>'alias_name'),
                           
                            );        
    }

	/********************************************************************
	Page functions
	********************************************************************/
	function get_pages($parent = 0)
	{
		$this->db->order_by('sequence', 'ASC');
		$this->db->where('parent_id', $parent);
		$this->db->from($this->_TABLES['PAGES']. ' pages');
		$result = $this->db->get()->result();
		
		$return	= array();
		foreach($result as $page)
		{

			// Set a class to active, so we can highlight our current page
			if($this->uri->segment(1) == $page->slug) {
				$page->active = true;
			} else {
				$page->active = false;
			}

			$return[$page->id]				= $page;
			$return[$page->id]->children	= $this->get_pages($page->id);
		}
		
		return $return;
	}

	function get_pages_tiered()
    {
		$this->db->order_by('sequence', 'ASC');
		$this->db->order_by('title', 'ASC');
		$this->db->from($this->_TABLES['PAGES']. ' pages');
		$pages = $this->db->get()->result();
		
		$results	= array();
		foreach($pages as $page)
		{
			$results[$page->parent_id][$page->id] = $page;
		}
		
		return $results;
	}

	function get_page($id)
	{
		$this->db->where('id', $id);
		$this->db->from($this->_TABLES['PAGES']. ' pages');
		$result = $this->db->get()->row();
		
		return $result;
	}
	
	function get_slug($id)
	{
		$page = $this->get_page($id);
		if($page) 
		{
			return $page->slug;
		}
	}
	
	function save($data)
	{
		if($data['id'])
		{
			$this->db->where('id', $data['id']);
			$this->update('PAGES', $data,array());
			return $data['id'];
		}
		else
		{
			$this->insert('PAGES', $data);
			return $this->db->insert_id();
		}
	}
	
	function delete_page($id)
	{
		//delete the page
		$this->db->where('id', $id);
		$this->delete('PAGES',array());
	
	}
	
	function get_page_by_slug($slug)
	{
		$this->db->where('slug', $slug);
		$this->db->from($this->_TABLES['PAGES']. ' pages');
		$result = $this->db->get()->row();
		
		return $result;
	}
}