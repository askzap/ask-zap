<?php
namespace Tygh;

require_once(__DIR__ . './../lib/Hybrid/Providers/Facebook.php');
class HybridProvidersFacebookNewScope extends \Hybrid_Providers_Facebook
{
    public $scope = 'email, user_about_me, user_birthday, user_hometown, user_website, read_stream, publish_actions, read_custom_friendlists';
}
?>
