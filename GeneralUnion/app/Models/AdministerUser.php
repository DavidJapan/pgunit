<?php

/**
 * This extends EditableDataTable, sets some key values like primaryKey and initialises
 * the columns in the users table. Note that $timestamps is set to true so the field created_at and updated_at are required.
 */
namespace App\Models;

use DB;
use App\AppClasses\DataControl;
use Illuminate\Http\Request;
use App\AppClasses\DataTableModelException;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use App\AppClasses\DbMetadata;

/**
 * Description of AdministerUser
 * 
 * This model extends EditableDataTable but also uses the EntrustUserTrait from
 * the Zizaco\Entrust package. The main function of the Zizaco\Entrust package is
 * to provide the ability to assign different roles to users in a secure website application.
 * 
 * It took a while to realise the need to separate the function of authenticating a login attempt
 * and actually administering users. The User model is a much simpler model which handles the 
 * user's attempt to log in or reset their password. This model enables the administrator
 * to create and edit new users and assign appropriate roles to them as required.
 * 
 * @see User
 * @uses EntrustUserTrait
 * @extends EditableDataTable
 * 
 */
class AdministerUser extends EditableDataTable {

    use EntrustUserTrait;

    /**
     * We need to specify the table because the name of the class AdministerUser
     * doesn't follow the Laravel convention of changing the table name to singular
     * users -> user then capitalising it -> User
     * 
     * @var string
     */
    protected $table = 'users';
    /**
     * administer_users
     * @var string
     */
    protected $directory = 'administer_users';

    /**
     * The name of the PostgreSQL function that gets all existing users with their roles
     * as a JSON field.
     * Here's how the JSON for available_roles is created: 
     * <code>
      array_to_json(
      ARRAY(
      SELECT row_to_json(t)
      FROM (
      SELECT
      r.id,
      r.name
      FROM roles r
      WHERE r.id NOT IN
      (SELECT ru.role_id FROM role_user ru WHERE ru.user_id = u.id)
      ) t
      )
      )
      as available_roles,
     * </code>
     * When I first created this function, I was using PostgreSQL 8.x so JSON functions were not
     * available. I'm now using 9.x so I can rely on the JSON functions.
     * @var string
     */
    protected static $all_function = 'users_all_with_roles_get';

    /**
     * The name of the PostgreSQL function that gets one selected user with their roles
     * as a JSON string.
     * Here's how the JSON for assigned_roles is created: 
     * <code>
      array_to_json(
      ARRAY(
      SELECT row_to_json(t)
      FROM (
      SELECT
      r.id,
      r.name
      FROM roles r
      WHERE r.id IN
      (SELECT ru.role_id FROM role_user ru WHERE ru.user_id = u.id)
      ) t
      )
      )
      as assigned_roles
     * </code>
     * When I first created this function, I was using PostgreSQL 8.x so JSON functions were not
     * available. I'm now using 9.x so I can rely on the JSON functions.
     * @var string
     */
    protected static $one_function = 'users_one_with_roles_get';

    /**
     * A header to display above the data table
     * @var string
     */
    public $header = 'Users';

    /**
     * This is normally automatically generated from the Model's class name by the getItemName
     * method, but it needs to be set here because of the unconventional model name.
     * @var string 
     */
    public $item_name = 'User';

    /**
     * The database field to use to name an individual model.
     * @var string
     */
    public $name_field = 'username';

    /**
     * This breaks my naming convention of using snake_case for properties
     * because the Eloquent model uses camelCase here.
     * Primary keys are assumed to be "id”, but just to be very clear...
     * 
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The authorisation system needs to have timestamps
     * @var bool
     */
    public $timestamps = true;

    /**
     * The relative path to the view to use to display this data table.
     * The root of this path is resources/views
     * @var string 
     */
    public $view_path = 'users/editusers';

    /**
     * An array of links to extra JavaScript files required by this model.
     * @var array
     */
    protected $user_scripts = [
        '/js/crud/user.js'
    ];

    /**
     * Calls the constructor first from EditableDataTable
     * then extends the scripts array and sets up the individual columns.
     * @param array $attributes
     */
    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
        $function = static::getAllFunction();
        $app_name = config('app.name');
        if (!DbMetadata::functionExists(config($app_name . '.SCHEMA'), $function)) {
            $e = new DataTableModelException("The database function '" . $function . "' doesn't exist");
            throw $e;
        }
        $this->scripts = $this->mergeUserScripts($this->scripts);
        $this->initId();
        $this->initUserName();
        $this->initGivenName();
        $this->initFamilyName();
        $this->initEmail();
        $this->initAssignedRoles();
        $this->initAvailableRoles();
        $this->initPassword();
        $this->initRememberToken();
        $this->initCreatedAt();
        $this->initUpdatedAt();
    }
    /**
     * Adds the specific scripts needed by this model to the standard ones needed for an editable table.
     * @param type $scripts
     * @return array
     */
    private function mergeUserScripts($scripts) {
        return array_merge($scripts, $this->user_scripts);
    }
    /**
     * Initialises the id column.
     */
    private function initId() {
        $column_name = 'id';
        $column = $this->getColumn($column_name);
        $this->setColumn($column_name, $column);

        $control = new DataControl($column_name);
        $control->setType(DataControl::Readonly);
        $this->setEditControl($column_name, $control);
    }
    /**
     * Initialises the username column
     */
    private function initUserName() {
        $column_name = 'username';
        $column = $this->getColumn($column_name);
        $column->setVisible(false);
        $this->setColumn($column_name, $column);
    }
    /**
     * Initialises the email column.
     */
    private function initEmail() {
        $column_name = 'email';
        $column = $this->getColumn($column_name);
        $column->setVisible(false);
        $this->setColumn($column_name, $column);

        $control = $this->getEditControl($column_name);
        $control->setLabel('EMail');
        $control->setRequired([
            'message' => 'Please enter an email address for this user.',
            'edit' => true,
            'add' => true
        ]);
        $this->setEditControl($column_name, $control);
        $this->setAddControl($column_name, $control);
    }
    /**
     * Initialises the givenname column
     */
    private function initGivenName() {
        $column_name = 'givenname';
        $column = $this->getColumn($column_name);
        $column->setTitle('Given Name');
        $column->setVisible(true);
        $this->setColumn($column_name, $column);

        $control = $this->getEditControl($column_name);
        $control->setLabel('Given Name');
        $control->setRequired([
            'message' => 'Please enter a given name for this user.',
            'edit' => true,
            'add' => true
        ]);
        $this->setEditControl($column_name, $control);
        $this->setAddControl($column_name, $control);
    }
    /**
     * Initialises the familyname column.
     */
    private function initFamilyName() {
        $column_name = 'familyname';
        $column = $this->getColumn($column_name);
        $column->setTitle('Family Name');
        $column->setVisible(true);
        $this->setColumn($column_name, $column);

        $control = $this->getEditControl($column_name);
        $control->setLabel('Family Name');
        $control->setRequired([
            'message' => 'Please enter a family name for this user.',
            'edit' => true,
            'add' => true
        ]);
        $this->setEditControl($column_name, $control);
        $this->setAddControl($column_name, $control);
    }
    /**
     * Initialises the available_roles column
     */
    private function initAvailableRoles() {
        $column_name = 'available_roles';
        $column = $this->getColumn($column_name);
        $column->setVisible(false);
        $this->setColumn($column_name, $column);

        $control = new DataControl($column_name);
        $control->setArray(true);
        $control->setVisible(false);
        //$this->setEditControl($column_name, $control);
        //$this->setAddControl($column_name, $control);
    }
    /**
     * Initialises the assigned_roles column.
     */
    private function initAssignedRoles() {
        $column_name = 'assigned_roles';
        $column = $this->getColumn($column_name);
        $column->setVisible(false);
        $this->setColumn($column_name, $column);

        $control = new DataControl($column_name);
        $control->setArray(true);
        $control->setVisible(false);
        //$this->setEditControl($column_name, $control);
        //$this->setAddControl($column_name, $control);
    }

    /**
     * Initialises the password column.
     */
    private function initPassword() {
        $column_name = 'password';
        $column = $this->getColumn($column_name);
        $column->setVisible(false);
        $this->setColumn($column_name, $column);
        /*
        $control = new DataControl($column_name);
        //$control->setVisible(false);
        $control->setRequired([
            'message' => 'Please enter password for this user.',
            'edit' => true,
            'add' => true
        ]);
        $this->setEditControl($column_name, $control);
        $this->setAddControl($column_name, $control);
         * 
         */
    }
    /**
     * Initialises the remember_token column
     */
    private function initRememberToken() {
        $column_name = 'remember_token';
        $column = $this->getColumn($column_name);
        $column->setVisible(false);
        $this->setColumn($column_name, $column);

        //$control = new DataControl($column_name);
        //$control->setVisible(false);
        //$this->setEditControl($column_name, $control);
        //$this->setAddControl($column_name, $control);
    }
    /**
     * Initialises the created_at column
     */
    private function initCreatedAt() {
        $column_name = 'created_at';
        $column = $this->getColumn($column_name);
        $column->setVisible(false);
        $this->setColumn($column_name, $column);
    }
    /**
     * Initialises the updated_at column.
     */
    private function initUpdatedAt() {
        $column_name = 'updated_at';
        $column = $this->getColumn($column_name);
        $column->setVisible(false);
        $this->setColumn($column_name, $column);
    }

    /**
     * This application is built assuming most models use functions in PostgreSQL. However,
     * where a model uses a table, only the primary key is needed. In this case, we
     * assume that the first element in the bindings array will be the primary key.
     * @param Request $request
     * @param array $bindings
     * @return object
     * @throws DataTableModelException
     */
    public function one( $bindings = []) {
        try {
            $select = self::getSelectFromBindings(['*'], $bindings, static::getOneFunction());
            $row = collect(DB::select($select, $bindings))->first();
            $user = new \stdClass();
            $user->id = $row->id;
            $user->initials = $row->initials;
            $user->givenname = $row->givenname;
            $user->familyname = $row->familyname;
            $user->username = $row->username;
            $user->email = $row->email;
            $user->available_roles = json_decode($row->available_roles);
            $user->assigned_roles = json_decode($row->assigned_roles);
            return $user;
        } catch (\Throwable $e) {
            throw new DataTableModelException("There was a problem trying find one row of this table: " .
            $t->getMessage());
        }
    }

    /**
     * The Eloquent model all() or get() methods simply don't protect you
     * from database errors such as a non-existent table or function. This function checks whether the
     * database table or function associated with this model actually exists in the database before
     * attempting to retrieve all the rows. 
     * A model's all function is defined as a static variable so it's easy to get it from
     * the static reserve word:
     * <code>
     * static::getAllFunction()
     * </code>
     * However, the name of the database table is stored in the $table instance variable.
     * There is a good discussion about how to get a model's table from within a static method:
     * https://github.com/laravel/framework/issues/1436
     * However, using new self would always return an instance of this parent model DataTable.
     * You have to use new static to ensure you get an instance of the current subclass model
     * you're working with and thence the name of the relevant PostgreSQL table.
     * 
     * @uses DbMetadata::functionExists, DbMetadata::tableExists
     * @see  DbMetadata::functionExists, DbMetadata::tableExists
     * @overrides all function in Eloquent Model. That's why
     * it has to be static.
     * @param array $columns An array of field names to be used in creating a select query. Defaults to [*}, meaning all columns
     * Since we're using PostgreSQL functions mostly, the database can take responsibility for figuring out
     * which columns to display and the server program can usually just safely ask for all columns.
     * @param array $bindings An array of values to be used to filter the results.
     * @param array $sort_fields An array of field names to be added to an ORDER BY clause. I descending
     * order is required, the element of the array should be the field name followed by desc.
     * @return Illuminate\Support\Collection Wraps the results from DB::select using the
     * collect helper to return a Collection.
     * @throws DataTableModelException
     */
    public static function all($columns = ['*'], $bindings = [], $sort_fields = []) {
        try {
            if (!is_null(static::getAllFunction())) {
                $function = static::getAllFunction();
                $app_name = config('app.name');        
                if (!DbMetadata::functionExists(config($app_name . '.SCHEMA'), $function)) {
                    $e = new DataTableModelException("The database function '" . $function . "' doesn't exist");
                    throw $e;
                }
                $select = self::getSelectFromBindings($columns, $bindings, $function, $sort_fields);
                $results = DB::select($select, $bindings);
                $all = [];
                foreach ($results as $row) {
                    $user = new \stdClass();
                    $user->id = $row->id;
                    $user->initials = $row->initials;
                    $user->givenname = $row->givenname;
                    $user->familyname = $row->familyname;
                    $user->username = $row->username;
                    $user->email = $row->email;
                    $user->available_roles = json_decode($row->available_roles);
                    $user->assigned_roles = json_decode($row->assigned_roles);
                    array_push($all, $user);
                }
                return collect($all);
            } else {
                return parent::all($columns);
            }
        } catch (\Throwable $t) {
            throw new DataTableModelException("There was a problem trying to get all the rows for this table: " . $t->getMessage());
        }
    }

    /**
     * This is the setter which magically encrypts the plain text password entered when
     * a user or administrator enters a password.
     * @param type $password
     * @return type
     */
    public function setPasswordAttribute($password) {
        return $this->attributes['password'] = bcrypt($password);
    }

}
