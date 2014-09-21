<?php

/**
 * Main SF View
 *
 * @author cvgellhorn
 */
class SF_View
{
	/**
	 * @var Controller and action for dynamic layout loading
	 */
	private $_layoutAction;
	private $_layoutController;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{}

	/**
	 * Get action content in default layout
	 */
	private function _getContent()
	{
		$this->loadView($this->_layoutAction, $this->_layoutController);
	}
	
	/**
	 * Load view from controller action
	 *
	 * @param string $action Current action name 
	 * @param string $controller Current controller name
	 * @throws SF_Exception
	 */
	public function loadView($action, $controller)
	{
		try {
			$viewPath = APP_PATH . DS .  'views' . DS 
				. $controller . DS . $action . '.phtml';
			
			if (file_exists($viewPath)) {
				require_once $viewPath;
			} else {
				throw new SF_Exception('File does not exists' . $action . '.phtml', 3334);
			}
		} catch (SF_Exception $e) {
			throw new SF_Exception('Could not load view from action: ' . $action, 3333);
		}
	}
	
	/**
	 * Load view from controller action with default layout
	 *
	 * @param string $action Current action name 
	 * @param string $controller Current controller name
	 * @throws SF_Exception If main template not exists
	 */
	public function loadLayoutView($action, $controller)
	{
		//-- Set current controller and action for layout loading
		$this->_layoutAction = $action;
		$this->_layoutController = $controller;
		
		$layoutPath = APP_PATH . DS .  'layout' . DS 
			. SF_Ini::get('template') . '.phtml';

		if (file_exists($layoutPath)) {
			require_once $layoutPath;
		} else {
			throw new SF_Exception('Layout template does not exists: '
				. SF_Ini::get('template') . '.phtml', 3334);
		}
	}
	
	/**
	 * Load controler action
	 * 
	 * @param string $action Action name
	 * @param string $controller Controller name
	 * @param mixed $params Action params
	 */
	public function action($action, $controller, $params = array())
	{
		//-- Add given params into request
		$request = SF_Request::getInstance();
		foreach ($params as $key => $value) {
			$request->setParam($key, $value);
		}
		
		//-- Route to new controller action
		$request->setIsInternal();
		SF_Router::getInstance()->route($request->setUri(
			SF_Ini::get('base_path') . $controller . '/' . $action
		));
	}
}