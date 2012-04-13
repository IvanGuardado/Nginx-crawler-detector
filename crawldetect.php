<?php
//Set this value with the minimum access per minute to be considered as a crawler
define('MAX_ACCESS_MINUTE', 12);

//Set the return this function to indentify only the Document files (exclude static scripts)
function isDocumentResource($resource)
{
    return !preg_match('/\./', $resource);
}

//
//Not modify below!
//
printTable(parse());

function parse()
{
    $list = array();
    $fileHandle = fopen('php://stdin', 'r');
    $regex = 
    /* IP match */
    '/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}).*'
    /* Datetime match */
    .'\[(\d{1,2}\/\w{3}\/\d{4}:\d{2}:\d{2}):\d{2} \+?\d+\]'
    /* File match */
    .'\s"GET ([^\s]+)/';

    while( ($line = fgets($fileHandle)) !== false){
        if(preg_match($regex, $line, $matches)){
            $ip = $matches[1];
            $time = $matches[2];
            $resource = $matches[3];
            if(isDocumentResource($resource)){
                if(!isset($list[$ip])){
                    $list[$ip] = array();
                }
                if(!isset($list[$ip][$time])){
                    $list[$ip][$time] = 1;
                }else{
                    $list[$ip][$time]++;
                }
            }
        }
    }
    fclose($fileHandle);
    return $list;
}

function printTable($list)
{
    echo "IP\t\tMinutes of crawling".PHP_EOL;
    foreach($list as $ip=>$timeAccessList){
        $level = 0;
        foreach($timeAccessList as $timeAccess=>$numAccess){
            if($numAccess > MAX_ACCESS_MINUTE){
                $level++;
            }
        }
        if($level > 0){
            echo "$ip\t$level".PHP_EOL;
        }
    }
    echo PHP_EOL;
}

