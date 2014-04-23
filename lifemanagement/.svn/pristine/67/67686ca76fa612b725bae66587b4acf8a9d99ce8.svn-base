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
    * No sorting by the status column
    */
    $grid->NoSort('cm_status');

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
    $grid->NoSpecialChars('ne_title', 'cm_status');
    
    /**
    * Hide the cm_neid column as it's only used for linking purposes
    */
    $grid->HideColumn('cm_neid');
    
    /**
    * Here just for show. This function sets the number of rows to set per page.
    * The default is 20.
    */
    $grid->SetPerPage(15);
    
    /**
    * This is the callback that colours the text red or green
    */
    $grid->AddCallback('RowCallback');

    function RowCallback(&$row) // The ampersand is so that any changes made are reflected in the final grid
    {
        $row['cm_status'] = ($row['cm_status'] == 'ACTIVE' ? '<span style="font-weight: 700; color: green">ACTIVE</span>' : '<span style="font-weight: 700; color: red">ACTIVE</span>');
        $row['ne_title']  = sprintf('<a href="/article.php/%d" target="_blank">%s</a>', $row['cm_neid'], $row['ne_title']);
    }
    
    /**
    * The HTML. The appearance can be customised using CSS. Naturally you would (should) put
    * all of the datagrid styling in a central CSS file that can be <link>ed to by all of
    * your websites' pages. That way:
    *  o All of your datagrids will look the same
    *  o Changes to the appearance will affect all of your datagrids across your
    *    entire website. You might want this necessarily, but must do.
    *
    * eg. <link rel="stylesheet" type="text/css" media="screen" href="/css/datagrid.css" />
    */
?>
<html>
<head>
    <title>Datagrid example</title>
    
    <style type="text/css">
        body {
            background-color: #eee;
        }

        table.datagrid {
            font-family: Tahoma;
            font-size: 8pt;
            border-collapse: collapse;
            width: 600px;
        }


        .datagrid thead th {
            color: black;
            background-color: #d4d0c8;
            border-color: white #808080 #808080 white;
            border-width: 1px;
            border-style: solid;
            text-align: left;
            padding-left: 5px;
            font-weight: normal;
        }

        /**
        * Format the table cells
        */
        .datagrid tbody td {
            padding-left: 5px;
            border: 1px solid #ddd;
            background-color: white;
        }
        
        /*
        * Format the alternate table columns
        */
        .datagrid tbody td.col_1,
        .datagrid tbody td.col_3, {
            background-color: #eee;
        }
        
        /**
        * No link underlining
        */
        .datagrid a {
            text-decoration: none;
        }
    </style>
    
    <script language="javascript" type="text/javascript" defer="defer">
        /**
        * 
        */
        function TdMouseOver()
        {
            this.className += ' mouseover';
        }
        var elements = document.getElementsByTagName('td');

        for (var i=0; i<elements.length; i++) {
            elements[i].onmouseover = TdMouseOver;
        }
    </script>
</head>
<body>
    <p>
        This datagrid is styled to look vaguely like Windows.
    </p>

    <?php $grid->Display() ?>
    
    Stuff you could do:
    
    <ul>
        <li>
            When a row is clicked, colour it <span style="color: #0A246A; font-weight: 700">dark blue</span> like
            a Windows datagrid would. Also you check on the status of the SHIFT and CTRL keys to determine which
            rows should be highlighted.
        </li>
    </ul>
    
    <p>
        You can see the ill-effect trying to order by the date/time column has. ie It's not the expected order.
        You can get aroung this by not specifying the format in your SQL query and instead using a callback
        function to do the formatting. In this case you would need to use strtotime().
    </p>
</body>
</html>