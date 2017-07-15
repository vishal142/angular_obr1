<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Example
 *
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array.
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		Phil Sturgeon
 * @link		http://philsturgeon.co.uk/code/
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions

class Home extends CI_Controller
{
    function __construct()
    {
    	
        parent::__construct();
    
    }

     /*
     * --------------------------------------------------------------------------
     * @ Function Name            : index()
     * @ Added Date               : 12-11-2015
     * @ Added By                 : Subhankar
     * --------------------------------------------------------------------------
     * @ Description              : test api index page
     * --------------------------------------------------------------------------
     * @ return                   : view page
     * --------------------------------------------------------------------------
     * @ Modified Date            : 12-11-2015
     * @ Modified By              : Subhankar
     * 
     */
    public function index()
    {
        $this->load->view('tester/main');
    }

     /*
     * --------------------------------------------------------------------------
     * @ Function Name            : login()
     * @ Added Date               : 12-11-2015
     * @ Added By                 : Subhankar
     * --------------------------------------------------------------------------
     * @ Description              : login page
     * --------------------------------------------------------------------------
     * @ return                   : view page
     * --------------------------------------------------------------------------
     * @ Modified Date            : 12-11-2015
     * @ Modified By              : Subhankar
     * 
     */
    public function login()
    {
        $this->load->view('tester/login');
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : product()
     * @ Added Date               : 12-11-2015
     * @ Added By                 : Subhankar
     * --------------------------------------------------------------------------
     * @ Description              : product page
     * --------------------------------------------------------------------------
     * @ return                   : view page
     * --------------------------------------------------------------------------
     * @ Modified Date            : 12-11-2015
     * @ Modified By              : Subhankar
     * 
     */
    public function block()
    {
        $this->load->view('tester/block');
    }

     /*
     * --------------------------------------------------------------------------
     * @ Function Name            : brochure()
     * @ Added Date               : 12-11-2015
     * @ Added By                 : Subhankar
     * --------------------------------------------------------------------------
     * @ Description              : product page
     * --------------------------------------------------------------------------
     * @ return                   : view page
     * --------------------------------------------------------------------------
     * @ Modified Date            : 12-11-2015
     * @ Modified By              : Subhankar
     * 
     */
    public function notification()
    {
        $this->load->view('tester/notification');
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : resource()
     * @ Added Date               : 16-11-2015
     * @ Added By                 : Subhankar
     * --------------------------------------------------------------------------
     * @ Description              : resource page
     * --------------------------------------------------------------------------
     * @ return                   : view page
     * --------------------------------------------------------------------------
     * @ Modified Date            : 16-11-2015
     * @ Modified By              : Subhankar
     * 
     */
    public function track()
    {
        $this->load->view('tester/track');
    }

     /*
     * --------------------------------------------------------------------------
     * @ Function Name            : contact()
     * @ Added Date               : 16-11-2015
     * @ Added By                 : Subhankar
     * --------------------------------------------------------------------------
     * @ Description              : contact page
     * --------------------------------------------------------------------------
     * @ return                   : view page
     * --------------------------------------------------------------------------
     * @ Modified Date            : 16-11-2015
     * @ Modified By              : Subhankar
     * 
     */
    public function contact()
    {
        $this->load->view('tester/contact');
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : hospital()
     * @ Added Date               : 18-11-2015
     * @ Added By                 : Subhankar
     * --------------------------------------------------------------------------
     * @ Description              : hospital page
     * --------------------------------------------------------------------------
     * @ return                   : view page
     * --------------------------------------------------------------------------
     * @ Modified Date            : 18-11-2015
     * @ Modified By              : Subhankar
     * 
     */
    public function hospital()
    {
        $this->load->view('tester/hospital');
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : order()
     * @ Added Date               : 08-12-2015
     * @ Added By                 : Subhankar
     * --------------------------------------------------------------------------
     * @ Description              : order page
     * --------------------------------------------------------------------------
     * @ return                   : view page
     * --------------------------------------------------------------------------
     * @ Modified Date            : 08-12-2015
     * @ Modified By              : Subhankar
     * 
     */
    public function order()
    {
        $this->load->view('tester/order');
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : chargesheet()
     * @ Added Date               : 08-12-2015
     * @ Added By                 : Subhankar
     * --------------------------------------------------------------------------
     * @ Description              : chargesheet page
     * --------------------------------------------------------------------------
     * @ return                   : view page
     * --------------------------------------------------------------------------
     * @ Modified Date            : 08-12-2015
     * @ Modified By              : Subhankar
     * 
     */
    public function chargesheet()
    {
        $this->load->view('tester/chargesheet');
    }
     /*
     * --------------------------------------------------------------------------
     * @ Function Name            : lead()
     * @ Added Date               : 08-12-2015
     * @ Added By                 : Subhankar
     * --------------------------------------------------------------------------
     * @ Description              : lead page
     * --------------------------------------------------------------------------
     * @ return                   : view page
     * --------------------------------------------------------------------------
     * @ Modified Date            : 08-12-2015
     * @ Modified By              : Subhankar
     * 
     */
    public function lead()
    {
        $this->load->view('tester/lead');
    }
    


/*End of home controller*/

}