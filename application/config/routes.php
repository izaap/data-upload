<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "user";
$route['404_override'] = '';

$route['upload_form'] = "upload_form/index";
$route['upload_form/(:any)'] = "upload_form/index/$1";
$route['upload_form/(:any)/(:any)'] = "upload_form/index/$1/$2";

$route['view/(:any)/(:any)'] = "upload_form/view/$1/$2";
$route['download/(:any)/(:any)/(:any)'] = "upload_form/view/$1/$2/$3";
$route['download_all'] = "upload_form/download_all";
/* End of file routes.php */
/* Location: ./application/config/routes.php */