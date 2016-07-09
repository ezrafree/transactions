<?php

/**
 * Functions
 */

// iterate through the files in the directory
function getFiles( $dirname ) {
    $dir = new DirectoryIterator( $dirname );
    foreach ($dir as $fileinfo) {
        if (!$fileinfo->isDot()) {
            $filenames[] = $fileinfo->getFilename();
        }
    }
    return $filenames;
}

// parse the date from the filename
function parseStatementDate( $filename ) {
    if(preg_match("/\d{2}-\d{2}-\d{4}/", $filename, $match)) {
        return $match[0];
    }
}

// return active class if current page
function isPage($page) {
    if( isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] == '/' ) {
        if( $page == 'home' ) {
            return ' class="active"';
        }
    } else if( isset($_GET['page']) && $_GET['page'] ) {
        if( $page == $_GET['page'] ) {
            return ' class="active"';
        }
    }
}

// returns true if $needle is a substring of $haystack
function contains($needle, $haystack) {
    return stripos($haystack, $needle) !== false;
}
