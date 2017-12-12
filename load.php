<?php
function load_pages($url){
	if(isset($url[0]) && !isset($url[1])){
	  switch ($url[0]) {
			case '':
			include(ABSPATH.'/class/pages/overview.php');
			break;
			case 'index':
			include(ABSPATH.'/class/pages/overview.php');
			break;
			case 'websits':
			include(ABSPATH.'/class/pages/websits.php');
			break;
			case 'overview':
			include(ABSPATH.'/class/pages/overview.php');
			break;
			case 'url-status':
			include(ABSPATH.'/class/pages/url-status.php');
			break;
			case 'keyword-status':
			include(ABSPATH.'/class/pages/keyword-status.php');
			break;
			case 'notifications':
			include(ABSPATH.'/class/pages/notifications.php');
			break;
			case 'messages':
			include(ABSPATH.'/class/pages/messages.php');
			break;
			case 'links-status':
			include(ABSPATH.'/class/pages/links-status.php');
			break;
			case 'users':
			include(ABSPATH.'/class/pages/users.php');
			break;
			case 'users-access':
			include(ABSPATH.'/class/pages/user-access.php');
			break;
			default:
			include(ABSPATH.'/404.php');
			break;
	  }
	}
}
?>