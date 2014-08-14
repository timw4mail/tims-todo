<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * Class for building pages
 * @author Timothy J. Warren <tim@timshomepage.net>
 *
 * All methods are chainable, with the exception of the constructor,
 * build_header(), build_footer(), and _headers() methods.
 */
class Page {
	
	private static $meta, $head_js, $foot_js, $css, $title,
			$head_tags, $body_id;

	/**
	 * Current Controller Instance
	 *
	 * @var CI_Controller
	 */
	private $CI;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->meta = "";
		$this->head_js = "";
		$this->foot_js = "";
		$this->css = "";
		$this->title = "";
		$this->head_tags = "";
		$this->body_id = "";
		$this->CI =& get_instance();
	}
	
	// --------------------------------------------------------------------------

	/**
	 * Sets server headers and doctype
	 *
	 * Also sets page mime type, based on if sent as
	 * html or xhtml, and what the target browser
	 * supports
	 *
	 * @return Page
	 */
	private function _headers()
	{
		$this->CI->output->set_header("Cache-Control: must-revalidate, public");
		$this->CI->output->set_header("Vary: Accept");
		
		//Predefine charset and mime
		$charset = "UTF-8";
		$mime = "text/html";

		$doctype_string = doctype('html5') . "\n<html lang='en'>";

		// finally, output the mime type and prolog type
		$this->CI->output->set_header("Content-Type: $mime;charset=$charset");
		$this->CI->output->set_header("X-UA-Compatible: chrome=1");
		$this->CI->output->set_output($doctype_string);
		
		return $this;
	}
	
	// --------------------------------------------------------------------------

	/**
	 * Set Meta
	 *
	 * Sets meta tags, with codeigniter native meta tag helper
	 * 
	 * @param array $meta
	 * @return Page
	 */
	public function set_meta($meta)
	{
		$this->meta .= T1.meta($meta).NL;
		return $this;
	}
	
	// --------------------------------------------------------------------------

	/**
	 * Sets minified javascript group in header
	 * @param string $group
	 * @param bool $debug
	 * @return Page
	 */
	public function set_head_js_group($group, $debug=FALSE)
	{
		$file = $this->CI->config->item('group_style_path') . $group;
		$file .= ($debug == TRUE) ? "/debug/1" : "";
		$this->head_js .= $this->script_tag($file, FALSE);
		return $this;
	}
	
	// --------------------------------------------------------------------------

	/**
	 * Sets a minified css group
	 * @param string $group
	 * @return Page
	 */
	public function set_css_group($group)
	{
		$link = array(
		  'href' => $this->CI->config->item('group_style_path') . $group,
		  'rel' => 'stylesheet',
		  'type' => 'text/css',
		);
		$this->css .= T1.link_tag($link).NL;

		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Sets a minified javascript group for the page footer
	 * @param string $group
	 * @return Page
	 */
	public function set_foot_js_group($group, $debug=FALSE)
	{
		$file = $this->CI->config->item('group_style_path') . $group;
		$file .= ($debug == TRUE) ? "/debug/1" : "";
		$this->foot_js .= $this->script_tag($file, FALSE);
		return $this;
	}
	
	// --------------------------------------------------------------------------

	/**
	 * Sets html title string
	 * @param string $title
	 * @return Page
	 */
	public function set_title($title="")
	{
		$title = ($title == "") ?
			$this->CI->config->item('default_title') : $title;
			
		$this->title = $title;
		
		return $this;
	}
	
	// --------------------------------------------------------------------------

	/**
	 * Sets custom body id
	 * @param string $id
	 * @return Page
	 */
	public function set_body_id($page_id="")
	{
		$this->body_id = $page_id;
		return $this;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Sets custom page header
	 * @return $this
	 */
	public function build_header()
	{
		$data = array();

		//Set Meta Tags
		$this->meta = T1.'<meta charset="utf-8" />'.NL. $this->meta;
		$data['meta'] = $this->meta;

		//Set CSS
		if ($this->css != "")
		{
			$data['css'] = $this->css;
		}
		else
		{
			//Set default CSS group
			$this->set_css_group($this->CI->config->item('default_css_group'));
			$data['css'] = $this->css;
		}

		//Set head javascript
		$data['head_js'] = ( ! empty($this->head_js)) ?  $this->head_js : "";

		//Set Page Title
		$data['title'] = ( ! empty($this->title)) ? $this->title : $this->CI->config->item('default_title');

		//Set Body Id
		$data['body_id'] = $this->body_id;

		//Set Server Headers and Doctype
		$this->_headers();

		//Output Header
		$this->CI->load->view('header', $data);
		
		return $this;
	}
	
	// --------------------------------------------------------------------------

	/**
	 * Shortcut method to cut down on boilerplate
	 *
	 * @param string $view
	 * @param array|object $data
	 * @return void
	 */
	public function build($view, $data = array())
	{
		$this->build_header();
		$this->CI->load->view($view, $data);
		$this->build_footer();
	}

	// --------------------------------------------------------------------------

	/**
	 * Builds common footer with any additional js
	 */
	public function build_footer()
	{
		$data = array();

		$data['foot_js'] = ($this->foot_js != "") ?
			$this->foot_js : '';

		$this->CI->load->view('footer', $data);
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Script Tag
	 *
	 * Helper function for making script tags
	 *
	 * @param string $js
	 * @param bool $domain
	 * @return string
	 */
	private function script_tag($javascript, $domain=TRUE)
	{
		$path = $this->CI->config->item('content_domain');
		$js_file = $path . "/js/" . $javascript . ".js";

		if ($domain == FALSE)
			$js_file = $javascript;

		$tag = '<script src="' .
			$js_file .
			'"></script>'.NL;

		return $tag;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Num Queries
	 * 
	 * Returns number of queries run on a page
	 * 
	 * @return int
	 */
	public function num_queries()
	{
		return	(isset($this->CI->db)) ? count($this->CI->db->queries) : 0;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Set Message
	 *
	 * Adds a message to the page
	 * @param string $type
	 * @param string $message
	 * @param bool $return
	 * @return mixed
	 */
	public function set_message($type, $message, $return = FALSE)
	{
		$data = array();
		$data['stat_type'] = $type;
		$data['message'] = $message;

		return $this->CI->load->view('message', $data, $return);
	}
}


