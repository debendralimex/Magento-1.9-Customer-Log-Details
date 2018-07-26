<?php

class Limex_Customerlog_Model_Observer {
    
	public function customerLogin($observer)
    {
		
		$customer = $observer->getCustomer();
		$frontName = Mage::app()->getRequest()->getRouteName();
		$controllerName = Mage::app()->getRequest()->getControllerName();
		$actionName = Mage::app()->getRequest()->getActionName();
		$myURI = $frontName . '_'. $controllerName . '_' . $actionName.', Current url: '.Mage::helper('core/http')->getHttpReferer();		
		$data = "IP: ".Mage::app()->getRequest()->getClientIp().", Customer Email: ".$customer->getEmail().', Controller - '.$myURI;		
		//Mage::log($customer, null, 'mylogfile.log', true);		
		$filename = Mage::getBaseDir('var').'/log/'.'customer_login.log';
		$this->addlog($filename, $data);
    }
	
	
	public function OrderSuccess($observer)
    {
		//echo "<pre>"; print_r($order->getData()); exit;
		$order = $observer->getEvent()->getOrder();
		$orderid = $order->getIncrementId();
		//$customer = $order->getCustomer();
		
		$myURI = 'Current url: '.Mage::helper('core/http')->getHttpReferer();		
		$data = "IP: ".Mage::app()->getRequest()->getClientIp().", Message - Success, Order #".$orderid." Customer Email: ".$order->getCustomerEmail().', '.$myURI;		
		//Mage::log($customer, null, 'mylogfile.log', true);		
		$filename = Mage::getBaseDir('var').'/log/'.'orders.log';
		$this->addlog($filename, $data);
    }
	
	public function OrderUnSuccess($observer)
    {
		$order = $observer->getEvent()->getOrder();
		$orderid = $order->getIncrementId();
		$myURI = 'Current url: '.Mage::helper('core/http')->getHttpReferer();		
		$data = "IP: ".Mage::app()->getRequest()->getClientIp().", Message - Faild, Order # - ".$orderid.', '.$myURI;		
		//Mage::log($customer, null, 'mylogfile.log', true);		
		$filename = Mage::getBaseDir('var').'/log/'.'orders.log';
		$this->addlog($filename, $data);
    }
	
	public function addlog($filename, $data)
	{
		$enabled = Mage::getStoreConfigFlag('customerlog_options/admin_user_monitoring/limex_customerlog_admin_user_change_monitoring_enable');
        if (!$enabled) {
            return $this;
        }
		
		$count = 1;
		$string = fopen($filename, "r");
		while ($line = fgets($string)) {
			$count++;
		}
		if($count == 1) {
		file_put_contents($filename, '#'.$count.'::  '.$data, FILE_APPEND | LOCK_EX);
		}
		else {
			file_put_contents($filename, PHP_EOL.'#'.$count.'::  '.$data, FILE_APPEND | LOCK_EX);
		}
	}
	
	public function customerRegisterSuccess($observer)
    {
		$event = $observer->getEvent();
		$customer = $event->getCustomer();
		$email = $customer->getEmail();

		$frontName = Mage::app()->getRequest()->getRouteName();
		$controllerName = Mage::app()->getRequest()->getControllerName();
		$actionName = Mage::app()->getRequest()->getActionName();

		$myURI = $frontName . '_'. $controllerName . '_' . $actionName.', Current url: '.Mage::helper('core/http')->getHttpReferer();

		
		
		$admin = Mage::getSingleton('admin/session')->getUser();
        if (count($admin) > 0) {
            $admin_username = $admin->getUsername();
			$data = "IP: ".Mage::app()->getRequest()->getClientIp().", Message: Success, Customer Email: ".$email.',  Updated by admin : '.$admin_username.', Controller - '.$myURI;
		}else{
			$data = "IP: ".Mage::app()->getRequest()->getClientIp().", Message: Success, Customer Email: ".$email.', Controller - '.$myURI;
		}

		$filename = Mage::getBaseDir('var').'/log/'.'customer_register.log';
		$this->addlog($filename, $data);
    }
	
	
	public function customerAddUpdate($observer)
    {
		$address = $observer->getCustomerAddress();
		$customer= $address->getCustomer();
		$email = $customer->getEmail();
		$frontName = Mage::app()->getRequest()->getRouteName();
		$controllerName = Mage::app()->getRequest()->getControllerName();
		$actionName = Mage::app()->getRequest()->getActionName();
		$myURI = $frontName . '_'. $controllerName . '_' . $actionName.', Current url: '.Mage::helper('core/http')->getHttpReferer();		
		
		
		
		$admin = Mage::getSingleton('admin/session')->getUser();
        if (count($admin) > 0) {
            $admin_username = $admin->getUsername();
			$data = "IP: ".Mage::app()->getRequest()->getClientIp().", Customer Email: ".$email.', Updated by admin : '.$admin_username.', Controller - '.$myURI;
		}else{
			$data = "IP: ".Mage::app()->getRequest()->getClientIp().", Customer Email: ".$email.', Controller - '.$myURI;
		}
		
		
		
		$filename = Mage::getBaseDir('var').'/log/'.'customer_register.log';		
		//$this->addlog($filename, $data);
    }
	
	
	 public function checkoutTypeOnepageSaveOrderAfter(Varien_Event_Observer $observer) {
        if($observer->getEvent()->getQuote()->getCheckoutMethod(true) == Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER) {
            /* @var Mage_Customer_Model_Customer $customer */
			$customer = $observer->getEvent()->getOrder()->getCustomer();
			$email = $customer->getEmail();
			$customer->getStore()->getId();
			$data = "IP: ".Mage::app()->getRequest()->getClientIp().", Customer Email: ".$email.', Page: On Checkout Page';	
			$filename = Mage::getBaseDir('var').'/log/'.'customer_register.log';		
			$this->addlog($filename, $data);
		
        }
    }
	
	##########################################  Admin ######################################
	
	public function logAdminUserSave(Varien_Event_Observer $observer)
    {
        $adminUser = $observer->getEvent()->getObject();
        $message = '';
		$ip = Mage::app()->getRequest()->getClientIp();		
		$admin = Mage::getSingleton('admin/session')->getUser();
        if ($admin->getId()) {
            $admin_username = $admin->getUsername();
		}
        if ($adminUser->getOrigData('user_id') == null) { 
            $message .= "IP: ".$ip.", New admin user '" . $adminUser->getUsername() . "' created by '".$admin_username."', Time - ".date('Y-m-d h:i:s');
        }
        else {
            $message .= "IP: ".$ip.", Existing admin user '" . $adminUser->getOrigData('username') . "' updated by '".$admin_username."', Time - ".date('Y-m-d h:i:s');
        }
		$filename = Mage::getBaseDir('var').'/log/'.'admin_login.log';
		$this->addlog($filename, $message);

        return $this;
    }

    
    public function logAdminUserDelete(Varien_Event_Observer $observer)
    {
       
	   $admin = Mage::getSingleton('admin/session')->getUser();
        if ($admin->getId()) {
            $admin_username = $admin->getUsername();
		}
        $user_data = Mage::getModel('admin/user')->load( $observer->getEvent()->getObject()->getUserId() )->getData();
		$ip = Mage::app()->getRequest()->getClientIp();
        $message = "IP: ".$ip.", Admin user '" . $user_data['username'] . "' deleted by '".$admin_username."', Time - ".date('Y-m-d h:i:s');        		
		$filename = Mage::getBaseDir('var').'/log/'.'admin_login.log';
		$this->addlog($filename, $message);
        return $this;
    }

    
    public function logAdminUserLoginSuccess(Varien_Event_Observer $observer)
    {
      
        $admin = Mage::getSingleton('admin/session')->getUser();
        if ($admin->getId()) {
            $admin_username = $admin->getUsername();
            $message = "IP: ".Mage::app()->getRequest()->getClientIp().", Message - Successful admin user log in, Username - '" . $admin_username . "', Time - ".date('Y-m-d h:i:s');            
			$filename = Mage::getBaseDir('var').'/log/'.'admin_login.log';
			$this->addlog($filename, $message);
        }
        return $this;
    }

   
    public function logAdminUserLoginFail(Varien_Event_Observer $observer)
    {
        $username = $observer->getEvent()->getUserName();        
        $message = "IP: ".Mage::app()->getRequest()->getClientIp().", Message - Failed admin user login, Username - '" . $username . "', Time - ".date('Y-m-d h:i:s');
		$filename = Mage::getBaseDir('var').'/log/'.'admin_login.log';
		$this->addlog($filename, $message);
        return $this;
    }
	
	
}
