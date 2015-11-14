<?php

App::uses('AppController', 'Controller');

class UsersController extends AppController {

	public $uses = array('User');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array('login', 'logout', 'add'));
	}

	public function index() {
		$this->set('title_for_layout', 'ユーザー管理');
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

	public function login() {
		if ($this->Auth->User('id')) {
			return $this->redirect($this->Auth->redirectUrl()); 
		}
		$this->set('title_for_layout', 'ログイン');
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				return $this->redirect($this->Auth->redirectUrl());
			}
			$this->Session->setFlash(__('IDまたはパスワードが違います。'), 'default', array(), 'auth');
		}
	}

	public function logout() {
		$this->Session->destroy();
		return $this->redirect($this->Auth->logout());
	}

	public function change_pass() {
		$this->set('title_for_layout', 'パスワード変更');
	}

	public function add() {
		$this->set('title_for_layout', 'ユーザ追加');
		if ($this->request->is('post')) {
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('ユーザを追加しました。', 'default', array(), 'auth'));
                return $this->redirect(array('action' => 'login'));
            }
            $this->Session->setFlash(__('追加が失敗しました。', 'default', array(), 'auth'));
        }
	}

}