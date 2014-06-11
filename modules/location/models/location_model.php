<?php
class Location_model extends MY_Model 
{
	function __construct()
	{
		
		parent::__construct();
        $this->prefix='';
        $this->_TABLES=array('CATEGORIES'=>$this->prefix.'categories',
							'CATEGORY_PRODUCTS'=>$this->prefix.'category_products',
							'PRODUCTS'=>$this->prefix.'products',
							'COUNTRY_ZONE_AREAS'=>$this->prefix.'country_zone_areas',
							'COUNTRY_ZONES'=>$this->prefix.'country_zones',
							'COUNTRIES'=>$this->prefix.'countries'
							);
							
			$this->_JOINS=array('KEY'=>array('join_type'=>'LEFT','join_field'=>'join1.id=join2.id',
                                           'select'=>'field_names','alias'=>'alias_name'),
                           
                            );   
	}
	
	//zone areas
	function save_zone_area($data)
	{
		if(!$data['id']) 
		{
			$this->db->insert($this->_TABLES['COUNTRY_ZONE_AREAS'], $data);
			return $this->db->insert_id();
		} 
		else 
		{
			$this->db->where('id', $data['id']);
			$this->db->update($this->_TABLES['COUNTRY_ZONE_AREAS'], $data);
			return $data['id'];
		}
	}
	
	function delete_zone_areas($country_id)
	{
		$this->db->where('zone_id', $country_id)->delete($this->_TABLES['COUNTRY_ZONE_AREAS']);
	}
	
	function delete_zone_area($id)
	{
		$this->db->where('id', $id);
		$this->db->delete($this->_TABLES['COUNTRY_ZONE_AREAS']);
	}
	
	function get_zone_areas($country_id) 
	{
		$this->db->where('zone_id', $country_id);
		$this->db->from($this->_TABLES['COUNTRY_ZONE_AREAS']);
		return $this->db->get()->result();
	}
	
	function get_zone_area($id)
	{
		$this->db->where('id', $id);
		$this->db->from($this->_TABLES['COUNTRY_ZONE_AREAS']);
		return $this->db->get()->row();
	}
	
	//zones
	function save_zone($data)
	{
		if(!$data['id']) 
		{
			$this->db->insert($this->_TABLES['COUNTRY_ZONES'], $data);
			return $this->db->insert_id();
		} 
		else 
		{
			$this->db->where('id', $data['id']);
			$this->db->update($this->_TABLES['COUNTRY_ZONES'], $data);
			return $data['id'];
		}
	}
	
	function delete_zones($country_id)
	{
		$this->db->where('country_id', $country_id)->delete($this->_TABLES['COUNTRY_ZONES']);
	}
	
	function delete_zone($id)
	{
		$this->delete_zone_areas($id);
		
		$this->db->where('id', $id);
		$this->db->delete($this->_TABLES['COUNTRY_ZONES']);
	}
	
	function get_zones($country_id) 
	{
		$this->db->where('country_id', $country_id);
		return $this->db->get($this->_TABLES['COUNTRY_ZONES'])->result();
	}
	
	
	function get_zone($id)
	{
		$this->db->where('id', $id);
		return $this->db->get($this->_TABLES['COUNTRY_ZONES'])->row();
	}
	
	
	
	//countries
	function save_country($data)
	{
		if(!$data['id']) 
		{
			$this->db->insert($this->_TABLES['COUNTRIES'], $data);
			return $this->db->insert_id();
		} 
		else 
		{
			$this->db->where('id', $data['id']);
			$this->db->update($this->_TABLES['COUNTRIES'], $data);
			return $data['id'];
		}
	}
	
	function organize_countries($countries)
	{
		//now loop through the products we have and add them in
		$sequence = 0;
		foreach ($countries as $country)
		{
			$this->db->where('id',$country)->update($this->_TABLES['COUNTRIES'], array('sequence'=>$sequence));
			$sequence++;
		}
	}
	
	function get_countries()
	{
		$this->db->order_by('sequence', 'ASC');
		$this->db->from($this->_TABLES['COUNTRIES']);
		return $this->db->get()->result();
	}
	
	function get_country_by_zone_id($id)
	{
		$zone	= $this->get_zone($id);
		return $this->get_country($zone->country_id);
	}
	
	function get_country($id)
	{
		$this->db->where('id', $id);
		$this->db->from($this->_TABLES['COUNTRIES']);
		return $this->db->get()->row();
	}
	
	
	function delete_country($id)
	{
		$this->db->where('id', $id);
		$this->db->delete($this->_TABLES['COUNTRIES']);
	}
	
	
	function get_countries_menu()
	{	
		$countries	= $this->db->order_by('sequence', 'ASC')->where('status', 1)->get($this->_TABLES['COUNTRIES'])->result();
		$return		= array();
		foreach($countries as $c)
		{
			$return[$c->id] = $c->name;
		}
		return $return;
	}
	
	function get_zones_menu($country_id)
	{
		$zones	= $this->db->where(array('status'=>1, 'country_id'=>$country_id))->get($this->_TABLES['COUNTRY_ZONES'])->result();
		$return	= array();
		foreach($zones as $z)
		{
			$return[$z->id] = $z->name;
		}
		return $return;
	}
	
	function has_zones($country_id)
	{
		if(!$country_id)
		{
			return false;
		}
		$count = $this->db->where('country_id', $country_id)->count_all_results($this->_TABLES['COUNTRY_ZONES']);
		if($count > 0)
		{
			return true;
		} else {
			return false;
		}
	}

	/*
	// returns array of strings formatted for select box
	function get_countries_zones()
	{
		$countries = $this->db->get('countries')->result_array();
		
		$list = array();
		foreach($countries as $c)
		{
			if(!empty($c['name']))
			{		
				$zones =  $this->db->where('country_id', $c['id'])->get('country_zones')->result_array();
				$states = array();
				foreach($zones as $z)
				{
					// todo - what to put if there are no zones in a country?
					
					if(!empty($z['code']))
					{
						$states[$z['id']] = $z['name'];
					}
				}
				
				$list[$c['name']] = $states;
			}
		}
		
		return $list;
	}
	*/
}	