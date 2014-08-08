<?php

/**
 * Category Controller
 */
class Category extends MY_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->page->set_foot_js_group('js');
		$this->page->set_title('Categories');

	}

	/**
	 * Redirect to list
	 */
	public function index()
	{
		$this->todo->redirect_303(site_url('category/list'));
	}

	/**
	 * List of categories
	 */
	public function category_list()
	{
		$data['category'] = $this->todo->get_category_list();
		$this->page->set_title("Category List");
		$this->page->build('task/cat_list', $data);
	}

	/**
	 * Add a category
	 */
	public function add_sub()
	{
		if($this->input->post('add_sub') != FALSE)
		{
			$this->todo->add_category();
			$this->todo->redirect_303(site_url('category/list'));
		}
	}

	/**
	 * Category edit form
	 */
	public function edit($cat_id)
	{
		$data['cat'] = $this->todo->get_category((int) $cat_id);
		$this->page->set_title("Edit Category");
		$this->page->build('task/cat_add', $data);
	}

	/**
	 * Update the category
	 */
	public function edit_sub()
	{
		$title = $this->input->post('title', TRUE);
		$desc = $this->input->post('desc', TRUE);
		$cat_id = (int) $this->input->post('id');
		$group_id = $this->todo->get_user_group();

		$this->db->set('title', $title)
			->set('description', $desc)
			->where('group_id', $group_id)
			->where('id', $cat_id)
			->update('category');

		$this->todo->redirect_303('category/list');
	}

	/**
	 * Delete a category
	 */
	public function del_sub($cat_id)
	{
		$this->output->set_output($this->todo->del_cat((int) $cat_id));
	}
}
// End of controllers/category.php