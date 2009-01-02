<?php
	//admin
	Router::connect('/admin/', array('controller' => 'pages', 'action' => 'display', 'home', 'admin' => 1));
	Router::connect('/admin/:controller/:action/*', array('admin' => 1));
	//pages
	Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
?>