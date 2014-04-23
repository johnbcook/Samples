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
    * Turn the column headers off/on
    */
    $grid->showHeaders = true;

    /**
    * Sets nice(r) display names instead of the raw column names
    */
    $grid->SetDisplayNames(array('cm_id'       => 'ID',
                                 'ne_title'    => 'Title',
                                 'cm_author'   => 'Author',
                                 'cm_email'    => 'Email',
                                 'cm_datetime' => 'Date&nbsp;&&nbsp;time',
                                 'cm_status'   => 'Status'));

    /**
    * This simply sets the specified columns not to be passed through htmlspecialchars()
    * Generally any column that shows HTML
    */
    $grid->NoSpecialChars('ne_title', 'status', 'cm_datetime');
    
    /**
    * Hide the cm_neid column as it's only used for linking purposes
    */
    $grid->HideColumn('cm_neid', 'cm_status');
    
    /**
    * Here just for show. This function sets the number of rows to set per page.
    * The default is 20.
    */
    $grid->SetPerPage(15);
    
    /**
    * Sets the header
    */
    $grid->headerHTML = '<img src="header.png" />';
    
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
        /**
        * The overall datagrid width
        */
        table.datagrid {
            width: 600px;
        }

        /**
        * Format the table headers
        */
        .datagrid thead th {
            text-align: left;
            padding-left: 5px;
            font-size: 10pt;
            border-bottom: 1px solid #666;
        }
        
        /**
        * Don't want the header HTML to have a border.
        */
        .datagrid thead th#header {
            border: 0;
        }
        
        /**
        * Format the table cells
        */
        .datagrid tbody td {
            padding-left: 5px;
            padding-right: 5px;
        }
        
        /**
        * Specify the datagrids font
        */
        table.datagrid {
            font-family: Verdana;
        }
        
        .datagrid tbody td a {
            text-decoration: none;
        }
        
        .datagrid tbody td.col_1,
        .datagrid tbody td.col_3,{
            background-color: #eee;
        }
        
        .datagrid thead th a {
            text-decoration: none;
        }
    // -->
    </style>
</head>
<body>
    <h1>Example 4</h1>
    
    <p>
        As you can see, design is not exactly my forte... However the presentation was only changed 
        by CSS and a header image. No modification of the datagrid class code. Bonza.
    </p>

    <?php $grid->Display() ?>
</body>
</html>