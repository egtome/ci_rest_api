<?php
/**
 * Solver
 * @package    CI
 * @author     Gino Tome <ginotome@gmail.com>
 */   
require APPPATH . 'libraries/REST_Controller.php';
     
class Solver extends REST_Controller {
	/**
	 * 
	 * Manage API REQUESTS
	 *
	 * @return jquery
	 */    
    public function __construct() {
       parent::__construct();
       $this->load->database();
    }
         
    /**
     * Get post request, calculate formula according values a, b, c
	 * Formula ax2; + bx + c = 0"
     *
     * @return json $response
    */
    public function index_post()
    {
		$response = [];
		try{
			$input = $this->post();
			$result_one = null;
			$result_two = null;
			$a = !empty($input['a']) ? (int)$input['a'] : null;
			$b = isset($input['b']) ? (int)$input['b'] : null;
			$c = isset($input['b']) ? (int)$input['c'] : null;
			$ip = !empty($input['ip']) ? $input['ip'] : null;
			$token = !empty($input['token']) ? $input['token'] : null;

			if($a === null || $b === null || $c === null || $ip === null || $token === null){
				$response['Status'] = -1;
				$response['Message'] = 'Invalid parameters';
				$response['X1'] = null;
				$response['X2'] = null;				
				$this->response($response, REST_Controller::HTTP_BAD_REQUEST);
			}elseif($this->validate_token($a,$b,$c,$token) === false){
				$response['Status'] = -1;
				$response['Message'] = 'Invalid token';
				$response['X1'] = null;
				$response['X2'] = null;	
				$this->response(null, REST_Controller::HTTP_UNAUTHORIZED);	
			}else{
				$this->load->model('Api_request');
				$this->load->model('Api_response');
				$this->Api_request->a_value = $a;
				$this->Api_request->b_value = $b;
				$this->Api_request->c_value = $c;
				$this->Api_request->remote_address = $ip;
				$this->Api_request->token = $token;

				$duplicated = $this->Api_request->check_duplicate();
				
				if($duplicated === false){
					//Calculate results...
					$result = $this->calculate_solutions($a,$b,$c);
					//Insert in api_requests table
					$id = $this->Api_request->insert_request();
					//Insert in api_responses table
					$this->Api_response->api_request_id = $id;
					$this->Api_response->solution_one = $result[0];
					$this->Api_response->solution_two = $result[1];
					$this->Api_response->insert_request();
					$result_one = $result[0];
					$result_two = $result[1];			
				}else{
					$counter = ++$duplicated[0]['request_counter'];
					$this->Api_request->update_duplicated($duplicated[0]['id'],$counter);
					//Get previous calculated results...
					$this->Api_response->api_request_id = $duplicated[0]['id'];
					$previous = $this->Api_response->get_previous();
					$result_one = $previous[0]['solution_one'];
					$result_two = $previous[0]['solution_two'];
				}
				$response['Status'] = 1;
				$response['Message'] = "Check solutions in values X1 and X2 for provided values: a = $a | b = $b | c = $c";
				$response['X1'] = $result_one;
				$response['X2'] = $result_two;	
			}		
		}catch(\Exception $e){
			$response['Status'] = -1;
			$response['Message'] = $e->getMessage();
			$response['X1'] = null;
			$response['X2'] = null;
		}
		$this->response($response, REST_Controller::HTTP_OK);
    } 
     
    /**
     * Method not implemented
     *
     * @return Response
    */
    public function index_put($id)
    {
        $this->response(null, REST_Controller::HTTP_NOT_IMPLEMENTED);
    }
     
    /**
     * Method not implemented
     *
     * @return Response
    */
    public function index_delete($id)
    {
        $this->response(null, REST_Controller::HTTP_NOT_IMPLEMENTED);
	}

    /**
     * Method not implemented.
     *
     * @return Response
    */
	public function index_get($id = 0)
	{
        $this->response(null, REST_Controller::HTTP_NOT_IMPLEMENTED);
	}	
	
	private function validate_token($a,$b,$c,$token){
		$valid = sha1("$a.$b.$c");
		if($token != $valid){
			return false;
		}
		return true;
	}

	private function calculate_solutions($a,$b,$c){
		$one = ($b * -1);
		$two = (pow($b, 2)) - (4*$a*$c);
		$divide = (2*$a);

		$results[] = $two < 0 ? 'No solution' : (($one + sqrt($two)) / $divide);
		$results[] = $two < 0 ? 'No solution' : (($one - sqrt($two)) / $divide);
		return $results;
	}
    	
}