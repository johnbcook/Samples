<?php
    /**
    * o------------------------------------------------------------------------------o
    * | This package is licensed under the Phpguru license. A quick summary is       |
    * | that for commercial use, there is a small one-time licensing fee to pay. For |
    * | registered charities and educational institutes there is a reduced license   |
    * | fee available. You can read more  at:                                        |
    * |                                                                              |
    * |                  http://www.phpguru.org/static/license.html                  |
    * o------------------------------------------------------------------------------o
    *
    * © Copyright 2008,2009 Richard Heyes
    */
error_reporting(0);
    /**
    * This an example script that uses the datagrid and shows it off
    */
    
    /**
    * Include the datagrid code
    */
    require_once('RGrid.php');
    
    $params['hostname'] = '127.0.0.1';
    $params['username'] = 'root';
    $params['password'] = 'biteme';
    $params['database'] = 'lifemanagement';

    $sql = "SELECT name, type, description, priority, duedate, actionable,
            nextaction, project, status
            FROM tasks                   
            ORDER BY 'Due Date'  DESC";

    /**
    * Create the datagrid with the connection parameters and SQL query
    * defined above. You MUST specify an ORDER BY
    * clause (with ASC/DESC direction indicator) - if not then ordering
    * will be fubar. I will eventually fix this so that the headers
    * aren't clickable if you don't supply an ORDER BY, for the time
    * being just specify an ORDER BY clause (the one you specify will
    * be used by default).
    */
    $grid = RGrid::Create($params, $sql);

    /**
    * Disable sorting
    */
    $grid->allowSorting = false;

    /**
    * Turn the column headers off/on
    */
    $grid->showHeaders = true;

    /**
    * Sets nice(r) display names instead of the raw column names
    */
    $grid->SetDisplayNames(array('name'   => 'Task',
                                 'duedate'    => 'Due',
                                 'type'   => 'Type',
                                 'priority'    => 'Priority',
                                 'description' => 'Description',
                                 'project'   => 'Project',
    							 'actionable'   => 'Actionable',
    							 'nextaction'   => 'Next Action',
    							 'status' => 'Status'));

    /**
    * This simply sets the specified columns not to be passed through htmlspecialchars()
    * Generally any column that shows HTML
    */
   /** $grid->NoSpecialChars('ne_title', 'status', 'actions');
    
    /**
    * Hide the cm_neid column as it's only used for linking purposes
    */
   /** $grid->HideColumn('cm_neid', 'cm_status');
    
    /**
    * Here just for show. This function sets the number of rows to set per page.
    * The default is 20.
    */
    $grid->SetPerPage(15);
    
    /**
    * The callback function that adds the extra column
    */
  /**  $grid->AddCallback('RowCallback');

  /** function RowCallback(&$row)
    {
        $row['actions'] = sprintf('<a href="http://www.phpguru.org/article.php/%d#comment_%d" target="_blank">View</a>', $row['cm_neid'], $row['cm_id']);

        $length = 25;
        if (strlen($row['ne_title']) > $length) {
            $row['ne_title'] = substr($row['ne_title'],0, 25) . '...';
        }

        $row['ne_title'] = sprintf('<a href="/article.php/%d" target="_blank" title="%s">%s</a>', $row['cm_neid'], $row['ne_title'], $row['ne_title']);
        
        // Format the datetime field. We do it here so that ordering is OK
        $row['cm_datetime'] = date('H:i jS F Y', strtotime($row['cm_datetime']));
    }
    
    /**
    * The HTML. The appearance can be customised using CSS. Naturally you would (should) put
    * all of the datagrid styling in a central CSS file that can be <link>ed to by all of
    * your websites' pages. That way:
    *  o All of your datagrids will look the same
    *  o Changes to the appearance will affect all of your datagrids across your
    *    entire website. You might not want this necessarily, but might.
    *
    * eg. <link rel="stylesheet" type="text/css" media="screen" href="/css/datagrid.css" />
    */
?>
<html>
<head>
    <title>Datagrid example</title>
    
    <style type="text/css">
    <!--
        /* Format the page */
        body {
            background-color: #eee;
        }
        
        /* Format the entire table */
        .datagrid {
            font-family: Arial;
            font-size: smaller;
            width: 590px;
        }

        /* Format the table headers */
        .datagrid thead th {
            background-color: #003;
            color: #fff;
            height: 25px;
            text-align: left;
        }
        
        /* Colour the headers white */
        .datagrid thead th a {
            color: white;
            text-decoration: none;
        }
        
        /* Padding on headers and body cells */
        .datagrid thead th,
        .datagrid tbody td {
            padding-left: 5px;
            padding-right: 5px;
        }
        
        /* Row colours */
        .datagrid tbody td.col_0,
        .datagrid tbody td.col_2        {
            background-color: #fff;
        }
        
        /* Alternate row colours */
        .datagrid tbody td.col_1,
        .datagrid tbody td.col_3        {
            background-color: #cff;
        }
        
        /* Show the left hand border on all table cells */
        .datagrid tbody td {
            border-left: 1px solid #6c9;
        }
        
        /* Don't show the left border on the last column... */
        .datagrid tbody td.col_4 {
            border-left: 0;
        }
        
        /* ...show the right border on the next to last column */
        .datagrid tbody td.col_3 {
            border-right: 1px solid #6c9;
        }
        
        /* Show the bottom border */
        .datagrid tbody td.row_14 {
            border-bottom: 1px solid #6c9;
        }
        
        /* No underline for the view links */
        .datagrid tbody td.row_14 {
            border-bottom: 1px solid #6c9;
        }
        
        .datagrid tbody td.col_1 a,
        .datagrid tbody td.col_4 a {
            text-decoration: none;
        }
        
        .datagrid tbody td.col_4 {
            border-bottom: 0;
        }
    // -->
    </style>
</head>
<body>
    <?php $grid->Display() ?>
</body>
</html>