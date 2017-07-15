<?php
/* * ******************************************************************
* User model for Mobile Api 
---------------------------------------------------------------------
* @ Added by                 : Subhankar 
* @ Framework                : CodeIgniter
* @ Added Date               : 02-03-2016
---------------------------------------------------------------------
* @ Details                  : It Cotains all the api related methods
---------------------------------------------------------------------
***********************************************************************/
class Product_model extends CI_Model{

    public $_master_configurations = 'master_configurations';
    function __construct(){
        //load the parent constructor
        parent::__construct();        
        $this->tables = $this->config->item('tables'); 
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getAllProduct()
    * @ Added Date               : 20-05-2016
    * @ Added By                 : Subhankar    
    * -----------------------------------------------------------------
    * @ Description              : get all Agent code
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function getAllProduct($param = array()){
        $this->db->select($this->tables['master_products'].'.*,'.$this->tables['master_profession_types'].'.profession_type,'.$this->tables['master_payment_types'].'.payment_type'); 

        $this->db->join($this->tables['master_profession_types'], $this->tables['master_profession_types'].'.id = '.$this->tables['master_products'].'.fk_profession_type_id', 'left');
        $this->db->join($this->tables['master_payment_types'], $this->tables['master_payment_types'].'.id = '.$this->tables['master_products'].'.fk_payment_type_id', 'left');

        if(!empty($param['searchByProfession'])){
            $this->db->like($this->tables['master_products'].'.fk_profession_type_id', $param['searchByProfession']);
        }

        if(!empty($param['order_by']) && !empty($param['order'])){
            $this->db->order_by($this->tables['master_products'].'.'.$param['order_by'], $param['order']);
        } 

        if(!empty($param['page']) && !empty($param['page_size'])){
            $limit = $param['page_size'];
            $offset = $limit*($param['page']-1);
            $this->db->limit($limit, $offset);
        }

        $result = $this->db->get($this->tables['master_products'])->result_array();
        //echo $this->db->last_query();
        return $result;
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getAllProductCount()
    * @ Added Date               : 20-05-2016
    * @ Added By                 : Subhankar    
    * -----------------------------------------------------------------
    * @ Description              : get all Agent code count
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function getAllProductCount($param = array()){
        //$this->db->select('COUNT('.$this->tables['master_products'].'.id) as total');     
        $this->db->select($this->tables['master_products'].'.id');     

        $this->db->join($this->tables['master_profession_types'], $this->tables['master_profession_types'].'.id = '.$this->tables['master_products'].'.fk_profession_type_id', 'left');
        $this->db->join($this->tables['master_payment_types'], $this->tables['master_payment_types'].'.id = '.$this->tables['master_products'].'.fk_payment_type_id', 'left');

        if(!empty($param['searchByProfession'])){
            $this->db->like($this->tables['master_products'].'.fk_profession_type_id', $param['searchByProfession']);
        }

        if(!empty($param['order_by']) && !empty($param['order'])){
            $this->db->order_by($this->tables['master_products'].'.'.$param['order_by'], $param['order']);
        } 

        $result = $this->db->count_all_results($this->tables['master_products']);
        //echo $this->db->last_query();
        return $result;
    }
/*
    * --------------------------------------------------------------------------
    * @ Function Name            : getProductDetail()
    * @ Added Date               : 20-05-2016
    * @ Added By                 : Subhankar    
    * -----------------------------------------------------------------
    * @ Description              : get all Agent code
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function getProductDetail($param = array()){
            
        $this->db->select($this->tables['master_products'].'.*, '.$this->tables['master_profession_types'].'.profession_type, '.$this->tables['master_payment_types'].'.payment_type, '.$this->tables['master_products'].'.id AS p_id');

        $this->db->join($this->tables['master_profession_types'], $this->tables['master_profession_types'].'.id = '.$this->tables['master_products'].'.fk_profession_type_id', 'left');
        $this->db->join($this->tables['master_payment_types'], $this->tables['master_payment_types'].'.id = '.$this->tables['master_products'].'.fk_payment_type_id', 'left');
        
        $this->db->where($this->tables['master_products'].'.id', $param['id']);
        $result = $this->db->get($this->tables['master_products'])->row_array();
        //echo $this->db->last_query();
        return $result;
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getProductItemDetail()
    * @ Added Date               : 20-05-2016
    * @ Added By                 : Subhankar    
    * -----------------------------------------------------------------
    * @ Description              : get all Agent code
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function getProductItemDetail($param = array()){
        $result = array();
        if($param['state'] == 'payment_interest'){

            $this->db->select($this->tables['master_product_variants'].'.*');            
            $this->db->join($this->tables['master_products'], $this->tables['master_products'].'.id = '.$this->tables['master_product_variants'].'.fk_product_id', 'inner');
            $this->db->where($this->tables['master_product_variants'].'.fk_product_id', $param['id']);
            $result = $this->db->get($this->tables['master_product_variants'])->row_array();

        } else if($param['state'] == 'coin_earning'){

            $this->db->select($this->tables['master_mcoin_earnings'].'.*, '.$this->tables['master_mcoin_activities'].'.activity');

            $this->db->join($this->tables['master_products'], $this->tables['master_products'].'.id = '.$this->tables['master_mcoin_earnings'].'.fk_product_id', 'left')
                        ->join($this->tables['master_mcoin_activities'], $this->tables['master_mcoin_activities'].'.id = '.$this->tables['master_mcoin_earnings'].'.fk_mcoin_activity_id', 'left');

            $this->db->where($this->tables['master_mcoin_earnings'].'.fk_product_id', $param['id'])
                        ->where($this->tables['master_mcoin_activities'].'.is_active', 1);

            $this->db->order_by($this->tables['master_mcoin_activities'].'.id', 'ASC');
            $result = $this->db->get($this->tables['master_mcoin_earnings'])->result_array();

        } else if($param['state'] == 'rewards'){

            $this->db->select($this->tables['master_reward_earnings'].'.*, '.$this->tables['master_reward_activities'].'.activity');

            $this->db->join($this->tables['master_products'], $this->tables['master_products'].'.id = '.$this->tables['master_reward_earnings'].'.fk_product_id', 'left')
                        ->join($this->tables['master_reward_activities'], $this->tables['master_reward_activities'].'.id = '.$this->tables['master_reward_earnings'].'.fk_reward_activity_id', 'left');

            $this->db->where($this->tables['master_reward_earnings'].'.fk_product_id', $param['id'])
                        ->where($this->tables['master_reward_activities'].'.is_active', 1)
                        ->where($this->tables['master_reward_activities'].'.id != ', 1);

            $this->db->order_by($this->tables['master_reward_activities'].'.id', 'ASC');
            $result = $this->db->get($this->tables['master_reward_earnings'])->result_array();

        } else if($param['state'] == 'credit_rating_benefits'){

            $this->db->select($this->tables['master_product_credit_rating_benefits'].'.*');

            $this->db->join($this->tables['master_products'], $this->tables['master_products'].'.id = '.$this->tables['master_product_credit_rating_benefits'].'.fk_product_id', 'left');

            $this->db->where($this->tables['master_product_credit_rating_benefits'].'.fk_product_id', $param['id']);

            $this->db->order_by($this->tables['master_product_credit_rating_benefits'].'.id', 'desc');
            $result = $this->db->get($this->tables['master_product_credit_rating_benefits'])->result_array();

        } else if($param['state'] == 'tier_benefits'){

             $this->db->select($this->tables['master_tier_usage_fee_discounts'].'.*, '.$this->tables['master_user_levels'].'.level_name');

            $this->db->join($this->tables['master_products'], $this->tables['master_products'].'.id = '.$this->tables['master_tier_usage_fee_discounts'].'.fk_product_id', 'left')
                        ->join($this->tables['master_user_levels'], $this->tables['master_user_levels'].'.id = '.$this->tables['master_tier_usage_fee_discounts'].'.fk_user_level_id', 'left');

            $this->db->where($this->tables['master_tier_usage_fee_discounts'].'.fk_product_id', $param['id']);

            $this->db->order_by($this->tables['master_user_levels'].'.level_rank', 'asc');
            $result = $this->db->get($this->tables['master_tier_usage_fee_discounts'])->result_array();
        }
        //echo $this->db->last_query();
        return $result;
    }

   
    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : addProduct
    * @ Added Date               : 06-05-2016
    * @ Added By                 : Subhankar Pramanik
    * -----------------------------------------------------------------
    * @ Description              : add Products
    * -----------------------------------------------------------------
    * @ param                    : array(param)
    * @ return                   : int()
    * -----------------------------------------------------------------
    * 
    */    
    public function addProduct($params){
        $this->db->insert($this->tables['master_products'], $params);
        return $this->db->insert_id();
    }
/*
    * --------------------------------------------------------------------------
    * @ Function Name            : getConfigTaxRate
    * @ Added Date               : 31-10-2016
    * @ Added By                 : Subhankar Pramanik
    * -----------------------------------------------------------------
    * @ Description              : get all tax rates from db
    * -----------------------------------------------------------------
    * @ param                    : array(param)
    * @ return                   : int()
    * -----------------------------------------------------------------
    * 
    */

public function getConfigTaxRate()
    {
        $this->db->select('mct.service_tax_rate as str,
                            mct.swach_bharat_cess_rate as sbcr,
                            mct.krishi_kalyan_cess_rate as kkcr

                             ');
        $result = $this->db->get($this->_master_configurations . ' AS mct' )->row_array();
        //pre($result,1);

        return $result;
    }

    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : addProductItems
    * @ Added Date               : 06-05-2016
    * @ Added By                 : Subhankar Pramanik
    * -----------------------------------------------------------------
    * @ Description              : add Products
    * -----------------------------------------------------------------
    * @ param                    : array(param)
    * @ return                   : int()
    * -----------------------------------------------------------------
    * 
    */    
    public function addProductItems($params){


        $config_data=$this->getConfigTaxRate();
        //product_variant
        $product_variant_arr = array();
        $product_variant_arr['fk_product_id']   = $params['product_id'];
        $product_variant_arr['input_principle'] = $params['principle'];
        $product_variant_arr['input_air']       = '0.0';
        $product_variant_arr['input_npm']       = '0';
        $product_variant_arr['input_lpfp']      = '0.00';
        $product_variant_arr['input_ufp']       = '0.00';
        $product_variant_arr['config_str']      = $config_data['str'];
        $product_variant_arr['config_sbcr']     = $config_data['sbcr'];
        $product_variant_arr['config_kkcr']     = $config_data['kkcr'];
        $product_variant_arr['input_lfr']       = '0.00';
        $product_variant_arr['input_pfpd']      = '0.00';
        $product_variant_arr['input_pprm']      = '0.00';
        $product_variant_arr['calc_mir']        = '0.000';
        $product_variant_arr['calc_lpfa']       = '0';
        $product_variant_arr['calc_arl']        = '0';
        $product_variant_arr['calc_ufa']        = '0';
        $product_variant_arr['calc_tfdb']       = '0';
        $product_variant_arr['calc_tst']        = '0.00';
        $product_variant_arr['calc_rufa']       = '0.00';
        $product_variant_arr['calc_stufa']      = '0.00';
        $product_variant_arr['calc_da']         = '0';
        $product_variant_arr['calc_ra']         = '0';
        $product_variant_arr['calc_lfa']        = '0';
        $product_variant_arr['calc_rlf']        = '0.00';
        $product_variant_arr['calc_stlf']       = '0.00';
        
        if($params['payment_type'] == '2'){
            $product_variant_arr['input_emi_lty']   = '0';
            $product_variant_arr['input_emi_obfpf'] = '0.00';
            $product_variant_arr['calc_emi_tp']     = '0';
            $product_variant_arr['calc_emi_amount'] = '0';
        }
       // pre($product_variant_arr,1);
        $this->db->insert($this->tables['master_product_variants'], $product_variant_arr);           

        //mcoin_earnings
        $master_mcoin_earnings_data = array();
        $mcoin_activity_arr = array('2','3');
        foreach ($mcoin_activity_arr as $mcoin_activity_key => $mcoin_activity_value) {
            $product_mcoin_earnings_arr = array();

            $product_mcoin_earnings_arr['fk_product_id']            = $params['product_id'];
            $product_mcoin_earnings_arr['fk_mcoin_activity_id']     = $mcoin_activity_value;
            $product_mcoin_earnings_arr['non_referred_connections'] = '0';
            $product_mcoin_earnings_arr['referred_connections']     = '0';
            $product_mcoin_earnings_arr['own_activity']             = '0'; 

            $master_mcoin_earnings_data[] = $product_mcoin_earnings_arr;
        }
        $this->db->insert_batch($this->tables['master_mcoin_earnings'], $master_mcoin_earnings_data);


        //reward_earnings
        $master_reward_data = array();
        $reward_activity_arr = array('2','3');
        foreach ($reward_activity_arr as $reward_activity_key => $reward_activity_value) {
            $product_reward_arr = array();

            $product_reward_arr['fk_product_id']            = $params['product_id'];
            $product_reward_arr['fk_reward_activity_id']    = $reward_activity_value;
            $product_reward_arr['reward_point']             = '0';

            $master_reward_data[] = $product_reward_arr;
        }
        $this->db->insert_batch($this->tables['master_reward_earnings'], $master_reward_data);


        //tier_benefits
        $master_tier_benefits_data = array();
        $user_level_arr = array('1','2','3','4');
        foreach ($user_level_arr as $user_level_key => $user_level_value) {
            $product_tier_benefits_arr = array();

            $product_tier_benefits_arr['fk_product_id']            = $params['product_id'];
            $product_tier_benefits_arr['fk_user_level_id']         = $user_level_value;
            $product_tier_benefits_arr['usage_fee_discount_amount']= '0.00';
            $product_tier_benefits_arr['interest_adjustment']      = '0.00';

            $master_tier_benefits_data[] = $product_tier_benefits_arr;
        }
        $this->db->insert_batch($this->tables['master_tier_usage_fee_discounts'], $master_tier_benefits_data);
    }



    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : editProduct()
    * @ Added Date               : 08-09-2016
    * @ Added By                 : Subhankar    
    * -----------------------------------------------------------------
    * @ Description              : get all degree
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function editProduct($param = array()){
        $this->db->where(array('id' => $param['id']));
        $this->db->update($this->tables['master_products'], $param);
        //echo $this->db->last_query();
        //$affected_rows = $this->db->affected_rows(); 
        return $param['id'];
    }




    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : editProductVariant()
    * @ Added Date               : 08-09-2016
    * @ Added By                 : Subhankar    
    * -----------------------------------------------------------------
    * @ Description              : get all degree
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function editProductVariant($param = array()){
        $this->db->where(array('fk_product_id' => $param['fk_product_id']));
        $this->db->update($this->tables['master_product_variants'], $param);
        //echo $this->db->last_query();
        //$affected_rows = $this->db->affected_rows(); 
        return $param['fk_product_id'];
    }



    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : batchUpdateCoinEarning()
    * @ Added Date               : 20-05-2016
    * @ Added By                 : Subhankar    
    * -----------------------------------------------------------------
    * @ Description              : batch Insert / ignore
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function batchUpdateCoinEarning($param = array(), $where_key = ''){  
        return $this->db->update_batch($this->tables['master_mcoin_earnings'], $param, $where_key); 
        //die($this->db->last_query());
    }


   /*
    * --------------------------------------------------------------------------
    * @ Function Name            : batchUpdateRewards()
    * @ Added Date               : 20-05-2016
    * @ Added By                 : Subhankar    
    * -----------------------------------------------------------------
    * @ Description              : batch Insert / ignore
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */    
    public function batchUpdateRewards($param = array(), $where_key = ''){  
        return $this->db->update_batch($this->tables['master_reward_earnings'], $param, $where_key); 
        //die($this->db->last_query());
    }



    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : deleteCreditRatingBenefits()
    * @ Added Date               : 20-05-2016
    * @ Added By                 : Subhankar    
    * -----------------------------------------------------------------
    * @ Description              : Delete Credit Rating Benefits
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function deleteCreditRatingBenefits($param = array()){  
        $this->db->where('id', $param['id']);
        $this->db->delete($this->tables['master_product_credit_rating_benefits']);
        return $affected_rows_count = $this->db->affected_rows();
    }



    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : addProductCreditRatingBenefits
    * @ Added Date               : 06-05-2016
    * @ Added By                 : Subhankar Pramanik
    * -----------------------------------------------------------------
    * @ Description              : add Products
    * -----------------------------------------------------------------
    * @ param                    : array(param)
    * @ return                   : int()
    * -----------------------------------------------------------------
    * 
    */    
    public function addProductCreditRatingBenefits($params){
        $this->db->insert($this->tables['master_product_credit_rating_benefits'], $params);
        return $this->db->insert_id();
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : batchUpdateTierBenefits()
    * @ Added Date               : 20-05-2016
    * @ Added By                 : Subhankar    
    * -----------------------------------------------------------------
    * @ Description              : batch Insert / ignore
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */    
    public function batchUpdateTierBenefits($param = array(), $where_key = ''){  
        return $this->db->update_batch($this->tables['master_tier_usage_fee_discounts'], $param, $where_key); 
        //die($this->db->last_query());
    }

    /*****************************************
     * End of user model
    ****************************************/
}