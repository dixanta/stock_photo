<?php
Class Category_model extends MY_Model
{

	var $joins=array();
    public function __construct()
    {
    	parent::__construct();
        $this->prefix='';
        $this->_TABLES=array('CATEGORIES'=>$this->prefix.'categories','CATEGORY_PRODUCTS'=>$this->prefix.'category_products',
							 'PRODUCTS'=>$this->prefix.'products');
		$this->_JOINS=array('KEY'=>array('join_type'=>'LEFT','join_field'=>'join1.id=join2.id',
                                           'select'=>'field_names','alias'=>'alias_name'),
                           
                            );        
    }

    function get_categories($parent = false)
    {
        if ($parent !== false)
        {
            $this->db->where('parent_id', $parent);
        }
        $this->db->select('id');
        $this->db->order_by('sequence', 'ASC');
        
        //this will alphabetize them if there is no sequence
        $this->db->order_by('name', 'ASC');
		$this->db->from($this->_TABLES['CATEGORIES']. ' categories');
        $result = $this->db->get();
        
        $categories = array();
        foreach($result->result() as $cat)
        {
            $categories[]   = $this->get_category($cat->id);
        }
        
        return $categories;
    }
    
	function category_autocomplete($name, $limit)
	{
		return	$this->db->like('name', $name)->get($this->_TABLES['CATEGORIES'], $limit)->result();
	}
	
    function get_categories_tiered($admin = false)
    {
        if(!$admin) $this->db->where('enabled', 1);
        
        $this->db->order_by('sequence');
        $this->db->order_by('name', 'ASC');
		$this->db->from($this->_TABLES['CATEGORIES']. ' categories');
        $categories = $this->db->get()->result();
        
        $results    = array();
        foreach($categories as $category) {

            // Set a class to active, so we can highlight our current category
            if($this->uri->segment(1) == $category->slug) {
                $category->active = true;
            } else {
                $category->active = false;
            }

            $results[$category->parent_id][$category->id] = $category;
        }
        
        return $results;
    }
    
    function get_category($id)
    {
        return $this->db->get_where('categories', array('id'=>$id))->row();
    }
    
    function get_category_products_admin($id)
    {
        $this->db->order_by('sequence', 'ASC');
        $result = $this->db->get_where($this->_TABLES['CATEGORY_PRODUCTS'], array('category_id'=>$id));
        $result = $result->result();
        
        $contents   = array();
        foreach ($result as $product)
        {
            $result2    = $this->db->get_where($this->_TABLES['PRODUCTS'], array('products.id'=>$product->product_id));
            $result2    = $result2->row();
            
            $contents[] = $result2; 
        }
        
		return $contents;
    }
    
    function get_category_products($id, $limit, $offset)
    {
        $this->db->order_by('sequence', 'ASC');
        $result = $this->db->get_where($this->_TABLES['CATEGORY_PRODUCTS'], array('category_id'=>$id), $limit, $offset);
        $result = $result->result();
        
        $contents   = array();
        $count      = 1;
        foreach ($result as $product)
        {
            $result2    = $this->db->get_where($this->_TABLES['PRODUCTS'], array('id'=>$product->product_id));
            $result2    = $result2->row();
            
            $contents[$count]   = $result2;
            $count++;
        }
        
        return $contents;
    }
    
    function organize_contents($id, $products)
    {
        //first clear out the contents of the category
        $this->db->where('category_id', $id);
        $this->delete('CATEGORY_PRODUCTS',array());
        
        //now loop through the products we have and add them in
        $sequence = 0;
        foreach ($products as $product)
        {
            $this->insert('CATEGORY_PRODUCTS', array('category_id'=>$id, 'product_id'=>$product, 'sequence'=>$sequence));
            $sequence++;
        }
    }
    
    function save($category)
    {
        if ($category['id'])
        {
            $this->db->where('id', $category['id']);
            $this->update('CATEGORIES', $category,array());
            
            return $category['id'];
        }
        else
        {
            $this->insert('CATEGORIES', $category);
            return $this->db->insert_id();
        }
    }
    
    function delete_category($id)
    {
        $this->delete('CATEGORIES',array('id'=> $id));
        $this->delete('CATEGORY_PRODUCTS',array('category_id'=> $id));
    }
}