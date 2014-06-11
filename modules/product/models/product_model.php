<?php

class Product_model extends MY_Model
{
	public function __construct()
    {
    	parent::__construct();
        $this->prefix='gc_';
        $this->_TABLES=array('CATEGORIES'=>$this->prefix.'categories',
							'CATEGORY_PRODUCTS'=>$this->prefix.'category_products',
							'PRODUCTS'=>$this->prefix.'products'
		);
		$this->_JOINS=array('KEY'=>array('join_type'=>'LEFT','join_field'=>'join1.id=join2.id',
                                           'select'=>'field_names','alias'=>'alias_name'),
                           
                            );        
    }
	
	function product_autocomplete($name, $limit)
	{
		$this->db->like('name', $name);
		$this->db->from($this->_TABLES['PRODUCTS']. ' products');
		$this->db->limit($limit);
		return $this->db->get()->result();
	}
	
	function products($data=array(), $return_count=false)
	{
		if(empty($data))
		{
			//if nothing is provided return the whole shabang
			$this->get_all_products();
		}
		else
		{
			//grab the limit
			if(!empty($data['rows']))
			{
				$this->db->limit($data['rows']);
			}
			
			//grab the offset
			if(!empty($data['page']))
			{
				$this->db->offset($data['page']);
			}
			
			//do we order by something other than category_id?
			if(!empty($data['order_by']))
			{
				//if we have an order_by then we must have a direction otherwise KABOOM
				$this->db->order_by($data['order_by'], $data['sort_order']);
			}
			
			//do we have a search submitted?
			if(!empty($data['term']))
			{
				$search	= json_decode($data['term']);
				//if we are searching dig through some basic fields
				if(!empty($search->term))
				{
					$this->db->like('name', $search->term);
					$this->db->or_like('description', $search->term);
					$this->db->or_like('excerpt', $search->term);
					$this->db->or_like('sku', $search->term);
				}
				
				if(!empty($search->category_id))
				{
					//lets do some joins to get the proper category products
					$this->db->join($this->_TABLES['CATEGORY_PRODUCTS']. ' category_products', 'category_products.product_id=products.id', 'right');
					$this->db->where('category_products.category_id', $search->category_id);
					$this->db->order_by('sequence', 'ASC');
				}
			}
			
			if($return_count)
			{
				return $this->db->count_all_results($this->_TABLES['PRODUCTS']);
			}
			else
			{

				$this->db->from($this->_TABLES['PRODUCTS']. ' products');
				return $this->db->get()->result();
			}
			
		}
	}
	
	function get_all_products()
	{
		//sort by alphabetically by default
		$this->db->order_by('name', 'ASC');
		$this->db->from($this->_TABLES['PRODUCTS']. ' products');
		$result	= $this->db->get();

		return $result->result();
	}
	
	function get_filtered_products($product_ids, $limit = false, $offset = false)
	{
		
		if(count($product_ids)==0)
		{
			return array();
		}
		
		$this->db->select('id, LEAST(IFNULL(NULLIF(saleprice, 0), price), price) as sort_price', false)->from($this->_TABLES['PRODUCTS']);
		
		if(count($product_ids)>1)
		{
			$querystr = '';
			foreach($product_ids as $id)
			{
				$querystr .= 'id=\''.$id.'\' OR ';
			}
		
			$querystr = substr($querystr, 0, -3);
			
			$this->db->where($querystr, null, false);
			
		} else {
			$this->db->where('id', $product_ids[0]);
		}
		
		$result	= $this->db->limit($limit)->offset($offset)->get()->result();

		//die($this->db->last_query());

		$contents	= array();
		$count		= 0;
		foreach ($result as $product)
		{

			$contents[$count]	= $this->get_product($product->id);
			$count++;
		}

		return $contents;
		
	}
	
	function get_products($category_id = false, $limit = false, $offset = false, $by=false, $sort=false)
	{
		//if we are provided a category_id, then get products according to category
		if ($category_id)
		{
			$this->db->select('category_products.*, products.*, LEAST(IFNULL(NULLIF(saleprice, 0), price), price) as sort_price', false)->from($this->_TABLES['CATEGORY_PRODUCTS']. ' category_products')->join($this->_TABLES['PRODUCTS'].' products', 'category_products.product_id=products.id')->where(array('category_id'=>$category_id, 'enabled'=>1));

			$this->db->order_by($by, $sort);
			
			$result	= $this->db->limit($limit)->offset($offset)->get()->result();
			
			return $result;
		}
		else
		{
			//sort by alphabetically by default
			$this->db->order_by('name', 'ASC');
			$this->db->from($this->_TABLES['PRODUCTS']. ' products');
			$result	= $this->db->get();

			return $result->result();
		}
	}
	
	function count_all_products()
	{
		$this->db->from($this->_TABLES['PRODUCTS']. ' products');
		return $this->db->count_all_results();
	}
	
	function count_products($id)
	{
		return $this->db->select('product_id')->from($this->_TABLES['CATEGORY_PRODUCTS']. ' category_products')->join($this->_TABLES['PRODUCTS']. ' products', 'category_products.product_id=products.id')->where(array('category_id'=>$id, 'enabled'=>1))->count_all_results();
	}

	function get_product($id, $related=true)
	{
		$result	= $this->db->get_where('products', array('id'=>$id))->row();
		if(!$result)
		{
			return false;
		}

		$related	= json_decode($result->related_products);
		
		if(!empty($related))
		{
			//build the where
			$where = array();
			foreach($related as $r)
			{
				$where[] = '`id` = '.$r;
			}

			$this->db->where('('.implode(' OR ', $where).')', null);
			$this->db->where('enabled', 1);
			$this->db->from($this->_TABLES['PRODUCTS']. ' products');
			$result->related_products	= $this->db->get()->result();
		}
		else
		{
			$result->related_products	= array();
		}
		$result->categories			= $this->get_product_categories($result->id);

		return $result;
	}

	function get_product_categories($id)
	{
		$this->db->from($this->_TABLES['CATEGORY_PRODUCTS']. ' category_products');
		$this->db->where('product_id', $id)->join($this->_TABLES['CATEGORIES'].' categories', 'categories.id = categories.id');

		return $this->db->get()->result();
	}

	function get_slug($id)
	{
		return $this->db->get_where('products', array('id'=>$id))->row()->slug;
	}

	function check_slug($str, $id=false)
	{
		$this->db->select('slug');
		$this->db->from('products');
		$this->db->where('slug', $str);
		if ($id)
		{
			$this->db->where('id != ', $id);
		}
		$count = $this->db->count_all_results();

		if ($count > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function save($product, $options=false, $categories=false)
	{
		if ($product['id'])
		{
			$this->db->where('id', $product['id']);
			$this->db->update($this->_TABLES['PRODUCTS'], $product);

			$id	= $product['id'];
		}
		else
		{
			$this->db->insert($this->_TABLES['PRODUCTS'], $product);
			$id	= $this->db->insert_id();
		}

		//loop through the product options and add them to the db
		if($options !== false)
		{
			$obj =& get_instance();
			$obj->load->model('Option_model');

			// wipe the slate
			$obj->Option_model->clear_options($id);

			// save edited values
			$count = 1;
			foreach ($options as $option)
			{
				$values = $option['values'];
				unset($option['values']);
				$option['product_id'] = $id;
				$option['sequence'] = $count;

				$obj->Option_model->save_option($option, $values);
				$count++;
			}
		}
		
		if($categories !== false)
		{
			if($product['id'])
			{
				//get all the categories that the product is in
				$cats	= $this->get_product_categories($id);
				
				//generate cat_id array
				$ids	= array();
				foreach($cats as $c)
				{
					$ids[]	= $c->id;
				}

				//eliminate categories that products are no longer in
				foreach($ids as $c)
				{
					if(!in_array($c, $categories))
					{
						$this->db->delete($this->_TABLES['CATEGORY_PRODUCTS'], array('product_id'=>$id,'category_id'=>$c));
					}
				}
				
				//add products to new categories
				foreach($categories as $c)
				{
					if(!in_array($c, $ids))
					{
						$this->db->insert($this->_TABLES['CATEGORY_PRODUCTS'], array('product_id'=>$id,'category_id'=>$c));
					}
				}
			}
			else
			{
				//new product add them all
				foreach($categories as $c)
				{
					$this->db->insert($this->_TABLES['CATEGORY_PRODUCTS'], array('product_id'=>$id,'category_id'=>$c));
				}
			}
		}
		
		
		//return the product id
		return $id;
	}
	
	function delete_product($id)
	{
		// delete product 
		$this->db->where('id', $id);
		$this->db->delete($this->_TABLES['PRODUCTS']);
		
		
		
		//delete references in the product to category table
		$this->db->where('product_id', $id);
		$this->db->delete($this->_TABLES['CATEGORY_PRODUCTS']);
		
		// delete coupon reference
		$this->db->where('product_id', $id);
		$this->db->delete('coupons_products');

	}

	function add_product_to_category($product_id, $optionlist_id, $sequence)
	{
		$this->db->insert($this->_TABLES['CATEGORY_PRODUCTS'], array('product_id'=>$product_id, 'category_id'=>$category_id, 'sequence'=>$sequence));
	}

	function search_products($term, $limit=false, $offset=false, $by=false, $sort=false)
	{
		$results		= array();
		
		$this->db->select('*, LEAST(IFNULL(NULLIF(saleprice, 0), price), price) as sort_price', false);
		//this one counts the total number for our pagination
		$this->db->where('enabled', 1);
		$this->db->where('(name LIKE "%'.$term.'%" OR description LIKE "%'.$term.'%" OR excerpt LIKE "%'.$term.'%" OR sku LIKE "%'.$term.'%")');
		$results['count']	= $this->db->count_all_results('products');


		$this->db->select('*, LEAST(IFNULL(NULLIF(saleprice, 0), price), price) as sort_price', false);
		//this one gets just the ones we need.
		$this->db->where('enabled', 1);
		$this->db->where('(name LIKE "%'.$term.'%" OR description LIKE "%'.$term.'%" OR excerpt LIKE "%'.$term.'%" OR sku LIKE "%'.$term.'%")');
		
		if($by && $sort)
		{
			$this->db->order_by($by, $sort);
		}
		$this->db->from($this->_TABLES['PRODUCTS']. ' products');
		$this->db->limit($limit, $offset);
		$results['products'] = $this->db->get()->result();
		
		return $results;
	}

	// Build a cart-ready product array
	function get_cart_ready_product($id, $quantity=false)
	{
		$product	= $this->db->get_where($this->_TABLES['PRODUCTS'], array('id'=>$id))->row();
		
		//unset some of the additional fields we don't need to keep
		if(!$product)
		{
			return false;
		}
		
		$product->base_price	= $product->price;
		
		if ($product->saleprice != 0.00)
		{ 
			$product->price	= $product->saleprice;
		}
		
		
		// Some products have n/a quantity, such as downloadables
		//overwrite quantity of the product with quantity requested
		if (!$quantity || $quantity <= 0 || $product->fixed_quantity==1)
		{
			$product->quantity = 1;
		}
		else
		{
			$product->quantity = $quantity;
		}
		
		
		// attach list of associated downloadables
		$product->file_list	= $this->Digital_Product_model->get_associations_by_product($id);
		
		return (array)$product;
	}
}

?>