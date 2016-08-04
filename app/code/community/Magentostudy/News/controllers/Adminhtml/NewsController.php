<?php

// News Controller
class Magentostudy_News_adminhtml_NewsController extends Mage_adminhtml_Controller_Action{
	// Init action
	protected function _initAction(){
		//load layout, set active menu nad breadcrumbs
		// $this->loadLayout()->_setActiveMenu('news/manage')
		// ->addCrumb(
		// 	Mage::helper('magentostudy_news')->__('News'),
		// 	Mage::helper("magentostudy_news")->__('News')
		// 	)
		// ->addCrumb(
		// 	Mage::helper('magentostudy_news')->__('News'),
		// 	Mage::helper("magentostudy_news")->__('News')
		// 	)
		// ;
		// return $this;

	}
	public function indexAction(){
		// $this->_title($this->__('News'))
		// 	->_title($this->__('Manage News'));

		// $this->_initAction();
		$this->loadLayout();	//*
		$this->renderLayout();
	}}
/*
	// Create News item
	public function newAction(){
		//the same form is used to create and edit
		$this->_forward('edit');
	}

	//Edit news item
	public function editAction(){
		$this->_title($this->__('News'))
		->$this->_title($this->__('Manage News'));
		//instance news model
		//Magentostudy_News_Model_Item
		$model = Mage::getModel('magentostudy_news/news');
		//if ID exists, check it and load data
		$newsId = $this->getRequest()->getParam('id');
		if($newsId){
			$model->load($newsId);

			if(!$model->getId()){
				$this->_getSession()->addError(
					Mage::helper('magentostudy_news')->__('News item dóe not exist')
					);
				return $this->_redirect('*#/*#/');
			}
			//prepare title
			$this->_title($model->getTitle());
			$breadCrumb = Mage::helper('magentostudy_news')->__('Edit Item');
		}else{
			$this->_title(Mage::helper('magentostudy_news'))->__('New Item');
			$breadCrumb = Mage::helper('magentostudy_news')->__('News Item');
		}

		//Init breadcrumbs
		$this->_initAction()->_addBreadcrumb($breadCrumb, $breadCrumb);

		//3. Set entered data if there was an error during save
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		if(!empty($data)){
			$model->addData($data);
		}

		//4. Register model to use later in blocks
		Mage::register('news_item',$model);

		//5. Render layout
		$this->renderLayut();
	}

	// Save action
	public function saveAction(){
		$redirectPath = '*#/*#/';
		$redirectParams = array();

		//check if data sent
		$data - $this->getRequest()->getPost();
		if($data){
			$data = $this->_filterPostData($data);
			//init model and set data
			//Magentostudy_news_Model_Item
			$model  = Mage::getModel('magentostudy_news/news');

			//If news item exists, try to load it
			$newsId = $this->getRequest()->getParam('news_id');
			if($newsId){
				$model->load($newsId);
			}

			//save image data and remode from data array
			if(isset($data['image'])){
				$imageData = $data['image'];
				unset($data['image']);
			}else{
				$imageData = array();
			}
			$model->addData($data);

			try{
				$hasError = false;
				//imageHelper: Magentostudy_News_Helper_Image
				$imageHelper = Mage::helper('magentostudy_news/image');
				//remove image

				if(isset($imageData['delete']) && $model->getImage()){
					$imageHelper->removeImage($model->getImage());
					$model->setImage(null);
				}

				//upload new image
				$imageFile = $imageHelper->uploadImage('image');
				if($imageFile){
					if($model->getImage()){
						$imageHelper->removeImage($model->getImage());
					}
					$model->setImage($imageFile);
				}

				//save the data
				$model->save();

				//display success message
				$this->_getSession()->addSuccess(
					Mage::helper('magentostudy_news')->__('The news item has been saved')
					);

				//check if 'Save and Continue'
				if($this->getRequest()->getParam('back')){
					$redirectPath = '*#/*#/edit';
					$redirectParams = array('id'=>$model->getId());
				}
			}catch(Exception $e){
				$hasError = true;
				$this->_getSession()->addException($e,
					Mage::helper('magentostudy_news')->__('An error occured while saving the news item')
					);
			}

			if($hasError){
				$this->_getSession()->setFormData($data);
				$redirectPath = '*#/*#/edit';
				$redirectParams = array('id'=>$this->getRequest()->getParam('id'));
			}
		}

		$this->_redirect($redirectPath, $redirectParams);
	}

	//delete action
	public function deleteAction(){
		//check if weknow what should be deleted
		$itemId = $this->getRequest()->getParam('id');
		if($itemId){
			try{
				//init model and delete
				// model: Magentostudy_News_Model_Item
				$model = Mage::loadModel('magentostudy_news/news');
				$model->load($itemId);
				if(!$model->getId()){
					Mage::throwException(Mage::helper('magentostudy_news')->__("Unable to find a news item. "));
				}
				$model->delete();

				//display success massage
				$this->_getSSion()->addSuccess(
						Mage::helper('magentostudy_news')->__('The news item has been deleted.')
					);
				}catch(Mage_Core_Exception $e){
					$this->_getSession()->addError($e->getMessage());
				}catch(Exception $e){
					$this->_getSession()->addException($e,
						Mage::helper('magentostudy_news')->__('An error occured while deleting the news item'));
				}

				
		}

		//go to grid
		$this->_redirect('*#/*#/');

	}
		//check the permission to run it
	protected function _isAllowed(){
		switch($this->getRequest()->getActionName()){
			case 'new':
			case 'save':
				return Mage::setSingleton('Admin/session')->isAllowed('news/manage/save');
				break;
			case 'delete':
			return Mage::getSingleton('Addmin/session')->isAllowed('news/manage/delete');
			default:
				return Mage::getSingleton('Admin/session')->isAllowed('news/manage');
				break;
		}
	}

	// Filtering posted data. Converting localized data if needed
	protected function _filterPostData($data){
		$data = $this->_filterDates($data, array('time_published'));
		return $data;
	}

	// Grid ajax action
	public function gridAction(){
		$this->loadLayout();
		$this->renderLayout();
	}

	//Flush News Posts Images Cache action
	public function flushAction(){
		if(Mage::helper('magentostudy_news/image')->flushImagesCache()){
			$this->_getSession()->addSuccess('Cache successfully flushed');
		}else{
			$this->_getSession()->addError('There was error during flushing cache');
		}
		$this->_forward('index');
	}
	

}*/