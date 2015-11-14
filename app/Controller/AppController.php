<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	public $helpers = array('Session', 'Html', 'Form');
	public $components = array(
			'Session',
			'Cookie',
			'Auth' => array(
					'authError' => 'このページにアクセスできません。管理者に連絡してください。',
					'loginRedirect' => array(
							'controller' => 'sellers',
							'action' => 'index'
					),
					'logoutRedirect' => array(
						'controller' => 'users',
						'action' => 'login'
					),
					'loginAction' => array(
							'controller' => 'users',
							'action' => 'login'
					),
					'authorize' => array('Controller'),
					'authenticate' => array(
		                'Form' => array(
		                    'passwordHasher' => 'Blowfish'
		                )
		            )
			)
	);

	function beforeFilter() {
		parent::beforeFilter();
		$this->set('authUser', $this->Auth->user());
	}

	public function isAuthorized($user = null) {
		return isset($user['role']);
	}
}
