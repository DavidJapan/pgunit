<?php
/**
 * I'm using a virtual directory as the key for a client-side request to
 * specify which model to call from the controller. So, I've plumped for simple generic controllers
 * which call complex, specific models to administer the database.
 */
namespace App\AppClasses\Tools;

use Illuminate\Http\Request;
use App\AppClasses\DataTableModelException;

/**
 * In order to have specific models that can be called from generic
 * controllers, we need this mapper that gets the path used to call a controller
 * and initialises an instance of the model associated with that path.
 *
 * @author David Mann
 */
class PathToModelMapper implements \JsonSerializable {
    //const NAMESPACE = 'App\\Models\\';
    /**
     * Don't add the public visibility modifier to this constant because PHPDocumentor breaks at that.
     * PHP 7.1 and up accept visibility modifiers for consts 
     * But maybe the biggest problem is I can't use the name NAMESPACE because for some reason it breaks PHPDocumentor and the class
     * throws an error that there is no summary for this file. So, I'm forced to change the name to
     * NAME_SPACE
     * @link https://www.php.net/manual/en/language.oop5.constants.php
     * @var NAME_SPACE.
     */
    const NAME_SPACE = 'App\\Models\\';
    /**
     * The request object passed to a controller on request has
     * the path() method from which we can extract the directory
     * @var string  
     */
    private $path;
    /**
     * We derived this from the path passed in the request object
     * @var string
     */
    private $directory;
    /**
     * We can determine this by passing the directory to the config for this app
     * which will return the name of the desired model.
     * @var string
     */
    private $model_name;
    /**
     * The name of this application
     * @var string 
     */
    private $app_name = 'datatables';

    /**
     * Gets the model name from the specified request, or in situations
     * such as debugging, from the specified directory.
     * @param Request $request
     * @param type $directory
     * @throws DataTableModelException
     */
    public function __construct(Request $request = null, $directory = null) {
        if (!is_null($request)) {
            $this->path = $request->path();
            $this->directory = strtok($this->path, "/");
        } else {
            $this->directory = $directory;
        }
        $models = config($this->app_name . ".models");
        if (is_null($models)) {
            throw new DataTableModelException("Unable to find a configuration file "
                    . "called " . $this->app_name . "php or an array called models."
                    . " Check the configuration files.");
        }
        if (!array_key_exists($this->directory, $models)) {
            throw new DataTableModelException('Could not find a model using this URL: ' . $this->directory);
        }
        $this->model_name = self::NAME_SPACE . $models[$this->directory];
    }

    /**
     * The Getter method for path
     * @return string
     */
    public function path() {
        return $this->path;
    }

    /**
     * The Setter method for path.
     * @param string $path
     */
    public function setPath($path) {
        $this->path = $path;
    }
    /**
     * The Getter method for directory
     * @return string
     */
    public function directory() {
        return $this->directory;
    }
    /**
     * Getter method for model_name
     * @return type
     */
    public function modelName() {
        return $this->model_name;
    }
    /**
     * Returns a stdClass object with 3 properties:
     * path, directory and model_name
     * which can be encoded as a JSON string and sent to the client.
     * @return \stdClass
     */
    public function jsonSerialize() {
        $map = new \stdClass();
        $map->path = $this->path;
        $map->directory = $this->directory;
        $map->model_name = $this->model_name;
        return $map;
    }
}
