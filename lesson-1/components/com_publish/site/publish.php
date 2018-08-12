<?php
/**
 * @author diphuaji
 */ 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// Get an instance of the controller prefixed by HelloWorld
$controller = JControllerLegacy::getInstance('Publish'); // Perform the Request task$input = JFactory::getApplication()->input;$controller->execute($input->getCmd('task')); // Redirect if set by the controller
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));
$controller->redirect();