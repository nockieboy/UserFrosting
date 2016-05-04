    /**
     *  Sorts, paginates, and renders a collection according to the GET parameters (so, JSON or CSV)
     */
    function sortPaginateRender($collection, $sort_field, $total, $csv_type, $csv_callback = null){
        $get = $this->request->get();
        
        $size = isset($get['size']) ? $get['size'] : null;
        $page = isset($get['page']) ? $get['page'] : null;
        $sort_order = isset($get['sort_order']) ? $get['sort_order'] : "asc";
        $format = isset($get['format']) ? $get['format'] : "json";
        
        // Count filtered results
        $total_filtered = count($collection);
        
        // Sort
        if ($sort_order == "desc")
            $collection = $collection->sortByDesc($sort_field, SORT_NATURAL|SORT_FLAG_CASE);
        else        
            $collection = $collection->sortBy($sort_field, SORT_NATURAL|SORT_FLAG_CASE);
            
        // Paginate
        if ( ($page !== null) && ($size !== null) ){
            $offset = $size*$page;
            $collection = $collection->slice($offset, $size);
        }
        
        $result = [
            "count" => $total,
            "rows" => $collection->values()->toArray(),
            "count_filtered" => $total_filtered
        ];
        
        //$query = Capsule::getQueryLog();    
        
        if ($format == "csv"){
            $settings = http_build_query($get);
            $date = date("Ymd");
            $this->response->headers->set('Content-Disposition', "attachment;filename=$date-$csv_type-$settings.csv");
            $this->response->headers->set('Content-Type', 'text/csv; charset=utf-8');
            $keys = $collection->keys()->toArray();
            echo implode(array_keys($result['rows'][0]), ",") . "\r\n";
            
            if ($csv_callback){
                echo $csv_callback($result['rows']);
            } else {
                foreach ($result['rows'] as $row){
                    echo implode($row, ",") . "\r\n";    
                }
            }
        } else {
            // Be careful how you consume this data - it has not been escaped and contains untrusted user-supplied content.
            // For example, if you plan to insert it into an HTML DOM, you must escape it on the client side (or use client-side templating).
            $this->response->headers->set('Content-Type', 'application/json; charset=utf-8');
            echo json_encode($result, JSON_PRETTY_PRINT);
        }
    }     