<?php

class login
{
    /**
     * @var object The database connection
     */
    private $db_connection = null;
    /**
     * @var string The user's name
     */
    private $user_name = "";
    /**
     * @var string The user's mail
     */
    private $user_email = "";
	private $user_id = "";
    /**
     * @var string The user's password hash
     */
    private $user_password_hash = "";
    /**
     * @var boolean The user's login status
     */
    private $user_is_logged_in = false;
    /**
     * @var array Collection of error messages
     */
    public $errors = array();
    /**
     * @var array Collection of success / neutral messages
     */
    public $messages = array();
	
	private $user_level = 0;

    /**
     * the function "__construct()" automatically starts whenever an object of this class is created,
     * you know, when you do "$login = new Login();"
     */
    public function __construct()
    {        

        // if user tried to log out
        if (isset($_GET["logout"]) || isset($_POST["logout"])) {
            $this->doLogout();
        }
        // if user has an active session on the server
        elseif (!empty($_SESSION['user_name']) && ($_SESSION['user_logged_in'] == 1)) {
            $this->loginWithSessionData();
        }
        // if user just submitted a login form
        elseif (isset($_POST["login"])) {
            $this->loginWithPostData();
        }
    }

    /**
     * log in with session data
     */
    private function loginWithSessionData()
    {
        // set logged in status to true, because we just checked for this:       
        $this->user_is_logged_in = true;
    }

    /**
     * log in with post data
     */
    private function loginWithPostData()
    {
        // if POST data (from login form) contains non-empty user_name and non-empty user_password
        if (!empty($_POST['user_name']) && !empty($_POST['user_password'])) {

            // create a database connection, using the constants from config/db.php (which we loaded in index.php)
            $this->db_connection = new mysqli(Db_Host, Db_User, Db_Password, Db_Name);

            // if no connection errors (= working database connection)
            if (!$this->db_connection->connect_errno) {

                // escape the POST stuff
                $this->user_name = $this->db_connection->real_escape_string($_POST['user_name']);
                // database query, getting all the info of the selected user
                $checklogin = $this->db_connection->query("SELECT * FROM users WHERE `user_name` = '" . $this->user_name . "';");

                // if this user exists
                if ($checklogin->num_rows == 1) {

                    // get result row (as an object)
                    $result_row = $checklogin->fetch_object();

                    // using PHP 5.5's password_verify() function to check if the provided passwords fits to the hash of that user's password
                    if (sha1($_POST['user_password'])== $result_row->password && $result_row->status == 1 && $result_row->validate == '') {

                        // write user data into PHP SESSION [a file on your server]
                        $_SESSION['user_name'] = $result_row->user_name;
                        $_SESSION['email'] = $result_row->email;
						$_SESSION['level'] = $result_row->level;
                        $_SESSION['user_logged_in'] = 1;
						$_SESSION['user_id'] = $result_row->id;

                        // set the login status to true
                        $this->user_is_logged_in = true;
						$this->user_level = $result_row->level;
						$this->user_id = $result_row->id;

                    } else {
                        $this->errors[] = "Wrong password. Try again.";
                    }
                } else {
                    $this->errors[] = "This user does not exist.";
                }
            } else {
                $this->errors[] = "Database connection problem.";
            }
        } elseif (empty($_POST['user_name'])) {
            $this->errors[] = "Username field was empty.";
        } elseif (empty($_POST['password'])) {
            $this->errors[] = "Password field was empty.";
        }
    }

    /**
     * perform the logout
     */
    public function doLogout()
    {
        $_SESSION = array();
        session_destroy();
        $this->user_is_logged_in = false;
		$this->user_level = 0;
        $this->messages[] = "You have been logged out.";

    }

    /**
     * simply return the current state of the user's login
     * @return boolean user's login status
     */
    public function isUserLoggedIn()
    {
        return $this->user_is_logged_in;
    }
	
	public function userLevel(){
		return $_SESSION['level'];
	}
	public function userId(){
		return $_SESSION['user_id'];
	}
}
