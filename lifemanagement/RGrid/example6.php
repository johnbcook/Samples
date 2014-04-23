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

    /**
    * This an example script that uses the datagrid and shows it off
    */
    
    /**
    * Include the datagrid code
    */
    require_once('RGrid.php');
    
    $params['hostname'] = 'localhost';
    $params['username'] = 'datagrid';
    $params['password'] = 'datagrid';
    $params['database'] = 'phpguru';

    $sql = "SELECT cm_id,
                   ne_title,
                   cm_author,
                   cm_datetime,
                   cm_status,
                   cm_neid
              FROM comments,
                   news
             WHERE cm_status = 'ACTIVE'
               AND cm_neid = ne_id
          ORDER BY cm_id DESC";

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
    //$grid->allowSorting = false;

    /**
    * Turn the column headers off/on (they're on by default)
    */
    $grid->showHeaders = true;

    /**
    * Sets nice(r) display names instead of the raw column names
    */
    $grid->SetDisplayNames(array('cm_id'       => 'ID',
                                 'ne_title'    => 'Title',
                                 'cm_author'   => 'Author',
                                 'cm_email'    => 'Email',
                                 'cm_datetime' => 'Date & time',
                                 'cm_status'   => 'Status'));

    /**
    * This simply sets the specified columns not to be passed through htmlspecialchars()
    * Generally any column that shows HTML
    */
    $grid->NoSpecialChars('ne_title', 'status', 'actions');
    
    /**
    * Hide the cm_neid column as they're only used for linking purposes
    */
    $grid->HideColumn('cm_neid', 'cm_status');
    
    /**
    * Here just for show. This function sets the number of rows to set per page.
    * The default is 20 so this really is unnecessary.
    */
    $grid->SetPerPage(20);
    
    /**
    * The callback function that adds the extra column
    */
    $grid->AddCallback('RowCallback');

    function RowCallback(&$row)
    {
        $row['actions'] = sprintf('<a href="http://www.phpguru.org/article.php/%d#comment_%d" target="_blank">View</a>', $row['cm_neid'], $row['cm_id']);

        $length = 25;
        if (strlen($row['ne_title']) > $length) {
            $row['ne_title'] = substr($row['ne_title'],0, 25) . '...';
        }

        $row['ne_title'] = sprintf('<a href="/article.php/%d" target="_blank" title="%s">%s</a>', $row['cm_neid'], $row['ne_title'], $row['ne_title']);
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
        body {
            background-color: #eee;
        }
        
        .datagrid thead th,
        .datagrid tbody td,
        .datagrid tfoot td {
            font-family: Verdana;
            font-size: 75%;
        }

        .datagrid thead th,
        .datagrid tbody td,
        .datagrid a {
            color: #085A75;
            text-decoration: none;
            padding-left: 5px;
            padding-right: 5px;
        }

        .datagrid thead th {
            background-color: #D4E3E7;
            text-align: center;
            border: 1px solid white;
            border-right: 1px solid #95AFB9;
            border-right: 1px solid #95AFB9;
            border-bottom: 1px solid #95AFB9;
            height: 30px;
        }
        
        .datagrid tbody td.col_0,
        .datagrid tbody td.col_1,
        .datagrid tbody td.col_2,
        .datagrid tbody td.col_3 {
            background-color: white;
            border-right: 1px solid #D1D3D4;
        }
        
        .datagrid tbody td.col_1 a,
        .datagrid tbody td.col_4 a {
            text-decoration: none;
            color: #06779B;
        }
        
        .datagrid tbody td.altrow {
            background-color: #E7F3F7;
        }
        
        .datagrid tbody td.col_4 {
            background-color: #eee;
        }

        .datagrid tbody td.col_3 {
            border-right: none;
        }
    // -->
    </style>
</head>
<body>

    <h1>Example 6</h1>

    <?php $grid->Display() ?>

</body>
</html>