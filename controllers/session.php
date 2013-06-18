<?php

require "StudipMobileController.php";

/**
 *    create, destroy (...) sessions
 *    login process
 *    @author Marcus Lunzenauer - mlunzena@uos.de
 *    @author André Klaßen - aklassen@uos.de
 *    @author Nils Bussmann - nbussman@uos.de
 */
class SessionController extends StudipMobileController
{

    function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        if ($action === "destroy") {
            $this->requireUser();
        }
    }

    function new_action()
    {
    }

    function create_action()
    {

        $username = strtolower(Request::get("username"));
        $password = Request::get("password");

        if (isset($username) && isset($password)) {
            $result = StudipAuthAbstract::CheckAuthentication($username, $password);
        }

        if (!isset($result) || $result['uid'] === false) {
            $this->flash["notice"] = "login unsuccessful!";
            \NotificationCenter::postNotification('mobile.SessionDidNotCreate', $this);
            $this->redirect("session/new");
            return;
        }

        $user_id = get_userid($username);

        if (isset($user_id)) {
            $this->start_session($user_id);
        }

        $this->flash["notice"] = "login successful!";
        \NotificationCenter::postNotification('mobile.SessionDidCreate', $this);
        $this->redirect("quickdial");
    }

    function destroy_action()
    {
        global $perm, $user, $auth, $sess, $forced_language, $_language;
        $auth->logout();
        $perm = null;
        $user = null;
        $forced_language = null;
        $_language = null;
        $sess->delete();
        closeObject();
        \NotificationCenter::postNotification('Mobile.SessionDidDestroy', $this);
        $this->redirect("session/new");
    }

    protected function start_session($user_id)
    {
        global $perm, $user, $auth, $sess, $forced_language, $_language;


        $user = new Seminar_User();
        $user->start($user_id);

        foreach (array(
                     "uid" => $user_id,
                     "perm" => $user->perms,
                     "uname" => $user->username,
                     "auth_plugin" => $user->auth_plugin,
                     "exp" => time() + 60 * 15,
                     "refresh" => time()
                 ) as $k => $v) {
            $auth->auth[$k] = $v;
        }

        $auth->nobody = false;


        $sess->regenerate_session_id(array('auth', 'forced_language','_language'));
        $sess->freeze();
    }
}
