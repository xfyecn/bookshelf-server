<?php

namespace Bookshelf\ExternalApi;

use Bookshelf\DataType;
use Bookshelf\DataType\BookMetadata;
use Bookshelf\DataIo;

// implemented according to https://developers.google.com/books/docs/v1/using#PerformingSearch
class GoogleBooksApiRequest extends ExternalApiRequest {
    const GB_ID = 'GoogleBooks';

    public function __construct(){
        $this->identifier = self::GB_ID;
        parent::__construct();
    }

    public function request($request) {
        $this->volumeSearch($request);
    }

    public function volumeSearch($q, $limit = 0) {
        $this->request = $q;

        $json_string = DataIo\NetworkConnection::curlRequest('https://www.googleapis.com/books/v1/volumes?q=' . urlencode($q). '&prettyPrint=true');
        $data_array = json_decode($json_string, true);

        if(count($data_array['items']) <= 0) {
            $this->results = new DataType\ExternalApiResult();
            return;
        }

        // If limit == 0: set row limit to the number of returned rows, else set row limit to $limit
        $max_rows = ($limit == 0 ? count($data_array['items']) : $limit);
        // If row limit is larger than number of returned rows: set row limit to number of rows returned
        $max_rows = ($max_rows > count($data_array['items']) ? count($data_array['items']) : $max_rows);

        for($i = 0; $i < $max_rows; $i++) {
            $current_book_metadata = new BookMetadata();

            $current_book_metadata->author = implode(', ', $data_array['items'][$i]['volumeInfo']['authors']);
            $current_book_metadata->cover_image = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($data_array['items'][$i]['volumeInfo']['imageLinks']['smallThumbnail']));
            $current_book_metadata->description = $data_array['items'][$i]['volumeInfo']['description'];
            $current_book_metadata->identifier = $data_array['items'][$i]['volumeInfo']['industryIdentifiers'][1]['identifier']; // TODO: see ExternalApiRequest
            $current_book_metadata->language = $data_array['items'][$i]['volumeInfo']['language'];
            if($data_array['items'][$i]['volumeInfo']['subtitle']) {
                $current_book_metadata->title = $data_array['items'][$i]['volumeInfo']['title'] . ' - ' . $data_array['items'][$i]['volumeInfo']['subtitle'];
            }
            else {
                $current_book_metadata->title = $data_array['items'][$i]['volumeInfo']['title'];
            }

            $this->results->addMetadata($current_book_metadata, "GoogleBooks.{$i}");
        }
    }

    public function getBookFromIdentifier($identifier) {
        $book_metadata = new BookMetadata();

        return $book_metadata;
    }
}