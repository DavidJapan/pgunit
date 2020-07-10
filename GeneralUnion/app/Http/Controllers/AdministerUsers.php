<?php
/**
 * Extends EditableDataTableController to handle a data table that can be used to administer users.
 */
namespace App\Http\Controllers;

use App\Models\AdministerUser;
use App\AppClasses\DataTableModelException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailMessage;

/**
 * Description of AdministerUsers The AdministerUsers controller extends EditableDataTableController. Every user must have 
 * one or more roles, otherwise they won't see any menu items on the left-hand panel navigation menu.
 * So, the delete function has to delete a user's roles first, and then delete the user. There are
 * also two special functions: attachRole and detachRole.
 * 
 * @uses EditItem, AddItem
 * @depends This controllers depends on these PostgreSQL functions:
 * get_users_with_roles()
 * get_user_with_roles(integer)
 * 
 * and these tables:
 * users
 * roles
 * role_user
 */
class AdministerUsers extends EditableDataTableController {

    /**
     * Adds the specified role to the user's list of roles s/he belongs to.
     * @uses Zizaco\Entrust\Traits\EntrustUserTrait::attachRole, AddItem::handleSuccessfulStore
     * @param Request $request
     * @return string JSON-encoded string with the property data, to which is attached
     * a property named after the URL for invoking the current model containing data
     * about the role just added.
     */
    public function attachRole(Request $request) {
        try {
            $this->init($request);
            $userid = $request->userid;
            $roleid = $request->roleid;
            $user = AdministerUser::find($userid);
            $user->attachRole($roleid);
            return $this->handleSuccessfulStore($request, $userid);
        } catch (\Throwable $t) {
            throw new DataTableModelException($t->getMessage(), $t->getCode());
        }
    }

    /**
     * Removes the specified role from the user's list of roles s/he belongs to.
     * @uses Zizaco\Entrust\Traits\EntrustUserTrait::detachRole 
     * @uses AddItem::handleSuccessfulStore
     * @see AddItem::handleSuccessfulStore
     * @param Request $request
     * @return string JSON-encoded string with the property data, to which is attached
     * a property named after the URL for invoking the current model containing data
     * about the role just added.
     */
    public function detachRole(Request $request) {
        try {
            $this->init($request);
            $userid = $request->userid;
            $roleid = $request->roleid;
            $user = AdministerUser::find($userid);
            $user->detachRole($roleid);
            return $this->handleSuccessfulStore($request, $userid);
        } catch (\Throwable $t) {
            throw new DataTableModelException($t->getMessage(), $t->getCode());
        }
    }

    /**
     * If the new password has been saved successfully, this calls the method
     * handleSuccessfulUpdate()
     * @uses EditItem::handleSuccessfulUpdate
     * @param Request $request
     * @param type $id
     * @return type
     */
    public function updatePassword(Request $request, $id) {
        try {
            $this->init($request);
            $user = AdministerUser::find($id);
            $user->password = $request->input("password");
            $result = $user->save();
            if ($result) {
                return $this->model->handleSuccessfulUpdate($request, $this->url, $id);
            } else {
                $error = $user->errors()->all(':message');
                $json = new \stdClass();
                $json->error = $error;
                return json_encode($json);
            }
        } catch (\Throwable $t) {
            throw new DataTableModelException($t->getMessage(), $t->getCode());
        }
    }

    /**
     * sendEmail sends an email according to the user's email data and calls this function to save it in
     * the database table. 
     * This is a nuisance but hard to avoid. The email address in the users table is email
     * but in the email_messages table the field is called mail_to.
     * When we store a message from the users table email form, the incoming request field is email
     * but when we restore a deleted message from the email messages page, the request field is mail_to.
     * The client-side ajax request is responsible for converting the email() field to a property 
     * called mail_to in the data object posted to the server.
     * @use EmailMessage
     * @param Request $request
     * @return string  a JSON-encoded string
     * representing an EmailMessage if successful, otherwise an object with the property error.
     */
    private function storeEmail(Request $request) {
        try {
            $userid = $request->get("id");
            $msg = new EmailMessage();
            //This is a nuisance but hard to avoid. The email address in the users table is email
            //but in the email_messages table the field is called mail_to.
            //When we store a message from the users table email form, the incoming request field is email
            //but when we restore a deleted message from the email messages page, the request field is mail_to.
            //The client-side ajax request is respnsible for converting the email() field to a property called mail_to in the
            //data object posted to the server.
            //if (!is_null($request->get("email"))) {
            //    $msg->mail_to = $request->get("email");
            //}else{
            $msg->mail_to = $request->get("mail_to");
            //}
            $msg->display_name = $request->get("display_name");
            $msg->subject = $request->get("subject");
            $msg->message = $request->get("message");
            //This is to accommodate undo posts where we are adding a pre-existing record.
            if (!is_null($request->get("id"))) {
                $msg->id = $request->get("id");
            }
            $result = $msg->save();
            if ($result) {
                $msg->created_at = strtotime($msg->created_at) * 1000;
                return json_encode($msg);
            } else {
                $error = $msg->errors()->all(':message');
                $json = new \stdClass();
                $json->error = $error;
                return json_encode($json);
            }
        } catch (\Exception $e) {
            return $this->handleException($e, $userid);
        } catch (\PDOException $p) {
            return $this->handleException($p, $userid);
        } catch (QueryException $qe) {
            return $this->handleException($qe, $userid);
        }
    }

    /**
     * It was complicated trying to figure out how to incorporate the Mail::queue method into this routine.
     * I found some good posts on StackOverflow.
     * Conclusion: $userdata is passed to the View emails.email and its properties are available to 
     * email.email (e.g. familyname, password etc).
     * The anonymous function is a closure and is passed to the queue function as a variable. 
     * With the use keyword, we make $user available to the scope of the closure.
     * Mail::queue('emails.email', $userdata, function($message) use ($user) {
     * 
     * @see http://stackoverflow.com/questions/14482102/passing-data-to-a-closure-in-laravel-4
     * @see http://stackoverflow.com/questions/1065188/in-php-5-3-0-what-is-the-function-use-identifier
     * @use Mail::queue
     * @uses storeEmail
     * @param Request $request
     * @return string This method calls storeEmail, which returns a JSON-encoded string
     * representing an EmailMessage if successful, otherwise an object with the property error.
     */
    public function sendEmail(Request $request) {
        try {
            $id = $request->id;
            $user = new \stdClass();
            $mail_to = $request->input("mail_to");
            $display_name = $request->input("display_name");
            $subject = $request->input("subject");
            $user->mail_to = $mail_to;
            $user->display_name = $display_name;
            $user->subject = $subject;
            $email_message = $request->input("message");
            //$userdata needs to be an array.
            $userdata = array(
                //  'givenname' => $user->givenname,
                //  'familyname' => $user->familyname,
                //  'username' => $user->username,
                'display_name' => $display_name,
                'email_message' => $email_message
            );
            Mail::send('emails.email', $userdata, function($message) use ($user) {
                $message->to($user->mail_to, $user->display_name)->subject($user->subject);
            });
            return $this->storeEmail($request);
        } catch (\Exception $e) {
            return $this->handleException($e, $id);
        } catch (\PDOException $p) {
            return $this->handleException($p, $id);
        } catch (QueryException $qe) {
            return $this->handleException($qe, $id);
        }
    }
    /**
     * This returns the current user. Not sure what it's for,but it's called
     * from the api.php file with this row:
     * ```
     * Route::middleware('auth:api')->get('/user', 'AdministerUsers@AuthRouteAPI');
     * ```
     * and allows us to avoid having closures in the routes config files.
     * @param Request $request
     * @return type
     */
    public function AuthRouteAPI(Request $request) {
        return $request->user();
    }

}
