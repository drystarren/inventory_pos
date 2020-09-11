<?php

class Login extends Controller {

	function __construct(){
	parent::__construct();
	//$this->output->enable_profiler('FALSE');
	}
	
	//Main Login Screen
	function index()
    {
		$data['main_content'] = 'branch_login';
		$this->load->view('includes/container', $data);	
	}

	//After login main screen
	function index_panel() {
 		$this->load->model('Products_model');		

		$data['main_content'] = 'background_main';
		$this->load->view('includes/container', $data);	
    }   
	
	/** start Login & Logout functions **/
	// Validate Credential username and password
	function validate_credentials1()
	{		
		$this->load->model('membership_model');
		$query = $this->membership_model->validate1();
		
		if($query) // if the user's credentials validated...
		{
			$data = array(
				'username' => $this->input->post('username'),
				'is_logged_in' => true,
				'pos_price_type'=>0
			);
			$this->session->set_userdata($data);
			$this->session->set_userdata('year',date('Y'));
			redirect('login/index_panel');
		}
		else // incorrect username or password
		{
			$data['url1']=$this->uri->segment(3);
			$data['main_content'] = 'error3';
			$this->load->view('includes/container_err', $data);	
		
			$this->load->view('error3', $data);
		}
	}
	
	//logout - redirect login
	function logout()
	{
		$this->session->sess_destroy();
		//$this->load->view('stud_login');
		//redirect("login/index_branch");
		redirect("login/index");
	}
	/** end Login & Logout functions **/
			
	
	/** start item & price registration functions **/	
	//load page details input to edit registration
	function edit_registration($id)
	{
		$this->load->model('Film_model');
		$data['ipos_user'] = $this->Film_model->call_user_by_id($id);
		
		$data['main_content'] = 'register_edit';
		$this->load->view('includes/container', $data);
	}
	
	//update registration edit registration 
	function update_person()
	{
		$this->load->model('membership_model');	
		$this->membership_model->update_person();
		//AUDIT TRAIL
			$page = 'User Registration';
			$action = 'Modify User Information';
			$previous = '';
			$created = '';
			$modified = 'Register User: '.$this->input->post('first_name')." ".$this->input->post('last_name');
			$data['audit_trail'] = $this->membership_model->audit_trail($page,$action,$previous,$created,$modified);
		// END AUDIT TRAIL
		redirect('login/display/ipos_registration');
	}
	
	//load page details input to register
	function register_someone()
	{
		$data['main_content'] = 'register_add';
		$this->load->view('includes/container', $data);
	}
	
	//save registration inputs 
	function register_person()
	{
		$this->load->model('membership_model');	
		$this->membership_model->register_person();
	
		//AUDIT TRAIL
			$page = 'User Registration';
			$action = 'Register User';
			$previous = '';
			$created = 'Register User: '.$this->input->post('first_name')." ".$this->input->post('last_name');
			$modified = '';
			$data['audit_trail'] = $this->membership_model->audit_trail($page,$action,$previous,$created,$modified);
		// END AUDIT TRAIL
		
		redirect('login/display/ipos_registration');
	}
	/** end user registration functions **/
	
	
	/** start  functions **/
	//display function for list of data details
	function display($dbf, $query_id = 0, $sort_by = 'id', $sort_order = 'desc', $offset = 0, $id=0, $pr_type='null') 
	{		
		$this->load->library('encrypt');
		$this->load->model('Film_model');
		
		if($dbf=='ipos_pos_management')
		{$menu='POS Management'; $template='details_pos_main'; $cnt=7;}
		
		if($dbf=='ipos_inventory_main')
		{$menu='Inventory Management'; $template='details_inventory_main'; $cnt=6;}
		
		if($dbf=='ipos_customer_reg')
		{$menu='Customer Registration'; $template='details_customer_reg'; $cnt=5;}
		
		if($dbf=='ipos_supplier_reg')
		{$menu='Supplier Registration'; $template='details_supplier_reg'; $cnt=4;}
		
		if($dbf=='ipos_item_price_maintenance')
		{$menu='Item & Price Maintenance'; $template='details_item_price_main'; $cnt=3;}
		
		if($dbf=='ipos_audittrail')
		{$menu='System Logs'; $template='details_audittrail'; $cnt=2;}

		if($dbf=='ipos_registration')
		{$menu='Register'; $template='details_registration'; $cnt=1;}
		
		$limit = 0;
					
		$this->input->load_query($query_id);
			
		$query_array = array('title' => $this->input->get('title'),);
		
		$data['query_id'] = $query_id;
	
		$dbf=$this->uri->segment(3);
	
		$results = $this->Film_model->search($query_array, $limit, $offset, $sort_by, $sort_order, $cnt);
			
		$data[$dbf] = $results['rows'];
		$data['num_results'] = $results['num_rows'];
		$data['main_content'] = $template;
		
		$data['sort_by'] = $sort_by;
		$data['sort_order'] = $sort_order;	
		$data['url1']=$this->uri->segment(3).'/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7).'/'.$this->uri->segment(8);
		$data['url2']=$this->uri->segment(3);
		$data['title1']=$menu;
	
		$this->load->view('includes/container', $data);		
	}
	

	//search function 					
	function search() 
	{	
		//$url2=$this->uri->segment(3);
		$url3=$this->uri->segment(3);
		//die(print_r($this->session->userdata('branch')));
		$query_array = array('title' => $this->input->post('title'),);
		$query_id = $this->input->save_query($query_array);
		//die(print_r($query_id));
		//redirect("login/display/$url3/$query_id");
		redirect("login/display/$url3/$query_id/id/desc/0/0/".$this->uri->segment(9));
	}
	
	//search function 	
	function search2($id) 
	{	
		//$url2=$this->uri->segment(3);
		$url3=$this->uri->segment(4);
		$query_array = array('title' => $this->input->post('title'),);
		$query_id = $this->input->save_query($query_array);
		redirect("login/display/$url3/$query_id/id/desc/0/".$id);
	}
	
	
	//delete function for all page details list
	function deleteitems() {
		$this->load->model('membership_model');

		//delete selected items from User Registration
		if($this->uri->segment(3) == 'ipos_registration'){
			if (isset($_POST['deleted_items']))
			{	
				$url3=$this->uri->segment(3);
				if ($_POST['deleted_items']) {
					$deleted_items = join(',', $_POST['deleted_items']);
					$deleted_items_audit = explode(',',$deleted_items);
					
					$query = $this->db->select("*")	
							->where_in('id', $deleted_items)
							->from('tbl_user')
							->get()->result();
	
					foreach ($query as $del_item) {
						#audit trail
						$page = 'Delete User';
						$action = 'Delete User:'.$del_item->first_name." ".$del_item->last_name;
						$previous = '';
						$created = '';
						$modified = '';
						$data['audit_trail'] = $this->membership_model->audit_trail($page,$action,$previous,$created,$modified);
						#end audit trail
					}					
					$query = "DELETE FROM tbl_user WHERE id IN ($deleted_items)";
					$result = mysql_query($query); 
					};
					redirect('login/display/ipos_registration/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7).'/'.$this->uri->segment(8));
			}
				redirect('login/display/ipos_registration/');	
		}
		
		//delete selected items from Item & Price Maintenance
		if($this->uri->segment(3) == 'ipos_item_price_maintenance'){
			if (isset($_POST['deleted_items']))
			{	
				$url3=$this->uri->segment(3);
				if ($_POST['deleted_items']) {
					$deleted_items = join(',', $_POST['deleted_items']);
					$deleted_items_price = explode(',',$deleted_items);
					
					$query = $this->db->select("*")	
							->where_in('item_id', $deleted_items)
							->from('tbl_item_price')
							->get()->result();
		
					foreach ($query as $del_item) {
						#audit trail
						$page = 'Delete Item & Price';
						$action = 'Delete Item $ Price Name:'.$del_item->item_name;
						$previous = '';
						$created = '';
						$modified = '';
						$data['audit_trail'] = $this->membership_model->audit_trail($page,$action,$previous,$created,$modified);
						#end audit trail
					}					
					$query = "DELETE FROM tbl_item_price WHERE item_id IN ($deleted_items)";
					$result = mysql_query($query); 
				};
					redirect('login/display/ipos_item_price_maintenance/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7).'/'.$this->uri->segment(8));
			}
			
			redirect('login/display/ipos_item_price_maintenance/');	
		}
		
		//delete selected items from Supplier Maintenance
		if($this->uri->segment(3) == 'ipos_supplier_reg'){
			if (isset($_POST['deleted_items']))
			{	
				$url3=$this->uri->segment(3);
				if ($_POST['deleted_items']) {
					$deleted_items = join(',', $_POST['deleted_items']);
					$deleted_items_price = explode(',',$deleted_items);
					
					$query = $this->db->select("*")	
							->where_in('sup_id', $deleted_items)
							->from('tbl_supplier')
							->get()->result();
		
					foreach ($query as $del_item) {
						#audit trail
						$page = 'Delete Supplier';
						$action = 'Delete Supplier Name:'.$del_item->sup_name;
						$previous = '';
						$created = '';
						$modified = '';
						$data['audit_trail'] = $this->membership_model->audit_trail($page,$action,$previous,$created,$modified);
						#end audit trail
					}					
					$query = "DELETE FROM tbl_supplier WHERE sup_id IN ($deleted_items)";
					$result = mysql_query($query); 
				};
					redirect('login/display/ipos_supplier_reg/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7).'/'.$this->uri->segment(8));
			}
			
			redirect('login/display/ipos_supplier_reg/');	
		}
		
		//delete selected items from Customer Maintenance
		if($this->uri->segment(3) == 'ipos_customer_reg'){
			if (isset($_POST['deleted_items']))
			{	
				$url3=$this->uri->segment(3);
				if ($_POST['deleted_items']) {
					$deleted_items = join(',', $_POST['deleted_items']);
					$deleted_items_price = explode(',',$deleted_items);
					
					$query = $this->db->select("*")	
							->where_in('cus_id', $deleted_items)
							->from('tbl_customer')
							->get()->result();
		
					foreach ($query as $del_item) {
						#audit trail
						$page = 'Delete Customer';
						$action = 'Delete Customer Name:'.$del_item->cus_name;
						$previous = '';
						$created = '';
						$modified = '';
						$data['audit_trail'] = $this->membership_model->audit_trail($page,$action,$previous,$created,$modified);
						#end audit trail
					}					
					$query = "DELETE FROM tbl_customer WHERE cus_id IN ($deleted_items)";
					$result = mysql_query($query); 
				};
					redirect('login/display/ipos_customer_reg/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7).'/'.$this->uri->segment(8));
			}
			
			redirect('login/display/ipos_customer_reg/');	
		}
		
		//delete Inventory Management
		if($this->uri->segment(3) == 'ipos_inventory_main'){
			if (isset($_POST['deleted_items']))
			{	
				$url3=$this->uri->segment(3);
				if ($_POST['deleted_items']) {
					$deleted_items = join(',', $_POST['deleted_items']);
					$deleted_items_price = explode(',',$deleted_items);
					
					$query = $this->db->select("*")	
							->where_in('inv_no', $deleted_items)
							->from('tbl_inventory_main')
							->get()->result();

					foreach ($query as $del_item) {
						#audit trail
						$page = 'Delete Inventory';
						
						$action = 'Delete Inventory No:'.$del_item->inv_no;
						$action .= '- Supplier Name:'.$del_item->inv_supplier_name;
						$action .= '- Date Received:'.$del_item->inv_date_received;
						
						$previous = '';
						$created = '';
						$modified = '';
						$data['audit_trail'] = $this->membership_model->audit_trail($page,$action,$previous,$created,$modified);
						#end audit trail
					}					
					$query = "DELETE FROM tbl_inventory_main WHERE inv_no IN ($deleted_items)";
					$result = mysql_query($query); 
				};
					redirect('login/display/ipos_inventory_main/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7).'/'.$this->uri->segment(8));
			}
			
			redirect('login/display/ipos_inventory_main/');	
		}
		
	}
	
	//print PDF report of Audit Trail
	function pdf_audit($id){
		$comment = "DISPLAY SPECIFIC LOG";
		$this->load->model('Products_model');
		//$this->load->model('Film_model');
		$this->load->model('membership_model');
		
		$data['file_data'] = $this->Products_model->pdf_audit($id);
		//AUDIT TRAIL
			$pre= null;
			$cre= 'viewed system log No:'.$id;
			$mod= null;
			$this->membership_model->audit_trail('System Log','View System Log',$pre,$cre,$mod);
		// END AUDIT TRAIL
		$this->load->view('pdf_audit',$data);
	}
	
	
	/** start item & price registration functions **/
	// load add item price page
	function add_item_price() {
		//die("hellow");
		$data['main_content'] = 'register_item_price_add';
		$this->load->view('includes/container', $data);
	}
	
	//save item price into the database
	function save_item_price() {
		$this->load->model('membership_model');		
		$this->membership_model->db_save_item_price();
		
		
		
		//AUDIT TRAIL
			$page = 'Item & Price Registration';
			$action = 'Register Item & Price Information';
			$previous = '';
			
			$created ="Item Code:".$this->input->post('item_code');
			$created .=" - Item Name:".$this->input->post('item_name');
			$created .=" - Item Unit:".$this->input->post('item_unit');
			$created .=" - Item Volume:".$this->input->post('item_volume');
			$created .=" - Item Type:".$this->input->post('item_type');
			$created .=" - Item Brabd:".$this->input->post('item_brand');
			$created .=" - Item Price 1:".$this->input->post('item_price1');
			$created .=" - Item Price 2:".$this->input->post('item_price2');
			$created .=" - Item Price 3:".$this->input->post('item_price3');
			$created .=" - Item Price 4:".$this->input->post('item_price4');
			
			$item_status = 'Inactive';
			if($this->input->post('status') == 1){
				$item_status = 'Active';
			}	
			$created .= "- Item Status: ".$item_status;
			$modified = '';
			$data['audit_trail'] = $this->membership_model->audit_trail($page,$action,$previous,$created,$modified);
		// END AUDIT TRAIL
		redirect('login/display/ipos_item_price_maintenance');
		
	}
	
	//load the edit item page
	function edit_item_price($id)
	{
		$this->load->model('Film_model');
		$data['ipos_item_price'] = $this->Film_model->call_item_price($id);
		
		// echo "<pre>";
		// die(print_r($data['ipos_item_price']));
		// echo "</pre>";
		
		$data['main_content'] = 'register_item_price_edit';
		$this->load->view('includes/container', $data);
	}
	
	//udpate item and price in the database
	function update_item_price()
	{
		$this->load->model('membership_model');	
		$this->load->model('Film_model');
		
		$id = $this->input->post('item_id_hidden');
		$data['ipos_item_price'] = $this->Film_model->call_item_price($id);
		$this->membership_model->db_update_item_price($id);
		
		//AUDIT TRAIL
			foreach ($data['ipos_item_price'] as $result) {
				$previous ="";
				$modified ="";
				
				//if($result->item_name !== $this->input->post('item_name') ){
					$previous .= " Item Name: ".$result->item_name;
					$modified .= " Item Name: ".$this->input->post('item_name');
				//}
				
				if($result->item_unit !== $this->input->post('item_unit') ){
					$previous .= " - Item Unit: ".$result->item_unit;
					$modified .= " - Item Unit: ".$this->input->post('item_unit');
				}

				if($result->item_volume !== $this->input->post('item_volume') ){
					$previous .= " - Item Volume: ".$result->item_volume;
					$modified .= " - Item Volume: ".$this->input->post('item_volume');
				}
				
				if($result->item_type !== $this->input->post('item_type') ){
					$previous .= " - Item Type: ".$result->item_type;
					$modified .= " - Item Type: ".$this->input->post('item_type');
				}
				
				if($result->item_price1 !== $this->input->post('item_price1') ){
					$previous .= " - Item Price 1: ".$result->item_price1;
					$modified .= " - Item Price 1: ".$this->input->post('item_price1');
				}
				
				if($result->item_price2 !== $this->input->post('item_price2') ){
					$previous .= " - Item Price 2: ".$result->item_price2;
					$modified .= " - Item Price 2: ".$this->input->post('item_price2');
				}
				
				if($result->item_price3 !== $this->input->post('item_price3') ){
					$previous .= " - Item Price 3: ".$result->item_price3;
					$modified .= " - Item Price 3: ".$this->input->post('item_price3');
				}
				
				if($result->item_price4 !== $this->input->post('item_price4') ){
					$previous .= " - Item Price 4: ".$result->item_price4;
					$modified .= " - Item Price 4: ".$this->input->post('item_price4');
				}
				
				if($result->status !== $this->input->post('status') ){
					$prev_status = 'Inactive';
					$mod_status = 'Inactive';

					if($result->status == 1){
						$prev_status = 'Active';
					}
					if($this->input->post('status') == 1){
						$mod_status = 'Active';
					}
					
					$previous .= " - Status: ".$prev_status;
					$modified .= " - Status: ".$mod_status;
				}
				
				$page = 'Item & Price Registration';
				$action = 'Modify Item & Price Information';
				$created = '';
				$data['audit_trail'] = $this->membership_model->audit_trail($page,$action,$previous,$created,$modified);
			}
		// END AUDIT TRAIL
		redirect('login/display/ipos_item_price_maintenance');
	}
	
	/** end item & price registration functions **/
	
	/** start Supplier registration functions **/
	// load add supplier page
	function add_supplier() {
		$data['main_content'] = 'register_supplier_add';
		$this->load->view('includes/container', $data);
	}
	
	//save supplier into the database
	function save_supplier() {
		$this->load->model('membership_model');		
		$this->membership_model->db_save_supplier();
		
		//AUDIT TRAIL
			$page = 'Supplier Registration';
			$action = 'Register Supplier Information';
			$previous = '';
			
			$created ="Sup Code:".$this->input->post('sup_code');
			$created .=" - Sup Name:".$this->input->post('sup_name');
			//$created .=" - Sup Description:".$this->input->post('sup_description');
			$created .=" - Sup Address:".$this->input->post('sup_address');
			$created .=" - Sup Contact No.:".$this->input->post('sup_contact_no');
			
			$sup_status = 'Inactive';
			if($this->input->post('sup_status') == 1){
				$sup_status = 'Active';
			}	
			$created .= "- Sup Status: ".$sup_status;
			
			$modified = '';
			$data['audit_trail'] = $this->membership_model->audit_trail($page,$action,$previous,$created,$modified);
		// END AUDIT TRAIL
		

		redirect('login/display/ipos_supplier_reg');
		
	}
	
	//load the edit supplier page
	function edit_supplier($id)
	{
		$this->load->model('Film_model');
		$data['ipos_supplier'] = $this->Film_model->call_supplier($id);
		$data['main_content'] = 'register_supplier_edit';
		$this->load->view('includes/container', $data);
	}
	
	//udpate supplier in the database
	function update_supplier()
	{
		$this->load->model('membership_model');	
		$this->load->model('Film_model');
		
		$id = $this->input->post('sup_id_hidden');
		$data['ipos_supplier'] = $this->Film_model->call_supplier($id);
		$this->membership_model->db_update_supplier($id);
		
		//die(print_r($data['ipos_supplier']));
		//AUDIT TRAIL
			foreach ($data['ipos_supplier'] as $result) {
				$previous ="";
				$modified ="";
				
				//if($result->sup_name !== $this->input->post('sup_name') ){
					$previous .= " Sup Name: ".$result->sup_name;
					$modified .= " Sup Name: ".$this->input->post('sup_name');
				//}
				
				if($result->sup_description !== $this->input->post('sup_description') ){
					$previous .= "- Sup Description: ".$result->sup_description;
					$modified .= "- Sup Description: ".$this->input->post('sup_description');
				}

				if($result->sup_address !== $this->input->post('sup_address') ){
					$previous .= "- Sup Address: ".$result->sup_address;
					$modified .= "- Sup Address: ".$this->input->post('sup_address');
				}
				
				if($result->sup_contact_no !== $this->input->post('sup_contact_no') ){
					$previous .= "- Sup Contact No.: ".$result->sup_contact_no;
					$modified .= "- Sup Contact No.: ".$this->input->post('sup_contact_no');
				}
			
				
				if($result->sup_status !== $this->input->post('sup_status') ){
					$prev_status = 'Inactive';
					$mod_status = 'Inactive';

					if($result->sup_status == 1){
						$prev_status = 'Active';
					}
					if($this->input->post('sup_status') == 1){
						$mod_status = 'Active';
					}
					
					$previous .= "- Status: ".$prev_status;
					$modified .= "- Status: ".$mod_status;
				}
				
				$page = 'Supplier Registration';
				$action = 'Modify Supplier Information';
				$created = '';
				$data['audit_trail'] = $this->membership_model->audit_trail($page,$action,$previous,$created,$modified);
			}
		// END AUDIT TRAIL
		
		redirect('login/display/ipos_supplier_reg');
	}
	
	/** end Supplier registration functions **/
	
	/** start Customer registration functions **/
	// load add supplier page
	function add_customer() {
		$data['main_content'] = 'register_customer_add';
		$this->load->view('includes/container', $data);
	}
	
		//save supplier into the database
	function save_customer() {
		$this->load->model('membership_model');		
		$this->membership_model->db_save_customer();
		
		//AUDIT TRAIL
			$page = 'Customer Registration';
			$action = 'Register Customer Information';
			$previous = '';
			
			$created ="Cus Code:".$this->input->post('cus_code');
			$created .=" - Cus Name:".$this->input->post('cus_name');
			//$created .=" - Cus Description:".$this->input->post('cus_description');
			$created .=" - Cus Address:".$this->input->post('cus_address');
			$created .=" - Cus Contact No.:".$this->input->post('cus_contact_no');
			$created .=" - Cus Price Status:".$this->input->post('cus_price_type');
			
			$cus_status = 'Inactive';
			if($this->input->post('cus_status') == 1){
				$cus_status = 'Active';
			}	
			$created .= "- Cus Status: ".$cus_status;
			
			$modified = '';
			$data['audit_trail'] = $this->membership_model->audit_trail($page,$action,$previous,$created,$modified);
		// END AUDIT TRAIL
		

		redirect('login/display/ipos_customer_reg');
		
	}
	
	//load the edit customer page
	function edit_customer($id)
	{
		$this->load->model('Film_model');
		$data['ipos_customer'] = $this->Film_model->call_customer($id);
		
		//die(print_r($data['ipos_customer']));
		$data['main_content'] = 'register_customer_edit';
		$this->load->view('includes/container', $data);
	}
	
	//udpate supplier in the database
	function update_customer()
	{
		$this->load->model('membership_model');	
		$this->load->model('Film_model');
		
		$id = $this->input->post('cus_id_hidden');
		$data['ipos_customer'] = $this->Film_model->call_customer($id);
		$this->membership_model->db_update_customer($id);
		
		//die(print_r($data['ipos_supplier']));
		//AUDIT TRAIL
			foreach ($data['ipos_customer'] as $result) {
				$previous ="";
				$modified ="";
				
				//if($result->cus_name !== $this->input->post('cus_name') ){
					$previous .= " Cus Name: ".$result->cus_name;
					$modified .= " Cus Name: ".$this->input->post('cus_name');
				//}
				
				if($result->cus_description !== $this->input->post('cus_description') ){
					$previous .= "- Cus Description: ".$result->cus_description;
					$modified .= "- Cus Description: ".$this->input->post('cus_description');
				}

				if($result->cus_address !== $this->input->post('cus_address') ){
					$previous .= "- Cus Address: ".$result->cus_address;
					$modified .= "- Cus Address: ".$this->input->post('cus_address');
				}
				
				if($result->cus_contact_no !== $this->input->post('cus_contact_no') ){
					$previous .= "- Cus Contact No.: ".$result->cus_contact_no;
					$modified .= "- Cus Contact No.: ".$this->input->post('cus_contact_no');
				}
			
				if($result->cus_price_type !== $this->input->post('cus_price_type') ){
					$previous .= "- Price Type: ".$result->cus_price_type;
					$modified .= "- Price Type: ".$this->input->post('cus_price_type');
				}
				
				if($result->cus_status !== $this->input->post('cus_status') ){
					$prev_status = 'Inactive';
					$mod_status = 'Inactive';

					if($result->cus_status == 1){
						$prev_status = 'Active';
					}
					if($this->input->post('cus_status') == 1){
						$mod_status = 'Active';
					}
					
					$previous .= "- Status: ".$prev_status;
					$modified .= "- Status: ".$mod_status;
				}
				
				$page = 'Customer Registration';
				$action = 'Modify Customer Information';
				$created = '';
				$data['audit_trail'] = $this->membership_model->audit_trail($page,$action,$previous,$created,$modified);
			}
		// END AUDIT TRAIL
		
		redirect('login/display/ipos_customer_reg');
	}
	/** end Customer registration functions **/
	
	
	/** start Inventory Management functions **/
	// load add supplier page
	function add_inventory_main() {
		//$this->load->model('Products_model');	
		$this->load->model('Film_model');	
		
		//item list for auto complete
		//$data['item_list'] =$this->Products_model->get_all_item_list(); 
		
		#getting inventory count
		$data['inv_count']=  $this->Film_model->get_new_inv_no();
		
		$data['main_content'] = 'register_inventory_main_add';
		$this->load->view('includes/container', $data);
	}
	

	
	// load add2 supplier page
	function add_inventory_main2($inv_no) {
		$this->load->model('Products_model');	
		$this->load->model('Film_model');	
		
		$data['ipos_inventory'] = $this->Film_model->call_inventory_no($inv_no);
		$data['data_count'] = $this->Film_model->get_inventory_count($inv_no);
		
		$data['main_content'] = 'register_inventory_main_add2';
		$this->load->view('includes/container', $data);
	}
	
	// load Edit supplier page
	function edit_inventory_main($inv_no) {
		$this->load->model('Products_model');	
		$this->load->model('Film_model');	
		
		$data['ipos_inventory'] = $this->Film_model->call_inventory_no($inv_no);
		$data['data_count'] = $this->Film_model->get_inventory_count($inv_no);
		
		$data['main_content'] = 'register_inventory_main_edit';
		$this->load->view('includes/container', $data);
	}

	
	//save inventory into the database
	function save_inventory() {
		$this->load->model('membership_model');		
		$this->load->model('Film_model');		
		$inv_no = $this->input->post('inv_no');
		
		//delete procedure for Edit and Add item list inventory
		if (isset($_POST['del_select'])) {
			if ($_POST['deleted_items']) {
				$deleted_items = join(',', $_POST['deleted_items']);
				#Start Audit Trail
					$deleted_items_audit = explode(',',$deleted_items);
					$check_deleted_query = $this->db->select('*')
										->where_in('inv_id',$deleted_items_audit)
										->from("tbl_inventory_main")
										->get()->result();
							foreach ($check_deleted_query as $item_delete) {
								$action = "Deleted Inventory No - ".$item_delete->inv_no;
								$cre= "";
								$pre="";
								$mod= "Delete Item = ".$item_delete->inv_item_name;
								$data['audit_trail'] = $this->membership_model->audit_trail("Edit Inventory",$action,$pre,$cre,$mod);
							}
				
				#Start Audit Trail
				
				#delete checked items
				$query = "DELETE FROM tbl_inventory_main WHERE inv_id IN ($deleted_items)";
				$result = mysql_query($query); 
			}
			
			if($this->input->post('mode') == 'edit_inventory_main'){
				redirect('login/edit_inventory_main/'.$inv_no.'/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6));
			} else {
				redirect('login/add_inventory_main2/'.$inv_no.'/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6));
			}
		} else {
			
			//save edit and add inventory
			$data['ipos_inventory'] = $this->Film_model->call_inventory_no($inv_no);
			$this->membership_model->db_save_inventory();
			
			//AUDIT TRAIL
			if(empty($data['ipos_inventory'])) {
				//die();
				$page = 'Inventory Registration';
				$action = 'Register Inventory';
				$previous = '';
				
				$created ="Inventory No:".$this->input->post('inv_no');
				$created ="Sup Code:".$this->input->post('inv_supplier_code');
				$created .=" - Sup Name:".$this->input->post('inv_supplier_name');
				//$created .=" - Cus Description:".$this->input->post('cus_description');
				$created .=" - Sup Address:".$this->input->post('inv_description');
				$created .=" - Created by:".$this->input->post('inv_created_by');
				$created .=" - Date Received:".$this->input->post('inv_date_received');
				
				$created .=" - Item Name:".$this->input->post('inv_item_name');
				$created .=" - Item Quantity:".$this->input->post('inv_item_quantity');
				$created .=" - Item Price:".$this->input->post('inv_item_price');
				$created .=" - Item Amount:".$this->input->post('inv_item_amount');
				$modified = '';
				$data['audit_trail'] = $this->membership_model->audit_trail($page,$action,$previous,$created,$modified);
			} else {
				if(!empty($data['ipos_inventory'])) {	
					foreach($data['ipos_inventory'] as $result){
						$x=0;
						$page = 'Inventory Registration';
						$action = 'Edit Inventory';
						$previous = '';
						$created = '';
						$modified ='';
						
						$previous ="Inventory No:".$this->input->post('inv_no');
						$modified ="Inventory No:".$this->input->post('inv_no');
						
						//if($result->inv_supplier_name !== $this->input->post('inv_supplier_name') ){
							$previous .= "- Sup Name: ".$result->inv_supplier_name;
							$modified .= "- Sup Name: ".$this->input->post('inv_supplier_name');
						//}
						
						if($result->inv_supplier_code !== $this->input->post('inv_supplier_code') ){
							$previous .= "- Sup Code: ".$result->inv_supplier_name;
							$modified .= "- Sup Code: ".$this->input->post('inv_supplier_name');
						}
						
						
						if($result->inv_supplier_addr !== $this->input->post('inv_supplier_addr') ){
							$previous .= "- Sup Address: ".$result->inv_supplier_addr;
							$modified .= "- Sup Address: ".$this->input->post('inv_supplier_addr');
						}
						
						//if($result->inv_date_received !== $this->input->post('inv_date_received') ){
							$previous .= "- Date Received: ".$result->inv_date_received;
							$modified .= "- Date Received: ".$this->input->post('inv_date_received');
						//}
						
						if($result->inv_created_by !== $this->input->post('inv_created_by') ){
							$previous .= "- Created by: ".$result->inv_created_by;
							$modified .= "- Created by: ".$this->input->post('inv_created_by');
						}
						
						if($result->inv_edited_by !== $this->input->post('inv_edited_by') ){
							$previous .= "- Edited by: ".$result->inv_edited_by;
							$modified .= "- Edited by: ".$this->input->post('inv_edited_by');
						}

						//if($result->inv_item_name !== $this->input->post('inv_item_name'.$x) ){
							$previous .= "- Item Name: ".$result->inv_item_name;
							$modified .= "- Item Name: ".$this->input->post('inv_item_name'.$x);
						//}
						if($result->inv_item_quantity !== $this->input->post('inv_item_quantity'.$x) ){
							$previous .= "- Item Quantity: ".$result->inv_item_quantity;
							$modified .= "- Item Quantity: ".number_format($this->input->post('inv_item_quantity'.$x),2,'.','');
						}
						if($result->inv_item_price !== $this->input->post('inv_item_price'.$x) ){
							$previous .= "- Item Price: ".$result->inv_item_price;
							$modified .= "- Item Price: ".number_format($this->input->post('inv_item_price'.$x),2,'.','');
						}
						if($result->inv_item_amount !== $this->input->post('inv_item_amount'.$x) ){
							$previous .= "- Item Amount: ".number_format($result->inv_item_quantity*$result->inv_item_price,2,'.','');
							$modified .= "- Item Amount: ".number_format($this->input->post('inv_item_amount'.$x),2,'.','');
						}
						
						$data['audit_trail'] = $this->membership_model->audit_trail($page,$action,$previous,$created,$modified);
						$x++;
					}
				}
			}
			// END AUDIT TRAIL
			//die($this->uri->segment(2));
			if($this->input->post('mode') == 'edit_inventory_main'){
				redirect('login/edit_inventory_main/'.$inv_no.'/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6));
			} else {
				redirect('login/add_inventory_main2/'.$inv_no.'/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6));
			}
		}		
	}
	
	function print_inventory_main($inv_no) {
		$this->load->model('Products_model');	
		$this->load->model('Film_model');	
		
		$data['ipos_inventory'] = $this->Film_model->call_inventory_no($inv_no);
		$data['data_count'] = $this->Film_model->get_inventory_count($inv_no);
		
		$this->load->view('pdf_print_inv_no',$data);		
	}
		
	/** end Inventory Management functions **/
	
	
	
	/** getting item using JSON **/
	function get_item_json() {
		$item_name_like = trim(strip_tags($_POST['term']));
		$q = $this->db->select('*')
				  ->from('tbl_item_price')
				  ->like('item_name',$item_name_like)
				  ->like('status',1)
				  ->order_by('item_name','asc')
				  ->get()->result();
		
		$pos_price_type = $this->session->userdata('pos_price_type');	
		$item_price =0;
		
		
		$response='[';
		foreach($q as $row){
			
			/* if($pos_price_type == 0){
				$item_price = $row->item_price1;
			} elseif($pos_price_type == 1){
				$item_price = $row->item_price2;
			} elseif($pos_price_type == 2){
				$item_price = $row->item_price3;
			} elseif($pos_price_type == 3){
				$item_price = $row->item_price4;
			} */
			
			switch ($pos_price_type) {
			  case 0:
				$item_price = $row->item_price1;
				break;
			  case 1:
				$item_price = $row->item_price2;
				break;
			  case 2:
				$item_price = $row->item_price3;
				break;
			  default:
				$item_price = $row->item_price4;
			}
							
			$response .= '{
			"label":"'.$row->item_name.'",
			"item_name":"'.$row->item_name.'",
			"item_code":"'.$row->item_code.'",
			"item_volume":"'.$row->item_volume.'",
			"item_unit":"'.$row->item_unit.'",
			"item_type":"'.$row->item_type.'",
			"item_price":"'.$item_price.'"
			},';
			
			//$response .= '{"label":"'.$row->item_name.'- Brand:'.$row->item_brand.'","item_name":"'.$row->item_name.'","item_code":"'.$row->item_code.'","item_volume":"'.$row->item_volume.'","item_unit":"'.$row->item_unit.'","item_type":"'.$row->item_type.'"},';
		}
		$response = trim($response,",");
		$response.=']';
			
		echo $response;

			
	}
	
	function get_supplier_json() {
		$item_sup_like = trim(strip_tags($_POST['term']));
		$q = $this->db->select('*')
				  ->from('tbl_supplier')
				  ->like('sup_name',$item_sup_like)
				  ->like('sup_status',1)
				  ->order_by('sup_name','asc')
				  ->get()->result();
				
		 $response='[';
		
		foreach($q as $row){
			$response .= '{
			"label":"'.$row->sup_name.'",
			"sup_name":"'.$row->sup_name.'",
			"sup_code":"'.$row->sup_code.'",
			"sup_description":"'.$row->sup_description.'",
			"sup_address":"'.$row->sup_address.'",
			"sup_contact_no":"'.$row->sup_contact_no.'"
			},';
			
		}
		$response = trim($response,",");
		$response.=']';
			
		echo $response;
			
	}
	
	function get_customer_json() {
		$item_cus_like = trim(strip_tags($_POST['term']));
				
		$q = $this->db->select('*')
				  ->from('tbl_customer')
				  ->like('cus_name',$item_cus_like)
				  ->like('cus_status',1)
				  ->order_by('cus_name','asc')
				  ->get()->result();
				
		$response='[';
		foreach($q as $row){
			$response .= '{
			"label":"'.$row->cus_name.'",
			"cus_name":"'.$row->cus_name.'",
			"cus_code":"'.$row->cus_code.'",
			"cus_description":"'.$row->cus_description.'",
			"cus_address":"'.$row->cus_address.'",
			"cus_contact_no":"'.$row->cus_contact_no.'",
			"cus_price_type":"'.$row->cus_price_type.'"
			},';
			
		}
		$response = trim($response,",");
		$response.=']';
			
		echo $response;
			
	}

	/** getting item using JSON **/
	
	/** start POS Management functions **/
	function add_pos_main() {
		//$this->load->model('Products_model');	
		$this->load->model('Film_model');
		
		//die(print_r(date("yy-m-d")));
		//die(print_r(date("h:i A")));
		//if new sale then set price type to whole sale price
		$this->session->set_userdata('pos_price_type',0);
		
		//item list for auto complete
		//$data['item_list'] =$this->Products_model->get_all_item_list(); 
		
		#getting inventory count
		$data['trans_count']=  $this->Film_model->get_new_pos_no();
		
		$data['main_content'] = 'register_pos_main_add';
		$this->load->view('includes/container', $data);
	}
	
	//save Pos sale to DB
	function save_pos_main() {
		$this->load->model('membership_model');		
		$this->load->model('Film_model');		
		$pos_no = $this->input->post('pos_transaction_no');
		
		//save edit and add pos
		$data['ipos_pos'] = $this->Film_model->call_pos_no($pos_no);
		$this->membership_model->db_save_pos();
		
		//new transaction
		if (isset($_POST['submit_final'])) {
			redirect('login/add_pos_main/ipos_pos_management');
		}
		
		if($this->input->post('mode') == 'edit_pos_main'){
				redirect('login/edit_pos_main/'.$pos_no.'/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6));
		} else {
				redirect('login/add_pos_main2/'.$pos_no.'/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6));
		}
		
	}
	
	// load add2 pos page
	function add_pos_main2($pos_no) {
		$this->load->model('Products_model');	
		$this->load->model('Film_model');	
		
		$data['ipos_pos'] = $this->Film_model->call_pos_no($pos_no);
		$data['data_count'] = $this->Film_model->get_pos_count($pos_no);
		
		
		
		$data['main_content'] = 'register_pos_main_add2';
		$this->load->view('includes/container', $data);
	}
	
	// load view pos page
	function view_pos_main($pos_no) {
		$this->load->model('Products_model');	
		$this->load->model('Film_model');	
		
		$data['ipos_pos'] = $this->Film_model->call_pos_no($pos_no);
		$data['data_count'] = $this->Film_model->get_pos_count($pos_no);
	
		$data['main_content'] = 'register_pos_main_view';
		$this->load->view('includes/container', $data);
	}
	
	// load view pos page
	function edit_pos_main($pos_no) {
		$this->load->model('Products_model');	
		$this->load->model('Film_model');	
		
		$data['ipos_pos'] = $this->Film_model->call_pos_no($pos_no);
		$data['data_count'] = $this->Film_model->get_pos_count($pos_no);
	
		$data['main_content'] = 'register_pos_main_edit';
		$this->load->view('includes/container', $data);
	}
	
	function change_price_type($price_type,$mode,$pos_no) {

			$this->session->set_userdata('pos_price_type',$price_type);
			//die($price_type." - ".$mode." - ".$trans_no);
			redirect('/login/'.$mode.'/'.$pos_no);
	}
	
	function pos_del_item($pos_id,$pos_no,$mode){
			$this->load->model('Products_model');
			
			#delete checked items
			$this->Products_model->call_del_pos_item($pos_id,$pos_no);
			
			#recompute 
			$this->Products_model->recompute_pos_sale($pos_no);

			redirect('/login/'.$mode.'/'.$pos_no);
	}
	
	
	function pos_cancel_transaction($pos_no) {
			$this->load->model('Products_model');
			#change transaction status to cancel
			$this->Products_model->call_cancel_transaction($pos_no);
			redirect('/login/display/ipos_pos_management');
			
	}
	
	function print_pos_transaction($pos_no) {
		$this->load->model('Products_model');	
		$this->load->model('Film_model');	
		
		$data['ipos_pos'] = $this->Film_model->call_pos_no($pos_no);
		$data['data_count'] = $this->Film_model->get_pos_count($pos_no);
		
		$this->load->view('pdf_print_pos_no',$data);		
	}
	
	

	
	/** end POS Management functions **/
}
