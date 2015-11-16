<?php

App::uses('AppController', 'Controller');

class UsersController extends AppController {

	public $uses = array('User');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array('login', 'logout'));
	}

	public function index() {
		// $this->set('title_for_layout', 'ユーザー管理');
		$this->redirect(array('action'=> 'add'));
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
		$this->User->validate = $this->User->validatePass;

		if ($this->request->is('post') || $this->request->is('put')) {
			$this->User->set($this->request->data);
			if ($this->User->validates()) {
				$data = $this->request->data['User'];
				if ($data['password'] != $data['psword']) {
					$this->Session->setFlash(__('新しいパスワードの確認は一致しません。'), 'default', array(), 'pass');
				} else {
					$curPass = $this->User->findById($this->Auth->User('id'), array('fields'=>'password'));
					$curPass = $curPass['User']['password'];
					if ($curPass != Security::hash($data['passwd'], 'blowfish', $curPass)) {
						$this->Session->setFlash(__('現在のパスワードは正しくありません。'), 'default', array(), 'pass');
					} else {
						$this->User->id = $this->Auth->User('id');
						$this->User->saveField('password', $data['password']);
						$this->Session->setFlash(__('パスワードを変更しました。'), 'default', array(), 'pass');
					}
				}
			}
		}
	}

	public function detail() {
		if (empty($this->request->query['id'])) {
			return $this->redirect(array('action'=> 'add'));
		}
		$id = $this->request->query['id'];
		if ($this->request->is('get')) {
			$user = $this->User->findById($id);
			if ($user) {
				$this->request->data = $user;
			} else {
				return $this->redirect(array('action'=> 'add'));
			}
		}
		$this->set('title_for_layout', 'ユーザ詳細');

		if ($this->request->is('post') || $this->request->is('put')) {
			$data = $this->request->data['User'];
			$data['updated_by'] = $this->Auth->User('id');
			$data['updated_date'] = date('Y-m-d H:i:s');
			$this->User->id = $id;
			$this->User->set($data);
			if ($this->User->save()) {
				$this->Session->setFlash(__('ユーザを更新しました。', 'default', array(), 'detail'));
			} else {
				$this->Session->setFlash(__('更新が失敗しました。', 'default', array(), 'detail'));
			}
		}
	}

	public function add() {
		$this->set('title_for_layout', 'ユーザ追加');
		if ($this->request->is('post') || $this->request->is('put')) {
			$data = $this->request->data['User'];
			$data['created_by'] = $this->Auth->User('id');
			$data['updated_by'] = $data['created_by'];
			$data['created_date'] = date('Y-m-d H:i:s');
			$data['updated_date'] = $data['created_date'];

			$this->User->create();
			$this->User->set($data);
			if ($this->User->save()) {
				$this->Session->setFlash(__('ユーザを追加しました。', 'default', array(), 'add'));
			} else {
				$this->Session->setFlash(__('追加が失敗しました。', 'default', array(), 'add'));
			}
		}
	}

}