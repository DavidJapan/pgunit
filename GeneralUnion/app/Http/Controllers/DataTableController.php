<?php

/**
 * 
 * A controller which provides the most frequently used methods needed for displaying a 
 * jQuery DataTable that gets its data from a PostgreSQL database.
 * 
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AppClasses\DataTableModelException;
use App\AppClasses\Tools\PathToModelMapper;
/**
 * Description of DataTableController: This controller is the base class responsible for providing
 * the necessary data to display a jQuery data table successfully. A lot can go wrong between the 
 * database and the user interface and this exception is thrown if this class fails to acquire 
 * the data it needs from a range of resources. Note that the __construct function is not used
 * in this controller.
 * 
 * @uses App\AppClasses\DataTableModelException 
 * @author David Mann
 */
class DataTableController extends Controller {

    /**
     * The model used to handle connections
     * to a PostgreSQL database to get and edit data from a related table.
     * @var object which extends Eloquent
     * @example App\Models\DataTable 
     */
    public $model;

    /**
     * This is the fully qualified actual class used as the model for this controller.
     * 
     * @var object
     * @example App\Models\DataTable The model used to connect to a PostgreSQL database table
     * and handle getting and editing data from it.
     */
    public $model_class;

    /**
     * The full name of the class invoked by the URL requesting this controller.
     * @example companies/view If the user requests the URL companies/view, this controller
     * will use the model App\Models\Company
     * @uses config\gu.php The config file gu has a hash map of URLs and models.
     * @var string 
     */
    public $short_model_name;

    /**
     *
     * @var array The name of the primary key(s) in the PostgreSQL database table used by the model
     * associated with this controller. This is an array to accomodate the possibility of composite primary keys.
     */
    protected $primaryKey;

    /**
     *
     * @var string The relative path to the view file which will display the data from the table 
     * associated with this controller.
     */
    protected $view_path;

    /**
     *
     * @var Array A hashed array of data required to display the data table correctly.
     * @see initViewData 
     */
    public $view_data;

    /**
     *
     * @var String The relative URL used to invoke this controller.
     * @requires A config file called datatables.php with an array called models.
     * @uses config/datatables.php 
     * @see model_name
     */
    public $directory;

    /**
     * I think I have to abandon this constructor. I need to be able to trap errors in the view method.
     * The problem is that the __construct function is called before view and exceptions get thrown 
     * outside the scope of the view method try/catch block. So, you end up with 
     * the full exception message and stack being displayed to the user. This function is called if this controller 
     * is invoked from a route in web.php, but if youtry to instantiate the controller from a method of another controller, the middleware
     * callback function is not called.
     * @param Request $request
     * @return void
     * @deprecated
     */
    public function __construct() {
        //$this->middleware(function ($request, $next) {
        //    return $next($request);
        //});
    }

    /**
     * I've added the $directory as an optional parameter to make testing
     * easier. For a normal table, we can get the model name from the configuration files by passing
     * the path gleaned from the current request. When testing, the path will have extra components like qunit
     * so we can't rely on it to extract the model name.
     * 
     * @param Request $request
     * @param string $directory
     */
    public function init(Request $request = null, $directory = null) {
        $mapper = new PathToModelMapper($request, $directory);
        $fully_qualified_model_name = $mapper->modelName();
        $this->model = new $fully_qualified_model_name();
        $this->model_class = $fully_qualified_model_name;
        $this->primaryKey = $this->model->getKeyName();
        $this->short_model_name = get_class($this->model);
        $this->model->setDirectory($mapper->directory());
        $this->model->initViewData();
        $this->view_data = $this->model->getViewData();
    }

    /**
     * This gets the correct path to the view from the model and returns a Laravel view 
     * (probably a blade template), passing the hash stored in the view_data array.
     * @uses view the Laravel helper function.
     * @return \Illuminate\View\View
     */
    public function view(Request $request) {
        try {
            $this->init($request);
            $view_path = $this->model->view_path();
            return response()
                            ->view($view_path, ["view_data" => $this->view_data], 200)
                            ->header('Content-Type', 'text/html');
        } catch (DataTableModelException $de) {
            return $this->handleException($de);
        }
    }

    /**
     * We can't arbitrarily add a directory property to the model here now
     * because the model then thinks directory is a field in the database
     * because we're using fillable automatically. Fillable gets its
     * values directly from the database table.
     * This would be a bad idea:
     * <code>
     * $this->model->directory = $this->directory;
     * </code>
     * This method sets the following variables:
     * <ul>
     * <li>
     * model: an instance of the model associated with the URL used to request this controller.
     * </li>
     * <li>
     * model_class: fully qualified name of this model
     * </li>
     * <li>
     * model_name: the name of this model
     * </li>
     * @return void
     */
    protected function setupModel(Request $request) {
        $mapper = new PathToModelMapper($request);
        $fully_qualified_model_name = $mapper->modelName();
        $this->model = new $fully_qualified_model_name();
        $this->model_class = $fully_qualified_model_name;
        $this->primaryKey = $this->model->getKeyName();
        $this->short_model_name = get_class($this->model);
        return json_encode($mapper->jsonSerialize());
    }
    /**
     * Uses PathToModelMapper and then returns the mapper serialised
     * as JSON.
     * @param Request $request
     * @return string JSON encoded string
     */
    public function getModelFromPath(Request $request) {
        $mapper = new PathToModelMapper($request);
        return json_encode($mapper->jsonSerialize());
    }

    /**
     * Returns the view_data array. 
     * @see initViewData
     * @return array 
     */
    public function view_data() {
        return $this->view_data;
    }

    /**
     * Initialises the view_data variable. 
     * <ul>
     * <li>
     * columns:
     * We pass the array of columns directly to the view.
     * The view consumes this in two ways:
     * 1. with a @json directive pass a json representation of the array
     * to the Javascript Datatable options object.
     * 2. use the PHP array in the row template, looping through
     * the array to create elements etc. 
     * Each column object has a display property and the templates
     * can handle
     * 1. plain text
     * 2. textbox with the name of a change event handler
     * 3. textarea with the name of a change event handler
     * data_table_id: The id of the data table element in the view.
     * </li>
     * <li>
     * primaryKey:
     * The primary key of the table in the database
     * associated with the model invoked with this controller.
     * </li>
     * <li>
     * directory: 
     * The URL to invoke this controller's model.
     * </li>
     * <li>
     * width: 
     * The width to be used to display the table.
     * Bootstrap installed on the client-side. The width should be set to a
     * number that matches the Bootstrap grid system. A width of 12 is the full width.
     * all: A JSON-encoded String of data generated by the all() method of the model
     * associated with this controller.
     * </li>
     * </ul> 
     * 
     * @requires Bootstrap on client-side
     * @return void 
     */
    protected function initViewData() {
        $this->model->initViewData();
        $this->view_data = $this->model->getViewData();
    }

    /**
     * This is used when debugging and printing directly to the screen without
     * the Laravel layer of views.
     * @param Exception $e
     */
    protected function handleEchoException($e) {
        echo 'There has been an error:<BR>';
        echo 'File: ' . $e->getFile() . '<BR>';
        echo 'Line: ' . $e->getLine() . '<BR>';
        echo 'Error: ' . $e->getMessage() . '<BR>';
        //echo $this->MakePrettyException($e);
        $trace = $e->getTrace();
        echo 'Full trace of error: ';
        echo '<table>';
        for ($i = 0; $i < 4; $i++) {
            echo '<tr>';
            echo '<td>';
            foreach ($trace[$i] as $key => $value) {
                echo $key . ': ' . json_encode($value) . '<br>';
            }
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    /**
     * The error property included in the response object contains
     * the message sent by the exception passed to this function and 
     * errorCode contains the index number of the exception if is available.
     * @param Exception $e
     * @param mixed usually integer $id
     * @return string JSON-encoded String with the properties error and errorCode. 
     */
    public function handleException($e) {
        $code = (string) $e->getCode();
        if (!($e instanceof DataTableModelException)) {
            $de = new DataTableModelException($e->getMessage(), $code, $e);
        } else {
            $de = $e;
        }
        return response($de->jsonSerialize())
                        ->header('Content-Type', 'application/json');
    }

}
