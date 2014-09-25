<?php

namespace Bookshelf\Core;

use Bookshelf\DataType\BookMetadata;
use Bookshelf\DataIo\DatabaseConnection;
use Bookshelf\DataIo;

class LibraryManager {
    require Application::ROOT_DIR . 'config.php';
    
    private $database_connection;
    
    function __construct() {
        $this->database_connection = new DatabaseConnection();
    }
    
    function addBook($file_name, $metadata = new BookMetadata()){
        $file_hash = DataIo\FileManager::hash($LIBRARY_DIR . '/' . $filename);
        $args = array('file_name' => "'" . $file_name . "'",
                      'file_hash' => "'" . $file_hash . "'");
        
        $args .= $metadata->getArray();
        foreach($args as $key => $value) {
            $args[$key] = "'" . $value . "'";
        }
        
        $this->database_connection->insertLibraryData($args)
    }
    
    function getBook($file_name, $file_hash) {
        $result = $this->database_connection->selectLibraryData(array('file_name' => $file_name, 
                                                                      'file_hash' => $file_hash));
        return (empty($result[0]) ? -1 : $result[0]);
    }
    
    function listBooks() {
        $result = $this->database_connection->selectLibraryData(NULL, array('file_name', 'file_hash', 'title'));
        return (empty($result[0]) ? -1 : $result[0]);
    }
}