<?php
Class Banner_model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
        $this->prefix='gc_';
        $this->_TABLES=array('CATEGORIES'=>$this->prefix.'categories',
							'CATEGORY_PRODUCTS'=>$this->prefix.'category_products',
							'PRODUCTS'=>$this->prefix.'products',
							'BANNER_COLLECTIONS'=>$this->prefix.'banner_collections',
							'BANNERS'=>$this->prefix.'banners'
							);
		
		$this->_JOINS=array('KEY'=>array('join_type'=>'LEFT','join_field'=>'join1.id=join2.id',
                                           'select'=>'field_names','alias'=>'alias_name'),
                           
                            );        
	}
	
	function get_banners($limit = false)
	{
		if($limit)
		{
			$this->db->limit($limit);
		}
		$this->db->order_by('sequence ASC');
		$this->db->from($this->_TABLES['BANNERS'].' banners');
		return $this->db->get()->result();
	}
	
	function banner_collections()
	{
		$this->db->order_by('name', 'ASC');
		$this->db->from($this->_TABLES['BANNER_COLLECTIONS']. ' banner_collections');
		return $this->db->get()->result();
	}
	
	function banner_collection($banner_collection_id)
	{
		$this->db->where('banner_collection_id', $banner_collection_id);
		$this->db->from($this->_TABLES['BANNER_COLLECTIONS']. ' banner_collections');
		return $this->db->get()->row();
	}
	
	function banner_collection_banners($banner_collection_id, $only_active=false, $limit=5)
	{
		$this->db->where('banner_collection_id', $banner_collection_id);
		$this->db->order_by('sequence', 'ASC');
		$this->db->from($this->_TABLES['BANNERS']. ' banners');
		$banners	= $this->db->get()->result();
		
		if($only_active)
		{
			$return	= array();
			foreach ($banners as $banner)
			{
				if ($banner->enable_date == '0000-00-00')
				{
					$enable_test	= false;
					$enable			= '';
				}
				else
				{
					$eo			 	= explode('-', $banner->enable_date);
					$enable_test	= $eo[0].$eo[1].$eo[2];
					$enable			= $eo[1].'-'.$eo[2].'-'.$eo[0];
				}

				if ($banner->disable_date == '0000-00-00')
				{
					$disable_test	= false;
					$disable		= '';
				}
				else
				{
					$do			 	= explode('-', $banner->disable_date);
					$disable_test	= $do[0].$do[1].$do[2];
					$disable		= $do[1].'-'.$do[2].'-'.$do[0];
				}

				$curDate		= date('Ymd');

				if ( (!$enable_test || $curDate >= $enable_test) && (!$disable_test || $curDate < $disable_test))
				{
					$return[]	= $banner;
				}

				if(count($return) == $limit)
				{
					break;
				}
			}
			
			return $return;
		}
		else
		{
			return $banners;
		}
	}
	
	function banner($id)
	{
		$this->db->where('id', $id);
		$this->db->from($this->_TABLES['BANNERS']. ' banners');
		$result = $this->db->get();
		$result = $result->row();
		
		if ($result)
		{
			if ($result->enable_date == '0000-00-00')
			{
				$result->enable_date = '';
			}
			
			if ($result->disable_date == '0000-00-00')
			{
				$result->disable_date = '';
			}
		
			return $result;
		}
		else
		{ 
			return array();
		}
	}
	
	function save_banner($data)
	{
		if(isset($data['id']))
		{
			$this->db->where('id', $data['id']);
			$this->db->update($this->_TABLES['BANNERS'], $data);
		}
		else
		{
			$data['sequence'] = $this->get_next_sequence($data['banner_collection_id']);
			$this->db->insert($this->_TABLES['BANNERS'], $data);
		}
	}
	
	function save_banner_collection($data)
	{
		if(isset($data['banner_collection_id']) && (bool)$data['banner_collection_id'])
		{
			$this->db->where('banner_collection_id', $data['banner_collection_id']);
			$this->db->update($this->_TABLES['BANNERS_COLLECTIONS'], $data);
		}
		else
		{
			$this->db->insert('banner_collections', $data);
		}
	}
	
	function get_homepage_banners($limit = false)
	{

		$this->db->order_by('sequence ASC');
		$this->db->from($this->_TABLES['BANNERS']. ' banners');
		$banners=$this->db->get()->result();
		$count	= 1;
		foreach ($banners as &$banner)
		{
			if ($banner->enable_on == '0000-00-00')
			{
				$enable_test	= false;
				$enable			= '';
			}
			else
			{
				$eo			 	= explode('-', $banner->enable_on);
				$enable_test	= $eo[0].$eo[1].$eo[2];
				$enable			= $eo[1].'-'.$eo[2].'-'.$eo[0];
			}

			if ($banner->disable_on == '0000-00-00')
			{
				$disable_test	= false;
				$disable		= '';
			}
			else
			{
				$do			 	= explode('-', $banner->disable_on);
				$disable_test	= $do[0].$do[1].$do[2];
				$disable		= $do[1].'-'.$do[2].'-'.$do[0];
			}

			$curDate		= date('Ymd');

			if (($enable_test && $enable_test > $curDate) || ($disable_test && $disable_test <= $curDate))
			{
				unset($banner);
			}
			else
			{
				$count++;
			}
			
			if($limit)
			{
				if($count > $limit)
				{
					continue;
				}				
			}
		}
		return $banners;
	}
	
	function delete_banner($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('banners');
	}
	
	function delete_banner_collection($banner_collection_id)
	{
		$this->db->where('banner_collection_id', $banner_collection_id);
		$this->db->delete('banners');
		
		$this->db->where('banner_collection_id', $banner_collection_id);
		$this->db->delete('banner_collections');
	}
	
	function get_next_sequence($banner_collection_id)
	{
		$this->db->where('banner_collection_id', $banner_collection_id);
		$this->db->select('sequence');
		$this->db->order_by('sequence DESC');
		$this->db->limit(1);
		$this->db->from($this->_TABLES['BANNERS']. ' banners');
		$result = $this->db->get();
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

	function organize($banners)
	{
		foreach ($banners as $sequence => $id)
		{
			$data = array('sequence' => $sequence);
			$this->db->where('id', $id);
			$this->db->update('banners', $data);
		}
	}
}

?>