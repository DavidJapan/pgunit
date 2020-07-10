<?php
/**
 * This extends EditableDataTable, sets some key values like primaryKey and initialises
 * the two columns report_heading_id and report_heading
 */
namespace App\Models;

/**
 * Description of ReportHeading. The model used to administer the table report_headings
 *
 * @author David Mann
 */
class ReportHeading extends EditableDataTable
{
    /**
     * report_headings
     * @var string
     */
    protected $table = 'report_headings';
    /**
     * report_heading_id
     * @var string
     */
    protected $primaryKey = 'report_heading_id';
    /**
     * report_heading
     * @var string 
     */
    public $name_field = 'report_heading';
    /**
     * 6
     * @var int
     */
    protected $width = 6;
    /**
     * [[2, 'asc']]
     * @var array a 2-dimensional array
     */
    public $order = [[2, 'asc']];
    /**
     * Initialises the columns.
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->initReportHeadingId();
        $this->initReportHeading();
    }
    /**
     * Initialises report_heading
     */
    private function initReportHeading()
    {
        $column_name = 'report_heading';
        $column = $this->getColumn($column_name);
        $column->setWidth('75%');
        $this->setColumn($column_name, $column);
        $control = $this->getAddControl($column_name);
        $control->setRequired([
            'message' => 'Please enter a name for the report heading.'
        ]);
        $this->setAddControl($column_name, $control);

        $control = $this->getEditControl($column_name);
        $control->setRequired(true);
        $this->setEditControl($column_name, $control);
    }
    /**
     * Initialises report_heading_id
     */
    private function initReportHeadingId()
    {
        $column = $this->getColumn('report_heading_id');
        $this->setColumn('report_heading_id', $column);
    }
}
