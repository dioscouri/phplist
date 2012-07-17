<?php
/**
 * @version	1.5
 * @package	Phplist
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Phplist::load( 'PhplistViewBase', 'views.base' );

class PhplistViewDashboard extends PhplistViewBase  
{
	/*
	function display($tpl=null) 
	{
		JLoader::import( 'com_phplist.helpers._base', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_phplist.library.grid', JPATH_ADMINISTRATOR.DS.'components' );
		if (empty($this->hidestats))
		{
			// TODO Update the stats method to get phplist-appropriate statistics
			$this->_lastThirty();
			
		    if (PhplistConfig::getInstance()->get('display_dashboard_statistics', '1'))
            {
                $this->_statistics();
            }
		}

        // form
            $validate = JUtility::getToken();
            $form = array();
            $controller = strtolower( $this->get( '_controller', JRequest::getVar('controller', JRequest::getVar('view') ) ) );
            $view = strtolower( $this->get( '_view', JRequest::getVar('view') ) );
            $action = $this->get( '_action', "index.php?option=com_phplist&controller={$controller}&view={$view}" );
            $form['action'] = $action;
            $form['validate'] = "<input type='hidden' name='{$validate}' value='1' />";
            $this->assign( 'form', $form );
            
        parent::display($tpl);
    }

    /**
     *
     * @return unknown_type
     
    function _lastThirty()
    {
        $database = JFactory::getDBO();
        $base = new PhplistHelperBase();
        $today = $base->getToday();
        $end_datetime = $today;
            $query = " SELECT DATE_SUB('".$today."', INTERVAL 1 MONTH) ";
            $database->setQuery( $query );
        $start_datetime = $database->loadResult();

        $runningtotal = 0;
        $runningsum = 0;
        $data = new stdClass();
        $num = 0;
        $result = array();
        $curdate = $start_datetime;
        $enddate = $end_datetime;
        while ($curdate <= $enddate)
        {
            // set working variables
                $variables = PhplistHelperBase::setDateVariables( $curdate, $enddate, 'daily' );
                $thisdate = $variables->thisdate;
                $nextdate = $variables->nextdate;

            // grab all records
                $model = JModel::getInstance( 'Subscriptions', 'PhplistModel' );
                $model->setState( 'filter_date_from', $thisdate );
                $model->setState( 'filter_date_to', $nextdate );
                $rows = $model->getList();
                $total = count( $rows );

            //store the value in an array
            $result[$num]['rows']       = $rows;
            $result[$num]['datedata']   = getdate( strtotime($thisdate) );
            $result[$num]['countdata']  = $total;
            $runningtotal               = $runningtotal + $total;

            // increase curdate to the next value
            $curdate = $nextdate;
            $num++;

        } // end of the while loop

        $data->rows         = $result;
        $data->total        = $runningtotal;

        // format for charts
        $alldata = new JObject();
        $categories = array();
        if (is_array($data->rows)) { foreach ($data->rows as &$r) {
            $r['label'] = $r['datedata']['mon']."/".$r['datedata']['mday'];
            $r['value'] = $r['countdata'];
            if (!in_array($r['label'], $categories)) {
                $categories[] = $r['label'];
            }
        } } // end foreach
        
        $data->title = 'Subscriptions';
        $data->categories = $categories;
        
        $alldata->datasets = array( $data );
        $this->getChartBarDaily( $alldata, 'Last Thirty Days', 'lastThirty' );
    }

    /**
     *
     * @param unknown_type $data
     * @param unknown_type $chart_title
     * @param unknown_type $variable_name
     * @return unknown_type
     
    function getChartBarDaily( $data, $chart_title, $variable_name, $chart_type='Column' )
    {
        $args = array();

        /** Charts expect data to come as an array of objects where the objects
         *  look like:
         *  JObject(){
         *     public $value;
         *     public $label;
         *  }
         */
        //$args['data'] = array();
/*
        $datasets = array();
        
        if (!empty($data)) 
        {
            foreach ($data->datasets as $key=>$dataset)
            {
                $datasets[$key]['categories'] = $dataset->categories;
                $datasets[$key]['title'] = $dataset->title;
                $datasets[$key]['data'] = array();
                foreach ($dataset->rows as $r) 
                {
                    $obj = new JObject;
                    $obj->value = floatval(str_replace(',', '', $r['value']));
                    $obj->label = $r['label'];
                    $datasets[$key]['data'][] = $obj;
                }   
            }
        }
        
        $args['datasets'] = $datasets;
        $args['title'] = $chart_title;
        $args['type']  = $chart_type;

        // Try to render the chart via an installed plugin first.
        $dispatcher =& JDispatcher::getInstance();
        $results = $dispatcher->trigger('renderPhplistChart', $args);

        if (empty($results)) {
            JLoader::import( 'com_phplist.library.charts', JPATH_ADMINISTRATOR.DS.'components' );
            // No Charts plugin enabled, use Fusion Charts.
            $chart = PhplistCharts::renderFusionChart($args['datasets'], $chart_title, $chart_type);
        } else {
            $chart = $results[0];
        }

        $row = new JObject();
        $row->image = $chart;
        $this->assign( "$variable_name", $row);
    }
    
    /**
     * Get some basic stats about the extension
     *
     
    function _statistics()
    {
    	$object = new JObject();
    	// returns an array of objects with ->title and ->value
    	$model = JModel::getInstance( 'Newsletters', 'PhplistModel' );
    	$model->setState( 'order', 'tbl.name' );
    	$list = $model->getList();
    	$this->assign('newsletters', $list);
    }*/
}

?>